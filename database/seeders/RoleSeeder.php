<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'member'],
            ['label' => 'Mahasiswa / Anggota']
        );

        Role::updateOrCreate(
            ['name' => 'mentor'],
            ['label' => 'Mentor']
        );

        Role::updateOrCreate(
            ['name' => 'admin'],
            ['label' => 'Administrator']
        );
    }
}
