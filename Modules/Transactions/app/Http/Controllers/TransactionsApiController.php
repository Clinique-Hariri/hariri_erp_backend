<?php

namespace Modules\Transactions\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Support\Enum\UserRoles;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Modules\Clinic\Models\Doctor;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Salary;
use Modules\MedicalReferences\Models\InsuranceSociety;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\CheckupAnalysis;
use Modules\Patients\Models\Hospitalization;
use Modules\Patients\Models\Operation;
use Modules\Patients\Models\Patient;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;
use Modules\Transactions\Http\Resources\PatientTransactionResource;
use Modules\Transactions\Http\Requests\Patient\StoreTransactionRequest;
use Modules\Transactions\Http\Requests\Patient\UpdateTransactionsRequest;
use Modules\Transactions\Http\Resources\TransactionResource;
use Modules\Transactions\Models\Transaction;
use Throwable;

class TransactionsApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::TRANSACTIONS_VIEW);
    }

    try {
      $user = auth()->user();
      $isAdminOrSuperAdmin = $user->hasRole(UserRoles::SUPER_ADMIN) || $user->hasRole(UserRoles::ADMIN);

      $model = Transaction::with([
        'user',
        'transactionable' => function (MorphTo $morphTo) {
          $morphTo->morphWith([
            Checkup::class => ['patient.insuranceSocietyBranch', 'doctor.user', 'paymentAction.user'],
            CheckupAnalysis::class => ['checkup.patient.insuranceSocietyBranch', 'checkup.doctor.user', 'paymentAction.user', 'services'],
            Hospitalization::class => ['patient.insuranceSocietyBranch', 'doctor.user', 'paymentAction.user'],
            Operation::class => ['patient.insuranceSocietyBranch', 'surgeons.doctor.user', 'paymentAction.user'],
            Salary::class => ['employee.user'],
          ]);
        },
        'accountable' => function (MorphTo $morphTo) {
          $morphTo->morphWith([
            Patient::class => ['insuranceSocietyBranch'],
            Doctor::class => ['user'],
            Employee::class => ['user'],
          ]);
        },
      ]);

      if (!$isAdminOrSuperAdmin) {
        $employee = $user->employee;
        $doctor = $user->doctor;

        $model->where(function ($query) use ($user, $employee, $doctor) {
          // Transactions where user is creator
          $query->where('user_id', $user->id);

          // Transactions where user's employee is accountable
          if ($employee) {
            $query->orWhere(function ($q) use ($employee) {
              $q->where('accountable_type', Employee::class)
                ->where('accountable_id', $employee->id);
            });
          }

          // Transactions where user's doctor is accountable
          if ($doctor) {
            $query->orWhere(function ($q) use ($doctor) {
              $q->where('accountable_type', Doctor::class)
                ->where('accountable_id', $doctor->id);
            });
          }
        });
      }

      if ($request->filled('type')) {
        $model->where('type', $request->type);
      }

      if ($request->filled('status')) {
        $model->where('status', $request->status);
      }

      if ($request->filled('transactionable_type')) {
        $map = [
          'checkup' => Checkup::class,
          'analysis' => CheckupAnalysis::class,
          'hospitalization' => Hospitalization::class,
          'operation' => Operation::class,
          'salary' => Salary::class,
          'other' => null,
        ];
        $model->where('transactionable_type', $map[$request->transactionable_type] ?? null);
      }

      if ($request->filled('transactionable_id')) {
        $model->where('transactionable_id', $request->transactionable_id);
      }

      if ($request->filled('accountable_type')) {
        $map = [
          'patient' => Patient::class,
          'doctor' => Doctor::class,
          'insurance' => InsuranceSociety::class,
          'employee' => Employee::class,
          'other' => null,
        ];
        $model->where('accountable_type', $map[$request->accountable_type] ?? null);
      }

      if ($request->filled('accountable_id')) {
        $model->where('accountable_id', $request->accountable_id);
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      if ($request->filled('search')) {
        $search = $request->search;
        $model->where(function ($q) use ($search) {
          $q->where('transaction_number', 'like', "%{$search}%")
            ->orWhere('details', 'like', "%{$search}%")
            ->orWhereHas('user', function ($q2) use ($search) {
              $q2->where('fullname', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        });
      }

      $model->orderBy('created_at', 'desc');

      if ($request->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: TransactionResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }


  public function show($id)
  {
    $this->authorizePermission(PermissionNames::TRANSACTIONS_VIEW);

    try {
      $user = auth()->user();
      $isAdminOrSuperAdmin = $user->hasRole(UserRoles::SUPER_ADMIN) || $user->hasRole(UserRoles::ADMIN);

      $query = Transaction::with(['transactionable', 'user']);
      
      if (!$isAdminOrSuperAdmin) {
        $employee = $user->employee;
        $doctor = $user->doctor;

        $query->where(function ($q) use ($user, $employee, $doctor) {
          $q->where('user_id', $user->id);

          if ($employee) {
            $q->orWhere(function ($subq) use ($employee) {
              $subq->where('accountable_type', Employee::class)
                ->where('accountable_id', $employee->id);
            });
          }

          if ($doctor) {
            $q->orWhere(function ($subq) use ($doctor) {
              $subq->where('accountable_type', Doctor::class)
                ->where('accountable_id', $doctor->id);
            });
          }
        });
      }

      $model = $query->findOrFail($id);

      return $this->successResponse(
        data: new TransactionResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNames::TRANSACTIONS_CREATE);

    $data = $this->validateRequest($request, StoreTransactionRequest::rules());
    $data['user_id'] = auth()->id();
    $data['status'] = Status::COMPLETED;
    try {
      $transaction = Transaction::create([
        'amount' => $data['amount'],
        'details' => $data['details'] ?? null,
        'type' => $data['type'],
        'status' => $data['status'],
        'user_id' => $data['user_id'],
      ]);

      return $this->successResponse(
        data: new TransactionResource($transaction)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::TRANSACTIONS_UPDATE);

    $data = $this->validateRequest($request, UpdateTransactionsRequest::rules($id));

    try {
      $model = Transaction::findOrFail($id);

      if ($model->transactionable == null){
        $model->update($data);
      }

      return $this->successResponse(
        data: new TransactionResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function stats(Request $request)
  {
    $this->authorizePermission(PermissionNames::TRANSACTIONS_VIEW);

    try {
      $query = Transaction::query();

      $totalCredits = (clone $query)->where('type', Type::CREDIT)
        ->where('status', Status::COMPLETED)
        ->sum('amount');
      $totalDebits = (clone $query)
        ->where('type', Type::DEBIT)
        ->where('status', Status::COMPLETED)
        ->sum('amount');

      $netTotal = $totalCredits - $totalDebits;

      $totalMonthCredits = (clone $query)
        ->where('type', Type::CREDIT)
        ->where('status', Status::COMPLETED)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('amount');

      $totalMonthDebits = (clone $query)
        ->where('type', Type::DEBIT)
        ->where('status', Status::COMPLETED)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('amount');

      $totalDayCredits = (clone $query)
        ->where('type', Type::CREDIT)
        ->where('status', Status::COMPLETED)
        ->whereDate('created_at', now()->toDateString())
        ->sum('amount');

      $totalDayDebits = (clone $query)
        ->where('type', Type::DEBIT)
        ->where('status', Status::COMPLETED)
        ->whereDate('created_at', now()->toDateString())
        ->sum('amount');

      return $this->successResponse(
        data: [
          'total_credits' => $totalCredits,
          'total_debits' => $totalDebits,
          'net_total' => $netTotal,
          'total_month_credits' => $totalMonthCredits,
          'total_month_debits' => $totalMonthDebits,
          'total_day_credits' => $totalDayCredits,
          'total_day_debits' => $totalDayDebits,
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
