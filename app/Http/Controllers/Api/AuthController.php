<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Presenters\AuthPresenter;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    protected $auth;

    public function __construct(AuthPresenter $authPresenter)
    {
        $this->auth = $authPresenter;

        $this->middleware('auth:api', [
            'only' => [ 'update' ]
        ]);
    }

    public function store(AuthRequest $request)
    {
        $credentials = $request->validated();

        if ( ! $token = Auth::attempt($credentials) ) {
            return response()->json([
                'error' => '很抱歉，您的邮箱和密码不匹配。'
            ]);
        }

        return $this->auth->respondWithToken($token);
    }

    public function update()
    {
        $newToken = auth()->refresh();

        return $this->auth->respondWithToken($newToken);
    }

    public function destroy()
    {
        Auth::logout();

        return [];
    }
}
