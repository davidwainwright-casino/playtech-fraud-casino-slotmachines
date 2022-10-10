<?php

declare(strict_types=1);

namespace Wainwright\CasinoDog\Traits;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;

use function response;

trait ApiResponseHelper
{

    /*
        // Provides the following methods:

        //404
        $this->respondNotFound(string|Exception $message, ?string $key = 'error')

        //200
        $this->respondWithSuccess(?array $contents = [])

        //200
        $this->respondOk(string $message)

        //401
        $this->respondUnAuthenticated(?string $message = null)

        //403
        $this->respondForbidden(?string $message = null)

        //400
        $this->respondError(?string $message = null)

        //201
        $this->respondCreated(?array $data = [])

        //204
        $this->respondNoContent(?array $data = [])
    */

    private ?array $_api_helpers_defaultSuccessData = ['response' => 'success'];
    private ?array $_api_helpers_defaultForbiddenData = ['response' => 'forbidden'];

    /**
     * @param string|\Exception $message
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotFound(
        $message,
        ?string $key = 'error'
    ): JsonResponse {
        return $this->apiResponse(
            [$key => $this->morphMessage($message)],
            Response::HTTP_NOT_FOUND
        );
    }

    public function respondWithSuccess(?array $data = null): JsonResponse
    {
        $data ??= [];
        return $this->apiResponse(
            ['state' => 'Success', 'data' => $this->morphToArray($data), 'code' => 200]
        );
    }

    public function setDefaultSuccessResponse(?array $content = null): self
    {
        $this->_api_helpers_defaultSuccessData = $content ?? [];
        return $this;
    }


    public function respondOk(?array $data = null): JsonResponse
    {
        $data ??= [];
        return $this->apiResponse(
            ['state' => 'Ok', 'data' => $this->morphToArray($data), 'code' => 200]
        );
    }


    public function respondUnAuthenticated(?array $data = null): JsonResponse
    {
        $data ??= [];
        return $this->apiResponse(
            ['state' => 'Not Authorized', 'data' => $this->morphToArray($data), 'code' => 401],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function respondForbidden(?array $data = null): JsonResponse
    {
        $data ??= [];
        return $this->apiResponse(
            ['state' => 'Forbidden', 'data' => $this->morphToArray($data), 'code' => 403],
            Response::HTTP_FORBIDDEN
        );
    }

    public function respondError(?array $data = null): JsonResponse
    {
        $data ??= [];
        return $this->apiResponse(
            ['state' => 'Bad Request', 'data' => $this->morphToArray($data), 'code' => 400],
            Response::HTTP_BAD_REQUEST
        );
    }


    public function respondCreated(?array $data = null): JsonResponse
    {
        $data ??= [];
        return $this->apiResponse(
            ['state' => 'Created', 'data' => $this->morphToArray($data), 'code' => 200],
            Response::HTTP_CREATED
        );
    }

    /**
     * @param string|\Exception $message
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondFailedValidation(
        $message,
        ?string $key = 'message'
    ): JsonResponse {
        return $this->apiResponse(
            [$key => $this->morphMessage($message)],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public function respondTeapot(): JsonResponse
    {
        return $this->apiResponse(
          ['message' => 'I\'m a teapot'],
          Response::HTTP_I_AM_A_TEAPOT
        );
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     */
    public function respondNoContent($data = null): JsonResponse
    {
        $data ??= [];
        $data = $this->morphToArray($data);

        return $this->apiResponse($data, Response::HTTP_NO_CONTENT);
    }

    private function apiResponse(array $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     * @return array|null
     */
    private function morphToArray($data)
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        if ($data instanceof JsonSerializable) {
            return $data->jsonSerialize();
        }

        return $data;
    }

    /**
     * @param string|\Exception $message
     * @return string
     */
    private function morphMessage($message): string
    {
        return $message instanceof Exception
          ? $message->getMessage()
          : $message;
    }
}
