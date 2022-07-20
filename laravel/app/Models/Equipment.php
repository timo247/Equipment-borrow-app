<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Borrow;
use App\Helpers\AppHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;
    protected $table = "equipments";
    protected $fillable = [
        'category',
        'name',
        'description',
        'image_url',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('type', 'start', 'end', 'start_validation', 'end_validation', 'start_validation_user_id', 'end_validation_user_id');
    }

    public function borrows()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('type', 'action', 'date', 'date_end', 'validated_at', 'user_validation_id')
            ->wherePivot('type', '=', 'borrow');
    }

    public function reservations()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('type', 'action', 'date', 'date_end', 'validated_at', 'user_validation_id')
            ->wherePivot('type', '=', 'reservation');
    }

    public function getCurrentReservation()
    {
        $reservation = EquipmentUser::where([
            ['type', '=', 'reservation'], ['end_validation', '=', null], ['equipment_id', '=', $this->id]
        ])->orderBy('start_confrimation', 'desc')->limit(1)->first();
        if (!empty($reservation)) {
            $reservation = $reservation->toArray();
            $reservation["username"] = User::findOrFail($reservation["user_id"])->username;
        }
        return ($reservation);
    }

    public function getCurrentBorrow()
    {
        $borrow = EquipmentUser::where([
            ['type', '=', 'borrow'], ['end_validation', '=', null], ['equipment_id', '=', $this->id]
        ])->orderBy('start_confrimation', 'desc')->limit(1)->first();
        if (!empty($borrow)) {
            $borrow = $borrow->toArray();
            $borrow["username"] = User::findOrFail($borrow["user_id"])->username;
        }
        return ($borrow);
    }

    public function checkAvailability($from, $to)
    {
        // dump("l'equipment", $this->id);
        $constraining_reservations = Reservation::equipmentReservationsCoveringTimeRange($this->id, $from, $to);
        // dump("constraining reservations:", $constraining_reservations);
        $constraining_delivered_borrows = Borrow::equipmentDeliveredBorrowsCoveringTimeRange($this->id, $from, $to);
        // dump("constraining  delivered borrows:", $constraining_delivered_borrows);
        $constraining_undelivered_borrows = Borrow::equipmentUndeliveredBorrowsUntilDate($this->id, $to);
        // dump("constraining undelivered borrows:", $constraining_undelivered_borrows);
        if (empty($constraining_reservations) && empty($constraining_delivered_borrows) && empty($constraining_undelivered_borrows)) {
            return true;
        } else {
            return false;
        }
    }

    public static function categories()
    {
        $categories = DB::Table('equipments')->distinct('category')->select('category')->get();
        return $categories;
    }

    public static function unreserved_ones($return_ids)
    {
        $reserved = EquipmentUser::where([
            ['type', '=', 'reservation'], ['end', '>=', Carbon::now()], ['end_validation', '=', null]
        ])->select('equipment_id')->distinct()->get()->toArray();
        $reserved = AppHelper::array2DSingleValuesTo1D($reserved, 'equipment_id');
        if ($return_ids) {
            $unreserved_equipments_ids = Equipment::whereNotIn('id', $reserved)->select('id')->get()->toArray();
            $unreserved_equipments_ids = AppHelper::array2DSingleValuesTo1D($unreserved_equipments_ids, 'id');
            return $unreserved_equipments_ids;
        } else {
            $unreserved_equipments = Equipment::whereNotIn('id', $reserved)->get();
            return $unreserved_equipments;
        }
    }

    public static function unborrowed_ones($return_ids = false)
    {
        $borrowed = EquipmentUser::where([
            ['type', '=', 'borrow'], ['start_validation', '!=', null], ['end_validation', '=', null]
        ])->select('equipment_id')->distinct()->get();
        $borrowed = AppHelper::array2DSingleValuesTo1D($borrowed, 'equipment_id');

        if ($return_ids) {
            $unborrowed_equipments_ids = Equipment::whereNotIn('id', $borrowed)->select('id')->get()->toArray();
            $unborrowed_equipments_ids =  AppHelper::array2DSingleValuesTo1D($unborrowed_equipments_ids, 'id');
            return $unborrowed_equipments_ids;
        } else {
            $unborrowed_equipments = Equipment::whereNotIn('id', $borrowed)->get();
            return $unborrowed_equipments;
        }
    }

    public static function available_ones($return_ids = false)
    {
        $all_ids = Equipment::select('id')->get()->toArray();
        if(!empty($all_ids)){
            $all_ids = AppHelper::array2DSingleValuesTo1D($all_ids, 'id');
        }
        $unborrowed = Equipment::unborrowed_ones(true);
        $available_ids = array_intersect($all_ids, $unborrowed);
        if ($return_ids) {
            $available_equipments = Equipment::whereIn('id', $available_ids)->select('id')->get()->toArray();
            $available_equipments = AppHelper::array2DSingleValuesTo1D($available_equipments, 'id');
        } else {
            $available_equipments = Equipment::whereIn('id', $available_ids)->get()->toArray();
        }
        return $available_equipments;
    }

    public static function unavailable_ones($return_ids = false)
    {
        $available_ones = Equipment::available_ones(true);
        $all_ids = Equipment::select('id')->get()->toArray();
        $all_ids =  AppHelper::array2DSingleValuesTo1D($all_ids, 'id');
        $unavailable_ones = array_diff($all_ids, $available_ones);
        if (!$return_ids) {
            $unavailable_ones = Equipment::whereIn('id', $unavailable_ones)->get()->toArray();
        }
        return ($unavailable_ones);
    }
}
