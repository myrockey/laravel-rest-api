<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\ResultMsgJson;
use Illuminate\Http\Request;

class ShowProfileController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($id)
    {
        //
        return ResultMsgJson::successReturn(User::find($id));
    }
}
