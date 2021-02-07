    <template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <activeComponent v-bind:active="'Dashboard / Unlisted Bills /'"/>                
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-lg-12 mb-5 text-center">
                There are the list of deleted bills and expenses. 
                <div class="mt-3">
                    <i class="fas fa-trash-restore font20 text-success"></i> = <strong class="text-success">restore</strong> bill and all the expenses related with it.
                    <br/>
                    <i class="fas fa-trash-alt font20 text-danger"></i> <strong class="text-danger">permanently delete</strong> bill and all the expenses associated with it.
                </div>                
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12">
                        <h6>Unlisted Bills (Current)</h6>
                        <table class="table table-striped table-bills table-bordered text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Bills</th>
                                    <th>Cost</th>
                                    <th>Deleted Date</th>
                                    <th>Action</th>
                                </tr>                
                            </thead>
                            <tbody>
                                 
                                <tr v-for="(bill, ctr) in bills" :key="bill.id" v-on:click="selectActive(ctr, bill.id, bill.bill_batch)">
                                    <td>{{ctr+1}}</td>
                                    <td>{{bill.bill}}</td>
                                    <td>                                        
                                        <span v-if="bill.cost != null">${{bill.cost}}</span>
                                        <span v-else>$0.00</span>
                                    </td>
                                    <td>{{bill.delete_at | dateFormat}} </td>
                                    <td>
                                        <button @click="confirmation('restore', bill.id, bill.bill)" title="Restore this payment" class="btn btn-primary"><i class="fas fa-trash-restore"></i></button>
                                        <button @click="confirmation('delete', bill.id, bill.bill)" title="Permanently delete this payment" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12 mt-2">
                        <h6>Bill History</h6>
                        <table class="table table-striped  text-center table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Bill</th>
                                    <th>Amount Paid</th>
                                    <th>Date Paid</th>
                                </tr>                                
                            </thead>
                            <tbody>
                                <tr v-if="history.length <=0 ">                               
                                    <td colspan="6" class="text-center">
                                        <div>There are no records to display.</div>                                    
                                    </td>
                                </tr>
                                <tr v-for="data in history" :key="data.id">
                                    <td>{{data.bill}}</td>
                                    <td>${{data.cost}}</td>
                                    <td>{{data.paid_date | dateFormat}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>                
            </div>
            <div class="col-lg-6">
                <h6>Related Expenses</h6>
                <table class="table table-striped table-bordered text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cost</th>
                            <th>Category</th>
                            <th>Date Purchased</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="expenses.length <=0 ">                               
                            <td colspan="6" class="text-center">
                                <div>There are no records to display.</div>                                    
                            </td>
                        </tr>
                        <tr v-for="expense in expenses" :key="expense.id">
                            <td>{{expense.bill.bill}}</td>
                            <td>${{expense.amount}}</td>
                            <td>{{expense.category}}</td>
                            <td>{{ expense.created_at | dateFormat }}</td>                                
                        </tr>
                        <tr>
                            <td>...</td>
                            <td>...</td>
                            <td>...</td>
                            <td>...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                            <button class="btn" v-bind:class="actionMode ? 'btn-danger': 'btn-success'"  @click="!actionMode ? action('restore') : action('delete')" v-text="modal.button"></button>                                                  
                            <button class="btn btn-outline-secondary" @click="globalModal = false">
                                Close
                            </button>                    
                    </div>
                    </div>
                </div>
            </div>
        </transition>

    </div>
</template>

<script>
    import activeComponent from './activeComponent';
    var moment = require('moment');
    export default {
        components:{
            activeComponent,
        },
        data(){
            return{
                moment:moment,
                expenses: [],
                bills: [],
                history: [],
                filterBy: '',
                id: '',
                globalModal: false,     
                modal:{
                    header: '',
                    body: '',
                    button: '',
                },
                actionMode: false,
            }
        },
        mounted(){            
            this.prepareData();            
        },
        filters:{
            dateFormat:function(value){
                return moment(value).format('MMMM Do YYYY');
            },
        }, 
        methods:{
            confirmation:function(state, id, bill){
                this.id = id;
                switch (state){
                    case "restore":
                        this.actionMode = false;
                        this.globalModal = true;
                        this.modal.header ='<i class="fas fa-trash-restore font16 text-success"></i> ' + bill;
                        this.modal.body = "Are you sure you want to restore this bill?";
                        this.modal.button = "Yes";
                    break;

                    case "delete":
                        this.actionMode = true;
                        this.globalModal = true;
                        this.modal.header ='<i class="fas fa-exclamation-triangle font16 text-danger"></i> ' + bill;
                        this.modal.body = "All expenses related to this bill will also be deleted.<br/>Are you sure you want to delete this bill?";
                        this.modal.button = "Yes";
                    break;
                }
            },
            action:function(state){    
                let results = "";           
                if(state === "restore"){
                    // restore payment
                    // console.log('restore' + id)
                    axios.patch(`/unlisted/action`,{
                        'id': this.id,
                    }).then(response =>{
                        //console.log(response.data);
                        results = response.data;
                        this.bills = response.data.bills;                        

                        // Hide modal
                        this.globalModal = false; 
                        
                        let $this = this;                                
                        if(!results){                                                     
                            setTimeout(function(){
                                $this.history = '';
                                $this.expenses = '';
                            }, 100)
                            
                            return;
                        }                        
                        this.selectActive('', this.bills[0].id, this.bills[0].bill_batch)  
                    })
               
                }else{
                    // delete payment                         
                    axios.delete(`/unlisted/delete/${this.id}`).then(response =>{
                        // console.log(response.data);
                        results = response.data;
                        this.bills = response.data.bills;

                        // Hide modal
                        this.globalModal = false;                                            

                        let $this = this;                                
                        if(!results){                                                     
                            setTimeout(function(){
                                $this.history = '';
                                $this.expenses = '';
                            }, 100)
                            
                            return;
                        }                        
                        this.selectActive('', this.bills[0].id, this.bills[0].bill_batch)   
                    })                   
                }
                    
            },
            prepareData:function(){
                axios.get(`/unlisted`).then((response) => {                                                           
                    if(!response.data.bills){                       
                        return;
                    }

                    this.bills = response.data.bills;

                    // Initial
                    this.selectActive('', this.bills[0].id, this.bills[0].bill_batch)
                    
                    setTimeout(function(){
                        $('.table-bills tbody tr:nth-child(1)').addClass('selRow');
                    }, 100)                                        
                })
            },
            selectActive:function(x, id, batch){  
                     
                // Highlight selected
                let rowIndex = x+1;                
                $('.table-bills tbody tr').removeClass('selRow');                
                $('.table-bills tbody tr:nth-child(' + rowIndex + ')').addClass('selRow');            

                // Load data for expenses            
                axios.post(`/unlisted/${id}`, {
                        'batch': batch,
                    }).then((response)=>{                         
                        this.expenses = response.data.expenses;
                        this.history = response.data.history;                                                
                })

            },
        }
    }
</script>

<style lang="scss" scoped>
h6{
    font-weight: 600;
    margin-bottom: 10px;
}
button{
    border-radius: 0;
    font-size: 13px;    
}

.table{
    th, td{
        vertical-align: baseline;
    }    
}
.table-bills{
    td{
        cursor: pointer;
    }
}

.selRow{    
    background-color: rgba(112,112,112, 1)!important;
    color:white!important;
    transition: 1s ease-in-out;
}
 
</style>