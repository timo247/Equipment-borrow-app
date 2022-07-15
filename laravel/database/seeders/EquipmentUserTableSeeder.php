<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * -1- Add a computer reserved by john doe for 1 week up untill next week but not borrowed
     * -2- Add a tablet reserved by john doe for 1 week, borrowed two weeks ago and delivered 1 week ago
     * -3- Add a camera reserved by alice for 3 weeks, borrowed two weeks ago and not delivered
     * -4- Add an earphone reserved by alice for one week, borrowed two week ago and not delivered
     * @return void
     */
    public function run()
    {
        // -1- Add a computer reserved by john doe for 1 week up untill next week but not borrowed
        DB::table('equipment_user')->insert([
            "user_id" => 3,
            "equipment_id" => 9,
            "type" => "reservation",
            "start" => Carbon::now()->subDay(1),
            "end" => Carbon::now()->addDay(6),
            "start_validation" => Carbon::now(),
            "end_validation" => null,
            "start_validation_user_id" => 1,
            "end_validation_user_id" => null
        ]);

        // -2- Add a tablet reserved by john doe for 1 week, borrowed two weeks ago and delivered 1 week ago
        DB::table('equipment_user')->insert([
            "user_id" => 3,
            "equipment_id" => 10,
            "type" => "reservation",
            "start" => Carbon::now()->subWeek(2),
            "end" => Carbon::now()->subWeek(1),
            "start_validation" => Carbon::now()->subWeek(2),
            "end_validation" => Carbon::now()->subWeek(1),
            "start_validation_user_id" => 1,
            "end_validation_user_id" => 1
        ]);
        DB::table('equipment_user')->insert([
            "user_id" => 3,
            "equipment_id" => 10,
            "type" => "borrow",
            "start" => Carbon::now()->subWeek(2),
            "end" => Carbon::now()->subWeek(1),
            "start_validation" => Carbon::now()->subWeek(2),
            "end_validation" => Carbon::now()->subWeek(1),
            "start_validation_user_id" => 1,
            "end_validation_user_id" => 1
        ]);

         // -3- Add a camera reserved by alice for 3 weeks, borrowed two weeks ago and not delivered
         DB::table('equipment_user')->insert([
            "user_id" => 4,
            "equipment_id" => 11,
            "type" => "reservation",
            "start" => Carbon::now()->subWeek(2),
            "end" => Carbon::now()->addWeek(1),
            "start_validation" => Carbon::now()->subWeek(2),
            "end_validation" => null,
            "start_validation_user_id" => 1,
            "end_validation_user_id" => null
        ]);
        DB::table('equipment_user')->insert([
            "user_id" => 4,
            "equipment_id" => 11,
            "type" => "borrow",
            "start" => Carbon::now()->subWeek(2),
            "end" => null,
            "start_validation" => Carbon::now()->subWeek(2),
            "end_validation" => null,
            "start_validation_user_id" => 1,
            "end_validation_user_id" => null
        ]);
 
        // -4-Add an earphone reserved by alice for one week, borrowed two week ago and not delivered
         DB::table('equipment_user')->insert([
            "user_id" => 4,
            "equipment_id" => 12,
            "type" => "reservation",
            "start" => Carbon::now()->subWeek(2),
            "end" => Carbon::now()->subWeek(1),
            "start_validation" => Carbon::now()->subWeek(2),
            "end_validation" => Carbon::now()->subWeek(1),
            "start_validation_user_id" => 1,
            "end_validation_user_id" => 1
        ]);
        DB::table('equipment_user')->insert([
            "user_id" => 4,
            "equipment_id" => 12,
            "type" => "borrow",
            "start" => Carbon::now()->subWeek(2),
            "end" => null,
            "start_validation" => Carbon::now()->subDay(13),
            "end_validation" => null,
            "start_validation_user_id" => 1,
            "end_validation_user_id" => null
        ]);
    }
}
