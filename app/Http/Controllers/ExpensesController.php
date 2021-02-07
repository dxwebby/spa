<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
 
// Model
use App\Expense;
use App\Payment;
use App\Bill;
use App\History;

use Auth;
use DB;
use Carbon\Carbon;
 

class ExpensesController extends Controller
{
    public $bill_batch = "";
    public function __construct()
    {
        $this->bill_batch = Carbon::now()->format('mY');
        $this->middleware('auth');
    }

    /**
     * When user accessed POST page
     */
    public function redirectPost(){
        return redirect('/');
    }

    /**
     * Get batches by Id
     */
    
    public function batchById($id){
        if($id === "current"){            
            $records = Expense::where('user_id', Auth::user()->id)
                            ->where('bill_batch', $this->bill_batch)
                            ->with('bill')
                            ->get();            
        }elseif($id === "allexpenses"){
            $records = Expense::where('user_id', Auth::user()->id)                            
                            ->with('bill')
                            ->get();
                    
                            #->withCount(['expense as total_expenses' => function($query){
                            #    $query->where('user_id', Auth::user()->id)
                            #          ->select(DB::raw('sum(amount)'));
                            #}])->get();           
                             
        }else{
            $records = Expense::where('user_id', Auth::user()->id)
                        ->where('bill_batch', $id)
                        ->with('bill')
                        ->get();
        }

        $total = collect($records)->sum('amount');
        
        return response()->json([
            'records' => $records,
            'total' => $total,
        ]);
    }

    /**
     * Get history batches 
     */
    public function batches(){
        $batch = History::where('user_id', Auth::user()->id)
                        ->get();

        $fulldata = array();
        $batches = array();
        $data = array();        
        foreach($batch as $d){
            if(!in_array($d->bill_batch, $batches)){
                
                array_push($batches, $d->bill_batch);      

                $month = substr($d->bill_batch, 0, 2);
                $year = substr($d->bill_batch, 2);

                $textdate = Carbon::createFromDate($year, $month, 1);           
                $data = array(
                    'value' => $d->bill_batch,
                    'text' => $textdate->format('F Y'),
                );

                array_push($fulldata, $data);
            }            
        }
        return response()->json($fulldata);
    }


    /** Chart full expenses */
    public function expensesChartFull(){             
       // Get active/current bills
       $bills = Bill::where('user_id', Auth::user()->id)->get();

       // Batch for Current
       $label_current = array();
       $batch_current = Carbon::now()->format('mY');  
       $cost_current = array();
       
       $current = Bill::orderBy('id', 'asc')
           ->where('user_id', Auth::user()->id)        
           ->where('bill_batch', $batch_current)
           ->where('bill_type', 'Payment')
           ->withCount(['expenses as total_expenses' => function($query) {                            
                   $query->where('bill_batch', $this->bill_batch)
                       ->select(DB::raw('sum(amount)'));
           }])->get();                    
               
       foreach($current as $key=>$value){            
           array_push($cost_current, $value->total_expenses);
           array_push($label_current, $value->bill);
       }

       // Batches for History
       $batch1 = Carbon::now()->subMonth()->format('mY');
       $batch2 = Carbon::now()->subMonth(2)->format('mY');
       $batch3 = Carbon::now()->subMonth(3)->format('mY');
      
       $labels = array();  
       $cost_1 = array();              
       $cost_2 = array();
       $cost_3 = array();
       $costs = array();

       $i = 0;
       foreach($bills as $data){
           $id = $data->id;
          
           $history = History::orderBy('bill_id', 'asc')
                   ->where('bill_id', $id)
                   ->where("user_id", Auth::user()->id)          
                   ->where("bill_type", "Payment")            
                   ->whereIn('bill_batch', [$batch1, $batch2, $batch3])                              
                   ->get();
           
           foreach($history as $d){                      
               $label = trim(strtolower($d->bill));           

               if(!in_array($label, $labels)){                    
                   array_push($labels, $label);                                   
               }

               if($d->bill_batch == $batch1){
                    array_push($cost_1, $d->cost);                         
                }
                               
               if($d->bill_batch == $batch2){
                   array_push($cost_2, $d->cost);                         
               }
               if($d->bill_batch == $batch3){
                   array_push($cost_3, $d->cost);                         
               }                
           }          
       }

       $updated_cost = array();
       foreach($cost_current as $d){
            if($d != "" || $d != null){
                array_push($updated_cost, $d);
            }else{
                array_push($updated_cost, 0);
            }
       }
       return response()->json([
           'label_current' => $label_current,
           'cost_current' => $updated_cost,
           'labels' =>  $labels = array_map('ucwords', $labels),
           'cost_1' => $cost_1,
           'cost_2' => $cost_2,
           'cost_3' => $cost_3,
       ]);     
    }

    /**
     * Charts for expenses
     */

    public function getsum($id, $month){
        $bill_batch = $month . Carbon::now()->format('Y');
        return Expense::where('user_id', Auth::user()->id)
                      //->whereYear('created_at', Carbon::now()->year)
                      //->whereMonth('created_at', $month)
                      ->where('bill_batch', $bill_batch)
                      ->where('bill_id', $id)
                      ->get()->sum('amount');  
    }

    public function chart(){        
        /*
        return $this->appointments()
                        ->whereIn('status_id', [3,4])
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->count();
        */
        // Get curren total bills
        $total_bills = Bill::where('user_id', Auth::user()->id)
                    ->whereIn('bill_type', ['Bill', 'Other'])
                    ->get()->sum('cost');

        // Get this month's total expenses
        $current_month = Carbon::now()->format('m');
        $previous_month = Carbon::now()->subMonth()->format('m');
                
        // Labels           
        //$data = Payment::orderBy('payment', 'asc')->get(); 
        $data = Bill::orderBy('bill', 'asc')
                    ->where("user_id", Auth::user()->id)
                    ->where("bill_type", "=", "Payment")
                    ->get();
        
        $labels = array();
        $sum_current = array();
        $sum_previous = array();        
        
        foreach($data as $val){         
            array_push($labels, $val->bill);
            
            // Initiate charting
            // Current month expenses
            $compute_current = (float)$this->getsum($val->id, $current_month);
            array_push($sum_current, number_format($compute_current, 2, '.', ''));

            // Previous month expenses
            $compute_previous = (float)$this->getsum($val->id, $previous_month);
            array_push($sum_previous, number_format($compute_previous, 2, '.', ''));
        }        
    
        return response()->json([
            'labels' => $labels,
            'sum_current' => $sum_current,
            'sum_previous' => $sum_previous,
            'total_bills' => $total_bills,            
            //'total' => '',
        ]);                          
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  
       # return $request->all();
       // Validate
       $validator = Validator::make($request->all(), [            
            'cost' => "required|regex:/^\d+(?:\.\d+)?$/|max:12",
        ]);
        
        if($validator->fails()){
            return response()->json($validator->invalid());
        }

        /*  Saving record using save()
            $d = new Expense();
            $d->user_id = Auth::user()->id;
            $d->amount = $request->cost;
            $d->payment_id = $request->payment;
            $d->description = $request->note;
            $d->created_at = Carbon::now('America/Chicago')->toDateTimeString();
            $d->save();
        */

        $note = $request->note;
        if(empty(str_replace(" ", "",$request->note))){
            $note = "...";
        }

        $expenses = new Expense();
                
        $data = $expenses->create([            
            'user_id' => Auth::user()->id,
            'amount' => $request->cost,            
            'bill_id' => $request->bill_id,
            'bill_batch' => Carbon::now()->format('mY'),
            'category' => $request->desc,
            'description' => $note,
            'created_at' => Carbon::now('America/Chicago')->toDateTimeString(),
        ]);

        /*
            Since we're pushing data from the client-side, there's no need to
            query the database except for the payment id name

            $newdata = Expense::where('user_id', Auth::user()->id)
                          ->orderBy('id', 'desc')
                          ->with('payment')->get();
                                  
            $filtered = Expense::where('payment_id', '=', $request->payment)
                                    ->where('user_id', Auth::user()->id)                                   
                                    ->with('payment')->get();  
            
            $total = collect($expenses->sum('amount'));  
        */
      
        // $payment = Payment::where('id',$request->payment)->first('payment');
        return response()->json([
            "success" => true,            
            "data" => $data,
            "filteredPayment" => $data->bill->bill,            
            //"newdata" => $newdata,
            //"filtered" => $filtered,
            //"total" => $total,
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
        //lastpoint                
        // return $request->all();
        /////////////////////////////////////
        // UPDATE PAYMENT
        if($request->change_payment){         
            $expenses = Expense::where('user_id', Auth::user()->id)
                                ->where('id', $id)                                
                                ->first();            
            $expenses->bill_id = $request->new_bill_id;
            $expenses->update();

            //$total_sum = Expense::where('user_id')
                                
            return response()->json([
                'success' => true,                
            ]);
        }
        
        /////////////////////////////////////
        // UPDATE EXPENSES        
        // Validate
        $validator = Validator::make($request->all(), [            
            'amount' => "required|regex:/^\d+(?:\.\d+)?$/|max:12",
        ]);

        if($validator->fails()){
            return response()->json($validator->invalid());
        }
                
        $note = empty(str_replace(' ', '', $request->note)) ? "..." : $request->note;        
        $expenses = Expense::where('id', '=', $id)
                        ->where('user_id', Auth::user()->id)
                        ->first();        
        $expenses->amount = $request->amount;
        $expenses->category = $request->desc;
        $expenses->description = $note;   
        $expenses->created_at = $request->created;         
        $expenses->update();        
        
        $total_sum = Expense::where('user_id', Auth::user()->id)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->get()->sum('amount');

        return response()->json([
            'success' => true,
            'total_sum' => $total_sum,
        ]);        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {                        
                
        $expenses = Expense::find($id);
        $expenses->delete();      

        return response()->json([
            "success" => true,
        ]);
    }

    /**
     * Update expenses chosen payment
     */
    public function updatePayment(Request $request, $id){
        
        $payment = Expense::where('user_id', Auth::user()->id)
                        ->where('id', $id)
                        ->first();
        $payment->payment_id = $request->new_payment;
        $payment->update();
        
        $payments = Payment::orderBy('payment', 'asc')->get();
        return response()->json($payments);
    }
}
