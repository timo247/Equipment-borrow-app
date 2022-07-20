<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Equipment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\EquipmentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ReservationRequest;

class ReservationsController extends Controller
{
    public function store(ReservationRequest $request)
    {
        dump($request->input());
        $user = Auth::user();
        //Only admins can make reservations for other users
        if (!Gate::allows('isAdmin')) {
            if ($user->id != $request->input('user_id')) {
                abort(403, "User sent and current user do not match.");
            }
        }
        $equipment = Equipment::where('id', '=', $request->input('equipment_id'))->first();
        if ($equipment == null) {
            abort(403, "Equipment sent do not exist.");
        }
        $reservation = new EquipmentUser();
        $reservation->user_id = $request->input('user_id');
        $reservation->equipment_id = $request->input('equipment_id');
        $reservation->type = 'reservation';
        $reservation->start = $request->input('from');
        $reservation->end = $request->input('to');
        $reservation->save();

        return redirect('/equipments')->withOk('Your reservation has been sent, please wait untill an admin confirms it to come get
        your equipment.');
    }

    public function acceptReservation(ReservationRequest $request)
    {
        $reservation = EquipmentUser::where([
            'id', '=', $request->input('reservation_id')
            ])->first();
        $reservation->update([
            "start_validation" => Carbon::now(),
            "start_validation_user_id" => Auth::id()
        ]);
    }

    public function cancelReservation(Request $request)
    {
        dd('cancel-reserve->');
    }
}
