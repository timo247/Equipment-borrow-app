<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\EquipmentUser;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AcceptRerservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!Gate::allows('isAdmin')){
            return false;
        } else {
            return true;
        }
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

        // Check if Equipment is currently reservationed:
        $reservation = EquipmentUser::where([
            ['equipment_id', '=', $this->input('equipment_id')], ['type', '=', 'reservation'], ['start_validation', '!=', null], ['end_validation', '=', null]
        ])->get()->toArray();
        
        //If the equipment is reservationed, it cannot be reserved
        if (!empty($reservation)) {
                throw ValidationException::withMessages(['equipment_is_reservationed' => __('This equipment is currently being reservationed and can unfortunately
                 not be reservationed until it is delivered')]);
        } 

        return [
            //
        ];
    }
}
