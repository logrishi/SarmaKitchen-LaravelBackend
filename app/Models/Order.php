<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $guarded = [];

  protected $casts = [
    'order_items' => 'array',
    'payment' => 'array',
  ];

  public function getCreatedAtAttribute($value)
  {
    if ($value) {
      $timestamp = strtotime($value);
      $modifiedDate = date("d-m-Y", $timestamp);
      return $modifiedDate;
    }
  }

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function orderItems()
  {
    return $this->hasMany('App\Models\OrderItem');
  }

  public function orderSubscriptionStatus()
  {
    return $this->hasMany('App\Models\OrderSubscriptionStatus');
  }

  public function address()
  {
    return $this->belongsTo('App\Models\Address');
  }
}