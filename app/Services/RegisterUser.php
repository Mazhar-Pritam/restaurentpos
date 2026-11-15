<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RegisterUser
{
    public function create(array $attributes): User
    {
        return Cache::lock('first-user-registration', 60)->block(30, function () use ($attributes): User {
            return DB::transaction(function () use ($attributes): User {
                $role = User::query()->exists() ? 'staff' : 'super-admin';

                $user = User::create($attributes);
                $user->assignRole($role);

                return $user;
            });
        });
    }
}
