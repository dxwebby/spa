<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Expense;
use App\Payment;
use App\Bill;
use App\History;

// PDF
use App;
use PDF;

use Auth;


use Carbon\Carbon;
class PDFController extends Controller
{    
    public $bill_batch = "";

    public function __construct(){
        $this->middleware('auth');        
        $this->bill_batch = Carbon::now()->format("mY");
    }    

    public function index(){                  
        return view('pages.dynamic_pdf');
    }    
        
    public function pdfExpenses($option){
        if($option === "allexpenses"){
            $expenses = Expense::orderBy('id', 'desc')
                    ->where('user_id', Auth::user()->id)                    
                    ->with('bill')
                    ->get();

            $total = Expense::where('user_id', '=', Auth::user()->id)                        
                    ->get()->sum('amount');       
            $date = "";
            $filename = Carbon::now()->format('Y') . " Expenses.pdf";                       
        }else{
            $expenses = Expense::orderBy('id', 'desc')
                    ->where('user_id', Auth::user()->id)
                    ->where('bill_batch', $option)
                    ->with('bill')
                    ->get();

            $total = Expense::where('user_id', '=', Auth::user()->id)
                    ->where('bill_batch', $option)
                    ->get()->sum('amount');

            $month = substr($option, 0, 2);
            $year = substr($option, 2);
            
            $textdate = Carbon::createFromDate($year, $month, 1);     
            $date = $textdate->format('F Y');
            $filename = $textdate->format('F Y') . ".pdf";                          
        }
                         
        $total = number_format($total, 2);
        $bank = 'All Banks';

        $pdf = PDF::loadView('pages.dynamic_pdf', compact('expenses', 'total', 'bank', 'date'));                
        return $pdf->download($filename);        
    }
    public function pdf($option){     
    
        if($option === "all"){        
            // Generate all    
            $expenses = Expense::orderBy('id', 'desc')                    
                                ->where('user_id', '=', Auth::user()->id)       
                                //->whereYear('created_at', Carbon::now()->year)                                     
                                //->whereMonth('created_at', Carbon::now()->month)                                
                                ->where('bill_batch', $this->bill_batch)
                                ->with('bill')
                                ->get();
            $total = Expense::where('user_id', '=', Auth::user()->id)
                            ->where('bill_batch', $this->bill_batch)
                            //->whereYear('created_at', Carbon::now()->year)                                     
                            //->whereMonth('created_at', Carbon::now()->month)                             
                            ->get()->sum('amount');
                            
            $total = number_format($total, 2);
            $bank = 'All Banks';
            $date = Carbon::now()->format('F Y');
            $filename = Carbon::now()->format('F Y') . ".pdf";
        }else{ 
            // Generate by filter
            //$payment = Payment::where('id', '=', $option)->first();
            $bill = Bill::where('user_id', Auth::user()->id)
                        ->where('bill', '=', $option)
                        ->first();

            $expenses = Expense::where('user_id', '=', Auth::user()->id)
                            ->orderBy('id', 'desc')
                            //->whereYear('created_at', Carbon::now()->year)                                     
                            //->whereMonth('created_at', Carbon::now()->month)             
                            ->where('bill_batch', $this->bill_batch)
                            ->where('bill_id', '=', $bill->id)                                                       
                            ->with('bill')
                            ->get();

            $total = Expense::where('user_id', '=', Auth::user()->id)
                            //->whereYear('created_at', Carbon::now()->year)                                     
                            //->whereMonth('created_at', Carbon::now()->month)             
                            ->where('bill_batch', $this->bill_batch)
                            ->where('bill_id', '=', $bill->id)                                                       
                            ->get()->sum('amount');
                                
            $total = number_format($total, 2);
            $bank = $bill->bill;
            $date = Carbon::now()->format('F Y');
            $filename = $bank . " " . Carbon::now()->format('F Y') . ".pdf";
        }
            
        // With redirecting the browser but with google chrome warning        
        
        $pdf = PDF::loadView('pages.dynamic_pdf', compact('expenses', 'total', 'bank', 'date'));                
        return $pdf->download($filename);        


        ////////////////////////////////////////////////////////
        // Redirects user to pdf with no Google Chrome warning
        $output = '
            <html>
            <head>
            <title>DXDESIGNS.NET</title>
            </head>
            <table class="table table-striped">
                <thead class="thead-dark">
                <tr><th>Name</th></tr>                            
                <tbody>
        ';

        foreach($expenses as $item){
            $output .='<tr>
                       <td>' . $item->payment->payment . '</td>' . 
                       '<td>' . $item->amount . '</td>' .
                       '<td>' . $item->description . '</td>' . 
                       '<td>' . $item->created_at . '</td>';
        }
        $output .= '</tr></tbody></table></html>';

        $filedate = Carbon::now()->format('FY');
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($output);      
        return $pdf->stream($filedate . '.pdf');       
        ////////////////////////////////////////////////////////
    }       
}
