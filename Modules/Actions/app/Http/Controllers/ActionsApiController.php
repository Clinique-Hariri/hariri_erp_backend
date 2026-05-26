<?php

namespace Modules\Actions\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Modules\Actions\Constants\ActionType;
use Modules\Actions\Http\Resources\ActionResource;
use Modules\Actions\Models\Action;
use Throwable;

class ActionsApiController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Action::with(['user', 'actionable']);

            if ($request->filled('action_type')) {
                $query->where('action_type', $request->action_type);
            }

            if ($request->filled('actionable_type') && $request->filled('actionable_id')) {
                $map = [
                    'checkup'  => 'Modules\Patients\Models\Checkup',
                    'analysis' => 'Modules\Patients\Models\CheckupAnalysis',
                    'operation' => 'Modules\Patients\Models\Operation',
                ];

                $type = $map[$request->actionable_type] ?? null;

                if ($type) {
                    $query->where('actionable_type', $type)
                          ->where('actionable_id', $request->actionable_id);
                }
            }

            if ($request->boolean('paginate')) {
                $perPage = $request->integer('per_page', 15);
                $actions = $query->latest()->paginate($perPage);
            } else {
                $actions = $query->latest()->get();
            }

            return $this->successResponse(
                data: ActionResource::collection($actions)
            );
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $action = Action::with(['user', 'actionable'])->findOrFail($id);

            return $this->successResponse(
                data: new ActionResource($action)
            );
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
