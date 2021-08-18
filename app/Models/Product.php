<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $guarded = [];

  protected $casts = [
    'meal_type' => 'array',
    'details' => 'array',
    'prices' => 'array',
  ];

  public function category()
  {
    return $this->belongsTo('App\Models\Product');
  }
}