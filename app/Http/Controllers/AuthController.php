<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        return view('auth.sign-in');
    }

    public function validate(AuthRequest $authRequest)
    {
        try {
            return $this->service->login($authRequest);
        } catch (\Exception $e) {
            return response()->json(["success" => false,  "message" =>  $e->getMessage()], $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
