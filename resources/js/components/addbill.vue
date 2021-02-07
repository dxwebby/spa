<template>
    <div>             
        <div class="row">
            <div class="col-lg-12">
                <activeComponent v-bind:active="'Dashboard / Manage Bills /'"/>
            </div>
        </div>     

        <div class="_box">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="route-h4">                        
                        My Bills
                        <span class="float-right" v-html="message"></span>                    
                    </h4>                    
                </div>            
            </div>   

            <form v-on:submit.prevent="saveBill"> 
            <div class="row w-75 m-auto">               
                <div class="col-lg-12 text-center mt-4">
                    <h5>Bills Management</h5>
                </div>
                
                <div class="col-lg-8 offset-lg-2 col-md-12 mt-4 p-0">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="bill">Current Bill</label>                                                         
                                <div class="input-group">                                
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-file-invoice"></i></span>
                                    </div>
                                    <select id="bill_select" v-model="bill_select" v-on:change="billTask" class="custom-select">
                                        <option value="new">Add New Bill</option>
                                        <option v-for="row in sortedBills" :key="row.index" :value="row.id">{{row.bill}}</option>
                                    </select>                                                        
                                </div>
                            </div>
                        </div><!-- /.current bill -->

                        <div class="col-lg-6">
                            <label for="title">Title</label>            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-file-invoice-dollar"></i></span>
                                </div>
                                <input type="text" v-model="bill" ref="bill_" class="form-control" 
                                        v-bind:class="{ 'is-invalid': errors.title }"
                                        placeholder="Bill title" aria-describedby="basic-addon1">
                                <div class="invalid-feedback">This field is required and must be unique. (min: 3 chars)</div>
                            </div>
                        </div><!-- /.title -->

                        <div class="col-lg-6">
                            <label for="title">Bill Type</label>                            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-credit-card"></i></span>
                                </div>
                                <select class="custom-select" v-on:change="focusCost" v-model="bill_type">
                                    <option value="Payment">Payment</option>
                                    <option value="Bill">Bill</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div><!-- /.bill type -->     

                        <div class="col-lg-6">
                            <label for="title">Cost</label>                            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control" ref="cost" v-model="cost" :disabled="bill_type == 'Payment'" placeholder="0.00">
                            </div>
                        </div><!-- /.cost -->

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="batch">Payment Batch Order</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-th-list"></i></span>
                                    </div>                            
                                    <select class="custom-select" ref="batch_" v-bind:class="{'is-invalid': errors.batch }" v-model="batch">
                                        <option value="">Select</option>
                                        <option value="1">Batch 1</option>
                                        <option value="2">Batch 2</option>
                                    </select>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div> 
                            </div>
                        </div><!-- /.batch -->
                        
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="notes">Payment Date</label>
                                <div class="date-container">                                    
                                    <button class="calendar"><i class="far fa-calendar-alt"></i></button>
                                    <Datepicker v-model="due_date"                                                 
                                                :format="customFormatter"                                               
                                                input-class="customInput text-center form-control">
                                    </Datepicker>                                             
                                </div>                                          
                            </div>
                        </div><!-- /.notes -->

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="notes">Notes (Optional)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="far fa-sticky-note"></i></span>
                                    </div>
                                    <input type="text" v-model="notes" class="form-control" placeholder="...">
                                </div>                                
                            </div>
                        </div><!-- /.notes -->
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="form-group text-center mt-4">
                        <button v-bind:disabled="addMode" @click.prevent="saveBill" 
                                class="btn btn-outline-success mr-2" 
                                data-toggle="tooltip" title="Save Bill">
                                <span v-text="addText"></span></button>                  
                        <button v-bind:disabled="delMode" 
                                @click.prevent="globalModal = true, 
                                modal.proceed = true,
                                modal.header='Delete Confirmation', 
                                modal.body = 
                                'Are you sure you want to delete this bill?<br/>' + 
                                '<strong>Warning:</strong> <br/><br/>' +
                                'If this bill is a payment type, all expenses associated with it will also be marked as an <strong class=text-danger>Unlisted Expenses</strong>.' "
                                class="btn btn-outline-danger mr-2" 
                                data-toggle="tooltip" title="Delete Bill">Delete</button>                        
                        <router-link to="/" class="btn btn-outline-secondary">
                            Back</router-link>
                    </div>
                </div><!-- /.buttons -->

                <div class="col-lg-12 footer-padding text-center mt-4">
                    <strong class="text-danger">WARNING:</strong><br/><br/>
                    * All expenses associated with any <strong>Bills</strong> that are <strong>Payment type</strong> will be marked as <span class="text-danger"><strong>Unlisted Payments</strong></span>
                    and will also be excluded from the computation when deleted or changed type to other than <span class="text-primary">Payment</span>.                    
                    <div class="mt-3">
                        To view, restore, or delete all unlisted bills and expenses, go to <span class="text-primary">Expenses > Unlisted Expenses</span> on your navigation.
                    </div>
                </div><!-- /.notice -->

            </div>
            </form>             
        </div>

        <!-- Global Modal (dynamic) -->
        <transition name="modal" v-if="globalModal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container">

                        <!-- header -->
                        <div class="modal-header">                            
                            <span v-html="modal.header"></span>
                        </div>

                        <!-- contents -->
                        <div class="modal-body text-center">
                            <span v-html="modal.body"></span>
                        </div>
                    
                        <!-- close -->
                        <div class="text-center mb-3">      
                            <button v-if="modal.proceed" v-bind:disabled="delMode" class="btn btn-danger" @click.prevent="deleteBill">Proceed</button>                                                 
                            <button class="btn btn-outline-primary" @click="globalModal = false">
                                Close
                            </button>                    
                    </div>
                    </div>
                </div>
            </div>
        </transition>

    </div>
</template>

<script lang>
    // Date picker
    import Datepicker from 'vuejs-datepicker';
    var moment = require('moment');
    export default {
        name: 'addbill',
        components: {            
            Datepicker,
        },
        data(){
            return{                
                moment: moment,
                message: '&nbsp;',
                timer: '',
                rows: [],
                id: '',
                bill: '',
                cost: '',
                original_bill: '',
                bill_select: 'new',
                bill_type: 'Payment',
                bill_type_original: '',
                due_date: new Date(),
                notes: '',
                batch: '',
                addMode: false,
                delMode: true,
                addText: 'Add',
                errors:{
                    title: false,
                    batch: false,                    
                },
                globalModal: false,
                modal:{
                    header: '',
                    body: '',
                    proceed: true,
                },
            }
        },
        mounted(){
            this.prepareBill();                                  
            this.$refs.bill_.focus();                    
        },
        computed:{
            sortedBills(){                                
                return this.rows.sort((a,b) => a.bill > b.bill ? 1 : -1);                
            },
        },        
        methods:{
            prepareBill(){                
                axios.get(`/bills`).then((response) =>{                    
                    this.rows = response.data;                                             
                })
            },
            focusCost(){
                // Warn user if current Bill type as a "Payment" was changed to "Other" or "Bill"
                
                if(this.bill_type_original == "Payment"){
                    // If bill_type_original was changed
                    if(this.bill_type_original != this.bill_type){
                        this.globalModal = true;
                        this.modal.proceed = false;
                        this.modal.header = "<span class='text-danger'>Warning</span>";
                        this.modal.body = "You're about to update this bill that's marked as a <strong>'Payment'</strong>! <br/><br/>" +
                                          "If you changed the <strong>Bill Type</strong> to other than <strong>Payment</strong>, all expenses related to this bill " +
                                          "will no longer be shown in computation and will be marked as <strong>'Unlisted Expenses'</strong>.<br/><br/>" +
                                          "*You can always restore this bill as a <strong>Payment</strong> along with any data related to it.";
                    }
                }

                let $this = this;
                setTimeout(function(){
                    $this.$refs.cost.focus();
                }, 1)                
            },
            customFormatter(date) {
                return 'Every ' + moment(date).format('Do');
                // return moment(date).format('dddd, MMMM D, YYYY h:mm:ss a');
            },
            billTask(){            
                if(this.bill_select === "new"){
                    // Add new bill
                    this.delMode = true;
                    this.addText = "Add";

                    // Clear forms and errors
                    this.bill = "";
                    this.batch = "";
                    this.cost = "";

                    this.errors.title = false;
                    this.errors.batch = false;
                }else{
                    // Update bill
                    this.delMode = false;
                    this.errors.title = false;
                    this.errors.batch = false;
                    this.addText = "Update";
                    
                    // Get payment_order from this.rows
                    for(let i=0; i < this.rows.length; i++){
                        let obj = this.rows[i];                        
                        if(obj.id == this.bill_select){                            
                            this.id = obj.id;
                            this.batch = obj.payment_order;
                            this.notes = obj.notes;
                            this.bill_type = obj.bill_type;
                            this.bill_type_original = obj.bill_type;
                            this.original_bill = obj.bill;
                            this.cost = obj.cost;
                        }
                    }
              
                    let selText = $('#bill_select option:selected').text();                    
                    this.bill = selText;                                                               
                }
                    this.$refs.bill_.focus();
            },          
            saveBill(){
                /**
                 * delMode == true to save new data
                 */

                // Clear tooltip
                $(".tooltip").tooltip('hide');

                if(this.delMode){
                    // Disable save button to avoid multiple submission
                    this.addMode = true;

                    // New Bill                       
                    axios.post(`/bills/new`,{
                        'bill' : this.bill,
                        'bill_type' : this.bill_type,
                        'due_date' : moment(this.due_date).format('YYYY-MM-DD'),
                        'cost' : this.cost,
                        'payment_order' : this.batch,        
                        'notes' : this.notes,
                    }).then((response)=>{                        

                        // Validation
                        let d = response.data;   
                        // console.log(d)               

                        this.errors.title = typeof d.bill === 'undefined' ? false : true;
                        this.errors.batch = typeof d.payment_order === 'undefined' ? false : true;                                                
                    
                        // Autofocus                        
                        if(this.errors.title){
                            this.$refs.bill_.focus();
                        }else if(this.errors.batch){
                            this.$refs.batch_.focus();
                        }

                        // Success
                        if(d.success){                         
                            const newBill = {
                                id: d.id,
                                bill: this.bill,
                                bill_type: this.bill_type,
                                payment_order: this.batch,  
                                notes: this.notes,                          
                            }

                            let bill = this.bill;

                            // Clear form
                            this.bill = "";
                            this.notes = "";                            
                            this.$refs.bill_.focus();                            

                            // Update rows
                            this.rows = d.bills;
                            // this.rows = [...this.rows, newBill];                                                   
                            
                            // Show success
                            clearTimeout(this.timer);
                            this.message = '<i class="fas fa-check-double"></i> ' + bill + " has been successfully added!";
                            let $this = this;
                            this.timer = setTimeout(function(){
                                $this.message = "&nbsp;";
                            }, 10000);
                        }
                        
                    }).catch(err => console.log(err))
                }else{
                    // Update Bill                                  
                    axios.patch(`/bills/update/${this.bill_select}`, {                        
                        'id': this.id,
                        'bill' : this.bill,
                        'original_bill' : this.original_bill,
                        'bill_type' : this.bill_type,
                        'due_date' : moment(this.due_date).format('YYYY-MM-DD'),
                        'cost': this.cost,
                        'payment_order' : this.batch,        
                        'notes' : this.notes,
                    }).then((response)=>{
                        let d = response.data;  
                        // console.log(d)
                        
                        // update original bill
                        this.original_bill = this.bill;
                        
                        /*
                        const updatedBill = {
                            id: d.id,
                            bill: this.bill,
                            payment_order: this.batch,  
                            notes: this.notes,                          
                        } 
                        */     

                        // Validation                                               
                        this.errors.title = typeof d.bill === 'undefined' ? false : true;
                        this.errors.batch = typeof d.payment_order === 'undefined' ? false : true;                                                

                        // Autofocus                        
                        if(this.errors.title){
                            this.$refs.bill_.focus();
                        }else if(this.errors.batch){
                            this.$refs.batch_.focus();
                        }

                        if(d.success){
                            // save updated bills
                            this.rows = d.bills;

                            this.$refs.bill_.focus();

                            // Show success
                            clearTimeout(this.timer);
                            this.message = '<i class="fas fa-pen-alt"></i> Record has been successfully updated!';
                            let $this = this;
                            this.timer = setTimeout(function(){
                                $this.message = "&nbsp;";
                            }, 10000);
                        }
                    }).catch(err => console.log(err))
                }

                // Re-enable add/update button
                  this.addMode = false;
                
            },            
            deleteBill(){    
                // Disable delete button
                this.delMode = true;
                
                axios.delete(`/bills/delete/${this.bill_select}`).then((response)=>{    
                    // debug
                    // console.log(response.data)                
                    if(response.data.success){                        
                        this.rows = this.rows.filter(el => el.id !== this.bill_select);
                        this.bill_select = "new";
                    }

                    // Clear form
                    this.bill = "";
                    this.delMode = true;
                    this.notes = "";
                    this.addText = "Add";

                    this.$refs.bill_.focus();

                    // Close confirmation box
                    this.globalModal = false;
                    
                    // Show success
                    clearTimeout(this.timer);
                    this.message = '<i class="fas fa-trash"></i> Record has been deleted!';
                    let $this = this;
                    this.timer = setTimeout(function(){
                        $this.message = "&nbsp;";
                    }, 10000);
                })
            },
        }
    }
</script>

<style lang="scss" scoped>

.footer-padding{
    padding-bottom: 60px;
}

input, select, button, .btn, .input-group-text{
    border-radius: 0!important;
}

i{
    font-size: 16px;
}

%line{
    position: absolute;
    content: '';
    left:-6px;
    top:-1px;
    width: 6px;
    height: 37px;    
} 
.btn-outline-success, .btn-outline-danger, .btn-outline-secondary{
    position: relative;    
    &:before{
        @extend %line;     
    }
}

.btn-outline-success:before{ background-color: #38c172;}
.btn-outline-danger:before{ background-color: crimson; }
.btn-outline-secondary:before{ background-color: #6c757d; }

label{
    margin-left: -20px;
}
.font12{
    font-size: 12px;
}


.date-container{
    position: relative;
    .calendar{
        position: absolute;
        top:0;
        left:0;
        z-index:100;
        background-color: #e9ecef;
        text-align:center;
        width: 40px;
        height: 37px;
        border:1px solid rgba(0,0,0, .1);
    }
}

.input-group-text{    
    width: 40px;
    text-align:center;
}

</style>