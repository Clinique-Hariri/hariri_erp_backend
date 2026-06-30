<?php

namespace Modules\HRM\app\Http\Controllers\Employee;

use Illuminate\Database\QueryException;
use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;

class EmployeeMultiController extends Controller
{
  use ApiResponseTrait;

  /**
   * Delete multiple employees
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request)
  {
      $this->authorizePermission(PermissionNames::EMPLOYEES_DELETE);

      $data = $this->validateRequest($request, [
          'ids' => 'required|array',
          'ids.*' => 'required|exists:employees,id'
      ]);

      try {
          DB::beginTransaction();

          $employees = Employee::with('user')->whereIn('id', $data['ids'])->get();

          foreach ($employees as $employee) {
              if ($employee->hasContractAt(now())) {
                  DB::rollBack();
                  return $this->errorResponse(
                      __('messages.cannot_delete_employee_with_active_contract') . " ({$employee->fullname})",
                      400
                  );
              }

              $this->deleteEmployeeRelations($employee);

              $employee->delete();
          }

          DB::commit();

          return $this->successResponse(
              message: count($data['ids']) . ' employees deleted successfully',
              data: [
                  'total' => Employee::count()
              ]
          );
      } catch (QueryException $e) {
          DB::rollBack();
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
          DB::rollBack();
          return $this->errorResponse($e->getMessage(), 500);
      }
  }

  private function deleteEmployeeRelations(Employee $employee): void
  {
      // Delete all related records
      $employee->contracts()->delete();
      $employee->employeeBonuses()->delete();
      $employee->careerChanges()->delete();
      $employee->attendances()->delete();
      $employee->loans()->delete();
      $employee->salaries()->delete();
      $employee->transactions()->delete();
      $employee->clearMediaCollection(Employee::IMAGE);

      // Delete the associated user if exists
      if ($employee->user) {
          $employee->user->delete();
      }
  }
}
