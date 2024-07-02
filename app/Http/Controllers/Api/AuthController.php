<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class AuthController extends Controller
{
    use ApiResponses;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // $token = $user->createToken('Personal Access Token')->accessToken;

        return $this->ok('User Registered Successfully!');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('Personal Access Token')->accessToken;
            $success['token'] = $token;
            $success['userDetails'] =  $user;

            return $this->success('User LoggedIn Successfully!', $success);
        }

        return $this->error('Unauthorised.', ['error' => 'Unauthorised']);
    }

    public function logout(Request $request)
    {
        // Get the authenticated user's token
        $token = $request->user()->token();

        // Revoke the token
        $token->revoke();
        
        return $this->ok('Successfully logged out');
    }

}
