<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\ResultMsgJson;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    public function __construct()
    {
        // 控制器中 写中间件 控制更精细方便
//        $this->middleware('auth');
//        $this->middleware('log')->only('index');
//        $this->middleware('subscribed')->except('store');
    }

    //
    public function show($id) {

        if ($id) {
            return ResultMsgJson::successReturn(User::find($id));
        } else {
            return ResultMsgJson::errorReturn('id不能为空');
        }
    }
}
