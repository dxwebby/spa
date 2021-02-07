<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'user_id', 'bill', 'cost', 'notes'
    ];

    public function expenses(){     
        return $this->hasMany('App\Expense');
    }

    public function bill(){
        return $this->belongsTo('App\Bill');
    }
}
