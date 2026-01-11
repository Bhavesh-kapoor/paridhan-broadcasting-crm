<?php

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthService
{

    public function login($request)
    {
        try {
            $auth =  Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->input('remember', false));
            if (!$auth)  throw new \Exception('Invalid Credentials!', Response::HTTP_UNAUTHORIZED);
            //  Auth::login($auth);
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
