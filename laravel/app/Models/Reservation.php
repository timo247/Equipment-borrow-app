<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    public static function validatedReservationsCoveringTimeRange($id, $from, $to, $return_ids = false)
    {
        $reservations = EquipmentUser::
            //begins before from, ends after from
            where([
                ["equipment_id", "=", $id], ["type", "=", "reservation"], ["start_validation", "!=", null],  ["start", "<=", $from], ["end", ">=", $from]
            ])->
            //begins before to, ends after to
            orWhere([
                ["equipment_id", "=", $id], ["type", "=", "reservation"], ["start_validation", "!=", null],  ["start", "<=", $to], ["end", ">=", $to]
            ])->
            //begins after from, ends before to
            orWhere([
                ["equipment_id", "=", $id], ["type", "=", "reservation"], ["start_validation", "!=", null],  ["start", ">=", $from], ["end", "<=", $to]
            ])->
            //begins before from, ends after to
            orWhere([
                ["equipment_id", "=", $id], ["type", "=", "reservation"], ["start_validation", "!=", null],  ["start", "<=", $from], ["end", ">=", $to]
            ])
            ->get()->sortBy('start')->toArray();
        $res_with_usernames = [];
        // dump('resModel: reservation untouched');
        // dump($reservations, $id, $from, $to);
        foreach ($reservations as $res) {
            $username = User::where('id', '=', $res["user_id"])->select('username')->get()->toArray();
            $username = AppHelper::array2DSingleValuesTo1D($username, "username");
            $res["username"] = $username[0];
            array_push($res_with_usernames, $res);
        }
        // dump('resModel: toutes les reservations', $reservations);
        return ($res_with_usernames);
    }

    public static function equipmentReservationsCoveringTimeRange($id, $from, $to, $return_ids = false)
    {
        $reservations = EquipmentUser::
            //begins before from, ends after from
            where([
                ["equipment_id", "=", $id], ["type", "=", "reservation"], ["start", "<=", $from], ["end", ">=", $from]
            ])->
            //begins before to, ends after to
            orWhere([
                ["equipment_id", "=", $id], ["type", "=", "reservation"], ["start", "<=", $to], ["end", ">=", $to]
            ])->
            //begins after from, ends before to
            orWhere([
                ["equipment_id", "=", $id], ["type", "=", "reservation"], ["start", ">=", $from], ["end", "<=", $to]
            ])->
            //begins before from, ends after to
            orWhere([
                ["equipment_id", "=", $id], ["type", "=", "reservation"], ["start", "<=", $from], ["end", ">=", $to]
            ])
            ->get()->sortBy('start')->toArray();
        $res_with_usernames = [];
        foreach ($reservations as $res) {
            $username = User::where('id', '=', $res["user_id"])->select('username')->get()->toArray();
            $username = AppHelper::array2DSingleValuesTo1D($username, "username");
            $res["username"] = $username[0];
            array_push($res_with_usernames, $res);
        }
        // dump('resModel: toutes les reservations', $reservations);
        return ($res_with_usernames);
    }

    public static function possibleReservationTimeRanges($equipment_id)
    {
        $possible_timeranges = [];

        //reservations validated and currently running (when end_validation = null -> currently running)
        $validated_unfinished_reservations = EquipmentUser::where([
            ['equipment_id', '=', $equipment_id], ['type', '=', 'reservation'], ['start_validation', '!=', null], ['end_validation', '=', null]
        ])->orderBy('end', 'asc')->get()->toArray();
        $nb_reservations = count($validated_unfinished_reservations);

        if ($nb_reservations == 0) {
            array_push($possible_timeranges, [
                "start" => Carbon::now()->toDateString(),
                "end" => Carbon::now()->addYear(1)->toDateString()
            ]);
            return $possible_timeranges;
        }


        //Add a first range if there is at least 3 days between now and first start of reservation
        if ($validated_unfinished_reservations[0]["start"] >= Carbon::now()->addDay(3)) {
            array_push(
                $possible_timeranges,
                [
                    "start" => Carbon::now()->toDateString(),
                    "end" => AppHelper::addDaysToString($validated_unfinished_reservations[0]["start"], -1)
                ]
            );
        }

        //If theres only one, reservation can be done from its end + 1 day untill in a year and from today untill its start
        //Resrvation must be able to last today + 3 days
        if ($nb_reservations == 1) {
            array_push(
                $possible_timeranges,
                [
                    "start" => AppHelper::addDaysToString($validated_unfinished_reservations[0]["end"], 1),
                    "end" => Carbon::now()->addYear(1)->toDateString()
                ]
            );
            return $possible_timeranges;
        }

        //Add a timerange between all concerned reservations, staarting from then end of the earlier (+1), ending in the beginning of the following (-1)  
        for ($i = 0; $i < $nb_reservations; $i++) {
            if ($i == 0) {
                $new_time_range = [
                    "start" => AppHelper::addDaysToString($validated_unfinished_reservations[$i]["end"], 1),
                    "end" => AppHelper::addDaysToString($validated_unfinished_reservations[$i + 1]["start"], -1)
                ];
            } else if ($i > 0 && $i <= $nb_reservations - 2) {
                $new_time_range = [
                    "start" =>  AppHelper::addDaysToString($validated_unfinished_reservations[$i]["end"], 1),
                    "end" =>  AppHelper::addDaysToString($validated_unfinished_reservations[$i + 1]["start"], -1)
                ];
            } else {
                $new_time_range = [
                    "start" =>  AppHelper::addDaysToString($validated_unfinished_reservations[$i]["end"], 1),
                    "end" =>  AppHelper::addDaysToString(Carbon::now()->addYear(1)->format('Y-m-d H:i:s'), -1)
                ];
            }
            array_push($possible_timeranges, $new_time_range);
        }
        return $possible_timeranges;
    }

    // public static function equipmentsReservationsCoveringTimeRange($from, $to){
    //     $ids = EquipmentUser::where([
    //         ['type', '=', 'reservation'],['end', '>=', $from]
    //     ])->select('id')->get()->toArray();
    //     if(!empty($ids)){
    //         $ids = AppHelper::array2DSingleValuesTo1D($ids, "id");
    //     }
    //     dd($ids);
    // }
}
