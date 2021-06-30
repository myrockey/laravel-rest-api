<?php

namespace App\Http\Middleware;

use App\Utils\ResultMsgJson;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckRepeat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->method() == 'POST') {
            $post = $request->post();
            $post['time'] = time();
            $key = "checkRepeat:" . md5(json_encode($post));  // 转md5
            //此限制只阻止同一秒内产生的重复提交，并不阻止每隔一秒发送一次的重复提交
            if (Cache::get($key)) {
                return response()->json(ResultMsgJson::errorReturn("Being processed, please do not submit repeatedly!"));  //校验错误，直接返回
            }

            // 缓存时间(10秒)
            Cache::set($key, 1, 5);
        }

        return $next($request);
    }
}
