<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $service;
    /**
     * __construct
     *
     * @param  mixed $authService
     * @return void
     */
    public function __construct(AuthService $authService)
    {
        $this->service = $authService;
    }
    /**
     * signin
     *
     * @param  mixed $request
     * @return void
     */
    public function signin(Request $request): mixed
    {
        // check if user already login 
        if (Auth::check()) redirect()->route('dashboard');

        // if user not logged in then redirect to sign in page
        return view('auth.sign-in');
    }

    /**
     * validate
     *
     * @param  mixed $authRequest
     * @return void
     */
    public function validate(AuthRequest $authRequest)
    {
        try {
            $data =   $this->service->login($authRequest);
            $authRequest->session()->regenerate();
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Login Successfully!',
                    'data' => $data,
                    'redirect' => redirect()->intended(route('dashboard'))->getTargetUrl()

                ],
                200
            );
        } catch (\Exception $e) {
            $status = (int) $e->getCode();
            if ($status < 400 || $status > 599) {
                $status = 500;
            }
            return response()->json(["success" => false,  "message" =>  $e->getMessage()], $status ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
