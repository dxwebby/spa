<template>
    <span>
         <!-- header -->
        <div class="row">
            <div class="col-lg-12">
                <activeComponent v-bind:active="'Dashboard / Edit Expenses /'"/>
            </div>
        </div>
        <!--/.header -->

        <!-- column-box -->   
        <div class="row column-box">
            <div class="col-lg-12 mt-5 text-center">
                <h4>Change Payment Type</h4>                
            </div>
            <div class="col-lg-12">                        
                <div class="row mt-5">
                    <div class="col-lg-6 offset-lg-3">
                        <form>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="payment">Payment</label>
                                    <select id="tender" v-on:change="formButtons = false" v-model="options" class="custom-select">                                
                                        <option v-for="payment in payments" :key="payment.id" v-bind:value="payment.id">{{payment.payment}}</option>
                                    </select>
                                </div>                                             
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="payment">Cost</label>
                                    <input type="text" v-on:keypress="formButtons = false" v-model="cost" class="form-control" v-bind:class="{'is-invalid' : amountError}">
                                    <div class="invalid-feedback">Please enter a valid number (max:12)</div>
                                </div>
                            </div>  
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="payment">Date Purchased</label><br/>
                                    <span class="my-auto" v-text="moment(purchased).format('MMMM d, Y @ hh:mm:s A')"></span>                            
                                </div>
                            </div>                      
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="payment">Notes</label>
                                    <textarea v-on:keypress="formButtons = false"  v-model="note" cols="30" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-12 my-auto text-center">
                                <button class="btn btn-primary" v-bind:disabled="formButtons" v-on:click.prevent="editItem">Update</button>
                                <button class="btn btn-danger" @click.prevent="deleteConfirm">Delete</button>                                                                            
                                <router-link  
                                    class="btn btn-outline-secondary"                                    
                                    :to="{name: source, params: { origin: tender } }">Back
                                </router-link>                                                            
                            </div>
                        </div>                        
                        </form>                        
                    </div>
                </div>

                <div class="row mt-3" v-if="success">
                    <div class="col-lg-4 offset-lg-4 text-center">
                        <div class="alert alert-success"><i class="fas fa-check"></i> This record has been successfully updated!</div>
                    </div>
                </div>

                <!-- /.content -->
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
                            <button class="btn btn-danger" @click.prevent="deleteItem">
                                Yes
                            </button>                    
                            <button class="btn btn-outline-primary" @click.prevent="globalModal = false">
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
    // import dx from './functions.vue';
    export default {
        props:['id', 'amount', 'tender', 'notes', 'purchased', 'origin', 'source'],
        name: 'edit',
        components:{
            //dx,
        },
        data(){
            return{                
              payments:[],
              options: '',
              cost: '',
              note: '',
              amountError: false,
              success: false,
              moment: moment,
              timer: '',
              formButtons: true,
              globalModal: false,              
              modal:{
                    header: '',
                    body: '',
              },
            }
        },
        mounted(){ 
            // Origin
            //console.log("Origin: " +  this.origin);
            //console.log("Source: " +  this.source);

            // Stop script if page refreshes
            if(typeof this.id === "undefined"){                
                this.$router.push({ name: 'dashboard' });
                return;
            }

            // Load data
            this.note = this.notes;
            this.options = this.tender;
            this.cost = this.amount;            
            
            // load payments                        
            axios.get(`/payments/getalldata`)
                 .then( (response) => {
                    this.payments = response.data;        
                 })
                 .catch(err => console.log(err));
        },
        methods:{     
            deleteConfirm(){
                this.globalModal = true;
                this.modal.header = '<i class="far fa-trash-alt"></i> Delete';
                this.modal.body = 'Are you sure you want to delete this record?';                    
            },            
            deleteItem(){                                  
                axios.post(`/delete/expenses/${this.id}`)
                    .then((response)=>{                            
                        if(response.data.success){                            
                            this.$router.push({ name: 'dashboard', params: {origin: this.origin } });
                        }
                    })
                    .catch(err => console.log(err))               
            },         
            editItem(){                                                            
                let request = axios.post(`/update/expenses/${this.id}`, {                    
                    amount: this.cost,
                    tender: this.options,
                    note: this.note,
                }).then( (response) =>{                    
                    return response.data;
                }).catch(err => console.log(err))

                request.then(result=>{                           
                    if(typeof result.amount === "undefined"){
                        // Success
                        this.amountError = false;
                        this.success = true;
                        this.formButtons = true;

                        clearTimeout(this.timer);
                        let $this = this;
                        this.timer = setTimeout(function(){
                            $this.success = false;
                        }, 3000)
                    }else{
                        // Error                        
                        this.amountError = true;
                    }
                })
                
            }
        }
    }
</script>

<style scoped>
input, select, textarea, button, .btn{
    border-radius: 0!important;
}

textarea{
    resize:none;
}

label{
    font-weight: 600;
}
</style>