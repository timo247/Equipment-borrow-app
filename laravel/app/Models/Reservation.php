<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public static function equipmentReservationsCoveringTimeRange($id, $from, $to, $return_ids = false){
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
        ->get()->toArray();
        $res_with_usernames = [];
        foreach($reservations as $res){        
           $username = User::where('id', '=', $res["user_id"])->select('username')->get()->toArray();
           $username = AppHelper::array2DSingleValuesTo1D($username, "username");
           $res ["username"] = $username[0];
           array_push($res_with_usernames, $res);
        }
        return($res_with_usernames);
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


