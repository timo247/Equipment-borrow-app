<?php

use App\Models\User;
use App\Models\Equipment;
use App\Models\EquipmentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
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
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', function () {
    return view('welcome');
});

Route::resource('equipments', EquipmentController::class);
Route::get('equipments/category/{category}', [EquipmentController::class, "index"])->name('equipments_category.index');
Route::get('categories', [EquipmentController::class, 'getCategories'])->name('categories');
Route::get('interactions', [EquipmentUserController::class, 'getInteractions']);
Route::post('reserve', [EquipmentUserController::class, 'reserve']);
Route::get('reserve', function(){
return view('add_reservations_view');
});







Route::get('test', function () {
    return Equipment::findOrFail(11)->getCurrentBorrow();
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

