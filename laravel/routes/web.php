<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\EquipmentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\EquipmentUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::get('logout', [LoginController::class, 'logout']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', function () {
    return view('welcome');
});

Route::resource('equipments', EquipmentController::class);
Route::get('equipments/category/{category}', [EquipmentController::class, "index"])->name('equipments_category.index');
Route::get('categories', [EquipmentController::class, 'getCategories'])->name('categories');

Route::get('interactions', [EquipmentUserController::class, 'getInteractions']);
Route::post('reserve', [ReservationsController::class, 'store'])->name('reservation.store');
// Route::post('reserve', function(){
//     dd('ici');
//     return null;
// });

Route::post('reserve/accept', [ReservationsController::class, 'acceptReservation'])->name('reserve.accept');
Route::post('reserve/cancel', [ReservationsController::class, 'cancelreservation'])->name('reserve.cancel');
Route::post('borrow/', [EquipmentUserController::class, 'borrow'])->name('borrow.start');
Route::post('borrow/end', [EquipmentUserController::class, 'endBorrow'])->name('borrow.end');









Route::get('test', function () {
    dd(Reservation::getPossibleReservationTimeRanges(11));
    // $eq = Equipment::findOrFail(12);
    // return $eq->checkAvailability(11, Carbon::now(), Carbon::now()->addYear(2));
});
Route::get('test2', function () {
    return EquipmentUser::validateAllFinishedReservations();
});

// function checkIfDelivered($borrow){
//     if($borrow["pivot"]["end"] == null){
//         dump($borrow["pivot"]);
//         return false;
//     } else {
//         return true;
//     }
// }

