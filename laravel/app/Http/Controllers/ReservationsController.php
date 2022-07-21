<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Equipment;
use App\Helpers\AppHelper;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\EquipmentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\AcceptRerservationRequest;
use App\Http\Requests\CancelRerservationRequest;
use App\Http\Requests\AcceptCancelRerservationRequest;

class ReservationsController extends Controller
{
    public function store(ReservationRequest $request)
    {
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

    public function acceptReservation(AcceptRerservationRequest $request)
    {
        $reservation = EquipmentUser::where([
            ['id', '=', $request->input('id')]
        ])->first();
        $reservation->update([
            "start_validation" => Carbon::now(),
            "start_validation_user_id" => Auth::id()
        ]);
        return redirect('/equipments')->withOk('The reservation'. $request->input('id').' is accepted.');
    }

    public function cancelReservation(CancelRerservationRequest $request)
    {
        $reservation = EquipmentUser::where([
            ['id', '=', $request->input('id')]
        ])->first();
        $reservation->update([
            "end_validation" => "0000-00-00",
            "end_validation_user_id" => Auth::id()
        ]);
        return redirect('/equipments')->withOk('The reservation'. $request->input('id').' is cancelled.');
    }

    public function index(){
        if(!Auth::check()){
            return route("login");
        }

        if(Gate::allows('isAdmin')){
            $reservations = EquipmentUser::where([
                ["type", "=", "reservation"]
            ])->get()->toArray();
            $equipments = Equipment::select('id', 'name', 'image_url')->get()->toArray();
        } else {
            $reservations = EquipmentUser::where([
                ["type", "=", "reservation"], ["user_id", '=', Auth::id()]
            ])->get()->toArray();
            $equipments = $user =Auth::user()->reservations;
        }
        $res_to_return = [];
        //dd($reservations);
        //recuperation of validation status, cancellation status and current effect and get the image url
        foreach($reservations as $res){
            $currently_running = false;
            $awaiting_validation = false;
            $cancelled = false;
            $eq = Equipment::where("id", "=", $res["equipment_id"])->select("image_url", "name")->first();

            if($res["end_validation"]  == null){
                $currently_running = true;
            }
            if($res["start_validation"] == null){
                $awaiting_validation = true;
            }
            if(strtotime($res["end_validation"]) == strtotime("0000-00-00")){
                $cancelled = true;
            }
            $res["currently_running"] = $currently_running;
            $res["awaiting_validation"] = $awaiting_validation;
            $res["cancelled"] = $cancelled;
            $res["equ_img_url"] = $eq->image_url;
            $res["equ_name"] = $eq->name;

            array_push($res_to_return, $res);
        }

        $data = [
            "reservations" => $res_to_return,
            "equipments" => $equipments
        ];

        return view("reservations_view")->with("data", $data);
    }
}
