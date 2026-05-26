<?php

namespace Modules\HRM\app\Http\Controllers\Designation;

use Illuminate\Database\QueryException;
use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Designation;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Http\Resources\DesignationResource;
use Modules\HRM\Http\Requests\Designation\StoreDesignationRequest;
use Modules\HRM\Http\Requests\Designation\UpdateDesignationRequest;

class DesignationController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::DESIGNATIONS_VIEW);
    }

    try {
      $query = Designation::query();

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
          $q->where('name', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('type')) {
        $query->where('type', $request->type);
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      if ($request->boolean('paginate')) {
        $model = $query->paginate($request->get('per_page', 10));
      } else {
        $model = $query->get();
      }

      return $this->successResponse(
        data: DesignationResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::DESIGNATIONS_VIEW);

    try {
      $model = Designation::findOrFail($id);

      return $this->successResponse(
        data: new DesignationResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNames::DESIGNATIONS_CREATE);

    $data = $this->validateRequest($request, StoreDesignationRequest::rules());

    try {
      $model = Designation::create($data);

      return $this->successResponse(
        data: new DesignationResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::DESIGNATIONS_UPDATE);

    $data = $this->validateRequest($request, UpdateDesignationRequest::rules());

    try {
      $model = Designation::findOrFail($id);
      $model->update($data);

      return $this->successResponse(
        data: new DesignationResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::DESIGNATIONS_DELETE);

    try {
      $model = Designation::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => Designation::count()
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
