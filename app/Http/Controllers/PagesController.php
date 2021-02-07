<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bill;
use App\History;

use Carbon\Carbon;
use Auth;

class PagesController extends Controller
{
    public $bill_batch = "";

    public function __construct(){
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
      
    public function index(){       
        //return view('pages.index');
        return redirect('/login');        
    }
    
}
