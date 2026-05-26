<?php

namespace Modules\HRM\app\Http\Controllers\EmployeeBonus;

use Illuminate\Database\QueryException;
use Throwable;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Models\EmployeeBonus;
use Modules\HRM\Constants\ContractStatus;
use Modules\HRM\Http\Requests\EmployeeBonus\EmployeeBonusRequest;

class EmployeeBonusMultiController extends Controller
{
    use ApiResponseTrait;


    public function store(EmployeeBonusRequest $request)
    {
        $this->authorizePermission(PermissionNames::BONUSES_CREATE);

        $data = $this->validateRequest($request, $request->rules());

        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($data['employee_id']);

            if($employee->contract_status == ContractStatus::NONE){
                return $this->errorResponse('Cannot assign bonuses to employee without no contract', 422);
            }

            foreach ($data['bonuses'] ?? [] as $bonus_id) {
              EmployeeBonus::updateOrCreate([
                'employee_id' => $employee->id,
                'bonus_id' => $bonus_id
              ]);
            }

            DB::commit();

            return $this->successResponse();

        } catch (Throwable $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function destroy(EmployeeBonusRequest $request)
    {
        $this->authorizePermission(PermissionNames::BONUSES_DELETE);

        $data = $this->validateRequest($request, $request->rules());

        try {

          $employee = Employee::findOrFail($data['employee_id']);

          $employee->employeeBonuses()
          ->whereIn('bonus_id', $data['bonuses'])
          ->delete();

            return $this->successResponse(
              message: count($data['bonuses']) . ' bonuses deleted successfully',
              data: [
                  'total' => $employee->employeeBonuses()->count()
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
