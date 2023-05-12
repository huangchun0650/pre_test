<?php
namespace App\Http\Responses;

use App\Library\ErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class Response
{
    public const SUCCESS_CODE = 1;

    /**
     * @param $status
     * @param int $code
     * @param array $data
     * @param string|null $message
     * @return JsonResponse
     */
    public function jsonResponse($status, int $code = self::SUCCESS_CODE, array $data = [], string|null $message = null): JsonResponse
    {
        $data = $this->format($code, $data, $message);

        return new JsonResponse($data, $status);
    }

    /**
     * 成功時的回傳
     * @param array|JsonResource $data
     * @return JsonResponse
     */
    public function success(array|JsonResource $data = []): JsonResponse
    {
        return $this->jsonResponse(200, self::SUCCESS_CODE,  $data);
    }

    /**
     * 一般失敗時的回傳
     * @param int $code
     * @param string $message
     * @return JsonResponse
     */
    public function failed(int $code, string $message): JsonResponse
    {
        return $this->jsonResponse(500, $code, message: $message);
    }

    /**
     * 驗證失敗時的回傳
     * @param int $code
     * @param string $message
     * @return JsonResponse
     */
    public function failedValidation(int $code, string $message): JsonResponse
    {
        return $this->jsonResponse(422, $code, message: $message);
    }

    /**
     * 權限不足失敗的回傳
     *
     * @param string|null $message
     * @return JsonResponse
     */
    public function failedPermissionDefined(string $message = null): JsonResponse
    {
        return $this->jsonResponse(403, ErrorCode::PERMISSION_DENIED, message: $message);
    }

    /**
     * 未經授權的失敗回傳
     *
     * @param  int  $code
     * @param  string  $message
     * @return JsonResponse
     */
    public function unauthorized(int $code, string $message): JsonResponse
    {
        return $this->jsonResponse(403, $code, message: $message);
    }

    /**
     * 異常操作失敗
     *
     * @param string|null $message
     * @return JsonResponse
     */
    public function failedAbnormalOperation(string $message = null): JsonResponse
    {
        return $this->jsonResponse(500, ErrorCode::SYSTEM_ERROR, message: $message);
    }

    /**
     * 重複提交表單失敗
     */
    public function failedRepeatPost(): JsonResponse
    {
        return $this->jsonResponse(429, ErrorCode::REPEAT_POST);
    }

    /**
     * @param int $code
     * @param array $data
     * @param string|null $message
     * @return array
     */
    private function format(int $code, array $data, string|null $message): array
    {
        $format = collect([
            'code' => $code,
            'data' => $data,
            'time' => time()
        ]);

        if (config('app.debug') && !is_null($message)) {
            $format->prepend($message, 'message');
        }

        return $format->toArray();
    }
}
