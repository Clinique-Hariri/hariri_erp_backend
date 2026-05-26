<?php
namespace App\Traits;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Validator;

trait ApiResponseTrait
{
  protected function validateRequest(Request $request, array $rules, array $messages = [], array $customAttributes = []): array
  {
      $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);

      if ($validator->fails()) {
          abort(response()->json([
              'status' => 0,
              'message' => $validator->errors()->first()
          ], 422));
      }

      return $validator->validated();
  }



  protected function successResponse($message = 'Success', $data = null, $statusCode = 200): JsonResponse
  {
    $response = [
      'status' => 1,
      'message' => $message,
    ];

    if ($data instanceof ResourceCollection) {
      $paginator = $data->resource;

      if ($paginator instanceof Paginator) {
        $response['data'] = $data->collection->values();
        $response['meta'] = [
          'current_page' => $paginator->currentPage(),
          'per_page' => $paginator->perPage(),
          'total' => $paginator->total(),
          'last_page' => $paginator->lastPage(),
        ];
        $response['links'] = [
          'first' => $paginator->url(1),
          'last' => $paginator->url($paginator->lastPage()),
          'prev' => $paginator->previousPageUrl(),
          'next' => $paginator->nextPageUrl(),
        ];
      } else {
        $response['data'] = $data->resolve();
      }
    }

    elseif ($data instanceof JsonResource) {
      $response['data'] = $data->resolve();
    }

    elseif ($data instanceof Paginator) {
      $response['data'] = $data->items();
      $response['meta'] = [
        'current_page' => $data->currentPage(),
        'per_page' => $data->perPage(),
        'total' => $data->total(),
        'last_page' => $data->lastPage(),
      ];
      $response['links'] = [
        'first' => $data->url(1),
        'last' => $data->url($data->lastPage()),
        'prev' => $data->previousPageUrl(),
        'next' => $data->nextPageUrl(),
      ];
    }

    elseif ($data !== null) {
      $response['data'] = $data;
    }

    return response()->json($response, $statusCode);
  }

  protected function errorResponse($message = 'An error occurred', $statusCode = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 0,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Check if the authenticated user has a specific permission.
     *
     * @param string $permission
     * @return \Illuminate\Http\JsonResponse|void
     */
  protected function authorizePermission(string|array $permissions)
  {
    $user = auth()->user();

    if (!$user) {
      throw new HttpResponseException(
        $this->errorResponse(__('app.unauthorized'), 403)
      );
    }

    $permissions = is_array($permissions) ? $permissions : [$permissions];

    $hasPermission = collect($permissions)->some(fn($p) => $user->hasPermissionTo($p));

    if (!$hasPermission) {
      throw new HttpResponseException(
        $this->errorResponse(__('app.unauthorized'), 403)
      );
    }
  }
}
