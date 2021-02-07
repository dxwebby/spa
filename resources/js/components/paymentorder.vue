<template>        
    <div class="row">            
        <div class="col-lg-7">
            <div class="card" >                
                <div class="card-body">
                    <h5 class="text-center font16"><i class="fas fa-chart-line text-primary"></i> Payments</h5>                    
                    <ChartsL v-bind:options="options" :chart-data="datacollection"></ChartsL> 
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="row mt-lg-0 mt-4">
                <div class="col-lg-12">
                    <div class="card">                
                        <div class="card-body card-salary" style="margin-bottom: 30px;">
                            <div class="card-bg" style="color: rgba(77,189,116, .2);"><i class="fas fa-coins"></i></div>
                            <h5 class="card-title text-center text-success  font16"><i class="fas fa-hand-holding-usd"></i> My Salary</h5>                                                        
                            <div class="card-text">
                                <div class="row text-center">
                                    <div class="col-lg-6" style="border-right: 1px solid rgba(0,0,0,.15);">
                                        <div>                                            
                                            <label class="font20">$1,500</label>
                                        </div>              
                                        <p>First Salary</p>
                                        <i class="fas fa-money-check"></i>
                                    </div>
                                    <div class="col-lg-6 mt-lg-0 mt-md-4">
                                        <div>                                            
                                            <label class="font20">$1,500</label>
                                        </div>    
                                        <p>Second Salary</p>                                          
                                        <i class="fas fa-money-check"></i>
                                    </div>
                                </div>                               
                            </div>                                                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-lg-4 mt-4">
                <div class="col-lg-12">
                    <div class="card">                
                        <div class="card-body card-salary px-4" style="margin-bottom: 11px;">
                            <div class="card-bg" style="color: rgba(226,52,47, .2);"><i class="fas fa-clipboard-list"></i></div>
                            <h5 class="card-title text-center font16 text-danger"><i class="far fa-credit-card font16"></i> This Month's Total Expenses</h5>                            
                            <p class="card-text">
                                <table class="table table-sm t1able-bordered">
                                    <tbody>
                                        <tr>
                                            <td><label>Bills</label></td>
                                            <td>${{total.bills | digits}}</td>
                                        </tr>
                                        <tr>
                                            <td><label>Payments</label></td>
                                            <td>${{total.expenses | digits}}</td>
                                        </tr>
                                        <tr>
                                            <td><label>Total</label></td>
                                            <td><u>${{total.all | digits}}</u></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </p>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--
        <h5 class="header_bg bg-danger mb-4 p-2 text-center font14">
            <img src="https://icongr.am/clarity/bar-chart.svg?size=20&color=ffffff" alt="chart">
            Current Expenses & Bills
        </h5>                           
        
        <div class="container-fluid mb-5">                           
                <div class="row text-center">
                    <div class="col-lg-12">    
                        <h5>Expenses</h5>                    
                        <ChartsL v-bind:options="options" :chart-data="datacollection"></ChartsL>                    
                    </div>     
                          
                    <div class="col-lg-6 mt-sm-4 mt-lg-0">                        
                        <h5>Bills</h5>
                        <ChartsB v-bind:options="options" :chart-data="datacollection2"></ChartsB>
                    </div>             
                     
                </div>            
        </div>   
        -->     
    </div>
</template>

<script>   
    var moment = require('moment');
    import ChartsL from './Charts-Line.js'
    import ChartsB from './Charts-Bar.js'  
    export default {            
        components: {          
           ChartsL,
           ChartsB,           
        },
        data(){
            return{
                moment: moment,
                datacollection: {},
                datacollection2: {},
                options: null,
                bgcolors:[],

                payments: [],               
                sum_current: [],
                sum_previous: [],
                label1: '',
                label2: '',
          
                bills_label: '',
                bills_month_a: '',
                bills_month_b: '',
                cost_1: '',
                cost_2: '',

                total:{
                    bills: 0,
                    expenses: 0,
                    all: 0,
                }
            }
        },         
        mounted(){
            this.prepareData();                                    
        },
        created(){
            let $this = this;
            eventBus.$on('updateChart', function(payload){
                // debug
                // console.log("Accessed external component paymentorder.vue")
                // console.log("Payload: " + payload)

                // Update chart
                $this.prepareData();                
            })            
        },
        filters:{
            digits:function(nStr){
                nStr += '';
                let x = nStr.split('.');
                let x1 = x[0];
                let x2 = x.length > 1 ? '.' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }
                let y = x1+x2;
                return x1 + x2;
            },   
        },    
        methods:{ 
            updatechart(){                        
                this.prepareData();
            },
            generateColor(){
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            },          
            prepareData(){
                // Create options
                this.options = {                            
                    responsive: true,
                    maintainAspectRatio: false,    
                    legend: {
                        display: true,
                        labels: {
                            fontColor: 'rgb(255, 99, 132)',                                  
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{                                
                            barPercentage: .4
                        }]
                    },
                };

                // Get data for Expenses
                axios.get(`/chart/expenses`).then((response)=>{                    
                    let d = response.data;                                                    
                    // console.log(d)

                    // Get total expenses and bills
                    this.total.bills = d.total_bills.toFixed(2);
                    
                    let tmp = 0;
                    for(let i=0; i < d.sum_current.length; i++){
                        tmp += parseFloat(d.sum_current[i]);
                    }

                    this.total.expenses = tmp.toFixed(2);
                    let tmp_all = parseFloat(this.total.expenses) + parseFloat(this.total.bills);
                    this.total.all = parseFloat(tmp_all).toFixed(2);

                    // Current expenses
                    this.sum_current = d.sum_current;

                    // Previous expenses
                    this.sum_previous = d.sum_previous;                    
                    
                    // Labels
                    this.payments = d.labels;                          
                                        
                    this.label1 = moment().format('MMMM YYYY');
                    this.label2 = moment().subtract(1, 'months').format('MMMM YYYY');

                    // Generate color based on the labels length                                        
                    for(let i=1; i <= this.payments.length; i++){                        
                        this.bgcolors.push(this.generateColor());
                    }
                    
                    // Generate chart
                    this.fillData();
                })

                // Get data for Bills
                axios.get(`/chart/bills`).then((response)=>{                    
                    let d = response.data;     
                    
                    this.cost_1 = d.cost_1;
                    this.cost_2 = d.cost_2;
                 
                    // Labels
                    this.bills_label = d.labels;                                
                  
                    // Legend
                    this.bills_month_a = moment().subtract(1, 'months').format('MMMM YYYY');
                    this.bills_month_b  = moment().subtract(2, 'months').format('MMMM YYYY');
                    
                    // Generate color based on the labels length                                        
                    for(let i=1; i <= this.bills_label.length; i++){                        
                        this.bgcolors.push(this.generateColor());
                    }
                    // Generate chart
                    this.fillData2();
                })               
            },         
            fillData(){                          
                this.datacollection = {
                    labels: this.payments,
                    datasets: [                                          
                        {
                            label: this.label2,
                            data: this.sum_previous,                                                
                            //backgroundColor: this.bgcolors,                             
                            // Line Graph
                            type: 'line',
                            backgroundColor: 'rgba(0,131,255, .1)',
                            pointStyle: 'crossRot',                            
                            borderColor: 'rgba(0,131,255, .5)',
                            pointBackgroundColor: 'rgba(4,9,30, .75)',
                            pointBorderWidth: 8,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 1,                            
                        },  
                         {
                            label: this.label1,
                            data: this.sum_current,                                                
                            //backgroundColor: this.bgcolors,                             
                            type: 'line',
                            backgroundColor: 'rgbA(226,52,47, .1)',
                            pointStyle: 'crossRot',                            
                            borderColor: 'rgba(226,52,47, .75)',
                            pointBackgroundColor: 'rgba(4,9,30, .75)',
                            pointBorderWidth: 8,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 1,
                            
                        },   
                                    
                    ],
                }    
                
                /*
                this.datacollection2 = {
                    labels: this.payments,
                    datasets: [
                        {
                            label: 'Monthly Expenses', 
                            backgroundColor: this.bgcolors,                             
                            data: this.sum_previous,                                                
                        },                      
                                    
                    ],
                }   
                */
            },
            fillData2() {                          
                this.datacollection2 = {
                    labels: this.bills_label,
                    datasets: [                                         
                        {
                            label: this.bills_month_b,
                            data: this.cost_2,          
                            type: 'bar',                                                                                              
                            backgroundColor: 'rgba(0,131,255, .1)',
                            pointStyle: 'crossRot',                            
                            borderColor: 'rgba(0,131,255, .5)',
                            pointBackgroundColor: 'rgba(4,9,30, .75)',
                            pointBorderWidth: 8,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 1,                            
                        },  
                        {
                            label: this.bills_month_a,
                            data: this.cost_1,   
                            type: 'bar',                                                                         
                            backgroundColor: 'rgbA(226,52,47, .1)',
                            pointStyle: 'crossRot',                            
                            borderColor: 'rgba(226,52,47, .75)',
                            pointBackgroundColor: 'rgba(4,9,30, .75)',
                            pointBorderWidth: 8,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 1,                            
                        },    
                                    
                    ],
                }   
            },
            getRandomInt () {
                return Math.floor(Math.random() * (50 - 5 + 1)) + 5
            },
        }
    }
</script>

<style lang="scss" scoped> 
#globalUpdateChart{
    display:none;
} 
.header_bg{
    color:white;
}
button{    
    border-radius:0;    
    padding:0 10px;
}


label{
    font-weight: 600;
}
table{    
    td{                         
        vertical-align: middle;        
    }

}
.fa-money-check{
    color: rgba(0,0,0, .5);
    font-size: 30px;
}

.card-salary{
    position: relative;
    overflow:hidden; 
    padding:0; 
    .card-bg{
        position: absolute;
        right:-20px;;
        bottom:-20px;
        font-size: 150px;
        color: rgba(0,0,0, .08);
    }
    .card-title{
        //background-image: linear-gradient(#0083ff, #479be9);       
        padding: 20px;
        //color:white;
        color:black;                
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;        
    }
}


</style>