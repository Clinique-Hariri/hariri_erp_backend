<?php

namespace Modules\HRM\app\Http\Controllers\Bonus;

use Illuminate\Database\QueryException;
use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Bonus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;

class BonusMultiController extends Controller
{
  use ApiResponseTrait;

  /**
   * Delete multiple bonuses
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request)
  {
      $this->authorizePermission(PermissionNames::BONUSES_DELETE);

      $data = $this->validateRequest($request, [
          'ids' => 'required|array',
          'ids.*' => 'required|exists:bonuses,id'
      ]);

      try {
          DB::beginTransaction();

          Bonus::whereIn('id', $data['ids'])->delete();

          DB::commit();

          return $this->successResponse(
              message: count($data['ids']) . ' bonuses deleted successfully',
              data: [
                  'total' => Bonus::count()
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
