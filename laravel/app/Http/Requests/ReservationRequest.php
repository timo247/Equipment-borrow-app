<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\EquipmentUser;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

       //Check if user exists
       $user = User::where('id', '=', $this->input('user_id'))->get()->toArray();
       if(empty($user)){
           throw ValidationException::withMessages(['user_id' => __('The user sent does not exist.')]);
       }

        $possible_reservations_timeRanges = Reservation::possibleReservationTimeRanges($this->input('equipment_id'));
        dump($possible_reservations_timeRanges);
        for ($i = 0; $i < count($possible_reservations_timeRanges); $i++) {
            if ($this->input("to") <= $possible_reservations_timeRanges[$i]["end"]) {
                return [
                    "user_id" => 'required|numeric',
                    "equipment_id" => 'required|numeric',
                    "from" => 'required|date|after_or_equal:' . $possible_reservations_timeRanges[$i]["start"],
                    "to" => 'required|date|after:from|before_or_equal:' . $possible_reservations_timeRanges[$i]["end"],
                ];
            }
            //If none of the cases is taken, basic rule apply
            if ($i == count($possible_reservations_timeRanges) - 1) {
                return [
                    "user_id" => 'required|numeric',
                    "equipment_id" => 'required|numeric',
                    "from" => 'required|date|after_or_equal:' . $possible_reservations_timeRanges[$i]["start"],
                    "to" => 'required|date|after:from|before_or_equal:' . $possible_reservations_timeRanges[$i]["end"],
                ];
            }
        }
    }
}
