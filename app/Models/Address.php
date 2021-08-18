<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
  protected $guarded = [];

  protected $casts = [
    // 'address_coords' => 'json',
    'address_coords' => 'array',
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function order()
  {
    return $this->hasOne('App\Models\Order');
  }
}