<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users=[
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin'),
            ],

            [
                'name' => 'Ahsan Al Bashar',
                'email' => 'ahsan@gmail.com',
                'password' => bcrypt('ahsan'),
            ],


        ];

        User::insert($users);
    }
}
