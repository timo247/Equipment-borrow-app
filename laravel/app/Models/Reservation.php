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
        // dd($reservations);
        return($reservations);
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


