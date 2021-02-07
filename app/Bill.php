<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Soft deletes
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    // Soft deletes
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'bill', 'cost', 'notes', 'bill_batch'
    ];
    
    public function expenses(){        
        return $this->hasMany(Expense::class, 'bill_id');
        return $this->hasMany('App\Expense');
    }

    public function history(){
        return $this->belongsTo('App\History');
    }

    public function histories(){
        // With
        return $this->hasMany(Histories::class, 'bill_id');
        // return $this->hasMany('App\History');
    }
    
}
