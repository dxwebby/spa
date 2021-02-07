<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Model
use App\Expense;
use App\User;
use App\Payment;
use App\Bill;
use App\History;

// Auth
use Auth;

// Carbon
use Carbon\Carbon;

use Faker\Factory as Faker;
class ExpensesController extends Controller
{    
    
    public function test(){

        /*
        $test = Expense::where('user_id', 1)->with('payment')->get()->take(6);
        dd($test);

        return;
        // Create fake values
        $faker = Faker::create();        
        $populator = new \Faker\Provider\Payment($faker);
        foreach (range(1,10) as $index) {            
            echo $populator->creditCardType() . "<br>";
        }
        */
    }


    /**
     * Get all expenses
     * REMEMBER:
     * This is API access, no sessions or auth are accessible.
     * I created $authenticate by accepting passed values from user when accessing this controller
     */    

    protected $authenticate;
    public function authenticate($id, $api_token){
        if(User::where('id', $id)
            ->where('api_token', '=', $api_token)->exists()){
        
            // Id exists
            return true;        
        }else{
            // Id or api_Token doesn't match
            return false;
        }   
    }

    public function batchById($id){
        if($id === "current"){            
            $records = Expense::where('user_id', Auth::user()->id)
                            ->where('bill_batch', $this->bill_batch)
                            ->with('bill')
                            ->get();            
        }else{
            $records = Expense::where('user_id', Auth::user()->id)
                        ->where('bill_batch', $id)
                        ->with('bill')
                        ->get();
        }
        
        return response()->json($records);
    }
    
    /**
     * Get current month expenses only
     */
    public function expenses($id, $api_token){     
       
        // Check user since Auth doesn't work in API environment           
        if(User::where('id', $id)
               ->where('api_token', '=', $api_token)               
               ->exists()){

            // Id exists
            /**
             * if payment_id === 0, it will be marked as Unlisted Expenses
             */
            
            // This data is for Dashboard only.
            $bill_batch = Carbon::now()->format('mY');            
            $expenses = Expense::orderBy('id', 'desc')
                            ->where('user_id', '=', $id)
                            ->where('bill_batch', $bill_batch)
                            //->whereYear('created_at', Carbon::now()->year)
                            //->whereMonth('created_at', Carbon::now()->month)
                            //->where('bill_id', '!=', 0)
                            ->with('bill')->get();                                                                    

            $total = Expense::where('user_id', '=', $id)
                            ->where('bill_batch', $bill_batch)
                            //->whereYear('created_at', Carbon::now()->year)
                            //->whereMonth('created_at', Carbon::now()->month)
                            ->get()->sum('amount');

            return response()->json([
                'expenses' => $expenses,                
                'total_expenses' => $total,             
            ]);
        }else{
            // Id or api_Token doesn't match
            return "Unauthorized access!";
        }   
    }

  
    public function destroy(Request $request, $id){        
        // Authenticate
        /*
        if($this->authenticate($id, $request->api_token)){
            $delete = Expense::where('user_id', $id)                         
                         ->where('id', $request->expenses_id);          
            if($delete->delete()){                
                return response()->json([
                    'success' => true,
                ]);
            }else{
                return response()->json([
                    'success' => false,
                ]);
            }
        }     
        */   
    }

}
