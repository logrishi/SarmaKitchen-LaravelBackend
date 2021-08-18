<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
  public function isAdmin()
  {
    return $this->is_admin; // AuthAdmin middleware
  }
  public function isCourier()
  {
    return $this->is_courier; // AuhCourier middleware
  }

  public function orders()
  {
    return $this->hasMany('App\Models\Order');
  }

  public function address()
  {
    return $this->hasMany('App\Models\Address');
  }

  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  //   protected $fillable = ['name', 'phone_no', 'email', 'password'];

  protected $guarded = [];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = ['password', 'remember_token'];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'address' => 'array',
  ];

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  public function getJWTCustomClaims()
  {
    return [];
  }
}