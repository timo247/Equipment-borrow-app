<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Models\Equipment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\EquipmentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReservationsController extends Controller
{
    public function store(Request $request){
        dump($request->input());
        $user = Auth::user();
        if(!Gate::allows('isAdmin')){
            if($user->id != $request->input('user_id')){
                abort(403, "User sent and current user do not match.");
            }
        }
        $equipment = Equipment::where('id', '=', $request->input('equipment_id'))->first();
        if($equipment == null){
            abort(403, "Equipment sent do not exist.");
        }
        $available = $equipment->checkAvailability($request->input('from'), $request->input('to'));
        dump($available);
        if($available){
            $reservation = new EquipmentUser();
            $reservation->user_id = $request->input('user_id');
            $reservation->equipment_id = $request->input('equipment_id');
            $reservation->type = 'reservation';
            $reservation->start = $request->input('from');
            $reservation->end = $request->input('to');
            dd($reservation);
            $reservation->save();
            // return redirect('/equipments')
            // ->withOk('Your reservation is confirmed, you may recieve an email with the details within the next minutes.');
        } else {
            abort(403, "The cannot be done because the equipment is unavailable");
        }
        dd($available);
    }

    public function acceptReservation(Request $request){
        dd('end-reserve->');
    }

    public function cancelReservation(Request $request){
        dd('cancel-reserve->');
    }
}
