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
        $requestUser = $request->user();

        $query = User::where('id', '!=', $requestUser->id);

        if ($search) {
            $query->where('username', 'like', '%'.$search.'%');
        }

        $query->limit($limit);
        $users = $query->get();

        return response()->json(['users' => $users]);
    }

    public function show(User $user)
    {
        return response()->json(['user' => new UserResource($user->load('groups.budgets'))]);
    }
}
