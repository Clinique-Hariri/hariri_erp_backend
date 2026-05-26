<?php

namespace Modules\MedicalReferences\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\MedicalReferences\Http\Requests\MedicalServiceRequest;
use Modules\MedicalReferences\Http\Resources\MedicalServiceResource;
use Modules\MedicalReferences\Models\MedicalService;
use Modules\MedicalReferences\Models\MedicalServiceGroup;
use Throwable;

class MedicalServicesApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $groupId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::MEDICAL_SERVICES_VIEW);
    }

    try {
      $model = MedicalService::with(['group'])
        ->where('group_id', $groupId)
        ->orderBy('name', 'asc');

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $model->where(function($q) use ($searchTerm) {
          $q->where('name', 'like', "%{$searchTerm}%");
        });
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
        data: MedicalServiceResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($groupId, $id)
  {
    $this->authorizePermission(PermissionNames::MEDICAL_SERVICES_VIEW);

    try {
      $model = MedicalService::with(['group'])
        ->where('group_id', $groupId)
        ->findOrFail($id);

      return $this->successResponse(
        data: new MedicalServiceResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request, $groupId)
  {

    $this->authorizePermission(PermissionNames::MEDICAL_SERVICES_CREATE);
    $data = $this->validateRequest($request, MedicalServiceRequest::rules());

    try {
      $model = MedicalServiceGroup::findOrFail($groupId);
      $model = $model->services()->create($data);

      return $this->successResponse(
        data: new MedicalServiceResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $groupId, $id)
  {

    $this->authorizePermission(PermissionNames::MEDICAL_SERVICES_UPDATE);
    $data = $this->validateRequest($request, MedicalServiceRequest::rules());

    try {
      $model = MedicalService::where('group_id', $groupId)
        ->where('id', $id)
        ->firstOrFail();

      $model->update($data);

      return $this->successResponse(
        data: new MedicalServiceResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($groupId, $id)
  {
    $this->authorizePermission(PermissionNames::MEDICAL_SERVICES_DELETE);

    try {
      $model = MedicalService::where('group_id', $groupId)
        ->where('id', $id)
        ->firstOrFail();

      $model->delete();

      return $this->successResponse(
        data: [
          'total' => MedicalService::count()
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

  public function listGrouped(Request $request)
  {
    try {
      $model = MedicalService::with('group');

      if (filled($request->type)) {
        $model->where('type', $request->type);
      }

      $model = $model->get()
        ->groupBy('group_id')
        ->map(function ($services) {
          return [
            'group_name' => optional($services->first()->group)->name,
            'services' => MedicalServiceResource::collection($services),
          ];
        })
        ->values();

      return $this->successResponse(
        data: $model
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
