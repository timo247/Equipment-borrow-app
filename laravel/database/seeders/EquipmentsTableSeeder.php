<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EquipmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create two empty equipments per categories
     * -1- Add a computer reserved by john doe for 1 week up untill next week but not borrowed
     * -2- Add a tablet reserved by john doe for 1 week, borrowed two weeks ago and delivered 1 week ago
     * -3- Add a camera reserved by alice for 3 weeks, borrowed two weeks ago and not delivered
     * -4- Add an earphone reserved by alice for one week, borrowed two weeks ago and not delivered 
     * 
     *
     * @return void
     */
    public function run()
    {
        $categories = ["laptop", "tablet", "camera", "earphone"];
        $adjectives = ["reliable", "performing"];

        for($i = 0; $i < count($categories); $i++){
            for($z = 0; $z < 2; $z ++){
                DB::table('equipments')->insert([
                    "category" => $categories[$i],
                    "name" => $categories[$i]." ".$z+1,
                    "description" => "A ".$adjectives[$z]." ".$categories[$i].".",
                    "image_url" => "/equipments/categories/".$categories[$i]."/".$categories[$i].$z+1 .".jpg",
                ]);
            }
        }

        //-1-
        DB::table('equipments')->insert([
            "category" => "laptop",
            "name" => "laptop 3",
            "description" => "A ligth and powerful laptop, idealistic for programming during travels.",
            "image_url" => "/equipments/categories/laptop/laptop9.jpg",
        ]);

        //-2- Add a tablet reserved by john doe for 1 week, borrowed two weeks ago and delivered 1 week ago
        DB::table('equipments')->insert([
            "category" => "tablet",
            "name" => "tablet 3",
            "description" => "A handy tablet, very usefull for its powerfull camera during demonstration meetings.",
            "image_url" => "/equipments/categories/tablet/tablet10.jpg",
        ]);

        // Add a camera reserved by alice for 3 weeks, borrowed two weeks ago and not delivered
        DB::table('equipments')->insert([
            "category" => "camera",
            "name" => "camera 3",
            "description" => "A top quality camera, to make awesome pictures.",
            "image_url" => "/equipments/categories/camera/camera11.jpg",
        ]);

        // -4- Add an earphone reserved by alice for one week, borrowed two weeks ago and not delivered 
        DB::table('equipments')->insert([
            "category" => "earphone",
            "name" => "earphone 3",
            "description" => "Qualitative earphones.",
            "image_url" => "/equipments/categories/earphone/earphone12.jpg",
        ]);
    }
}
