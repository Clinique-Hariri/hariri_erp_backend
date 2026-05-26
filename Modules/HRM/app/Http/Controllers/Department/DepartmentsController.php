<?php

namespace Modules\HRM\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\HRM\Http\Requests\Department\StoreDepartmentRequest;
use Modules\HRM\Http\Requests\Department\UpdateDepartmentRequest;
use Modules\HRM\Http\Resources\DepartmentMiniResource;
use Modules\HRM\Http\Resources\DepartmentResource;
use Modules\HRM\Models\Department;
use Throwable;

class DepartmentsController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {

    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::DEPARTMENTS_VIEW);
    }

    try {
      $model = Department::query();

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      if (request()->boolean('with_doctors')) {
        $model = $model->with('doctors');
      }

      if (request()->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: (request()->boolean('paginate')) ? DepartmentResource::collection($model) : DepartmentMiniResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::DEPARTMENTS_VIEW);

    try {
      $model = Department::findOrFail($id);

      return $this->successResponse(
        data: new DepartmentResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {

    $this->authorizePermission(PermissionNames::DEPARTMENTS_CREATE);
    $data = $this->validateRequest($request, StoreDepartmentRequest::rules());

    try {
      $model = Department::create($data);

      return $this->successResponse(
        data: new DepartmentResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {

    $this->authorizePermission(PermissionNames::DEPARTMENTS_UPDATE);
    $data = $this->validateRequest($request, UpdateDepartmentRequest::rules());

    try {
      $model = Department::findOrFail($id);
      $model->update($data);

      return $this->successResponse(
        data: new DepartmentResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::DEPARTMENTS_DELETE);

    try {
      $model = Department::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => Department::count()
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

