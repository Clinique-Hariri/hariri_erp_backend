<?php

namespace Modules\MedicalReferences\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\MedicalReferences\Http\Requests\MedicineRequest;
use Modules\MedicalReferences\Http\Resources\MedicineResource;
use Modules\MedicalReferences\Models\Medicine;
use Throwable;

class MedicinesApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {

    if ($request->boolean('paginate')) {
      $this->authorizePermission([
        PermissionNames::MEDICINES_VIEW,
        PermissionNames::CHECKUPS_DOCTOR_VIEW
      ]);
    }

    try {
      $model = Medicine::query();

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
        data: MedicineResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission([
      PermissionNames::MEDICINES_VIEW,
      PermissionNames::CHECKUPS_DOCTOR_VIEW
    ]);

    try {
      $model = Medicine::findOrFail($id);

      return $this->successResponse(
        data: new MedicineResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {

    $this->authorizePermission(PermissionNames::MEDICINES_CREATE);
    $data = $this->validateRequest($request, MedicineRequest::rules());

    try {
      $model = Medicine::create($data);

      return $this->successResponse(
        data: new MedicineResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {

    $this->authorizePermission(PermissionNames::MEDICINES_UPDATE);
    $data = $this->validateRequest($request, MedicineRequest::rules());

    try {
      $model = Medicine::findOrFail($id);
      $model->update($data);

      return $this->successResponse(
        data: new MedicineResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::MEDICINES_DELETE);

    try {
      $model = Medicine::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => Medicine::count()
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
