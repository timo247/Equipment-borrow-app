<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * -0- Add 2 unborrowed and unreerved devices fors 4 categories (laptop, tablet, camera, earphone)
     * -1- Add a computer reserved by john doe for 1 week up untill next week but not borrowed
     * -2- Add a tablet reserved by john doe for 1 week, borrowed two weeks ago and delivered 1 week ago
     * -3- Add a camera reserved by alice for 3 weeks, borrowed two weeks ago and not delivered
     * -4- Add an earphone reserved by alice for one week, borrowed two week ago and not delivered
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(EquipmentsTableSeeder::class);
        $this->call(EquipmentUserTableSeeder::class);
    }
}
