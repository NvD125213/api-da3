<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getAllUser() {
        $users = User::all();
        return response()->json([
            'status' => 200,
            'users' => $users
        ]);
    }
}
