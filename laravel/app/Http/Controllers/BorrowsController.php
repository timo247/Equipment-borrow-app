<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Models\EquipmentUser;
use App\Http\Requests\BorrowRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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

        return redirect('/equipments#equipment-'.$request->input('equipment_id'))->withOk('Your borrow has been registered');
    }

    public function endBorrow(EndBorrowRequest $request){
        $borrow = EquipmentUser::where([
            ['type', '=', 'borrow'], ['id', '=', $request->input('borrow_id')]
            ])->first();

        $borrow->update([
            "end_validation" => Carbon::now(),
            "end_validation_user_id" => Auth::id()
        ]);       
        return redirect('/equipments#equipment-'.$request->input('equipment_id'))->withOk('Your borrow has been ended.');
    }

    public function index(){
        if(!Auth::check()){
            return route("login");
        }

        if(Gate::allows('isAdmin')){
            $borrows = EquipmentUser::where([
                ["type", "=", "borrow"]
            ])->get()->toArray();
            $equipments = Equipment::select('id', 'name', 'image_url')->get()->toArray();
        } else {
            $borrows = EquipmentUser::where([
                ["type", "=", "borrow"], ["user_id", '=', Auth::id()]
            ])->get()->toArray();
            $equipments = Auth::user()->borrows;
        }
        $bor_to_return = [];
        foreach($borrows as $bor){
            $currently_running = false;
            $eq = Equipment::where("id", "=", $bor["equipment_id"])->select("image_url", "name")->first();
            if($bor["end_validation"]  == null){
                $currently_running = true;
            }
            $bor["currently_running"] = $currently_running;
            $bor["equ_img_url"] = $eq->image_url;
            $bor["equ_name"] = $eq->name;
            array_push($bor_to_return, $bor);
        }

        $data = [
            "borrows" => $bor_to_return,
            "equipments" => $equipments
        ];
        return view("borrows_view")->with("data", $data);
    }
}
