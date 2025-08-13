<?php

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthService
{

    public function login($request)
    {
        try {
            $auth =  Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password]);
            if (!$auth)  throw new \Exception('Invalid Credentials!', Response::HTTP_UNAUTHORIZED);
            //  Auth::login($auth);

            return [
                'user' => Auth::guard('web')->user(),
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
