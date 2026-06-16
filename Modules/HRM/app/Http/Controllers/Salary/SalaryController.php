<?php

namespace Modules\HRM\app\Http\Controllers\Salary;

use Illuminate\Database\QueryException;
use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Modules\Actions\Constants\ActionType;
use Modules\HRM\Models\Salary;
use Modules\HRM\Http\Resources\SalaryResource;
use Modules\HRM\Services\SalaryService;
use Modules\HRM\Constants\SalaryStatus;
use Modules\HRM\Services\SalaryReportService;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;

class SalaryController extends Controller
{
  use ApiResponseTrait;

  public function __construct(
    private readonly SalaryService $salaryService,
    private readonly SalaryReportService $salaryReportService
  ) {
  }

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::SALARIES_VIEW);
    }

    try {
      $query = Salary::with(['employee', 'bonuses.bonus', 'deductions.loanInstallment', 'paymentAction.user']);


      $month = $request->input('month');
      $targetStart = $month
        ? Carbon::parse($month)->startOfMonth()
        : now()->startOfMonth();

      $query->whereYear('month', $targetStart->year)
        ->whereMonth('month', $targetStart->month);


      // Search by employee code or fullname
      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->whereHas('employee', function($q) use ($searchTerm) {
          $q->where('fullname', 'like', "%{$searchTerm}%")
            ->orWhere('employee_code', 'like', "%{$searchTerm}%");
        });
      }

      // Filter by employee_id
      if ($request->filled('employee_id')) {
        $query->where('employee_id', $request->employee_id);
      }

      if ($request->boolean('paginate')) {
        $salaries = $query->paginate($request->get('per_page', 10));
      } else {
        $salaries = $query->get();
      }

      return $this->successResponse(
        data: SalaryResource::collection($salaries)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::SALARIES_VIEW);

    try {
      $salary = Salary::with(['employee', 'bonuses.bonus', 'deductions.loanInstallment'])
        ->findOrFail($id);

      return $this->successResponse(
        data: new SalaryResource($salary)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function generate(Request $request)
  {
    $this->authorizePermission(PermissionNames::SALARIES_CREATE);

    try {
      $summary = $this->salaryService->generate($request->input('month'));

      return $this->successResponse(
        message: 'Salaries generated successfully',
        data: $summary
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function old_updateStatus(Request $request, Salary $salary)
  {
    $data = $this->validateRequest($request, [
      'status' => ['required', 'string', 'in:' . implode(',', array_keys(SalaryStatus::all()))],
    ]);

    $newStatus = $data['status'];
    $currentStatus = $salary->status;

    if ($newStatus === $currentStatus) {
      return $this->successResponse(
        data: new SalaryResource($salary->load(['employee', 'bonuses.bonus', 'deductions.loanInstallment']))
      );
    }

    $permission = match ($newStatus) {
      SalaryStatus::PROCESSED => PermissionNames::SALARIES_UPDATE_TO_PROCESSED,
      SalaryStatus::PAID => PermissionNames::SALARIES_UPDATE_TO_PAID,
      default => null,
    };

    if (!$permission) {
      return $this->errorResponse('Invalid salary status.', 422);
    }

    $this->authorizePermission($permission);

    $allowedTransitions = [
      SalaryStatus::DRAFT => [SalaryStatus::PROCESSED],
      SalaryStatus::PROCESSED => [SalaryStatus::PAID],
      SalaryStatus::PAID => [],
    ];

    $nextStatuses = $allowedTransitions[$currentStatus] ?? [];
    if (!in_array($newStatus, $nextStatuses, true)) {
      return $this->errorResponse('Invalid salary status transition.', 422);
    }

    try {
      DB::transaction(function () use ($salary, $newStatus, $currentStatus) {
        $updateData = ['status' => $newStatus];

        if ($newStatus === SalaryStatus::PAID) {
          $updateData['pay_date'] = now();
        }

        $salary->update($updateData);

        if ($newStatus === SalaryStatus::PAID) {
          $monthLabel = optional($salary->month)->format('Y-m') ?? $salary->month;
          $employeeName = $salary->employee?->fullname ?? "Employee #{$salary->employee_id}";

          $salary->transactions()->create([
            'amount' => (float) $salary->net_salary,
            'details' => "Salary payment for {$employeeName} ({$monthLabel})",
            'type' => Type::DEBIT,
            'status' => Status::COMPLETED,
            'user_id' => auth()->id(),
          ]);

          $salary->actions()->create([
            'action_type' => ActionType::SALARY_PAYMENT_ACTION,
            'from_status' => $currentStatus,
            'to_status' => SalaryStatus::PAID,
            'user_id' => auth()->id(),
          ]);
        }
      });

      return $this->successResponse(
        data: new SalaryResource($salary->fresh(['employee', 'bonuses.bonus', 'deductions.loanInstallment','paymentAction.user']))
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(Request $request, Salary $salary)
  {
    $data = $this->validateRequest($request, [
      'status' => ['required', 'string', 'in:' . implode(',', array_keys(SalaryStatus::all()))],
    ]);

    $newStatus = $data['status'];
    $currentStatus = $salary->status;

    if ($newStatus === $currentStatus) {
      return $this->successResponse(
        data: new SalaryResource($salary->load(['employee', 'bonuses.bonus', 'deductions.loanInstallment']))
      );
    }

    $permission = match ($newStatus) {
      SalaryStatus::PROCESSED => PermissionNames::SALARIES_UPDATE_TO_PROCESSED,
      SalaryStatus::PAID => PermissionNames::SALARIES_UPDATE_TO_PAID,
      default => null,
    };

    if (!$permission) {
      return $this->errorResponse('Invalid salary status.', 422);
    }

    $this->authorizePermission($permission);

    $allowedTransitions = [
      SalaryStatus::DRAFT => [SalaryStatus::PROCESSED],
      SalaryStatus::PROCESSED => [SalaryStatus::PAID],
      SalaryStatus::PAID => [],
    ];

    $nextStatuses = $allowedTransitions[$currentStatus] ?? [];
    if (!in_array($newStatus, $nextStatuses, true)) {
      return $this->errorResponse('Invalid salary status transition.', 422);
    }

    try {
      DB::transaction(function () use ($salary, $newStatus, $currentStatus) {
        $updateData = ['status' => $newStatus];

        if ($newStatus === SalaryStatus::PAID) {
          $updateData['pay_date'] = now();
        }

        $salary->update($updateData);

        if ($newStatus === SalaryStatus::PAID) {
          $monthLabel = optional($salary->month)->format('Y-m') ?? $salary->month;
          $employee = $salary->employee;
          if (!$employee) {
            throw new \InvalidArgumentException("Salary #{$salary->id} has no employee to link as accountable.");
          }
          $employeeName = $employee->fullname ?? "Employee #{$salary->employee_id}";

          $salary->transactions()->create([
            'amount' => (float) $salary->net_salary,
            'details' => "Salary payment for {$employeeName} ({$monthLabel})",
            'type' => Type::DEBIT,
            'status' => Status::COMPLETED,
            'user_id' => auth()->id(),
            'accountable_type' => $employee::class,
            'accountable_id' => $employee->id,
          ]);

          $salary->actions()->create([
            'action_type' => ActionType::SALARY_PAYMENT_ACTION,
            'from_status' => $currentStatus,
            'to_status' => SalaryStatus::PAID,
            'user_id' => auth()->id(),
          ]);
        }
      });

      return $this->successResponse(
        data: new SalaryResource($salary->fresh(['employee', 'bonuses.bonus', 'deductions.loanInstallment', 'paymentAction.user']))
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::SALARIES_DELETE);

    try {
      $salary = Salary::findOrFail($id);
      $salary->delete();

      return $this->successResponse(
        message: 'Salary deleted successfully',
        data: [
          'total' => Salary::count()
        ]
      );
    } catch (QueryException $e) {
      // MySQL foreign key violation code = 23000
      if ($e->getCode() == 23000) {
        return response()->json([
          'success' => false,
          'message' => __('messages.cannot_delete_record_linked_to_other_records')
        ], 400);
      }

      // Fallback for any other database error
      return response()->json([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
      ], 500);
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function generateReport($id)
  {
    $this->authorizePermission(PermissionNames::SALARIES_VIEW);

    try {

      $salary = Salary::findOrFail($id);
      $url = $this->salaryReportService->generate($salary);


      return $this->successResponse(
        message: 'Salary report generated successfully',
        data: [
          'url' => $url,
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
