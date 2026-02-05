<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $limit = $request->query('limit', 10);

        $query = User::query();

        if ($search) {
            $query->where('username', 'like', '%' . $search . '%');
        }

        $query->limit($limit);
        $users = $query->get();

        return response()->json(['users' => $users]);
    }

    public function show(User $user, Request $request)
    {
        return response()->json(['user' => new UserResource($user->load('groups.budgets'))]);
    }
}
