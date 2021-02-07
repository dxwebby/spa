<template>
    <div>          
        <!--<button id="globalTodopayment" class="global_hidden_objects" @click.prevent="prepareBills">Update changes</button>-->
        <h4 class="route-h4">
            <img src="https://icongr.am/material/credit-card-settings.svg?color=ffffff&size=18" alt="pay">
            {{moment().format('MMMM Y')}} Payment Order
            <button data-toggle="tooltip" data-placement="right" title="Add/Modify Bills"  
                    v-on:click="addBill" class="no-border float-right my-auto">                    
                <img src="https://icongr.am/clarity/tree-view.svg?size=18&color=ffffff" alt="image"> Manage Bills</button>
        </h4>          
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-sm table-dark table-hover table-bordered" id="billTable">                
                        <thead>
                            <tr>
                                <th scope="col">Type</th>                                                                      
                                <th class="_hide" scope="col">Due</th>                                                                      
                                <th class="_hide" scope="col">Paid</th>                            
                                <th scope="col">Bill</th>                              
                                <th scope="col">Cost</th>                        
                                <th class="_hide" scope="col">Notes</th>                        
                                <th scope="col" class="text-center">Mark</th>                            
                            </tr>
                        </thead>
                        <!--<draggable tag="tbody">-->
                        <tbody>
                            <tr v-for="(row,ctr) in firstBatch" :key="row.index" v-bind:id="'row' + ++ctr">                        
                                <td v-text="row.bill_type"></td>
                                <td class="_hide" v-text="moment(row.payment_date).format('Do')"></td>
                                <td class="align-middle _hide">                  
                                    {{paymentDate(row.paid_date)}}          
                                </td>                            
                                <td v-text="row.bill"></td>                            
                                <td v-text="row.bill_type === 'Payment' ? '$'+ digits(row.total_expenses) : '$'+ digits(row.cost)">                            
                                </td>
                                <td class="_hide" v-text="row.notes"></td>
                                <td class="align-middle text-center ">                            
                                    <!-- lastpoint -->                                
                                    <input type='checkbox' class='ios8-switch ios8-switch-sm'                                         
                                            :checked="row.payment_status == 1"                                                            
                                            :value="row.payment_status"                                                                                
                                            v-on:click="showForm(row.id, row.payment_status, row.notes, row.paid_date, row.cost, row.bill_type, row.total_expenses, row.bill)"    
                                            v-bind:id="'checkbox-' + row.id">                                    
                                    <label v-bind:for="'checkbox-' + row.id" class="cursor-td">Paid</label>                                
                                </td>                            
                            </tr>          
                        </tbody>       
                        <!--</draggable>-->
                    </table><!-- /.batch 1 -->

                    <table style="margin-top: -15px;" class="table table-sm table-dark table-hover table-bordered" id="billTable">                
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Type</th>
                                <th class="_hide" scope="col">Due</th>                                                                      
                                <th class="_hide" scope="col">Paid</th>                            
                                <th scope="col">Bill</th>                              
                                <th scope="col">Cost</th>                        
                                <th class="_hide" scope="col">Notes</th>                        
                                <th scope="col" class="text-center">Mark</th>                            
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row,ctr) in secondBatch" :key="row.index" v-bind:id="'row' + ++ctr">                        
                                <td v-text="row.bill_type"></td>
                                <td class="_hide" v-text="moment(row.payment_date).format('Do')"></td>
                                <td class="align-middle _hide">                  
                                    {{paymentDate(row.paid_date)}}                            
                                </td>                            
                                <td v-text="row.bill"></td>
                                <td v-text="row.bill_type === 'Payment' ? '$' + digits(row.total_expenses) : '$'+ digits(row.cost)">
                                </td>
                                <td class="_hide" v-text="row.notes"></td>
                                <td class="align-middle text-center ">                                                            
                                    <input type='checkbox' class='ios8-switch ios8-switch-sm'                                         
                                            :checked="row.payment_status == 1"                                                            
                                            :value="row.payment_status"                                                                                
                                            v-on:click="showForm(row.id, row.payment_status, row.notes, row.paid_date, row.cost, row.bill_type, row.total_expenses, row.bill)"    
                                            v-bind:id="'checkbox-' + row.id">                                    
                                    <label v-bind:for="'checkbox-' + row.id" class="cursor-td">Paid</label>       
                                </td>                            
                            </tr>                 
                        </tbody>
                    </table><!-- /.batch 2 -->          
                </div>      
                                
            </div>
        </div>         

        <!-- payment form -->
        <!-- Global Modal (dynamic) -->
        <transition name="modal" v-if="globalModal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container">

                        <!-- header -->
                        <!--<form v-on:submit.prevent>-->
                        <div class="modal-header justify-content-center"> 
                            <div class="row">
                                <div class="col-lg-12 update_container">
                                    <h5 class="text-center">
                                        Mark Bills Status                                        
                                    </h5>      
                                    <span v-if="success"><img class="my-auto" src="https://icongr.am/clarity/thumbs-up.svg?size=26&color=4dbd74" alt="ok"></span>                               
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-group">                                        
                                        <label for="date" class="mx-auto">Today's Date</label>
                                    </div>
                                    <Datepicker v-model="bill.paidon" 
                                                :format="customFormatter" 
                                                input-class="customInput customInput_w form-control text-center">
                                    </Datepicker>                         
                                </div>
                                <div class="col-lg-12 mt-3">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="bill_" >Bill</label>
                                            <div><u v-text="bill.bill"></u></div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="cost_">Cost</label>
                                            <input type="text" id="cost_" :disabled="bill.total" v-model="bill.cost" class="form-control text-center" placeholder="0.00">
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="col-lg-12 mt-3">
                                    <label for="notes">Notes</label>                                                                        
                                    <input type="text" class="form-control" v-model="bill.notes">
                                </div>
                            </div>                              
                        </div>

                        <!-- close -->
                        <div class="text-center mb-3 mt-4">       
                            <button class="btn btn-success" v-bind:disabled="modal.saveBtn" @click="markPaid('mark')" v-text="modal.marker"></button>                                                
                            <button class="btn btn-primary" v-if="modal.updateBtn" @click="markPaid('update')">Update</button>
                            <button class="btn btn-outline-secondary" @click="closeForm">
                                Close
                            </button>                    
                        </div>
                        <!--</form>-->

                        <div class="text-center my-3" v-if="!modal.updateBtn && billType === 'Payment'">
                            Once you marked this bill as <strong>"Paid"</strong>,  it will no longer appear as a <strong>Payment</strong> when you add new expenses.
                        </div>

                    </div>
                </div>
            </div>
        </transition>

    </div>
</template>

<script>
    var moment = require('moment');

    // Date picker
    import Datepicker from 'vuejs-datepicker';

    // Draggable
    //import draggable from 'vuedraggable'
    export default {        
        components: {
            //draggable,
            Datepicker,
        },
        data(){
            return{                
                globalModal: false,
                modal:{
                    header:'',
                    body: '',
                    marker: 'Mark As Paid',
                    updateBtn: false,
                    saveBtn: false,
                },                                
                rows:[],                
                moment: moment,                
                status: [],
                billStatus: '',
                billType: '',
                editMode: true,
                bill:{
                    id: '',
                    bill: '',
                    paidon: new Date(),
                    cost: '',
                    notes: '',
                    total: false,
                },
                success: false,
                changes: false,                
                addedRow: true,
                saveButton: true,
                showCalendar: false,
            }
        },
        mounted(){            
            // Prepare bills
            this.prepareBills();            
        },
        created(){
            let $this = this;
            eventBus.$on('updateOrder', function(payload){
                // console.log('Accessed external component todopayment.vue')
                // console.log("Payload: " + payload)

                /**
                 * Note: 
                 * Payload - any data pass thru event
                 */ 
                $this.prepareBills(); 
            })
        },
        computed:{
            firstBatch(){
                let filter = new RegExp('1', 'i')    
                return this.rows.filter(el => el.payment_order.match(filter))                                
            },
            secondBatch(){
                let filter = new RegExp('2', 'i')    
                return this.rows.filter(el => el.payment_order.match(filter))                                
            },        
        },
        methods:{     
            digits(nStr){
                if(nStr == null){
                    return '0.00';
                }
                nStr += '';
                let x = nStr.split('.');
                let x1 = x[0];
                let x2 = x.length > 1 ? '.' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }
                return x1 + x2;
            },     
            closeForm:function(){                
                this.globalModal = false;
                this.success = false;                

                // No changes made by the user                
                let val =  $('#checkbox-' + this.bill.id).val();
                if(!this.changes){                    
                    if(val == 0){
                        $('#checkbox-' + this.bill.id).prop("checked", false);
                    }else{
                        $('#checkbox-' + this.bill.id).prop("checked", true);
                    }
                }
            },
            showForm:function(id, status, notes, date, cost, bill_type, total_expenses, bill){                
                
                this.changes = false;
                this.globalModal = true;
                
                this.bill.id = id;
                this.bill.bill = bill;
                this.bill.notes = notes;   
                this.billType = bill_type;
            
                // If bill type is payment
                if(bill_type === 'Payment'){
                    this.bill.cost = total_expenses;
                    this.bill.total = true;
                }else{
                    this.bill.cost = cost;
                    this.bill.total = false;
                }
                
                this.modal.marker = status == 1 ? 'Mark as Unpaid' : 'Mark as Paid';

                switch (status){
                    case '0':                        
                        this.modal.updateBtn = false;                        
                        this.bill.paidon = new Date();                        
                    break;

                    case '1':
                        this.modal.updateBtn = true;                        
                        this.bill.paidon = date;                                                                                                    
                    break;
                }

                setTimeout(function(){
                    //$('#cost_').focus();
                    $('input:text:visible:first').focus();                    
                }, 1)
                
            },            
            markPaid:function(state){

                let src =  $('#checkbox-' + this.bill.id).val();
                let updateOnly = false;
                // Paid Bills
                if(src == 1){
                    if(state === 'mark'){
                        // Mark this bill as unpaid                        
                        this.modal.saveBtn = true;                                             
                        axios.patch(`/bills/unpaid/${this.bill.id}`).then((response) =>{
                            // console.log(response.data)
                            this.rows = response.data.bills;

                            // Clear
                            this.bill.id = "";
                            this.bill.paidon = "";
                            this.bill.notes = "";
                            this.bill.cost = "";
                            this.modal.saveBtn = false;

                            // Notify changes
                            this.changes = true;
                            this.success = false;
                            
                            this.globalModal = false;
                            
                            // eventBus activePayment                            
                            eventBus.$emit('activePayments', true); 
                            
                            // eventBus total expenses from paymentorder.vue
                            eventBus.$emit('updateChart', true);                       
                        }).catch(err => console.log(err))

                        return;
                    }else{
                        // Update this bill but remained as marked                        
                        updateOnly = true;
                    }                   
                }
            
                // UnPaid Bills
                // Mark as Paid
                this.modal.saveBtn = true;                                
                axios.patch(`/bills/paid/${this.bill.id}`,{
                    'bill_date': moment(this.bill.paidon).format('YYYY-MM-DD h:mm:ss'),                                                                                
                    'bill_notes': this.bill.notes,
                    'bill_cost' : this.bill.cost,
                }).then( (response)=>{
                    // console.log(response.data)
                    
                    if(response.data.success){
                        this.rows = response.data.bills;
                     
                        // notify that changes was made only if updateOnly is false
                        if(!updateOnly){                                  
                            this.changes = true;
                            this.globalModal = false;                            
                        }else{
                            $('#checkbox-' + this.bill.id).prop("checked", true);                            
                            this.success = true;
                        }
                                                                                
                        this.modal.saveBtn = false;

                        // eventBus activePayment
                        eventBus.$emit('activePayments', true);

                        // eventBus total expenses from paymentorder.vue
                        eventBus.$emit('updateChart', true);                       
                    }                    
                }).catch(err => console.log(err))                                        
            },           
            addBill(){
                $(".tooltip").tooltip('hide');
                this.$router.push({ name: 'addbill'})
            },                     
            customFormatter(date) {                
                return moment(date).format('dddd, MMMM D, YYYY h:mm:ss a');
            },
            paymentDate(date){                                
                if(date != null){               
                    return moment(date).format('MMMM D, YYYY h:mm:ss a')
                }                
            },
            prepareBills(){
                axios.get(`/bills`).then((response) =>{  
                    // console.log(response.data)                  
                    this.rows = response.data;                             
                })
            },           
            addRow(){
                // REFERENCE:
                // Edit mode
                this.rows.push({});
                this.saveButton = false;
            
                // Get table length and activate added row
                let x = $('#billTable tr').length;
                setTimeout(function(){
                    $('#billTable tr:nth-child(' + x + ') input').prop("disabled", false);       
                    $('#datepicker' + x).removeClass('datepicker_cls')
                }, 1)                        

                let $this = this;
                setTimeout(function(){                    
                    // Focus on first enabled input                    
                    $('input:enabled:visible:first').focus();                    
                }, 1)              
            },
        }
        
    }
   
</script>

<style lang="scss" scoped>
 
input, textarea{
    border-radius: 0;
    font-size: 13px;
    resize:none;
}
input[type="checkbox"]{
    cursor: pointer;    
    box-shadow:none;        
}
 

/* set input as display */
input:disabled{
    background-color: #343a40;
    color:white;
    border:1px solid rgba(0,0,0, .35);
}
input:disabled:hover{
    cursor: all-scroll;
}

button{
    border-radius: 0;
}

.no-border{
    background-color:transparent;
    border:none;
    color:white;
    outline:none;
}

@media(max-width: 767px){
    ._hide{
        display:none;
    }
}

.table{
    /*cursor: move;    */
    td, th{
        vertical-align: baseline;     
    }

    td:nth-child(1){        
        width: 12%;
    }
    td:nth-child(2){        
        width: 10%;
    }
    td:nth-child(3){        
        width: 20%;
    }
    td:nth-child(4){        
        width: 20%;
    }
    td:nth-child(5){        
        width: 10%;
    }
    td:nth-child(6){        
        width: 20%;
    }
    td:nth-child(7){        
        width: 8%;
    }
}

// ios
input[type="checkbox"].ios8-switch {
    position: absolute;
    margin: 8px 0 0 16px;    
}
input[type="checkbox"].ios8-switch + label {
    position: relative;
    padding: 5px 0 0 50px;
    line-height: 2.0em;
}
input[type="checkbox"].ios8-switch + label:before {
    content: "";
    position: absolute;
    display: block;
    left: 0;
    top: 0;
    width: 40px; /* x*5 */
    height: 24px; /* x*3 */
    border-radius: 16px; /* x*2 */
    background: #fff;
    border: 1px solid #d9d9d9;
    -webkit-transition: all 0.3s;
    transition: all 0.3s;
}
input[type="checkbox"].ios8-switch + label:after {
    content: "";
    position: absolute;
    display: block;
    left: 0px;
    top: 0px;
    width: 24px; /* x*3 */
    height: 24px; /* x*3 */
    border-radius: 16px; /* x*2 */
    background: #fff;
    border: 1px solid #d9d9d9;
    -webkit-transition: all 0.3s;
    transition: all 0.3s;
}
input[type="checkbox"].ios8-switch + label:hover:after {
    box-shadow: 0 0 5px rgba(0,0,0,0.3);
}
input[type="checkbox"].ios8-switch:checked + label:after {
    margin-left: 16px;
}
input[type="checkbox"].ios8-switch:checked + label:before {
    background: #55D069;
}

// ios small
input[type="checkbox"].ios8-switch-sm {
    margin: 5px 0 0 10px;
}
input[type="checkbox"].ios8-switch-sm + label {
    position: relative;
    padding: 0 0 0 32px;
    line-height: 1.3em;
}
input[type="checkbox"].ios8-switch-sm + label:before {
    width: 25px; /* x*5 */
    height: 15px; /* x*3 */
    border-radius: 10px; /* x*2 */
}
input[type="checkbox"].ios8-switch-sm + label:after {
    width: 15px; /* x*3 */
    height: 15px; /* x*3 */
    border-radius: 10px; /* x*2 */
}
input[type="checkbox"].ios8-switch-sm + label:hover:after {
    box-shadow: 0 0 3px rgba(0,0,0,0.3);
}
input[type="checkbox"].ios8-switch-sm:checked + label:after {
    margin-left: 10px; /* x*2 */
}


.customClass{
    background-color: red;
}

.cursor-td{
    cursor: pointer!important;
}

.update_container{
    position: relative;
    span{
        position: absolute;
        top:-10px;
        right:0;
    }
}

</style>