<?php

namespace Modules\Clinic\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\Clinic\Http\Requests\CreateScheduleRequest;
use Modules\Clinic\Http\Resources\DoctorScheduleResource;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\DoctorSchedule;

class SchedulesController extends Controller
{
  use ApiResponseTrait;
  public function index($doctorId, Request $request)
  {
    if ($request->boolean('paginate')) {
//      $this->authorizePermission(PermissionNames::DOCTORS_VIEW);
    }

    try {
      $model = Doctor::findOrFail($doctorId)
        ->schedules()->with('workingPeriods');

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $model->where(function ($q) use ($searchTerm) {
          $q->where('day_of_week', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      if ($request->filled('day_of_week')) {
        $model->where('day_of_week', $request->day_of_week);
      }

      if ($request->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: DoctorScheduleResource::collection($model)
      );
    } catch (\Exception $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store($doctorId, Request $request)
  {
//    $this->authorizePermission(PermissionNames::DOCTORS_UPDATE);
    $data = $this->validateRequest($request , CreateScheduleRequest::rules());
    try {
      foreach ($data['days_of_week'] as $day) {
        $schedule = DoctorSchedule::firstOrCreate([
          'doctor_id' => $doctorId,
          'day_of_week' => $day,
        ]);

        $schedule->workingPeriods()->create([
          'start_time' => $data['start_time'],
          'end_time' => $data['end_time'],
        ]);
      }
      return $this->successResponse();
    } catch (\Exception $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($doctorId, $id)
  {
//    $this->authorizePermission(PermissionNames::DOCTORS_VIEW);
    try {
      $model = DoctorSchedule::where('doctor_id', $doctorId)
        ->where('id', $id)
        ->firstOrFail();

      $model->delete();

      return $this->successResponse();
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
    } catch (\Exception $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
