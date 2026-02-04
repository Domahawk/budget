<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user, Request $request)
    {
        return response()->json(['user' => new UserResource($user->load('groups.budgets'))]);
    }
}
