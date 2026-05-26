<?php

namespace Modules\HRM\app\Http\Controllers\Bonus;

use Illuminate\Database\QueryException;
use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Bonus;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Http\Resources\BonusResource;
use Modules\HRM\Http\Requests\Bonus\StoreBonusRequest;
use Modules\HRM\Http\Requests\Bonus\UpdateBonusRequest;

class BonusController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::BONUSES_VIEW);
    }

    try {
      $query = Bonus::query();

      if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      if ($request->filled('employee_id')) {
        $employeeId = $request->employee_id;

        $has = $request->has('has') ? $request->boolean('has') : true;

        if ($has) {
          $query->whereHas('EmployeeBonuses', function ($q) use ($employeeId) {
            $q->where('employee_id', $employeeId);
          });
        } else {
          $query->whereDoesntHave('EmployeeBonuses', function ($q) use ($employeeId) {
            $q->where('employee_id', $employeeId);
          });
        }
      }
      if ($request->boolean('paginate')) {
        $model = $query->paginate($request->get('per_page', 10));
      } else {
        $model = $query->get();
      }

      return $this->successResponse(
        data: BonusResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::BONUSES_VIEW);

    try {
      $model = Bonus::findOrFail($id);

      return $this->successResponse(
        data: new BonusResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(StoreBonusRequest $request)
  {
    $this->authorizePermission(PermissionNames::BONUSES_CREATE);

    $data = $this->validateRequest($request, $request->rules());

    try {
      $model = Bonus::create($data);

      return $this->successResponse(
        data: new BonusResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(UpdateBonusRequest $request, $id)
  {
    $this->authorizePermission(PermissionNames::BONUSES_UPDATE);

    $data = $this->validateRequest($request, $request->rules());

    try {
      $model = Bonus::findOrFail($id);
      $model->update($data);

      return $this->successResponse(
        data: new BonusResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::BONUSES_DELETE);

    try {
      $model = Bonus::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => Bonus::count()
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
