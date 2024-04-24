<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        $user = new User();
        $user->name = "Admin";
        $user->email = "admin@weddingbanquets.in";
        $user->phone = "9988776655";
        $user->password = password_hash("Y2n(m3/5R]dX", PASSWORD_DEFAULT);
        $user->save();
    }
}
