<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            "user_id" => 'required|numeric',
            "equipment_id" => 'required|numeric',
            "from" => 'required|date|after_or_equal:today',
            "to" => 'required|date|after:from|before:'.date('Y-m-d', strtotime("+1 year")),
        ];
    }
}
