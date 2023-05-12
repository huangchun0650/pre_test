<?php

namespace App\Library;

final class ErrorCode
{
    /**
     * 系統型錯誤
     */
    public const SYSTEM_ERROR = 100000; // 系統錯誤

    /**
     * 中間件驗證
     */
    public const PERMISSION_DENIED = 100101; // 權限不足
    public const REPEAT_POST       = 100102; // 重複提交表單
    public const JWT_EMPTY         = 100103; // JWT TOKEN 不存在
    public const JWT_INVALID       = 100104; // 無效的 JWT TOKEN
}
