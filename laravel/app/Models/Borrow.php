<?php

namespace App\Models;

use App\Models\EquipmentUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Borrow extends Model
{
    use HasFactory;

    public static function equipmentDeliveredBorrowsCoveringTimeRange($id, $from, $to, $return_ids = false){
    $borrows = EquipmentUser::
         //begins before from, ends after from
        where([
            ["equipment_id", "=", $id], ["type", "=", "borrow"], ["start_validation", "!=", null],  ["start", "<=", $from], ["end", ">=", $from]
        ])->
        //begins before to, ends after to
        orWhere([
            ["equipment_id", "=", $id], ["type", "=", "borrow"], ["start_validation", "!=", null],  ["start", "<=", $to], ["end", ">=", $to]
        ])->
        //begins after from, ends before to
        orWhere([
            ["equipment_id", "=", $id], ["type", "=", "borrow"], ["start_validation", "!=", null],  ["start", ">=", $from], ["end", "<=", $to]
        ])->
        //begins before from, ends after to
        orWhere([
            ["equipment_id", "=", $id], ["type", "=", "borrow"], ["start_validation", "!=", null],  ["start", "<=", $from], ["end", ">=", $to]
        ])
        ->get()->toArray();
        return $borrows;
    }

    public static function equipmentUndeliveredBorrowsUntilDate($id, $date, $return_ids = false){
        //begins before date, never delivered
        $borrows = EquipmentUser::where([
            ["equipment_id", "=", $id], ["type", "=", "borrow"], ["start_validation", "!=", null],  ["start", "<=", $date], ["end", "=", null]
        ])->get()->toArray();
        return $borrows;
    }

}
