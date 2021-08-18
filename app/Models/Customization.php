<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customization extends Model
{
  protected $guarded = [];

  protected $hidden = ['pivot'];

  protected $casts = [
    'options' => 'array',
  ];

  public function products()
  {
    return $this->belongsToMany('App\Models\Product');
  }
}