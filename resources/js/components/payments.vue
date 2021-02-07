<template>
    <span>
         <!-- header -->
        <div class="row">
            <div class="col-lg-12">
                <activeComponent v-bind:active="'Dashboard / Add Payments /'"/>
            </div>
        </div>
        <!--/.header -->

        <!-- column-box -->   
        <div class="row column-box">        
            <div class="col-lg-6 offset-lg-3">
                <div class="row box mt-5">
                    <div class="col-lg-12 mb-4 text-center">
                        <h5>Add Payment</h5>       
                    </div>
                    <div class="col-lg-12">
                        <form>
                        <div class="form-group">
                            <label class="col-lg-10 offset-lg-1" for="select">List of Payments</label>
                            <select id="payment" v-model="payment" v-on:change="managePayment" class="custom-select col-lg-8 offset-lg-2">
                                <option value="add">Add New Payment</option>
                                <option v-for="payment in payments" :key="payment.id" v-bind:value="payment.id">{{payment.payment}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-10 offset-lg-1" for="payment">Payment</label>
                            <input type="text" id="ref_title" v-bind:class="{'is-invalid': err_Payment}" class="form-control col-lg-8 offset-lg-2" ref="ref_title" v-model="data.title" placeholder="Wells Fargo, Walmart, Capital One...">
                            <div class="invalid-feedback text-center">This field is required. (min: 3 chars)</div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-10 offset-lg-1" for="account">Account #</label>
                            <input type="text" class="form-control col-lg-8 offset-lg-2" v-model="data.account" placeholder="(optional)">
                        </div>
                        <div class="form-group text-center mt-4">
                            <button class="btn btn-success" @click.prevent="newPayment" v-text="data.button"></button>
                            <button class="btn btn-danger" v-bind:disabled="deleteButton" @click.prevent="deleteConfirmation">Delete</button>              
                            <button class="btn btn-secondary" @click.prevent="cancelChanges">Cancel</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.column-box -->

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
                            <button class="btn btn-danger" @click="deleteItem">
                                Yes
                            </button>                                  
                            <button class="btn btn-outline-primary" @click="globalModal = false">
                                Cancel
                            </button>                    
                    </div>
                    </div>
                </div>
            </div>
        </transition>

    </span>
</template>

<script>        
    var moment = require('moment');    
    export default {
        props:['id'],
        name: 'payments',
        components:{
            
        },
        data(){
            return{                
                globalModal: false,              
                modal:{
                    header: '',
                    body: '',
                },
                payments: [],
                payment: 'add',
                data:{
                    title: '',
                    account: '',
                    button: 'Add Payment',
                },
                err_Payment: false,
                deleteButton: true,
            }
        },
        mounted(){ 
            this.paymentsList();
            let $this = this;
            this.$refs.ref_title.focus();            
        },
        methods:{  
            newPayment(){                
                this.$refs.ref_title.focus();
                axios.post(`/newpayment`,{
                    title: this.data.title,
                    account: this.data.account,                    
                }).then((response) =>{
                    
                    let d = response.data;
                    if(typeof d.title === "undefined"){                                             
                        // Push data                        
                        this.payments.push({ payment: this.data.title, id: d });

                        // Clear
                        this.data.title = "";
                        this.data.account = "";     
                        this.err_Payment = false;    
                    }else{
                        this.err_Payment = true;
                    }
                })
            },
            cancelChanges(){
                this.data.title = "";
                this.data.account = "";
                this.data.button = "Add Payment";
                this.payment = "add";     
                this.deleteButton = true;
                this.$refs.ref_title.focus();    
            },
            managePayment:function(){                
                //this.data.title = $('#payment option:selected').text();  
                
                if(this.payment === "add"){
                    // prepare form                    
                    this.cancelChanges();                  
                    return;
                }
                                
                axios.get(`/payments/data/${this.payment}`).then((response)=>{                    
                    let d = response.data;                    
                    this.data.title = d.payment;
                    this.data.account = d.account;
                    this.data.button = "Update Changes";    
                    this.deleteButton = false;                

                          
                    $('#ref_title').focus().select();                
                }).catch(err => console.log(err))                                
            },
            deleteConfirmation(){
                this.globalModal = true;
                this.modal.header = "Delete Confirmation";
                this.modal.body = "Are you sure you want to delete this record?"
            },
            deleteItem:function(){                
                axios.post(`/payment/delete/${this.payment}`).then((response)=>{                    
                    if(response.data === "success"){
                        this.payments = this.payments.filter(payments => payments.payment !== this.payment);                             
                    }
                })
            },
            paymentsList(){
                // load payments                   
                axios.get(`/payments/getalldata`)
                     .then( (response) => {
                        this.payments = response.data;                         
                      })
                      .catch(err => console.log(err));
            },
        }
    }
</script>

<style scoped>
input, select, textarea, button, .btn{
    border-radius: 0!important;
}

label{
    font-weight: 600;
}

.box{
    padding: 20px;
    -webkit-box-shadow: 0px 10px 11px -1px rgba(0,0,0,0.15);
    -moz-box-shadow: 0px 10px 11px -1px rgba(0,0,0,0.15);
    box-shadow: 0px 10px 11px -1px rgba(0,0,0,0.15);
    margin-left: 0px;
    margin-right: 0px;
    border: 1px solid rgba(0,0,0,0.05);
}

input:focus, select:focus{    
    box-shadow: 0px 2px 12px 0px rgba(218, 165,32, 1);    
}
.golden{
    background-color: goldenrod;
}

.form-group{
    position: relative;
}
</style>