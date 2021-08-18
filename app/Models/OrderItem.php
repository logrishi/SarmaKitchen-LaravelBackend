<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
  protected $guarded = [];

  protected $casts = [
    'subscription_menu' => 'array',
  ];

  public function getStartDateAttribute($value)
  {
    if ($value) {
      $timestamp = strtotime($value);
      $modifiedDate = date("d-m-Y", $timestamp);
      return $modifiedDate;
    }
  }

  public function getEndDateAttribute($value)
  {
    if ($value) {
      $timestamp = strtotime($value);
      $modifiedDate = date("d-m-Y", $timestamp);
      return $modifiedDate;
    }
  }

  public function order()
  {
    return $this->belongsTo('App\Models\Order');
  }

  public function product()
  {
    return $this->belongsTo('App\Models\Product');
  }
}