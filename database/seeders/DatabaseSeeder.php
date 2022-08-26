<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RolesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@editor.com',
            'password' => 'editor123',
        ]);

        $this->call(RolesSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(ProductSeeder::class);
    }
}
