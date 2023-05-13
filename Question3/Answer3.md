 # Laravel 當中的 middleware 能夠在進入 controller 和離開 controller 後提供額外的操作，參考 官方文件 。若換成自己設計類似的 middleware ，請描述一下會如何設計以及設計的做法。

**Answer:**
 若要設計 middleware， 先在中介層 kernel 中註冊。

 設計的 middleware中 function ``handle`` 裡設定，進入 controller 前 的邏輯寫在
 ``$next($request)`` 前，而離開 controller 後的邏輯寫在 ``$next($request)`` 之後。

實際情況可能長這樣：
 ```
 public function handle($request, Closure $next)
{
    // 在進入 controller 前 邏輯
    // ex: token 驗證
    if (!$request->has('token')) {
        return response()->json(['error' => 'token miss']);
    }
    $response = $next($request);

    // 在離開 controller 後 邏輯
    Log::info('something need to logging');
    return $response;
}
 ```

 最後 route 補上 要使用的 middleware 或使用 middleware 群組。
 ex : ```Route::get('', 'PostController@index')->middleware('auth');```