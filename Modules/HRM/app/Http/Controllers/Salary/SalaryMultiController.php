<?php

namespace Modules\HRM\app\Http\Controllers\Salary;

use Illuminate\Database\QueryException;
use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\HRM\Models\Salary;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Services\SalaryReportService;

class SalaryMultiController extends Controller
{
  use ApiResponseTrait;

  public function __construct(private readonly SalaryReportService $salaryReportService)
  {
  }

  /**
   * Delete multiple salaries
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request)
  {
    $this->authorizePermission(PermissionNames::SALARIES_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:salaries,id'
    ]);

    try {
      DB::beginTransaction();

      Salary::whereIn('id', $data['ids'])->delete();

      DB::commit();

      return $this->successResponse(
        message: count($data['ids']) . ' salaries deleted successfully',
        data: [
          'total' => Salary::count()
        ]
      );
    } catch (QueryException $e) {
      DB::rollBack();
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
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Generate reports for multiple salaries
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function generateReports(Request $request)
  {
    $this->authorizePermission(PermissionNames::SALARIES_VIEW);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array|min:1',
      'ids.*' => 'required|exists:salaries,id',
    ]);

    $reports = [];
    $failures = [];

    $salaries = Salary::with('employee')->whereIn('id', $data['ids'])->get();

    foreach ($salaries as $salary) {
      try {
        $url = $this->salaryReportService->generate($salary);
        $reports[] = [
          'salary_id' => $salary->id,
          'employee_id' => $salary->employee_id,
          'employee_code' => $salary->employee?->employee_code,
          'url' => $url,
        ];
      } catch (Throwable $e) {
        $failures[] = [
          'salary_id' => $salary->id,
          'employee_id' => $salary->employee_id,
          'error' => $e->getMessage(),
        ];
      }
    }

    return $this->successResponse(
      message: 'Salary reports processed',
      data: [
        'requested' => count($data['ids']),
        'generated' => count($reports),
        'reports' => $reports,
        'failures' => $failures,
      ]
    );
  }

  public function generateSummary(Request $request)
  {
    $this->authorizePermission(PermissionNames::SALARIES_VIEW);

    try {

      $data = $this->validateRequest($request, [
        'ids' => 'required|array|min:1',
        'ids.*' => 'required|exists:salaries,id',
      ]);

      $salaries = Salary::with('employee')->whereIn('id', $data['ids'])->get();

      [$filename, $pdfContent] = $this->salaryReportService->generateSummary($salaries, 'Summary');

      return response($pdfContent, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"',
      ]);
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
