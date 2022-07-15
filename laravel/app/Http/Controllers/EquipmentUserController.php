<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentUserController extends Controller
{
    
    public function reserve(Request $request){
        dd('reserve->');
    }

    public function acceptReservation(Request $request){
        dd('end-reserve->');
    }

    public function cancelReservation(Request $request){
        dd('cancel-reserve->');
    }


    public function borrow(Request $request){
        dd('borrow->');
    }

    public function endBorrow(Request $request){
        dd('end-borrow->');
    }



    public function store(Request $request){
        $inputs = $request->input();
        $equipment = Equipment::findOrFail($inputs['equipment_id']);      
        $user = auth()->user();
        if ($equipment != null && $user != null) {
            $reservation = EquipmentUser::retrieveLastUnvalidatedInteraction($user->id, $equipment->id, 'reservation');
            if ($reservation == null) {
                $equipment->users()->attach($user->id, [
                "type" => "reservation",
                "start" => $inputs["start"],
                "end" => $inputs["end"]]);
                return redirect()->route('/categories')->withOk('Thank you for you reservation, you may recieve a validation later.');
            } else {       
                $interaction = EquipmentUser::makeEmptyInteractionArray("reservation");     
                $interaction["start"] = $inputs["start"];
                $interaction["end"] = $inputs["end"];
                EquipmentUser::updateInteraction($reservation->id, $interaction);
                dd($interaction);
                
                return redirect('/categories')->withOk('Your reservation has been updated, you may recieve a validation later.');
            }
            return redirect('/catedories')->withOk('An error has occured.');
        }
    }

    public function getInteractions(){
        $user = auth()->user();
        $interactions = ["reservations" => [], "borrows" => []];
        //Garder que la dernière réservation
        $reservations = $user->reservations;
        if($reservations != null){
            $arr_reservations = $reservations->toArray();
            foreach($arr_reservations as $reservation){
                array_push($interactions["reservations"], $reservation);
            }
        }
        $borrows = $user->borrows;
        if($borrows != null){
            $arr_borrows = $reservations->toArray();
            foreach($arr_borrows as $reservation){
                array_push($interactions["borrows"], $reservation);
            }
        }
        //dd($interactions);
        return view('personal_interactions_view')->with('data', $interactions);
    }
}
