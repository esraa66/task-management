<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponse
{
    /**
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function success(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = ResponseAlias::HTTP_OK
    ): JsonResponse {

        $pagination = null;

        if ($data instanceof ResourceCollection && $data->resource instanceof LengthAwarePaginator) {
            $paginator = $data->resource;

            $pagination = [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ];
        }

        $responseData = [
            'success'       => $statusCode >= 200 && $statusCode < 300,
            'status_code'   => $statusCode,
            'message'       => $message,
            'data'          => $data,
            'errors'        => null,
        ];

        if ($pagination) {
            $responseData['meta'] = $pagination;
        }

        return response()->json($responseData, $statusCode);
    }

    /**
     * @param string $message
     * @param int $statusCode
     * @param array|null $errors
     * @return JsonResponse
     */
    public function error(string $message, int $statusCode, array|null $errors = null): JsonResponse
    {
        $responseData = [
            'success'       => $statusCode >= 200 && $statusCode < 300,
            'status_code'   => $statusCode,
            'message'       => $message,
            'data'          => null,
            'errors'        => $errors ?? [],
        ];
        return response()->json($responseData, $statusCode);
    }
}