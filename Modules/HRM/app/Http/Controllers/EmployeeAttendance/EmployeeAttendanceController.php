<?php

namespace Modules\HRM\app\Http\Controllers\EmployeeAttendance;

use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Employee;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Http\Resources\AttendanceResource;

class EmployeeAttendanceController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $employee_id)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::ATTENDANCES_VIEW);
    }

    try {
      $employee = Employee::findOrFail($employee_id);
      $query = $employee->attendances();

      if ($request->filled('status')) {
        $query->where('status', $request->status);
      }

      if ($request->filled('date_from')) {
        $query->whereDate('date', '>=', $request->date_from);
      }

      if ($request->filled('date_to')) {
        $query->whereDate('date', '<=', $request->date_to);
      }

      if ($request->boolean('paginate')) {
        $attendances = $query->paginate($request->get('per_page', 10));
      } else {
        $attendances = $query->get();
      }

      return $this->successResponse(data: AttendanceResource::collection($attendances));

    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

}
