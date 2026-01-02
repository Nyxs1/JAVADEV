<?php

namespace App\Services\Auth\Actions;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUser
{
    /**
     * Create a new user within a database transaction.
     * If any step fails, the entire operation is rolled back.
     *
     * @param array{username: string, email: string, password: string} $data
     * @return User
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Get or create member role to ensure it exists
            // This prevents FK constraint violations on fresh databases
            $memberRole = Role::firstOrCreate(
                ['name' => 'member'],
                ['label' => 'Mahasiswa / Anggota']
            );

            return User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $memberRole->id,
            ]);
        });
    }
}
