<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    public function success($message, $data = null)
    {
        $res = [
            'status_code' => 1,
            'status_text' => 'success',
            'message' => $message,
        ];

        if ($data != null || $data == []) {
            $res['data'] = $data;
        }

        return $res;
    }
    public function failed($message)
    {
        return   [
            'status_code' => 0,
            'status_text' => 'failed',
            'message' => $message,
        ];
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }
        $userData = $request->all();
        $userData['password'] = Hash::make($request->password);

        $user = User::create($userData);
        if ($user) {
            $data['token'] = $user->createToken('AccessToken')->accessToken;
        }
        return $user ? $this->success('User Registered Successfully', $data) : $this->failed('Unable to Registered at this moment');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }
        $check = Hash::check($request->password, ($user = User::where('email', $request->email)->first())->password);

        if ($check) {
            $data['token'] = $user->createToken('AccessToken')->accessToken;
        }
        return $check ? $this->success('Login Successfull', $data) : $this->failed('Check Credentials');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {

            $token= $request->user()->tokens->find(Auth::user()->token()->id)->revoke();

            return $token ? $this->success('Logout Successfully') : $this-> failed('Unable to Logout at this moment');
        }
    }

    public function hardlogout()
    {
            if (Auth::check()) {
                $userID = Auth::user()->id;
                $check =  DB::table('oauth_access_tokens')
                                ->where('user_id', $userID)
                                ->delete();

                return $check ? $this->success('Logout From All Devices Successfully') : $this-> failed('Unable to Logout at this moment');
            }
    }

}
