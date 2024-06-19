<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //Register 
    public function register(Request $request) {
        $validator = validator()->make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|min:8'
        ]);
        if($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->getMessageBag(),
            ]);
        }
        else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $token =  $user->createToken($user->email.'_Token')->plainTextToken;
            return response()->json([
                'status'=>200,
                'username'=>$user->name,
                'token'=>$token,
                'message'=>'Đăng ký thành công!'
            ]);

        }
    }

    // login
    public function login(Request $request) {
        $validator = validator()->make($request->all(), [
            'email' => 'required|email|max:191',
            'password' => 'required'
        ]);
        if($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->getMessageBag(),
            ]);
        }
        else {

            $user = User::where('email', $request->email)->first();
 
            if (! $user || ! Hash::check($request->password, $user->password)) {
               return response()->json([
                'status'=>401,
                'message'=>'Thông tin không hợp lệ!'
               ]);
            }
            else {
                if($user->role_as == 1) {
                    $token = $user->createToken($user->email.'_AdminToken', ['server:admin'])->plainTextToken;
                } 
                else {
                    $token = $user->createToken($user->email.'_Token')->plainTextToken;
                }
                
                return response()->json([
                    'status'=>200,
                    'name'=>$user->name,
                    'token'=>$token,
                    'role' => $user->role_as, 
                    'message'=>'Đăng nhập thành công!'
                ]);
            }

        }

    }
    public function logout() {
        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return response()->json([
            'status' => 200,
            'role' => $user->role_as,
            'message' => 'Thoát thành công!'
        ]);

    }

    public function checkAuth(Request $request)
    {
        if ($request->user()) {
            return response()->json(['authenticated' => true, 'status' => 200]);
        } else {
            return response()->json(['authenticated' => false, 'status' => 401]);
        }
    }
}
