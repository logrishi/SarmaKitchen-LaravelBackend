<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $guarded = [];

  protected $casts = [
    'meal_type' => 'array',
    'dish_type' => 'array',
    'subscriptions' => 'array',
  ];

  public function products()
  {
    return $this->hasMany('App\Models\Product');
  }
}