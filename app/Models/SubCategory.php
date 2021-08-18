<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $guarded = [];

    public function categories(){
        return $this->belongsToMany('App\Models\Category');
    }
    
    public function products(){
        return $this->belongsToMany('App\Models\Product');
    }
}