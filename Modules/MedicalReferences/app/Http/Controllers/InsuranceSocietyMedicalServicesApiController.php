<?php

namespace Modules\MedicalReferences\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyServicePricingResource;
use Modules\MedicalReferences\Models\InsuranceSociety;
use Modules\MedicalReferences\Models\InsuranceSocietyServicePricing;
use Throwable;

class InsuranceSocietyMedicalServicesApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $societyId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_VIEW);
    }

    try {
      $model = InsuranceSocietyServicePricing::with(['insuranceSociety', 'medicalService'])
        ->where('insurance_society_id', $societyId);

      if($request->filled('medical_service_id')) {
        $model->where('medical_service_id', $request->medical_service_id);
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      if ($request->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: InsuranceSocietyServicePricingResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_VIEW);

    try {
      $model = InsuranceSocietyServicePricing::with(['medicalService', 'insuranceSociety'])
        ->where('insurance_society_id', $societyId)
        ->findOrFail($id);

      return $this->successResponse(
        data: new InsuranceSocietyServicePricingResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request, $societyId)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    $data = $this->validateRequest($request, [
      'medical_service_price' => ['required', 'numeric', 'min:0'],
      'medical_service_id' => ['required', 'exists:medical_services,id',
        Rule::unique('insurance_society_service_pricings', 'medical_service_id')
          ->where(fn ($query) => $query->where('insurance_society_id', $societyId)),
      ]
    ], [
      'medical_service_id.unique' => __('messages.the_selected_medical_service_already_has_a_pricing_for_this_insurance_society')
    ]);

    try {
      $insurance_society = InsuranceSociety::findOrFail($societyId);
      $model = $insurance_society->medicalServicePricings()->create($data);

      return $this->successResponse(
        data: new InsuranceSocietyServicePricingResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    $data = $this->validateRequest($request, [
      'medical_service_price' => ['required', 'numeric', 'min:0'],
    ]);

    try {
      $model = InsuranceSocietyServicePricing::where('insurance_society_id', $societyId)
        ->findOrFail($id);

      $model->update($data);

      return $this->successResponse(
        data: new InsuranceSocietyServicePricingResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    try {
      $model = InsuranceSocietyServicePricing::where('insurance_society_id', $societyId)
        ->findOrFail($id);

      $model->delete();

      return $this->successResponse(
        data: [
          'total' => InsuranceSocietyServicePricing::where('insurance_society_id', $societyId)->count()
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
