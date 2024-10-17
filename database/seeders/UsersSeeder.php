<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin',
            'task_builder',  //يقوم بإنشاء المهام والتعديل عليها
            'task_manager',   // يقوم بتحديث المهام و إسنادها للمنفذ
            'task_executor'  // يقوم بتعديل حالة المهمة وإضافة تسليم
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        $admin = User::create([
            "name" => "admin",
            "email" => "admin@gmail.com",
            'national_id' => '111222333',
            "password" => '111222333',
            'role_id' => Role::where('name', 'admin')->first()->id
        ]);
        $builder = User::create([
            "name" => "builder",
            "email" => "builder@gmail.com",
            'national_id' => '111222334',
            "password" => '111222334',
            'role_id' => Role::where('name', 'task_builder')->first()->id
        ]);
        $manager = User::create([
            "name" => "manager",
            "email" => "manager@gmail.com",
            'national_id' => '111222335',
            "password" => '111222335',
            'role_id' => Role::where('name', 'task_manager')->first()->id
        ]);
        $executor = User::create([
            "name" => "executor",
            "email" => "executor@gmail.com",
            'national_id' => '111222336',
            "password" => '111222336',
            'role_id' => Role::where('name', 'task_executor')->first()->id
        ]);


    }
}
