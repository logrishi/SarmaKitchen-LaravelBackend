<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSubscriptionStatus extends Model
{
  protected $guarded = [];

  public function getDeliveryDateAttribute($value)
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
}