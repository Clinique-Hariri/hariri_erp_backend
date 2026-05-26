<?php

namespace Modules\MedicalReferences\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\MedicalReferences\Http\Requests\InsuranceSocietyBranchRequest;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyBranchResource;
use Modules\MedicalReferences\Models\InsuranceSociety;
use Modules\MedicalReferences\Models\InsuranceSocietyBranch;
use Throwable;

class InsuranceSocietyBranchesApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $societyId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_VIEW);
    }

    try {
      $model = InsuranceSocietyBranch::with(['insuranceSociety'])
        ->where('insurance_society_id', $societyId);

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      if($request->filled('search')){
        $search = $request->search;
        $model->where('name', 'LIKE', "%$search%");
      }

      if ($request->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: InsuranceSocietyBranchResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_VIEW);

    try {
      $model = InsuranceSocietyBranch::with(['insuranceSociety'])
        ->where('insurance_society_id', $societyId)
        ->where('id', $id)
        ->firstOrFail();

      return $this->successResponse(
        data: new InsuranceSocietyBranchResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request, $societyId)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    $data = $this->validateRequest($request, InsuranceSocietyBranchRequest::rules());

    try {
      $model = InsuranceSociety::findOrFail($societyId);
      $model = $model->branches()->create($data);

      return $this->successResponse(
        data: new InsuranceSocietyBranchResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    $data = $this->validateRequest($request, InsuranceSocietyBranchRequest::rules());

    try {
      $model = InsuranceSocietyBranch::with(['insuranceSociety'])
        ->where('insurance_society_id', $societyId)
        ->where('id', $id)
        ->firstOrFail();

      $model->update($data);

      return $this->successResponse(
        data: new InsuranceSocietyBranchResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    try {
      $model = InsuranceSocietyBranch::
        where('insurance_society_id', $societyId)
        ->where('id', $id)
        ->firstOrFail();

      $model->delete();

      return $this->successResponse(
        data: [
          'total' => InsuranceSocietyBranch::where('insurance_society_id', $societyId)->count()
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
