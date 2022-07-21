<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\EquipmentUser;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CancelRerservationRequest extends FormRequest
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
            ['id', '=', $this->input('id')], ['type', '=', 'reservation'], ["equipment_id", '=', $this->input('equipment_id')]
        ])->get()->toArray();
        
        //If the equipment is not reserved, it cannot be canceled
        if (empty($reservation)) {
                throw ValidationException::withMessages(['equipment_is_not_reservable' => __('This equipment is not reserved or its reservation is already accepted')]);
        } 

        return [
            'id' => "required|numeric",
            'equipment_id' => "required|numeric",
            'user_id' => "required|numeric",
        ];
    }
}
