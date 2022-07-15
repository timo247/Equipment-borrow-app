<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    public function reserve(Request $request){
        $user_id = auth()->user()->id;
        $equipment_id = $request->input('equipment_id');
        $available = Equipment::checkAvailabitly($equipment_id);
        
        dd($equipment_id);
        dd('reserve->');
    }

    public function acceptReservation(Request $request){
        dd('end-reserve->');
    }

    public function cancelReservation(Request $request){
        dd('cancel-reserve->');
    }
}
