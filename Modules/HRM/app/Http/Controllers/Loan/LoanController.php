<?php

namespace Modules\HRM\app\Http\Controllers\Loan;

use Illuminate\Database\QueryException;
use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\HRM\Models\Loan;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Constants\LoanStatus;
use Modules\HRM\Models\LoanInstallment;
use Modules\HRM\Constants\ContractStatus;
use Modules\HRM\Constants\InstallmentStatus;
use Modules\HRM\Http\Resources\LoanResource;
use Modules\HRM\Http\Requests\Loan\StoreLoanRequest;
use Modules\HRM\Http\Requests\Loan\UpdateLoanRequest;

class LoanController extends Controller
{
  use ApiResponseTrait;

  /**
   * Display a listing of loans.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::LOANS_VIEW);
    }

    try {
      $query = Loan::with(['employee', 'installments']);

      // Apply search filter
      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->whereHas('employee', function ($q) use ($searchTerm) {
          $q->where('fullname', 'like', "%{$searchTerm}%")
            ->orWhere('employee_code', 'like', "%{$searchTerm}%");
        });
      }

      // Filter by employee
      if ($request->filled('employee_id')) {
        $query->where('employee_id', $request->employee_id);
      }

      // Filter by status
      if ($request->filled('status')) {
        $query->where('status', $request->status);
      }

      // Filter by total installments
      if ($request->filled('total_installments')) {
        $query->where('total_installments', $request->total_installments);
      }

      // Filter by created date range
      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      if ($request->boolean('paginate')) {
        $loans = $query->paginate($request->get('per_page', 10));
      } else {
        $loans = $query->get();
      }

      return $this->successResponse(
        data: LoanResource::collection($loans)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Display the specified loan.
   *
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function show($id)
  {
    $this->authorizePermission(PermissionNames::LOANS_VIEW);

    try {
      $loan = Loan::with(['employee', 'installments'])->findOrFail($id);

      return $this->successResponse(
        data: new LoanResource($loan)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Store a newly created loan.
   *
   * @param StoreLoanRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StoreLoanRequest $request)
  {
    $this->authorizePermission(PermissionNames::LOANS_CREATE);

    $data = $this->validateRequest($request, $request->rules());

    try {
      DB::beginTransaction();

      $employee = Employee::findOrFail($data['employee_id']);
      $employee->load('contracts');

      if ($employee->contract_status == ContractStatus::NONE) {
        return $this->errorResponse('Employee with no contract can not apply for a loan', 422);
      }

      // Create loan
      $loan = Loan::create([
        'employee_id' => $data['employee_id'],
        'amount' => $data['amount'],
        'installment_amount' => $data['installment_amount'],
        'total_installments' => $data['total_installments'],
        'deduction_date' => $data['deduction_date'],
      ]);

      $installments = [];
      $loanDate = Carbon::parse($data['deduction_date'])->startOfMonth();

      for ($i = 0; $i < $data['total_installments']; $i++) {

        $installmentDate = $loanDate->copy()->addMonths($i)->toDateString();

        if (!$employee->hasContractAt($installmentDate)) {
          return $this->errorResponse(
            "Employee's contract will expire before installment date: {$installmentDate}",
            422
          );
        }

        $installments[] = [
          'loan_id' => $loan->id,
          'number' => $i,
          'month' => $installmentDate,
          'amount' => $data['installment_amount'],
          'status' => InstallmentStatus::PENDING,
          'created_at' => now(),
          'updated_at' => now(),
        ];

      }

      LoanInstallment::insert($installments);

      DB::commit();

      return $this->successResponse(
        message: 'Loan created successfully',
        data: new LoanResource($loan->load(['employee', 'installments']))
      );
    } catch (Throwable $e) {
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Update the specified loan.
   *
   * @param UpdateLoanRequest $request
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(UpdateLoanRequest $request, $id)
  {
    $this->authorizePermission(PermissionNames::LOANS_UPDATE);

    $data = $this->validateRequest($request, $request->rules());

    try {
      DB::beginTransaction();

      $loan = Loan::findOrFail($id);

      if ($loan->status !== LoanStatus::UNPAID) {
        return $this->errorResponse('Only unpaid loans can be updated', 422);
      }

      $loan->update($data);

      $loan->installments()->forceDelete();

      $employee = $loan->employee;
      $employee->load('contracts');

      $installments = [];
      $loanDate = Carbon::parse($data['deduction_date'])->startOfMonth();

      for ($i = 0; $i < $loan->total_installments; $i++) {

        $installmentDate = $loanDate->copy()->addMonths($i)->toDateString();

        if (!$employee->hasContractAt($installmentDate)) {
          return $this->errorResponse(
            "Employee's contract will expire before installment date: {$installmentDate}",
            422
          );
        }

        $installments[] = [
          'loan_id' => $loan->id,
          'number' => $i,
          'month' => $installmentDate,
          'amount' => $loan->installment_amount,
          'status' => InstallmentStatus::PENDING,
          'created_at' => now(),
          'updated_at' => now(),
        ];
      }

      LoanInstallment::insert($installments);


      DB::commit();

      return $this->successResponse(
        message: 'Loan updated successfully',
        data: new LoanResource($loan->load(['employee', 'installments']))
      );
    } catch (Throwable $e) {
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Remove the specified loan.
   *
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::LOANS_DELETE);

    try {
      DB::beginTransaction();

      $loan = Loan::findOrFail($id);


      if ($loan->status !== LoanStatus::UNPAID) {
        return $this->errorResponse('Only pending loans can be deleted', 422);
      }

      $loan->delete();

      DB::commit();

      return $this->successResponse(
        message: 'Loan deleted successfully'
      );
    } catch (QueryException $e) {
      // MySQL foreign key violation code = 23000
      if ($e->getCode() == 23000) {
        DB::rollBack();
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
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
