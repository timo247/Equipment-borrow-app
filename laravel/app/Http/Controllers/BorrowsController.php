<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EquipmentUser;
use App\Http\Requests\BorrowRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\EndBorrowRequest;

class BorrowsController extends Controller
{
    public function borrow(BorrowRequest $request){
        $borrow = new EquipmentUser;
        $borrow->user_id = $request->input('user_id');
        $borrow->equipment_id = $request->input('equipment_id');
        $borrow->type = "borrow";
        $borrow->start = Carbon::now();
        $borrow->start_validation = Carbon::now();
        $borrow->start_validation_user_id = Auth::id();
        $borrow->save();
    }

    public function endBorrow(EndBorrowRequest $request){
        $borrow = EquipmentUser::where([
            ['type', '=', 'borrow'], ['id', '=', $request->input('borrow_id')]
            ])->first();

        $borrow->update([
            "end_validation" => Carbon::now(),
            "end_validation_user_id" => Auth::id()
        ]);    
    }
}
