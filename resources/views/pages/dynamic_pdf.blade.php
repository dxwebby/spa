<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <meta http-equiv="Content-Type" content="text/plain; charset=utf-8"/>
    <title>DXDESINGS.NET Budget Manager v1</title>    
    <style>
        @page{
            margin-left: 0;
            margin-right:0;
        }

        body{
            font-family: 'Open Sans', sans-serif;
            font-size: 12px;
        }
        h4{
            font-size: 14px;
        }
        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .row {            
            display: flex;            
            
            margin-right: -15px;
            margin-left: -15px;
        }
        .col-lg-12{
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }
        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
        }
        .text-center {
             text-align: center !important;
        }

        table {
        border-collapse: collapse;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }
        h1, h2, h3, h4, h5, h6 {
        margin-top: 0;
        margin-bottom: 0.5rem;
        }
        .mt-5{
            margin-top: 3rem !important;
        }
        td, th{
            text-align:center;
        }
        
        header {
                position: fixed;
                top: -60px;
                left: 0px;
                right: 0px;
                height: 50px;

                /** Extra personal styles **/
                background-color: #03a9f4;
                color: white;
                text-align: center;
                line-height: 35px;
            }

            footer {
                position: fixed; 
                bottom: -60px; 
                left: 0px; 
                right: 0px;
                height: 50px;                            
                padding-bottom: 50px;
            }

    </style>
</head>
<body>           
    <?php         
        $ctr = 1; 
        /*

        $expenses = App\Expense::where('user_id', '=', Auth::user()->id)                                
                                ->with('payment')
                                ->get();    
        $total = App\Expense::where('user_id', '=', Auth::user()->id)->get()->sum('amount');
        
        */     
    ?>    
    @if(!isset($total) || !isset($bank))
        <div class="container" style="margin-top: 100px;">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1>This page generates PDF file.</h1><br/>
                    <a href="{{url('/')}}">Go back to Dashboard</a>
                    <?php 
                        return false;
                    ?>
                </div>
            </div>
        </div>
    @endif
    <div class="container">        
        <div class="row">
            <div class="col-lg-12 text-center">
                <h3>{{$bank}}</h3>                
                @if(empty($date))
                    <h4>{{ \Carbon\Carbon::now()->format('Y')}} Expenses</u></h4>
                @else
                    <h4>List of Expenses for the month of <u>{{ $date }}</u></h4>
                @endif 
                <br/>
                <h4>Total Expenses: ${{$total}}</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 mt-5">
                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Payment</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Notes</th>
                        <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $item)
                        <tr>                                
                            <th scrope="row">{{$ctr++}}</th>
                            <td>{{$item->bill->bill}}</td>
                            <td>${{$item->amount}}</td>
                            <td>{{$item->description}}</td>
                            <td>
                                {{$item->created_at->format('D, M d, Y')}}<br/>
                                {{$item->created_at->format('h:i:s A')}}
                            </td>                                
                        </tr>                             
                        @endforeach
                    </tbody>
                    </table>
            </div>
        </div>

        <footer>
            <div class="row">                
                <div class="col-lg-12 text-center mt-5">
                    Laravel Budget Management v.1 <br/><br/>
                    <a href="https://dxdesigns.net">Developed by: DXDESIGNS.NET</a>
                    &nbsp;|&nbsp;
                    Copyright Reserved 2019 (c)
                </div>
            </div>
        </footer>
    </div>

</body>
</html>