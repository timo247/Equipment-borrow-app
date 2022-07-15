<?php

namespace App\Models;

use App\Models\Equipment;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'user_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($password) {
        $this->attributes['password'] = Hash::make($password);
    }

    public function equipments(){
        return $this->belongsToMany(Equipment::class)
        ->withPivot('type', 'start', 'end', 'start_validation', 'end_validation', 'start_validation_user_id', 'end_validation_user_id');
    }

    public function reservations(){
        return $this->belongsToMany(Equipment::class)
        ->withPivot('type', 'start', 'end', 'start_validation', 'end_validation', 'start_validation_user_id', 'end_validation_user_id')
        ->wherePivot('type', '=', 'reservation');
    }

    public function borrows(){
        return $this->belongsToMany(Equipment::class)
        ->withPivot('type', 'start', 'end', 'start_validation', 'end_validation', 'start_validation_user_id', 'end_validation_user_id')
        ->wherePivot('type', '=', 'borrow');
    }

    public function givenValidations(){
        return $this->belongsToMany(Equipment::class)
        ->withPivot('type', 'start', 'end', 'start_validation', 'end_validation', 'start_validation_user_id', 'end_validation_user_id')
        ->wherePivot('start_validation_user_id', '=', $this->id)
        ->orWherePivot('end_validation_user_id', '=', $this->id);
    }
}
