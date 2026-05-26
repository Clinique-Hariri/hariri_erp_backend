<?php

namespace Modules\HRM\app\Http\Controllers\CareerChange;


use Illuminate\Database\QueryException;
use Throwable;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Modules\HRM\Models\CareerChange;
use App\Support\Enum\PermissionNames;
use Modules\HRM\Services\CareerChangeService;
use Modules\HRM\Http\Resources\CareerChangeResource;
use Modules\HRM\Http\Requests\CareerChange\StoreCareerChangeRequest;

class CareerChangeController extends Controller
{
  use ApiResponseTrait;

  protected CareerChangeService $careerChangeService;

  public function __construct(CareerChangeService $careerChangeService)
  {
    $this->careerChangeService = $careerChangeService;
  }

  /**
   * Display a listing of career changes.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::CAREER_CHANGES_VIEW);
    }

    try {
      $query = CareerChange::with([
        'employee',
        'oldContract',
        'newContract'
      ]);

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->whereHas('employee', function($q) use ($searchTerm) {
          $q->where('fullname', 'like', "%{$searchTerm}%")
            ->orWhere('employee_code', 'like', "%{$searchTerm}%");
        });
      }


      if ($request->filled('employee_id')) {
        $query->where('employee_id', $request->employee_id);
      }

      if ($request->filled('status')) {
        $query->where('status', $request->status);
      }

      if ($request->filled('type')) {
        $query->where('type', $request->type);
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      if ($request->boolean('paginate')) {
        $careerChanges = $query->paginate($request->get('per_page', 10));
      } else {
        $careerChanges = $query->get();
      }

      return $this->successResponse(
        data: CareerChangeResource::collection($careerChanges)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Display the specified career change.
   *
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function show($id)
  {
    $this->authorizePermission(PermissionNames::CAREER_CHANGES_VIEW);

    try {
      $careerChange = CareerChange::with([
        'employee',
        'oldContract',
        'newContract'
      ])->findOrFail($id);

      return $this->successResponse(
        data: new CareerChangeResource($careerChange)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Store a newly created career change in storage.
   *
   * @param StoreCareerChangeRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StoreCareerChangeRequest $request)
  {
    $this->authorizePermission(PermissionNames::CAREER_CHANGES_CREATE);

    $data = $this->validateRequest($request, $request->rules());

    try {
      $careerChange = $this->careerChangeService->create($data, $request->file('file'));

      return $this->successResponse(
        data: new CareerChangeResource($careerChange)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Remove the specified career change from storage.
   *
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::CAREER_CHANGES_DELETE);

    try {
      $careerChange = CareerChange::findOrFail($id);
      $careerChange->delete();

      return $this->successResponse(
        data: [
          'total' => CareerChange::count()
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
