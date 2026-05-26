<?php

namespace Modules\HRM\app\Http\Controllers\Attendance;

use Exception;
use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;
use Modules\HRM\Models\Attendance;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Constants\AttendanceStatus;
use Modules\HRM\Services\AttendanceService;

class AttendanceMultiController extends Controller
{
  use ApiResponseTrait;

  /**
   * Delete multiple attendances
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request)
  {
    $this->authorizePermission(PermissionNames::ATTENDANCES_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:attendances,id'
    ]);

    try {
      DB::beginTransaction();

      Attendance::whereIn('id', $data['ids'])->delete();

      DB::commit();

      return $this->successResponse(
        message: count($data['ids']) . ' attendances deleted successfully',
        data: [
          'total' => Attendance::count()
        ]
      );
    } catch (Throwable $e) {
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Update status for multiple attendances
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function updateStatus(Request $request)
  {
    $this->authorizePermission(PermissionNames::ATTENDANCES_UPDATE);

    $data = $this->validateRequest($request, [
      'employee_ids' => 'required|array',
      'employee_ids.*' => 'required|exists:employees,id',
      'date' => 'sometimes|date_format:Y-m-d',
      'status' => 'required|string|in:' . implode(',', AttendanceStatus::all())
    ]);

    try {
      DB::beginTransaction();

      $date = $data['date'] ?? now()->toDateString();

      // Validate employees have contracts at the given date
      $validEmployees = Employee::whereIn('id', $data['employee_ids'])
        ->whereHasContractAt($date)
        ->pluck('id')
        ->toArray();

      if (count($validEmployees) !== count($data['employee_ids'])) {
        $invalidEmployees = array_diff($data['employee_ids'], $validEmployees);
        return $this->errorResponse(
          'Some employees do not have valid contracts on ' . $date . '. Employee IDs: ' . implode(', ', $invalidEmployees),
          400
        );
      }

      $attendances = [];

      foreach ($data['employee_ids'] as $employeeId) {
        $attendances[] = [
          'employee_id' => $employeeId,
          'date' => $date,
          'status' => $data['status'],
          'created_at' => now(),
          'updated_at' => now()
        ];
      }

      Attendance::upsert(
        $attendances,
        ['employee_id', 'date'],
        ['status', 'updated_at']
      );

      DB::commit();

      return $this->successResponse(
        message: count($data['employee_ids']) . ' attendances updated successfully',
      );
    } catch (Throwable $e) {
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Check-in multiple employees at once
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function multiCheckIn(Request $request)
  {
    $this->authorizePermission(PermissionNames::ATTENDANCES_CREATE);

    $data = $this->validateRequest($request, [
      'employee_ids' => 'required|array',
      'employee_ids.*' => 'required|exists:employees,id',
      'check_time' => 'sometimes|date_format:Y-m-d H:i',
    ]);

    try {

      DB::beginTransaction();

      $checkTime = Carbon::parse($data['check_time'] ?? now());
      $date = $checkTime->toDateString();

      $employees = Employee::whereIn('id', $data['employee_ids'])
        ->whereHasContractAt($date)
        ->whereDoesntHave('activeShift')
        ->get();

      // Check if all employees have contracts
      $validEmployeeIds = $employees->pluck('id')->toArray();
      if (count($validEmployeeIds) !== count($data['employee_ids'])) {
        $invalidEmployees = array_diff($data['employee_ids'], $validEmployeeIds);
        return $this->errorResponse(
          'Some employees do not have valid contracts on ' . $date . ' or are already checked in. Employee IDs: ' . implode(', ', $invalidEmployees),
          400
        );
      }

      $attendances = [];

      foreach($employees as $employee){
        $attendances[] = [
          'employee_id' => $employee->id,
          'date' => $date,
          'check_in_time' => $checkTime,
          'created_at' => now(),
          'updated_at' => now()
        ];
      }

      Attendance::insert($attendances);

      DB::commit();

      return $this->successResponse();
    } catch (Throwable $e) {
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Check-out multiple employees at once
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function multiCheckOut(Request $request)
  {
    $this->authorizePermission(PermissionNames::ATTENDANCES_UPDATE);

    $data = $this->validateRequest($request, [
      'employee_ids' => 'required|array',
      'employee_ids.*' => 'required|exists:employees,id',
      'check_time' => 'sometimes|date_format:Y-m-d H:i',
    ]);

    try {
      DB::beginTransaction();

      $checkTime = Carbon::parse($data['check_time'] ?? now());
      $date = $checkTime->toDateString();

      $employees = Employee::whereIn('id', $data['employee_ids'])
        ->whereHasContractAt($date)
        ->whereHas('activeShift')
        ->with('activeShift')
        ->get();

      // Check if all employees have contracts and are checked in
      $validEmployeeIds = $employees->pluck('id')->toArray();
      if (count($validEmployeeIds) !== count($data['employee_ids'])) {
        $invalidEmployees = array_diff($data['employee_ids'], $validEmployeeIds);
        return $this->errorResponse(
          'Some employees do not have valid contracts on ' . $date . ' or are not checked in. Employee IDs: ' . implode(', ', $invalidEmployees),
          400
        );
      }

      $attendanceService = app(AttendanceService::class);

      $attendances = [];

      foreach ($employees as $employee) {
        $attendance = $employee->active_shift;
        $duration = $attendanceService->getDuration($attendance->check_in_time, $checkTime);
        $status = $attendanceService->getStatus($duration);

        $attendances[] = [
          'id' => $attendance->id,
          'employee_id' => $attendance->employee_id,
          'date' => $attendance->date,
          'check_in_time' => $attendance->check_in_time,
          'check_out_time' => $checkTime,
          'duration' => $duration,
          'status' => $status,
          'updated_at' => now()
        ];
      }

      Attendance::upsert(
        $attendances,
        ['id'],
        ['check_out_time', 'duration', 'status', 'updated_at']
      );

      DB::commit();

      return $this->successResponse();
    } catch (Throwable $e) {
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
