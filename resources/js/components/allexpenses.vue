<template>
    <div class="mb-5">                
        <div class="container-fluid">              
            <div class="row text-center">
                <div class="col-lg-6">
                    <h5>Current Expenses</h5>
                    <ChartsL v-bind:options="options" :chart-data="datacollection0"></ChartsL>                                                 
                </div>                
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <h5>Previous Expenses</h5>
                    <ChartsL v-bind:options="options" :chart-data="datacollection1"></ChartsL> 
                    <!--
                    <h5>Bills</h5>
                    <ChartsL v-bind:options="options" :chart-data="datacollection2"></ChartsL>
                    -->
                </div>
               
            </div>            
        </div>
    </div>
</template>

<script>
    import ChartsB from './Charts-Bar.js'
    import ChartsL from './Charts-Line.js'
    import ChartsP from './Charts-Pie.js'
     var moment = require('moment');
    export default {
        components: {          
           ChartsB, ChartsL, ChartsP
        },
        data(){
            return{
                moment: moment,
                expenses:[],
                datacollection0: {},
                datacollection1: {},
                datacollection2: {},
                options: null,                 
               
                bills_label1: '',
                bills_label2: '',
                bills_label3: '',
                bills_cost1: '',
                bills_cost2: '',
                bills_cost3: '',

                label_current: '',
                cost_current: '',
                
            }
        },
        mounted(){            
            this.prepareData();   
        },
        created(){

        },
        methods:{
            prepareExpenses:function(){
                axios.get(`/allexpenses`).then((response) =>{
                    //console.log(response.data);
                    this.expenses = response.data;
                })
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

                // Get data
                axios.get(`/chart/full/expenses`).then((response)=>{                                        
                    let d = response.data;                                                    
                    //console.log(d)
                    
                    // Labels
                    this.bills_labels = d.labels;    
                    
                    // Costs
                    this.bills_cost1 = d.cost_1;
                    this.bills_cost2 = d.cost_2;
                    this.bills_cost3 = d.cost_3;
                    
                    this.bills_label1 = moment().subtract(1, 'months').format('MMMM YYYY');
                    this.bills_label2 = moment().subtract(2, 'months').format('MMMM YYYY');
                    this.bills_label3 = moment().subtract(3, 'months').format('MMMM YYYY');

                    // Current
                    this.label_current = d.label_current;
                    this.cost_current = d.cost_current;                    

                    // Generate color based on the labels length                                        
                    //for(let i=1; i <= this.payments.length; i++){                        
                    //   this.bgcolors.push(this.generateColor());
                    // }
                    
                    // Generate chart
                    this.fillData1();
                })


                // Get data
                axios.get(`/chart/full/bills`).then((response)=>{                    
                    let d = response.data;                                                    
                    //console.log(d)

                    // Labels
                    this.bills_labels = d.labels;    
                    
                    // Costs
                    this.bills_cost1 = d.cost_1;
                    this.bills_cost2 = d.cost_2;
                    this.bills_cost3 = d.cost_3;
                    
                    this.bills_label1 = 'C' + ' - ' + moment().format('MMMM YYYY');
                    this.bills_label2 = 'H' + ' - ' + moment().subtract(1, 'months').format('MMMM YYYY');
                    this.bills_label3 = 'H' + ' - ' + moment().subtract(2, 'months').format('MMMM YYYY');

                    // Generate color based on the labels length                                        
                    //for(let i=1; i <= this.payments.length; i++){                        
                    //    this.bgcolors.push(this.generateColor());
                    //}

                    
                    
                    // Generate chart
                    this.fillData2();
                })
            },         
            fillData1() {      
                // Current Expenses
                this.datacollection0 = {
                    labels: this.label_current,
                    datasets: [
                        {
                            label:  moment().format('MMMM YYYY'),
                            
                            data: this.cost_current,
                            // Line Graph
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
                
                // Previous Expenses
                this.datacollection1 = {
                    labels: this.bills_labels,
                    datasets: [
                        {
                            label: this.bills_label3,
                            data: this.bills_cost3,                            

                            // Line Graph
                            backgroundColor: 'rgba(0,131,255, .1)',
                            pointStyle: 'crossRot',                            
                            borderColor: '#28a745',
                            pointBackgroundColor: 'rgba(4,9,30, .75)',
                            pointBorderWidth: 8,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 1,                            
                        },                                          
                        {
                            label: this.bills_label2,
                            data: this.bills_cost2,
                            
                            // Line Graph
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
                            label: this.bills_label1,
                            data: this.bills_cost1,
                            //backgroundColor: this.bgcolors,                             
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
            fillData2 () {                          
                this.datacollection2 = {
                    labels: this.bills_labels,
                    datasets: [
                        {
                            label: this.bills_label3,
                            data: this.bills_cost3, 
                            backgroundColor: 'rgba(0,131,255, 0)',
                            pointStyle: 'crossRot',                            
                            borderColor: '#28a745',
                            pointBackgroundColor: 'rgba(4,9,30, .75)',
                            pointBorderWidth: 8,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 1,
                            
                        },                                             
                        {
                            label: this.bills_label2,
                            data: this.bills_cost2, 
                            backgroundColor: 'rgba(0,131,255, 0)',
                            pointStyle: 'crossRot',                            
                            borderColor: 'rgba(0,131,255, .5)',
                            pointBackgroundColor: 'rgba(4,9,30, .75)',
                            pointBorderWidth: 8,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 1,                            
                        },  
                        {
                            label: this.bills_label1,
                            data: this.bills_cost1,
                            backgroundColor: 'rgbA(226,52,47, 0)',
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

        }
    }
</script>

<style lang="scss" scoped>

</style>