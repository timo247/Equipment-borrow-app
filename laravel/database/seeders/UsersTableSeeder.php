<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates two base admins and two base users.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        DB::table('users')->insert([
            "username" => "Base Admin",
            "email" => "email1@gmx.ch",
            "password" => Hash::make("password1"),
            "user_type" => "admin"
        ]);
        DB::table('users')->insert([
            "username" => "Test Admin",
            "email" => "test@admin.com",
            "password" => Hash::make("TestAdminPassword1"),
            "user_type" => "admin"
        ]);
        DB::table('users')->insert([
            "username" => "John Doe",
            "email" => "email3@gmx.ch",
            "password" => Hash::make("password3"),
        ]);
        DB::table('users')->insert([
            "username" => "Alice Foo",
            "email" => "email4@gmx.ch",
            "password" => Hash::make("password4"),
        ]);
        DB::table('users')->insert([
            "username" => "Test User",
            "email" => "test@user.com",
            "password" => Hash::make("TestUser1"),
        ]);
    }
}
