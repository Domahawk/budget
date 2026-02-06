<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\User;
use App\Rules\GroupType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'type' => ['required', new GroupType],
            'users' => ['array'],
            'users.*' => ['exists:users,id'],
        ]);

        $data['users'][] = $request->user()->id;

        $group = Group::create([
            'name' => $data['name'],
            'type' => $data['type'],
        ]);

        $group->users()->attach($data['users']);

        return response()->json(['group' => $group]);
    }

    public function update(Request $request, Group $group): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'type' => ['required', new GroupType],
            'users' => ['array'],
            'users.*' => ['exists:users,id'],
        ]);

        $group->name = $data['name'];
        $group->type = $data['type'];
        $group->save();

        $group->users()->sync($data['users']);

        return response()->json(['group' => $group]);
    }

    public function show(Group $group): JsonResponse
    {
        return response()->json(['group' => $group->load('users')]);
    }
}
