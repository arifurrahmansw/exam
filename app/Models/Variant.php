<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $fillable = [
        'title', 'description'
    ];

    public function variant_child()
    {
        return $this->hasMany('App\Models\ProductVariant', 'id', 'variant_id');
    }

}
