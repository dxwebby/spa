<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

// Model
use App\Payment;

use Carbon\Carbon;
class PaymentsController extends Controller
{
    public function payment($id){
        return $payments = Payment::find($id)->first();
    }

    public function store(Request $request){
                
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3'
        ]);

        if($validator->fails()){
            return response()->json($validator->invalid());
        }
        
        // Valid
        $data = new Payment();
        $payment = $data->create([
            "payment" => $request->title,
            "account" => $request->account,
            'created_at' => Carbon::now('America/Chicago')->toDateTimeString(),
        ]);    
            
        return $payment->id;
    }

    public function destroy($id){
        $payment = Payment::find($id);
        $payment->delete();
        return response()->json('success');
    }

 
}
