<?php

namespace App\Helpers;

class AppHelper
{

    /* 
     * Recieves an std collection and a collection,
     * and returns an array sorted by the std properties of its objects
    */
    public static function sortCollectionByStdValues($std_values, $collection)
    {
        $data = [];
        $collection_array = $collection->toArray();
        //Sort all equipments by their categories
        foreach ($std_values as $std) {
            $std_to_array = (array)$std;
            $std_key = array_key_first($std_to_array);
            $std_string = $std_to_array[$std_key];

            $sorted_collection["name"] = $std_string;
            $sorted_collection["objects"] = (array_filter($collection_array, function ($obj) use ($std_string, $std_key) {
                // dump($std_string);
                // dump($obj[$std_key]);
                return $obj[$std_key] == $std_string;
            }));
            array_push($data, $sorted_collection);
        }
        return $data;
    }
    public static function stdsArrayToStringsArray($std_arr)
    {
        $string_arr = [];
        foreach ($std_arr as $std) {
            $std_to_array = (array)$std;
            $std_key = array_key_first($std_to_array);
            $string = $std_to_array[$std_key];
            array_push($string_arr, $string);
        }
        return $string_arr;
    }

    //Transform 2d arrays into 1d (ex [ ['id' => 1], ['id' => 2]] -> [1, 2])
    public static function array2DSingleValuesTo1D($array_2d, $key){
        $new_array = [];
        foreach($array_2d as $array){
            $value = $array[$key];
            array_push($new_array, $value);
        }
        return $new_array;
    }

    public static function in_multidimensional_arra($arr1, $arr2, $comparaison_key){
        $in_arr = false;
        foreach($arr1 as $comp_arr){
            if($arr2[$comparaison_key] == $comp_arr[$comparaison_key]){
                $in_arr = true;
            }
        }
        return $in_arr;
    }
}

/*practical informaitons
All reservations having end_validation => 00.00.00 are cancelled ones
No reservation can be done ending after 1 year from the time the reservation is made. -> No reservation can end 2 year after it is done.



//Practical queries:

Create a reservation
App\Models\EquipmentUser::create(['user_id' => 1, 'equipment_id' => 1, 'type' => 'reservation', 'start' => '2022-06-14 11:18:47', 'end' => '2022-06-14 11:18:47', 'start_validation' => '2022-06-15 11:18:47', 'end_validation' => '2022-06-19 11:18:47', 'start_validation_user_id' => 1, 'end_validation_user_id' =>1])
Get a reservation
App\Models\EquipmentUser::where(['user_id' => 1, 'equipment_id' => 1, 'type' => 'reservation', 'start' => '2022-06-14 11:18:47', 'end' => '2022-06-14 11:18:47', 'start_validation' => '2022-06-15 11:18:47', 'end_validation' => '2022-06-19 11:18:47', 'start_validation_user_id' => 1, 'end_validation_user_id' =>1])->get()


Tests reservations ability:
    reservations clauses:
$reservations = EquipmentUser::where([["equipment_id", "=", 9], ["type", "=", "reservation"], ["start_validation", "!=", null],  ["start", "<=", "2022-07-02"], ["end", ">=", "2022-07-02"]])->orWhere([["equipment_id", "=", 9], ["type", "=", "reservation"], ["start_validation", "!=", null],  ["start", "<=", "2022-07-22"], ["end", ">=", "2022-07-22"]])->orWhere([["equipment_id", "=", 9], ["type", "=", "reservation"], ["start_validation", "!=", null],  ["start", ">=", "2022-07-02"], ["end", "<=", "2022-07-22"]])->orWhere([["equipment_id", "=", 9], ["type", "=", "reservation"], ["start_validation", "!=", null],  ["start", "<=", "2022-07-02"], ["end", ">=", "2022-07-22"]])->get()->toArray();
    borrows clauses:
        not null end cases



*/



