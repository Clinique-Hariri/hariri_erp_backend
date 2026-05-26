<?php

namespace Modules\HRM\app\Http\Controllers\Loan;

use Illuminate\Database\QueryException;
use Modules\HRM\Constants\LoanStatus;
use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Loan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;

class LoanMultiController extends Controller
{
  use ApiResponseTrait;

  /**
   * Delete multiple loans
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request)
  {
    $this->authorizePermission(PermissionNames::LOANS_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:loans,id'
    ]);

    try {
      DB::beginTransaction();

      // Only delete loans that are in pending status
      $loans = Loan::whereIn('id', $data['ids'])
        ->with('installments')
        ->get();

      $unpaidLoans = $loans->filter(function ($loan) {
        return $loan->status == LoanStatus::UNPAID;
      });

      if (count($unpaidLoans) !== count($data['ids'])) {
        return $this->errorResponse('Only unpaid loans can be deleted', 422);
      }

      $unpaidLoans->delete();

      DB::commit();

      return $this->successResponse(
        message: count($unpaidLoans) . ' loans deleted successfully',
        data: [
          'total' => Loan::count()
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
