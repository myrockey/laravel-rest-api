<?php

namespace App\Http\Controllers;

use App\Events\ActionLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends BasicController
{
    //
    public function index(Request $request) {
        event(new ActionLog('用户1', '1', '测试监听事件开始'));
        echo "index is run";
        event(new ActionLog('用户1', '1', '测试监听事件结束'));
    }
}
