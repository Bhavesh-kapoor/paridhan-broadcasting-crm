<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
     * @return mixed
     */
    public function validate(AuthRequest $authRequest): mixed
    {
        try {
            $data =   $this->service->login($authRequest);
            $authRequest->session()->regenerate();
            $dashboard_url = '';
            if (Auth::user()->role === "admin") {
                $dashboard_url = redirect()->intended(route('dashboard'))->getTargetUrl();
            } else if (Auth::user()->role === "employee") {
                $dashboard_url = redirect()->intended(route('employee.dashboard'))->getTargetUrl();
            }
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Login Successfully!',
                    'data' => $data,
                    'redirect' => $dashboard_url
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }

    /**
     * logout
     *
     * @param  mixed $request
     * @return void
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        return redirect()->route('login');
    }
}
