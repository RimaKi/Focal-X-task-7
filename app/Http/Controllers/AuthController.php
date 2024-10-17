<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * login user
     * @param LoginRequest $request
     * @return array
     * @throws \Exception
     */

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            throw new \Exception('Unauthorized', 401);
        }
        $user = Auth::user();
        return [
            'user' => $user->load('role'),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ];
    }

    /**
     * add user by admin
     * @param RegisterRequest $request
     * @return array
     */
    public function addUser(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->national_id),
            'national_id' => $request->national_id,
            'role_id'=>$request->role_id
        ]);
        return [
            'message' => 'User created successfully',
            'user' => $user->load('role')
        ];
    }

    /**
     * @return string
     */
    public function logout()
    {
        Auth::logout();
        return 'Successfully logged out';
    }

    /**
     * refresh token
     * @return array
     */
    public function refresh()
    {
        return [
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ];
    }

    /**
     * change password by  auth user
     * @param ChangePasswordRequest $request
     * @return string
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = User::findOrFail(\auth()->user()->id);
        $user->password = $request->password;
        $user->update();
        return "Done";
    }


}
