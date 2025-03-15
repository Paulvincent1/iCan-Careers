<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Admin','Employer', 'PWD','Senior'];

        foreach($roles as $role){

            Role::create(['name' => $role]);
        }

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => '123', 
        ]);

        $admin->roles()->attach(1);

    }
}
