<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Expense;
use App\History;
use App\Bill;

use Auth;
use Carbon\Carbon;

class UnlistedController extends Controller
{    
    public function unlistedExpenses(){ 
        $expenses = Expense::where('user_id', Auth::user()->id)
                            ->with('bill')
                            ->onlyTrashed()->get();
        
        $unlisted = array();                        
        foreach($expenses as $d){   
            if(!isset($d->bill->bill)){
                array_push($unlisted, 'Unknown' . '@' . '0');                               
            }else{                
                array_push($unlisted, $d->bill->bill . '@' . $d->bill->id);                
            }       
        }

        $data = array_count_values($unlisted);        

        $fulldata = array();
        $index=1;
        foreach($data as $key => $value){
            $x = explode("@", $key);    
            $fulldata[] = array(
                "id" => $x[1],
                "index" => $index++,
                "bill" => $x[0],
                "total" => $value,                
            );            
        }
        
        return response()->json([            
            "unlisted" => $fulldata,
        ]);
    }

    public function billHistory($id){        
        $history = History::where("user_id", Auth::user()->id)
                        ->where("bill_id", "=", $id)        
                        ->get();
        return $history;
    }

    public function billId(Request $request, $id){                
        $expenses = Expense::orderBy('deleted_at', 'desc')
                            ->where("user_id", auth::user()->id)            
                            ->where('bill_id', $id)
                            ->with('bill')                                            
                            ->onlyTrashed()->get()->take(10);

        return response()->json([
            'expenses' => $expenses,
            'history' => $this->billHistory($id),
        ]);
    }

    public function index(){
        $expenses = Expense::orderBy('deleted_at', 'desc')
                            ->where("user_id", auth::user()->id)            
                            ->with('bill')                                            
                            ->onlyTrashed()->get();                        

        $bills = Bill::orderBy('bill', 'asc')
                        ->where('user_id', auth::user()->id)
                        ->where("bill_type", "Payment")
                        ->onlyTrashed()->get();

        if(count($bills) > 0){            
            return response()->json([
                "expenses" => $expenses,
                "bills" => $bills,                
            ]);                                    
        }else{           
            return response()->json(false);
        }
        
    }

    /**
     * Restore Bills
     */   
    public function update(Request $request){                 
        if($request->isMethod('patch')){
            // PATCH method
            $bill = Bill::onlyTrashed()
                    ->where('id', $request->id)                                        
                    ->get();  
        
            if(count($bill) > 0){
                $bill = Bill::onlyTrashed()
                    ->where('id', $request->id)                    
                    ->restore();                

                $expense = Expense::onlyTrashed()
                    ->where('bill_id', $request->id)
                    ->restore();
                
                return $this->index();
            }     
        }else{
            // PUT Method
            //return $request->all();
            $expenses = Expense::onlyTrashed()
                            ->where('user_id', Auth::user()->id)
                            ->where('bill_id', '=', $request->id)
                            ->restore();

            return $expenses = Expense::where('user_id', Auth::user()->id)
                                ->where('bill_id', '=', $request->id)
                                ->update([
                                    'bill_id' => $request->assign_to
                                ]);                            
        }
                   
    }

    /**
     * Delete Bills
     */
    public function destroy($id){        
        Expense::onlyTrashed()->where('bill_id', $id)->forceDelete();
        Bill::onlyTrashed()->findOrFail($id)->forceDelete();
        return $this->index();
    }

    /**
     * Delete unlisted expenses
     */
    //Route::delete('/unlistedexpenses/delete/{id}', 'UnlistedController@deleteExpenses');
    public function deleteExpenses($id){
        $expenses = Expense::onlyTrashed()
                        ->where('user_id', Auth::user()->id)
                        ->where('bill_id', '=', $id)
                        ->forceDelete();
        return response()->json(true);
    }
}
