<?php

namespace Modules\HRM\app\Http\Controllers\Attendance;

use Illuminate\Database\QueryException;
use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Attendance;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Services\AttendanceService;
use Modules\HRM\Http\Resources\AttendanceResource;
use Modules\HRM\Http\Requests\Attendance\StoreAttendanceRequest;
use Modules\HRM\Http\Requests\Attendance\UpdateAttendanceRequest;
use Modules\HRM\Http\Resources\EmployeeResource;

class AttendanceController extends Controller
{
  use ApiResponseTrait;

  public function __construct(private AttendanceService $attendanceService)
  {
  }

  /**
   * Display a listing of attendances.
   */
  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::ATTENDANCES_VIEW);
    }

    try {

      $query = Employee::whereHasContractAt($request->date);

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where('fullname', 'like', "%{$searchTerm}%")
          ->orWhere('employee_code', 'like', "%{$searchTerm}%");
      }


      if ($request->has('status')) {

        $query->join('attendances', function ($join) use ($request) {
          $join->on('employees.id', '=', 'attendances.employee_id')
            ->whereDate('attendances.date', $request->date);

          if (empty($request->status)) {
            $join->whereNull('attendances.status');
          } else {
            $join->where('attendances.status', $request->status);
          }
        });

      } else {
        $query->leftJoin('attendances', function ($join) use ($request) {
          $join->on('employees.id', '=', 'attendances.employee_id')
            ->whereDate('attendances.date', $request->date);
        });
      }

      $query->select(
        'employees.*',
        'attendances.id as attendance_id',
        'attendances.date as attendance_date',
        'attendances.check_in_time',
        'attendances.check_out_time',
        'attendances.status',
        'attendances.duration',
        'attendances.created_at as attendance_created_at'
      )
        ->orderBy('attendances.date', 'ASC')
        ->orderBy('attendances.created_at', 'ASC');

      if ($request->boolean('paginate')) {
        $attendances = $query->paginate($request->get('per_page', 10));
      } else {
        $attendances = $query->get();
      }

      //return $attendances;
      return $this->successResponse(data: EmployeeResource::collection($attendances));
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Display the specified attendance.
   */
  public function show($id)
  {
    $this->authorizePermission(PermissionNames::ATTENDANCES_VIEW);

    try {
      $attendance = Attendance::with('employee')->findOrFail($id);
      return $this->successResponse(data: new AttendanceResource($attendance));
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Store a newly created attendance in storage.
   */
  public function store(StoreAttendanceRequest $request)
  {
    $this->authorizePermission(PermissionNames::ATTENDANCES_CREATE);
    try {
      $attendance = $this->attendanceService->create($request->validated());
      return $this->successResponse(
        message: 'Attendance created successfully',
        data: new AttendanceResource($attendance)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Update the specified attendance in storage.
   */
  public function update(UpdateAttendanceRequest $request, $id)
  {
    $this->authorizePermission(PermissionNames::ATTENDANCES_UPDATE);

    try {
      $attendance = Attendance::findOrFail($id);
      $updated = $this->attendanceService->update($attendance, $request->validated());

      return $this->successResponse(
        message: 'Attendance updated successfully',
        data: new AttendanceResource($updated)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Remove the specified attendance from storage.
   */
  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::ATTENDANCES_DELETE);

    try {
      $attendance = Attendance::findOrFail($id);
      $attendance->delete();

      return $this->successResponse(
        message: 'Attendance deleted successfully',
        data: ['total' => Attendance::count()]
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

  /**
   * Auto check-in or check-out for an employee
   */
  public function autoCheckInOut(Request $request, $employeeIdentifier)
  {
    $this->authorizePermission(PermissionNames::ATTENDANCES_CREATE);

    $this->validateRequest($request, [
      'check_time' => ['required', 'date_format:Y-m-d H:i'],
    ]);
    try {
      $checkTime = $request->input('check_time')
        ? Carbon::parse($request->input('check_time'))
        : Carbon::now();

      $result = $this->attendanceService->autoCheckInOut($employeeIdentifier, $checkTime);

      return $this->successResponse(
        data: new AttendanceResource($result)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
