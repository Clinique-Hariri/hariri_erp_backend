<?php

namespace Modules\MedicalReferences\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyCheckupPricingResource;
use Modules\MedicalReferences\Models\InsuranceSociety;
use Modules\MedicalReferences\Models\InsuranceSocietyCheckupPricing;
use Throwable;

class InsuranceSocietyCheckupPricingApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $societyId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_VIEW);
    }

    try {
      $model = InsuranceSocietyCheckupPricing::with(['insuranceSociety', 'doctor.user'])
        ->where('insurance_society_id', $societyId);

      if($request->filled('doctor_id')) {
        $model->where('doctor_id', $request->doctor_id);
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
        data: InsuranceSocietyCheckupPricingResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_VIEW);

    try {
      $model = InsuranceSocietyCheckupPricing::with(['doctor.user', 'insuranceSociety'])
        ->where('insurance_society_id', $societyId)
        ->findOrFail($id);

      return $this->successResponse(
        data: new InsuranceSocietyCheckupPricingResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request, $societyId)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    $data = $this->validateRequest($request, [
      'checkup_price' => ['required', 'numeric', 'min:0'],
      'doctor_id' => ['required', 'exists:doctors,id',
        Rule::unique('insurance_society_checkup_pricings', 'doctor_id')
          ->where(fn ($query) => $query->where('insurance_society_id', $societyId)),
      ]
    ], [
      'doctor_id.unique' => __('messages.doctor_already_has_pricing_for_this_insurance_society')
    ]);

    try {
      $insurance_society = InsuranceSociety::findOrFail($societyId);
      $model = $insurance_society->checkupPricings()->create($data);

      return $this->successResponse(
        data: new InsuranceSocietyCheckupPricingResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    $data = $this->validateRequest($request, [
      'checkup_price' => ['required', 'numeric', 'min:0'],
    ]);

    try {
      $model = InsuranceSocietyCheckupPricing::where('insurance_society_id', $societyId)
        ->findOrFail($id);

      $model->update($data);

      return $this->successResponse(
        data: new InsuranceSocietyCheckupPricingResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    try {
      $model = InsuranceSocietyCheckupPricing::where('insurance_society_id', $societyId)
        ->findOrFail($id);

      $model->delete();

      return $this->successResponse(
        data: [
          'total' => InsuranceSocietyCheckupPricing::where('insurance_society_id', $societyId)->count()
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
