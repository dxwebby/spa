<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Validator
use Illuminate\Support\Facades\Validator;

// Model
use App\Bill;
use App\Expense;
use App\Payment;
use App\History;

// Carbon
use Carbon\Carbon;

// Auth
use Auth;
use DB;

class BillsController extends Controller
{
    public $bill_batch = "";

    public function __construct()
    {
        $this->middleware('auth');
        $this->bill_batch = Carbon::now()->format("mY");
    }


    /**
     * Chart
     */

    public function getsum($batch){
        return History::orderBy('bill', 'asc')
                ->where("bill_batch", $bill_batch)
                ->where("user_id", Auth::user()->id)
                ->where("bill_type", "=", "Bill")                    
                ->orWhere("bill_type", "=", "Other")                                        
                ->get();
    }
    

    /**
     * Full bills chart
     */
    public function billsChartFull(){
        // Get active/current bills
        $bills = Bill::where('user_id', Auth::user()->id)->get();

        // Batch for Current        
        $batch1 = Carbon::now()->format('mY');        
        $cost_1 = array();
        $current = Bill::orderBy('id', 'asc')
            ->where('user_id', Auth::user()->id)        
            ->where('bill_batch', $batch1)
            ->whereIn("bill_type", ["Bill", "Other"])            
            ->get();                    
        
        foreach($current as $key=>$value){            
            array_push($cost_1, $value->cost);
        }

        // Batches for History
        $batch2 = Carbon::now()->subMonth()->format('mY');
        $batch3 = Carbon::now()->subMonth(2)->format('mY');
       
        $labels = array();                
        $cost_2 = array();
        $cost_3 = array();
        $costs = array();

        $i = 0;
        foreach($bills as $data){
            $id = $data->id;
           
            $history = History::orderBy('bill_id', 'asc')
                    ->where('bill_id', $id)
                    ->where("user_id", Auth::user()->id)          
                    ->whereIn('bill_batch', [$batch1, $batch2, $batch3])          
                    ->whereIn("bill_type", ["Bill", "Other"])            
                    ->get();
            
            foreach($history as $d){                                
                if(!in_array($d->bill, $labels)){
                    array_push($labels, $d->bill);                                   
                }
                             
                if($d->bill_batch == $batch2){
                    array_push($cost_2, $d->cost);                         
                }
                if($d->bill_batch == $batch3){
                    array_push($cost_3, $d->cost);                         
                }
            }          
        }
         
        return response()->json([
            'labels' => $labels,            
            'cost_1' => $cost_1,
            'cost_2' => $cost_2,
            'cost_3' => $cost_3,
        ]);     
    }

    /**
     * Range 2 months billsChart
     */
    public function billsChart(){
        // Get active/current bills
        $bills = Bill::where('user_id', Auth::user()->id)->get();
    
        $batch1 = Carbon::now()->subMonth()->format('mY');
        $batch2 = Carbon::now()->subMonth(2)->format('mY');

        $labels = array();
        $values = array();
        
        $cost_1 = array();
        $cost_2 = array();
        $i=0;
        foreach($bills as $data){
            $id = $data->id;
           
            $history = History::orderBy('bill', 'asc')
                    ->where('bill_id', $id)
                    ->where("user_id", Auth::user()->id)
                    ->whereIn("bill_batch", [$batch1, $batch2])                
                    ->whereIn("bill_type", ["Bill", "Other"])            
                    ->get();
            
            foreach($history as $d){                
                if(!in_array($d->bill, $labels)){
                    array_push($labels, $d->bill);                                   
                }
                
                $i++;
                if($i % 2 == 0){ 
                    array_push($cost_1, $d->cost);            
                }else{
                    array_push($cost_2, $d->cost);            
                }  
            }          
        }
      
        return response()->json([
            'labels' => $labels,            
            'cost_1' => $cost_1,            
            'cost_2' => $cost_2,                      
        ]);     
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /**
         * Return total sum of relational column.
         * E.g. 
         * Bill table hasMany on Expenses table.
         * 
         * |    Bill Table      |       Expenses Table
         * |    id              |       bill_id
         * |                    |       amount
         * 
         * To ouput on foreach loop
         *     echo $d->expenses->sum('amount') . "<br><hr>";
         */

        /* LARAVEL VERSION OUTPUT since output ->sum('amount') can't be displayed on vue
        $bills = Bill::where('user_id', Auth::user()->id)                    
                    ->withCount('expenses')  
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)                    
                    ->with([
                        'expenses' => function($query){
                            $query->sum('amount');
                        }                   
                    ])                        
                    ->get();
        */

        // LARAVEL AND VUE JS OUTPUT                
        return response()->json($this->paymentOrder());
        $bills = Bill::where('user_id', Auth::user()->id)
                    //->whereYear('created_at', Carbon::now()->year)
                    //->whereMonth('created_at', Carbon::now()->month)                                        
                    ->where('bill_batch', $this->bill_batch)
                    ->withCount(['expenses as total_expenses' => function($query) {                            
                            $query->where('bill_batch', $this->bill_batch)
                                ->select(DB::raw('sum(amount)'));
                    }])->get();
        
        return response()->json($bills);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Show current Payment Order
     */
    public function paymentOrder(){
        return Bill::where('user_id', Auth::user()->id)
                    ->where('bill_batch', $this->bill_batch)
                    ->withCount(['expenses as total_expenses' => function($query) {                            
                        $query->where('bill_batch', $this->bill_batch)
                        ->select(DB::raw('sum(amount)'));
                    }])->get(); 
    }

    /**
     * Mark bill as paid
     */
    public function markaspaid(Request $request, $id){
        // return $request->all();
        
        $bill = Bill::find($id);
        $bill->payment_status = 1;        
        $bill->paid_date = $request->bill_date;        
        $bill->cost = $request->bill_cost;
        $bill->notes = $request->bill_notes;
        $bill->update();

        return response()->json([
            'success' => true,
            'bills' => $this->paymentOrder(),
        ]);
        
    }

    /**
     * Unmark bill
     */
    public function unmarkaspaid($id){
        
        $bill = Bill::find($id);
        $bill->notes = '';
        $bill->payment_status = 0;
        $bill->cost = 0;
        $bill->paid_date = null;
        $bill->update();
        
        return response()->json([
            'success' => true,
            'bills' => $this->paymentOrder(),            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // return $request->all;
        $validator = Validator::make($request->all(), [
            'bill' => 'required|min:3|unique:bills',
            'payment_order' => 'required'
        ]);
            
        if($validator->fails()){
            return response()->json($validator->invalid());
        }

        // Add new bill
        $bill = new Bill();
        $bill->user_id = Auth::user()->id;
        $bill->bill = ucwords($request->bill);
        $bill->bill_type = $request->bill_type;
        $bill->bill_batch = $this->bill_batch;
        $bill->payment_order = $request->payment_order;
        $bill->payment_date = $request->due_date;
        $bill->cost = $request->cost;
        $bill->payment_status = 0;
        $bill->notes = $request->notes;
        $bill->created_at = Carbon::now('America/Chicago')->toDateTimeString();                
        $bill->updated_at = null;
        $bill->save();

        $bills = Bill::orderBy('payment_order', 'asc')
                    ->where('user_id', '=', Auth::user()->id)
                    //->whereYear('created_at', Carbon::now()->year)
                    //->whereMonth('created_at', Carbon::now()->month)                    
                    ->where('bill_batch', $this->bill_batch)
                    ->get();
        // Return last id
        return response()->json([
            'success' => true,
            'id' => $bill->id,
            'bills' => $bills,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {           
        // Get original bill title
        $bill = Bill::find($request->id);
        $original_title = $bill->bill;
        $original_type = $bill->bill_type;
        $bill_id = $bill->id;
          
        // If original bill and bill are not the same, check for validation              
        if(strtolower(str_replace(' ', '', $request->bill)) != strtolower(str_replace(' ', '', $original_title))){                
            $validator = Validator::make($request->all(), [
                'bill' => 'required|min:3|unique:bills',
                'payment_order' => 'required'
            ]);   
            
            if($validator->fails()){
                return response()->json($validator->invalid());
            }  
        }        
                
        // If $original_type is equal to "Payment"
        if($original_type == "Payment"){
            // If original_type is no longer the same or not equal to $request->bill_id,    
            // Since, this Bill was used as a Payment, soft delete all items related to this bill in the Expenses model/table.
            if($original_type !== $request->bill_type){                
                $expenses = Expense::where('user_id', '=', Auth::user()->id)  
                                ->whereIn('bill_id', explode(',', $id))->delete();
            }            
        }else{
            // If $original_type are "Others" and "Bill", check if $request->bill_id is "Payment"
            // If bill type was changed to "Payment", then, undelete all expenses related to this item.
            if($request->bill_type === "Payment"){                
                Expense::where('user_id', '=', Auth::user()->id)
                        ->where('bill_id', $id)
                        ->withTrashed()
                        ->restore();
            }
        }
                       
        // Get original payment type
        $orig_bill_type = $bill->bill_type;
        
        // Proceed to update
        $bill->bill = ucwords($request->bill);
        $bill->payment_order = $request->payment_order;
        $bill->payment_date = $request->due_date;
        $bill->bill_type = $request->bill_type;
        $bill->cost = $request->cost;
        $bill->notes = $request->notes;
        //$bill->paid_date = null;
        $bill->update();              
            
        // Send updated bill list from server
        $bills = Bill::orderBy('payment_order', 'asc')
                    ->where('user_id', '=', Auth::user()->id)
                    //->whereYear('created_at', Carbon::now()->year)
                    //->whereMonth('created_at', Carbon::now()->month)                    
                    ->where('bill_batch', $this->bill_batch)
                    ->get();

        return response()->json([
            'success' => true,
            'bills' => $bills,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        
        // Items will be soft deleted
        $bill = Bill::find($id);             
        $bill->delete();

        // Multiple delete
        $expenses = Expense::where('user_id', '=', Auth::user()->id)
                            ->whereIn('bill_id', explode(',', $id))->delete();
                                    
        return response()->json([
            "success" => true,
        ]);
    }
}
