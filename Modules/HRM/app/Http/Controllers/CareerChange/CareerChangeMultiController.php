<?php

namespace Modules\HRM\app\Http\Controllers\CareerChange;

use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\CareerChange;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;

class CareerChangeMultiController extends Controller
{
  use ApiResponseTrait;

  /**
   * Delete multiple career changes
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request)
  {
      $this->authorizePermission(PermissionNames::CAREER_CHANGES_DELETE);

      $data = $this->validateRequest($request, [
          'ids' => 'required|array',
          'ids.*' => 'required|exists:career_changes,id'
      ]);

      try {
          DB::beginTransaction();

          CareerChange::whereIn('id', $data['ids'])->delete();

          DB::commit();

          return $this->successResponse(
              message: count($data['ids']) . ' career changes deleted successfully',
              data: [
                  'total' => CareerChange::count()
              ]
          );
      } catch (Throwable $e) {
          DB::rollBack();
          return $this->errorResponse($e->getMessage(), 500);
      }
  }
  
  /**
   * Update status for multiple career changes
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function updateStatus(Request $request)
  {
      $this->authorizePermission(PermissionNames::CAREER_CHANGES_UPDATE);

      $data = $this->validateRequest($request, [
          'ids' => 'required|array',
          'ids.*' => 'required|exists:career_changes,id',
          'status' => 'required|string|in:' . implode(',', \Modules\HRM\Constants\Status::all())
      ]);

      try {
          DB::beginTransaction();

          CareerChange::whereIn('id', $data['ids'])->update(['status' => $data['status']]);

          DB::commit();

          return $this->successResponse(
              message: count($data['ids']) . ' career changes updated successfully',
              data: [
                  'total' => CareerChange::count()
              ]
          );
      } catch (Throwable $e) {
          DB::rollBack();
          return $this->errorResponse($e->getMessage(), 500);
      }
  }
}