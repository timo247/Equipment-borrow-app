<?php

namespace App\Http\Requests;

use App\Models\EquipmentUser;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class EndBorrowRequest extends FormRequest
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
         // Check if Equipment is currently borrowed:
            $borrow = EquipmentUser::where([
                ['id', '=', $this->input('borrow_id')]
            ])->get()->toArray();
            //If the equipment is borrowed, it cannot be reserved
            if (empty($borrow)) {
                    throw ValidationException::withMessages(['equipment_is_not_borrowed' => __('This equipment is currently not being borrowed.')]);
            } 
        return [
            "borrow_id" => "required|numeric"
        ];
    }
}
