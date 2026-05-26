<?php

namespace Modules\HRM\app\Http\Controllers\Statistics;

use Throwable;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Employee;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Constants\AttendanceStatus;

class StatisticsController extends Controller
{
  use ApiResponseTrait;

  /**
   * Get employee statistics
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function employeeStatistics()
  {
      $this->authorizePermission(PermissionNames::EMPLOYEES_VIEW);

      try {
          // Count total employees
          $totalEmployees = Employee::count();

          // Count employees with active contracts
          $activeContractsCount = Employee::whereHas('contract')->count();

          // Count employees present today
          $presentEmployeesToday = Employee::whereHas('attendances', function($query) {
              $query->whereDate('date', now()->format('Y-m-d'))
                    ->whereIn('status', [AttendanceStatus::PRESENT, AttendanceStatus::LATE]);
          })->count();

          // Calculate absent employees
          $absentEmployeesToday = $activeContractsCount - $presentEmployeesToday;

          return $this->successResponse(
              data: [
                  'total_employees' => $totalEmployees,
                  'active_contracts' => $activeContractsCount,
                  'present_today' => $presentEmployeesToday,
                  'absent_today' => $absentEmployeesToday
              ]
          );
      } catch (Throwable $e) {
          return $this->errorResponse($e->getMessage(), 500);
      }
  }
}
