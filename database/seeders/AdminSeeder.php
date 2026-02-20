<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'logo'  =>  'default.jpg',
            'name'  =>  'Sambel Gosreh',
            'username'  =>  'sambel_gosreh',
            'email' =>  'sambel90@email.test',
            'password'  =>  bcrypt('password'),
            'role'  =>  'admin'
        ]);
    }
}
