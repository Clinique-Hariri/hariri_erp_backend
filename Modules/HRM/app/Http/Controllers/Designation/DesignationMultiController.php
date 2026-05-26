<?php

namespace Modules\HRM\app\Http\Controllers\Designation;

use Illuminate\Database\QueryException;
use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Designation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;

class DesignationMultiController extends Controller
{
  use ApiResponseTrait;

  /**
   * Delete multiple designations
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request)
  {
      $this->authorizePermission(PermissionNames::DESIGNATIONS_DELETE);

      $data = $this->validateRequest($request, [
          'ids' => 'required|array',
          'ids.*' => 'required|exists:designations,id'
      ]);

      try {
          DB::beginTransaction();

          Designation::whereIn('id', $data['ids'])->delete();

          DB::commit();

          return $this->successResponse(
              message: count($data['ids']) . ' designations deleted successfully',
              data: [
                  'total' => Designation::count()
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
}
