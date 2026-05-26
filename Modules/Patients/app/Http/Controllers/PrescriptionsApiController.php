<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Patients\Http\Requests\ChronicDiseasePatient\StoreChronicDiseasePatientRequest;
use Modules\Patients\Http\Requests\ChronicDiseasePatient\UpdateChronicDiseasePatientRequest;
use Modules\Patients\Http\Requests\Prescription\StorePrescriptionRequest;
use Modules\Patients\Http\Requests\Prescription\UpdatePrescriptionRequest;
use Modules\Patients\Http\Resources\ChronicDiseasePatientResource;
use Modules\Patients\Http\Resources\PrescriptionResource;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\ChronicDiseasePatient;
use Modules\Patients\Models\Prescription;
use Throwable;

class PrescriptionsApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $checkupId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::PRESCRIPTIONS_VIEW);
    }

    try {
      $model = Prescription::with(['medicines', 'doctor.user.employee', 'checkup.patient'])->where('checkup_id', $checkupId);

      if (request()->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: PrescriptionResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($checkupId, $id)
  {
    $this->authorizePermission(PermissionNames::PRESCRIPTIONS_VIEW);

    try {
      $model = Prescription::with(['medicines', 'doctor.user.employee', 'checkup.patient'])->where('checkup_id', $checkupId)
        ->where('id', $id)
        ->firstOrFail();

      return $this->successResponse(
        data: new PrescriptionResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request, $checkupId)
  {

    $this->authorizePermission(PermissionNames::PRESCRIPTIONS_CREATE);
    $data = $this->validateRequest($request, StorePrescriptionRequest::rules());

    try {
      $checkup = Checkup::findOrFail($checkupId);

      $model = DB::transaction(function () use ($checkup, $data) {
        $prescription = $checkup->prescriptions()->create([
          'doctor_id' => $checkup->doctor_id,
        ]);

        if (!empty($data['medicines'])) {
          $medicinesData = collect($data['medicines'])->map(function ($medicine) {
            return [
              'medicine_name' => $medicine['medicine_name'],
              'dosage' => $medicine['dosage'],
              'instructions' => $medicine['instructions'] ?? null,
            ];
          })->toArray();

          $prescription->medicines()->createMany($medicinesData);
        }

        return $prescription;
      });

      return $this->successResponse(
        data: new PrescriptionResource($model)
      );

    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $checkupId, $id)
  {

    $this->authorizePermission(PermissionNames::PRESCRIPTIONS_UPDATE);
    $data = $this->validateRequest($request, UpdatePrescriptionRequest::rules());

    try {
      $model = DB::transaction(function () use ($checkupId, $id, $data) {
        $prescription = Prescription::with('medicines')
          ->where('checkup_id', $checkupId)
          ->where('id', $id)
          ->firstOrFail();

        // delete old medicines
        $prescription->medicines()->delete();

        // add new medicines
        if (!empty($data['medicines'])) {
          $prescription->medicines()->createMany(
            collect($data['medicines'])->map(fn ($m) => [
              'medicine_name' => $m['medicine_name'],
              'dosage' => $m['dosage'],
              'instructions' => $m['instructions'] ?? null,
            ])->toArray()
          );
        }

        return $prescription->fresh('medicines');
      });

      return $this->successResponse(
        data: new PrescriptionResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($checkupId, $id)
  {
    $this->authorizePermission(PermissionNames::PRESCRIPTIONS_DELETE);

    try {
      $model = Prescription::where('checkup_id', $checkupId)
        ->where('id', $id)
        ->firstOrFail();
      $model->medicines()->delete();
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => Prescription::count()
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
}
