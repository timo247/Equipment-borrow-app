<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class EquipmentUser extends Model
{
    public function __construct(){

    }

    protected $table = "equipment_user";
    protected $fillable = [
        "user_id",
        "equipment_id",
        "type",
        "start",
        "end",
        "start_validation",
        "end_validation",
        "start_validation_user_id",
        "end_validation_user_id"
    ];
    use HasFactory;


    public static function updateInteraction($id, $data){
        $interaction = EquipmentUser::findOrFail($id);
        $interaction->update([
            "start" => $data["start"],
            "end" => $data["end"],
            "start_validation" => $data["start_validation"],
            "end_validation" => $data["end_validation"],
        ]);
    }

    public static function makeEmptyInteractionArray($type){
        return [
            "type" => $type,
            "start" => Carbon::now(),
            "end" => Carbon::now(),
            "start_validation" => null,
            "end_validation" => null,
            "start_validation_user_id" => null,
            "end_validation_user_id" => null
        ];
    }

    public static function retrieveLastUnvalidatedInteraction($user_id, $equipment_id, $type){
        $interaction = DB::table('equipment_user')
            ->where('type', '=', $type)
            ->where('user_id', '=', $user_id)
            ->where('equipment_id', '=',$equipment_id)
            ->where('start_validation_user_id', '=', null)
            ->orderBy('created_at', 'desc')
            ->first();
        return $interaction;
    }

    public static function validateAllFinishedReservations(){
        $finished_unvalidated_reservations =  EquipmentUser::where([['type', '=', 'reservation'],['end', '<', "2022-07-14 13:05:13"],['end_validation', '=', null]])->get(); 
        foreach($finished_unvalidated_reservations as $reservation){
            $reservation->update(['end_validation' => Carbon::now(), 'end_validation_user_id' => 1]);
        }
    }


}
