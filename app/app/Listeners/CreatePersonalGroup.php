<?php

namespace App\Listeners;

use App\Models\Group;

class CreatePersonalGroup
{
    public function handle(object $event): void
    {
        $user = $event->user;

        $group = Group::create([
            'name' => $user->name,
            'type' => 'personal',
        ]);

        $group->users()->attach($user->id, ['role' => 'owner']);
    }
}
