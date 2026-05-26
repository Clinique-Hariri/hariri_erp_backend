<?php

namespace Modules\HRM\app\Http\Controllers\EmployeeContract;

use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Employee;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Http\Resources\ContractResource;

class EmployeeContractController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $employee_id)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::CONTRACTS_VIEW);
    }

    try {
      $employee = Employee::findOrFail($employee_id);
      $query = $employee->contracts();

      if ($request->boolean('paginate')) {
        $contracts = $query->paginate($request->get('per_page', 10));
      } else {
        $contracts = $query->get();
      }

      return $this->successResponse(data: ContractResource::collection($contracts));
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
