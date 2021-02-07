<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Soft deletes
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{    
    // Soft deletes
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'amount', 'description', 'payment_id', 'bill_id', 'category', 'bill_batch'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    // Get object return for table field payment_id
    public function payment(){
        return $this->belongsTo('App\Payment');
    }

    // Get object return for table field payment_id
    public function bill(){
        return $this->belongsTo('App\Bill')->withTrashed();
    }

     
}
