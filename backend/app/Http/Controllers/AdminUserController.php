<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
{
    $users = User::where('role', 'user')->get();
    return response()->json($users);
}

}
