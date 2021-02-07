<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payment', 'account'
    ];
    
    public function expenses(){
        return $this->hasMany('App\Expense');
    }
}
