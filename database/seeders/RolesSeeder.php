<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->roles() as $role)
        {
            Role::create([
                'name' => $role,
            ]);
        }
    }

    private function roles(): array
    {
        return [
            "Super Admin",
            "Content Writer"
        ];
    }
}
