<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Model
use App\Payment;
use App\Expense;
use App\Bill;
use App\History;

use Auth;
use DB;

// Charts
use App\Charts\PaymentsChart;
use App\Charts\ExpensesChart;

use Carbon\Carbon;

class HomeController extends Controller
{

    public $bill_batch = "";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->bill_batch = Carbon::now()->format('mY');
    }


    /**
     * Setup dashboard
     */
    public function activeDashboard($id){                   
        return $bills = Bill::where('user_id', '=',$id)
            ->where('bill_batch', Carbon::now()->format('mY'))
            ->get();  
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {        

        /*
        $chart = new ExpensesChart;
        $chart->labels(['Capital One', 'American Express', 'Walmart']);
        $chart->loader(true);                
        $chart->loaderColor('#e2342f');
        $chart->options([            
            'tooltip' => [
                'show' => true // or false, depending on what you want.
            ],
        ]);

        $co = Expense::where('user_id', '=', Auth::user()->id)     
                          ->where('payment_id', 1)
                          ->get()->sum('amount');   
                          

        $ae = Expense::where('user_id', '=', Auth::user()->id)     
                     ->where('payment_id', 3)
                     ->get()->sum('amount');                             

        $wl = Expense::where('user_id', '=', Auth::user()->id)     
                     ->where('payment_id', 6)
                     ->get()->sum('amount');                        
                
        $chart->displayLegend(true);                     
        $chart->barWidth(0.2);
        
        
        $chart->title('Monthly Expenses', $font_size = 14, $color = 'red',$bold = true, $font_family = "'Open Sans', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif");        
                
        $chart->dataset('Total $', 'horizontalBar', [200, 500, 100])->color('rgba(0,0,0, .1)')->options([
            'color'=> '#d33l4',
            'backgroundColor' => 'pink',
            'hoverBackgroundColor' => 'red',
            'pointBorderColor' => 'green',
            
            
        ]);

        return view('pages.home', ['chart' => $chart]);  
        */

         // check activeDashboard
         if(count($this->activeDashboard(Auth::user()->id)) === 0){              
             
            // If not found, copy all items from Bills to History  
            $bills = Bill::where('user_id', Auth::user()->id)->get();
            
            if(!History::where('user_id',Auth::user()->id)
                ->where('bill_batch', Carbon::now()->format('mY'))
                ->exists()){                    

                foreach($bills as $i => $data){                    
                    //echo $data->bill . "<br>";
                    //echo $data->user_id. "<br>";
                    
                    #$history[$i] = (new History())->forceCreate($data->only(['user_id', 'bill', 'id','bill_type', 'bill_batch', 'payment_order', 'payment_date', 'cost', 'notes', 
                     #               'payment_status', 'created_at', 'updated_at', 'paid_date', 'deleted_at']));

                    
                    $history[$i] = new History();
                    $history[$i]->user_id = Auth::user()->id;
                    $history[$i]->bill = $data->bill;
                    $history[$i]->bill_id = $data->id;
                    $history[$i]->bill_type = $data->bill_type;
                    $history[$i]->bill_batch = $data->bill_batch;
                    $history[$i]->payment_order = $data->payment_order;
                    $history[$i]->payment_date = $data->payment_date;
                    $history[$i]->cost = $data->cost;
                    $history[$i]->notes = $data->notes;
                    $history[$i]->payment_status = $data->payment_status;
                    $history[$i]->created_at = $data->created_at;
                    $history[$i]->updated_at = $data->updated_at;
                    $history[$i]->paid_date = $data->paid_date;
                    $history[$i]->deleted_at = $data->deleted_at;
                    $history[$i]->save();
                    
                }

                //return;
                // Then update bills to a new set                        
                $previous_month = Carbon::now()->subMonth()->format('mY');
                
                Bill::where('user_id',Auth::user()->id)                                                               
                    ->where('bill_batch', $previous_month)                                
                    ->update([
                        'bill_batch' => Carbon::now()->format('mY'),
                        'notes' => '',
                        'paid_date' => null,
                        'payment_status' => 0,
                        'cost'=> 0,
                    ]);                                                           
            }                    
        }         

        return view('pages.home');
    }

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

    /**
     * Show list of tenders/payments
     */
    public function payments($value){        
        //static $bill_batch = Carbon::now()->format('mY');

        // Data used for Filtering Records
        if($value === "alldata" || $value === "all"){            
            $expenses = Expense::orderBy('id', 'desc')
                                ->where('user_id', Auth::user()->id)
                                ->where('bill_batch', $this->bill_batch)
                                //->whereYear('created_at', Carbon::now()->year)
                                //->whereMonth('created_at', Carbon::now()->month)
                                ->where('bill_id', '!=', 0)
                                ->with('bill')->get();  
                                
            $total = collect($expenses->sum('amount'));            
            return response()->json([
                "expenses" => $expenses,
                "total" => $total,
            ]); 
        }
        elseif($value === 'getalldata'){ 
            
            /*
            $bill = Bill::orderBy('bill', 'asc')
                        ->where('user_id', Auth::user()->id)
                        ->where('bill_type', 'Payment')
                        ->get();
            return response()->json($bill);                            
            */
            $bills = Bill::orderBy('bill', 'asc')
                        ->where('user_id', Auth::user()->id)
                        ->where('bill_type', 'Payment')
                        ->get();

            return response()->json([
                'payments' => $bills,                
            ]);    
                                    
            return response()->json($bills);

            $payments = Payment::orderBy('payment', 'asc')
                                ->where('user_id', Auth::user()->id)
                                ->get();

           
        }else{                        
            // Filter records
            
            #$payment = Payment::where('id', '=', $value)->get();                        
            #return response()->json($payment);
            
            $expenses = Expense::orderBy('id', 'desc')
                                ->where('bill_id', '=', $value)
                                ->where('bill_batch', $this->bill_batch)
                                //->whereYear('created_at', Carbon::now()->year)
                                //->whereMonth('created_at', Carbon::now()->month)
                                ->where('user_id', Auth::user()->id)                                 
                                ->with('bill')->get();  
            
            $total = collect($expenses->sum('amount'));            

            return response()->json([
                "expenses" => $expenses,
                "total" => $total,
            ]);                
        }
        
    }

    /**
     * When user accessed POST page
     */
    public function redirectPost(){
        return redirect('/');
    }

    public function resetbatch(){        
        return $bills = Bill::where('user_id', Auth::user()->id)                                                               
            ->where('bill_batch', '012019')
            ->update(['bill_batch' => '042019']);
    }

    public function xx(){        
        $expenses = Expense::where('user_id', auth::user()->id)
                    ->where('bill_batch', '042019')
                    ->update(['created_at' => '2019-04-15 21:36:11']);
        return;
        $bill_batch = Carbon::now()->format('mY');
        $bill_batch = "052019";
        return $bills = Bill::where('user_id', '=', Auth::user()->id)
            ->where('bill_batch', $bill_batch)
            ->get();  
    }   

    public function testData(){
        $bills = Bill::where('user_id', Auth::user()->id)
                    ->whereIn('bill_type', ['Bill', 'Other'])
                    ->get()->sum('cost');
        
        return $bills;
        $bills = Bill::orderBy('bill', 'asc')
        ->where('user_id', Auth::user()->id)
        ->where('bill_type', 'Payment')
        ->get();

        return $bills;
        return;

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
               if(!in_array($d->bill, $labels)){                    
                   array_push($labels, $d->bill);                                   
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

       
       return response()->json([
           'label_current' => $label_current,
           'cost_current' => $cost_current,
           'labels' => $labels,            
           'cost_1' => $cost_1,
           'cost_2' => $cost_2,
           'cost_3' => $cost_3,
       ]);  
        
        $batch1 = Carbon::now()->format('mY');        
        $cost_1 = array();
        $current = Bill::orderBy('id', 'asc')
            ->where('user_id', Auth::user()->id)        
            ->where('bill_batch', $batch1)
            ->where('bill_type', 'Payment')
            ->withCount(['expenses as total_expenses' => function($query) {                            
                    $query->where('bill_batch', $this->bill_batch)
                        ->select(DB::raw('sum(amount)'));
            }])->get();                    
        
        foreach($current as $key=>$value){            
            array_push($cost_1, $value->total_expenses);
        }
            
        return $cost_1;
        return;
        $expenses = Expense::where('user_id', Auth::user()->id)
                            ->with('bill')
                            ->withCount('bill')
                            ->onlyTrashed()->get();
        
        $unlisted = array();        
        $unknown = 0;        
        $i = 0;

        foreach($expenses as $d){   
            if(!isset($d->bill->bill)){
                array_push($unlisted, 'Unknown' . '@' . '0');                  
            }else{
                //array_push($unlisted, $d->bill->bill . '@' . $d->id);                   
                array_push($unlisted, $d->bill->bill . '@' . $d->bill->id);                   
            }       
        }

      
        $data = array_count_values($unlisted);        

        $fulldata = array();
        foreach($data as $key => $value){            
            $x = explode("@", $key);            
            $fulldata[] = array(
                "bill" => $x[0],
                "id" => $x[1],
                "total" => $value,
            );            
        }
        
       
        return response()->json([            
            "unlisted" => $fulldata,
        ]);
        return $unlisted;
        return;
        $bills = Bill::orderBy('bill', 'asc')
                        ->where('user_id', auth::user()->id)
                        ->where("bill_type", "Payment")
                        ->onlyTrashed()->get();
        echo count($bills);
                        return;
        //$comment = App\Post::find(1)->comments()->where('title', 'foo')->first();
        //$bill = Bill::find(7)->expenses()->where('bill_id', 7)->first();
        
        $expenses = Bill::where('id', 7)                                        
                    ->onlyTrashed()                    
                    ->expenses()
                    ->get();
        dd($expenses);
        return $expenses;
        foreach($expenses as $d){
            echo $d->amount . "<br>";
            echo $d->bill;
        }

        return;
        $this->xx();
        return;
        $batch = History::where('user_id', Auth::user()->id)
                        ->get();

        $batches = array();
        
        $fulldata = array();
        $data = array();
        $i = 0;
        foreach($batch as $d){
            if(!in_array($d->bill_batch, $batches)){
                
                array_push($batches, $d->bill_batch);

                $month = substr($d->bill_batch, 0, 2);
                $year = substr($d->bill_batch, 2);

                $textdate = Carbon::createFromDate($year, $month, 1);

                $i++;
                $data = array(
                    $i => $d->bill_batch,
                    'text' => $textdate->format('F Y'),
                );

                array_push($fulldata, $data);
            }            
        }

        dd($fulldata);

        return;
        $fulldata = array();
        for($i=0; $i < count($batches); $i++){
            echo $batches[$i] . "<br>";
            $d = array(
                $i => $batches[$i],
            );

            array_push($fulldata, $d);
        }

        return;
        dd($fulldata);
        $data = array();
        $i = 0;
        $date = array();
        foreach($batches as $d){
            //echo Carbon::now($d)->format('mY') . "<br>";
            // echo $d . "<br>";
            
            ##echo $month = substr($d, 0, 2);
            #echo $year = substr($d, 2);

            
            // echo "date: " . Carbon::now($month)->format('F') . " " . $year . "<br>";
           
        }
        
        return;
        $bills = Bill::where('user_id', Auth::user()->id)                
                ->with('histories')
                ->get();
        
        foreach($bills as $d)                {
            echo $d->bill . "<br>";
            echo $d->histories. "<br><br>";
            //echo $d->histories[0]->bill. "<br><br>";
        }
        return;        
        // Get active/current bills
        $bills = Bill::where('user_id', Auth::user()->id)->get();

        // Batches
        $batch1 = Carbon::now()->subMonth()->format('mY');
        $batch2 = Carbon::now()->subMonth(2)->format('mY');
        $batch3 = Carbon::now()->subMonth(3)->format('mY');

        $history = History::orderBy('bill', 'asc')
        ->where('bill_id', 1)
        ->where("user_id", Auth::user()->id)          
        ->whereIn('bill_batch', [$batch1, $batch2, $batch3])          
        ->where("bill_type", '=', "Payment")            
        ->get();

        return;
       
        $labels = array();        
        $cost_1 = array();
        $cost_2 = array();
        $cost_3 = array();
        $costs = array();

        $i = 0;
        foreach($bills as $data){
            echo $id = $data->id;
           
            $history = History::orderBy('bill', 'asc')
                    ->where('bill_id', $id)
                    ->where("user_id", Auth::user()->id)          
                    ->whereIn('bill_batch', [$batch1, $batch2, $batch3])          
                    ->whereIn("bill_type", '=', "Payment")            
                    ->get();
            
            foreach($history as $d){                                
                if(!in_array($d->bill, $labels)){
                    array_push($labels, $d->bill);                                   
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

        return;

        // Get active/current bills
        $bills = Bill::where('user_id', Auth::user()->id)->get();
    
        $batch1 = Carbon::now()->subMonth()->format('mY');
        $batch2 = Carbon::now()->subMonth(2)->format('mY');

        $labels = array();
        $values = array();
        
        $i = 0;
        foreach($bills as $data){
            $id = $data->id;
           
            $history = History::orderBy('bill', 'asc')
                    ->where('bill_id', $id)
                    ->where("user_id", Auth::user()->id)                    
                    ->whereIn("bill_type", ["Bill", "Other"])            
                    ->get();
            
            foreach($history as $d){      
                if(!in_array($d->bill, $labels)){
                    array_push($labels, $d->bill);                                   
                }
                               
                array_push($values, $d->cost);  
            }          
        }


        dd($values);
        dd($labels);
        return; 
        // Separate labels
        $label_1 = array();         
        $i = 0;
        foreach($labels as $d){            
            $i++;
            if($i % 2 == 0){ 
                array_push($label_1, $d);            
            } 
        }
        dd($label_1);

        return;

        $date1 = Carbon::now()->subMonth()->format('m');
        echo $batch1 = $date1 . Carbon::now()->format('Y');

        echo "<br>";
        echo $batch1 = Carbon::now()->subMonth()->format('mY');
        echo $batch2 = Carbon::now()->subMonth(2)->format('mY');
        return;
        #$bill = "Visa";
        #$bill = History::where('bill', '=', $bill)->update(["bill_id" => 9]);

        // Get user's bill
        $bills = Bill::where('user_id', Auth::user()->id)->get();

        $date1 = Carbon::now()->subMonth()->format('m');
        $batch1 = $date1 . Carbon::now()->format('Y');

        $date2 = Carbon::now()->subMonth(2)->format('m');
        $batch2 = $date2 . Carbon::now()->format('Y');

        $labels = array();
        $values = array();        
        foreach($bills as $data){
            $id = $data->id;
           
            $history = History::orderBy('bill', 'asc')
                    ->where('bill_id', $id)
                    ->where("user_id", Auth::user()->id)
                    ->whereIn("bill_batch", [$batch1, $batch2])                
                    ->whereIn("bill_type", ["Bill", "Other"])            
                    ->get();

            foreach($history as $d){
                array_push($labels, $d->bill);                      
                array_push($values, $d->cost);  
            }           
        }

        // Separate labels
        $label_1 = array();
        $label_2 = array();
        $i = 0;
        foreach($labels as $d){            
            $i++;
            if($i % 2 == 0){ 
                array_push($label_1, $d);            
            }else{
                array_push($label_2, $d);            
            }
        }
        dd($label_2);

        // Separate costs
        $cost_1 = array();
        $cost_2 = array();
        $i = 0;
        foreach($values as $d){            
            $i++;
            if($i % 2 == 0){ 
                array_push($cost_1, $d);            
            }else{
                array_push($cost_2, $d);            
            }
        }
        
     

        return;
        $history = History::orderBy('bill', 'asc')
                ->where('bill_id', 1)
                ->where("user_id", Auth::user()->id)
                ->whereIn("bill_batch", [$batch1, $batch2])                
                ->whereIn("bill_type", ["Bill", "Other"])            
                ->get();

     foreach($history as $d){
         echo $d->bill . " - " . $d->cost .  "<br>";
     }

     
        return;
        $bills = Bill::where('user_id',Auth::user()->id)->get();
        foreach($bills as $i => $data){   

            $history[$i] = new History();
            $history[$i]->user_id = $data->user_id;
            $history[$i]->bill = $data->bill;
            $history[$i]->bill_id = $data->id;
            $history[$i]->bill_type = $data->bill_type;
            $history[$i]->bill_batch = $data->bill_batch;
            $history[$i]->payment_order = $data->payment_order;
            $history[$i]->payment_date = $data->payment_date;
            $history[$i]->cost = $data->cost;
            $history[$i]->notes = $data->notes;
            $history[$i]->payment_status = $data->payment_status;
            $history[$i]->created_at = $data->created_at;
            $history[$i]->updated_at = $data->updated_at;
            $history[$i]->deleted_at = $data->deleted_at;
            $history[$i]->save();
             
            echo $data->id . "<br>";
        }
            return;
        $users = DB::table('bills')
                ->leftJoin('histories', 'bills.id', '=', 'posts.user_id')
                ->get();

        return;
        $current_month = Carbon::now()->subMonth()->format('m');
        $bill_batch = $current_month . Carbon::now()->format('Y');
        $data = History::orderBy('bill', 'asc')
                    ->where("bill_batch", $bill_batch)
                    ->where("user_id", Auth::user()->id)
                    ->where("bill_type", "=", "Bill")                    
                    ->orWhere("bill_type", "=", "Other")                                        
                    ->get();
            
        $labels = array();
        $sum_current = array();
        $sum_previous = array();        

        foreach($data as $val){         
        array_push($labels, $val->bill);

        // Initiate charting
        // Current month expenses            
            array_push($sum_current, $val->bill);            
        }     
dd($sum_current);
        foreach($sum_current as $d){
            //echo $d->bill;
        }
        return;
     #   echo $previous_month = Carbon::now()->subMonth(2)->format('m');
        
      #  return;
        $data = Bill::orderBy('bill', 'asc')
        ->where("user_id", Auth::user()->id)
        ->where("bill_type", "=", "Bill")
        ->orWhere("bill_type", "=", "Other")
        ->get();

        foreach($data as $d){
            echo $d->bill . "<br>";
        }
        return;

        $bills = Bill::where('user_id', Auth::user()->id)
        //->whereYear('created_at', Carbon::now()->year)
        //->whereMonth('created_at', Carbon::now()->month)                                        
        ->where('bill_batch', $this->bill_batch)
        ->withCount(['expenses as total_expenses' => function($query) {                            
                $query->where('bill_batch', $this->bill_batch)
                    ->select(DB::raw('sum(amount)'));
        }])->get();

        return $bills;

        //$this->resetbatch();
        return;
        $category = array("Groceries", "Restaurant", "Online Purchases", "Gas", "Maintenance", "Other");
        $category_index = array_rand($category);
        echo $category[$category_index];

        

        
        return;
        $this->resetbatch();
        return;
        if(count($this->xx()) === 0){
            // If not found, copy all items from Bills to History            
            $bills = Bill::where('user_id', Auth::user()->id)->get();
            if(!History::where('user_id', Auth::user()->id)
                ->where('bill_batch', $this->bill_batch)
                ->exists()){

                foreach($bills as $i => $data){                    
                    $history[$i] = (new History())->forceCreate($data->only(['user_id', 'bill', 'bill_type', 'bill_batch', 'payment_order', 'payment_date', 'cost', 'notes', 
                                    'payment_status', 'created_at', 'updated_at', 'paid_date', 'deleted_at']));
                }

                // Then update bills to a new set                        
                $previous_month = Carbon::now()->subMonth()->format('mY');
                echo $previous_month = Carbon::now()->format('mY');
                
                
                Bill::where('user_id', Auth::user()->id)                                                               
                    ->where('bill_batch', $previous_month)                                
                    ->update([
                        'bill_batch' => '052019',
                        'notes' => '',
                        'paid_date' => null,
                        'payment_status' => 0,
                        'cost'=> 0,
                    ]);
            }                        
        }

        return;
       # Bill::where('user_id', Auth::user()->id)                                                               
       # ->where('bill_batch', '052019')
      #  ->update([
          //  'bill_batch' => '042019',
       # ]);
       //$this->resetbatch();
        $current_date = "052019";
        $old_date = "042019";
        
        $arr = "032019, 042019, 052019";
        $arr_x = explode(',', $arr);

        $bill_batch = Carbon::now()->format('mY');
        
        //$bill_batch = '042019';
      
      
        $bills = Bill::orderBy('id', 'desc')
                ->where('user_id', '=', Auth::user()->id)
                ->where('bill_batch', $bill_batch)
                ->get();                                                                    

        if(count($bills) === 0){                    
            echo "not";
            // If collection is empty, update new set of Bill Payment Order for the current/active month            
            $previous_month = Carbon::now()->subMonth()->format('mY');
            Bill::where('user_id', Auth::user()->id)                                                               
                ->where('bill_batch', $previous_month)                                
                ->update([
                //    'bill_batch' => $bill_batch,
                  //  'notes' => '',
                  //  'paid_date' => null,
                   // 'payment_status' => 0,
                   // 'cost'=> 0,
                ]);
        }else{      
            echo "FOUND:";
            return $bills;
        }



        return;
        
        $last_index = $arr_x[0];
        foreach($arr_x as $d){            
       #     echo $d . "<br>";
        }

        echo "<br>";
        
        #echo "Current Month: " . Carbon::now()->format('mY') . "<br>";
        #echo "First Index: " . $arr_x[0] . "<br>";
        #echo "Last Index: " . $arr_x[count($arr_x)-1];


        // Copy all Bills table
        //$results = DB::select("INSERT into histories SELECT * FROM bills"); 

        // Copy all Bills table
        
        echo $this->bill_batch;
        echo "<br>";
        echo "<br>";
        
        $bills = Bill::where('user_id', Auth::user()->id)                                                               
                    ->where('bill_batch', $this->bill_batch)
                    ->get();

        if(!History::where('user_id', Auth::user()->id)
                ->where('bill_batch', $this->bill_batch)
                ->exists()){

                foreach($bills as $i => $data){
                    echo $i . " " . $data->bill . "<br>";            
        
                    /*
                    $history[$i] = new History();
                    $history[$i]->user_id = $data->user_id;
                    $history[$i]->bill = $data->bill;
                    $history[$i]->bill_type = $data->bill_type;
                    $history[$i]->bill_batch = $data->bill_batch;
                    $history[$i]->payment_order = $data->payment_order;
                    $history[$i]->payment_date = $data->payment_date;
                    $history[$i]->cost = $data->cost;
                    $history[$i]->notes = $data->notes;
                    $history[$i]->payment_status = $data->payment_status;
                    $history[$i]->created_at = $data->created_at;
                    $history[$i]->updated_at = $data->updated_at;
                    $history[$i]->deleted_at = $data->deleted_at;
                    $history[$i]->save();
                    */
                                            
                    $history[$i] = (new History())->forceCreate($data->only(['user_id', 'bill', 'bill_type', 'bill_batch', 'payment_order', 'payment_date', 'cost', 'notes', 
                                    'payment_status', 'created_at', 'updated_at', 'paid_date', 'deleted_at']));
                }

                // Then update bills to a new set
        
                    
        }

        Bill::where('user_id', Auth::user()->id)                                                               
            ->where('bill_batch', '052019')
            ->update(['bill_batch' => '042019']);

        
        
                    //->DB::selectRaw("INSERT into histories SELECT * FROM bills");
                    //->DB::select("INSERT into histories SELECT * FROM bills"); 
                    //->get();


                    //$results = DB::select("INSERT into histories SELECT * FROM bills"); 
                    //->get("INSERT into histories SELECT * FROM bills");  
        
                    
        return;
        //return Bill::all();
        $current_month = Carbon::now()->subMonth()->format('m');
        echo $bill_batch = $current_month . Carbon::now()->format('Y');
        #echo $bill_batch = Carbon::now()->format('mY');
        return;
        
            return Bill::where('user_id', Auth::user()->id)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)                                        
            ->withCount(['expenses as total_expenses' => function($query) {
                $query->select(DB::raw('sum(amount)'));
            }])->get();
   
    }

    /**
     * Test
     */
    public function test(){
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
                   #echo strlen($d->bill) . " - " . $d->bill . " - " . $d->created_at;
                   #echo "<br>";
               }

               if($d->bill_batch == $batch1){
                    array_push($cost_1, $d->cost);           
                    echo $d->cost . "<br>";
                    #echo $d->bill_batch . " - {$batch1}<br>";
                }                
                               
               if($d->bill_batch == $batch2){
                   array_push($cost_2, $d->cost);                         
               }
               if($d->bill_batch == $batch3){
                   array_push($cost_3, $d->cost);                         
               }                
           }          
       }


        $labels = array_map('ucwords', $labels);
        return $cost_1;
     
       return;
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
           'labels' => $labels,            
           'cost_1' => $cost_1,
           'cost_2' => $cost_2,
           'cost_3' => $cost_3,
       ]);  

        return;
        // SOFT DELETES
       
        return $this->testData();
       
        return;
        $bills = Bill::onlyTrashed()                
                ->get();

        foreach($bills as $d){
           //  echo $d->bill . "<br/>";
        }

 
        // Undelete single
        $bills = Bill::withTrashed()
                ->where('id', 33)
                ->restore();

        // Undelete multiple
        $expenses = Expense::withTrashed()->get();
        foreach($expenses as $d){
            echo $d->amount . "<br>";
        }
        
        // Restoration
        $expenses = Expense::withTrashed()->restore();

        return;
        $data = Bill::orderBy('bill', 'asc')
        ->where("user_id", Auth::user()->id)
        ->where("bill_type", "=", "Payment")
        ->get();

        $current_month = Carbon::now()->month;
        $previous_month = Carbon::now()->month-1;

        $labels = array();
        $sum_current = array();
        $sum_previous = array();        

        foreach($data as $val){         
            
        array_push($labels, $val->bill);

        // Initiate charting
        // Current month expenses
        #$compute_current = (float)$this->getsum($val->id, $current_month);
        #array_push($sum_current, number_format($compute_current, 2, '.', ''));

        // Previous month expenses
        #$compute_previous = (float)$this->getsum($val->id, $previous_month);
        #array_push($sum_previous, number_format($compute_previous, 2, '.', ''));
        }        

        return response()->json([
        'labels' => $labels,
        'sum_current' => $sum_current,
        'sum_previous' => $sum_previous,
        ]);     


        return;
        $bills = Bill::where('user_id', Auth::user()->id)
        ->whereYear('created_at', Carbon::now()->year)
        ->whereMonth('created_at', Carbon::now()->month)                                        
        ->withCount(['expenses as total_expenses' => function($query) {
                $query->select(DB::raw('sum(amount)'));
        }])->get();

        dd($bills);
        return;
        $bills = Bill::where('user_id', Auth::user()->id)                            
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)                    
                    //->with('expenses')
                    //->get();
                    
                    ->with([
                        'expenses' => function($query){
                            $query->sum('amount');
                        }                   
                    ])->get();
                    
                 
                  dd($bills);
        foreach($bills as $d){
            echo $d->bill . "<br>";
            echo $d . "<br>";
        }

        return;
        $payments = Payment::orderBy('payment', 'asc')
        ->where('user_id', Auth::user()->id)
        ->get();
        return response()->json($payments);    

        return;
        $bill_id = 1;
               
        $x = "";        
        $bills = Bill::where('user_id', Auth::user()->id)                    
                    ->withCount('expenses')  
                    ->with([
                        'expenses' => function($query){ 
                           $query->sum('amount');                                                      
                        }                   
                    ])                 
                    ->get();

       #dd($bills);
        
        foreach($bills as $d){
            echo $d->bill . ' - Items: ' , ' === ';
            echo $d->expenses->sum('amount') . "<br><hr>";            
            
        }


        echo "<br><br>";

        $bill = Bill::where('user_id', 1)
                    ->withCount(['expenses as total_expenses' => function($query) {
                            $query->select(DB::raw('sum(amount)'));
                    }])->get();
               dd($bill);

                /*

                $bills = DB::table('bills')
                ->leftJoin('expenses', 'bills.id', '=', 'expenses.bill_id')
                ->get();


                 $bills = Bill::where('user_id', Auth::user()->id)                    
                            ->leftJoin('expenses', 'bills.id', '=', 'expenses.bill_id')
                            ->get();

          
                $bills = Bill::where('user_id', '=', Auth::user()->id)
                        ->leftJoin('expenses', 'expenses.bill_id', '=', 'bills.id')             
                        ->selectRaw('sum(expenses.amount) as total_expenses, bills.id')
                        ->get();
                        */
        /*

        Job::where('owner_id', $ownerId)
            ->leftJoin('tasks', 'tasks.job_id', '=', 'jobs.id')
            ->groupBy('jobs.id')
            ->selectRaw('sum(tasks.total_time) as total_time, jobs.id')
            ->lists('total_time', 'id');

        $users = DB::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();
            */
        
    }
}
