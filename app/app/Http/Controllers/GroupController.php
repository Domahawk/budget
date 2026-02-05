<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function Laravel\Prompts\search;

class GroupController extends Controller
{
    public function index(User $user, Request $request): JsonResponse
    {
        $search = $request->query('search');
        $limit = $request->query('limit', 10);

        $resource = GroupResource::collection(
            $user->groups()
                ->where('name', 'like', "%$search%")
                ->limit($limit)->get());

        return response()->json(['groups' => $resource]);
    }
}
