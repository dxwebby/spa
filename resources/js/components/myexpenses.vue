    <template>
    <div>      
        <div class="row" v-if="typeof control === 'undefined'">
            <div class="col-lg-12">
                <activeComponent v-bind:active="'Dashboard / My Expenses /'"/>                
            </div>
        </div>
        
        <div class="_box mb-5" v-if="typeof control === 'undefined'">
            <div class="row">     
                <div class="col-lg-12 mb-3">
                    <h4 class="route-h4 bg-danger"><img src="https://icongr.am/clarity/bar-chart.svg?color=ffffff&size=20" alt="browse"> Charts</h4>
                </div>              
            </div>
             <div class="row mt-3 px-3">
                <div class="col-lg-12">
                    <allexpenses />
                </div>
            </div>
        </div><!-- /.charts -->

        <div v-bind:class="{ '_box': control !== 'dashboard'}">
            <div class="row">     
                <div class="col-lg-12 mb-3">
                    <h4 class="route-h4"><img src="https://icongr.am/clarity/wallet.svg?color=ffffff&size=20" alt="expenses"> My Expenses</h4>
                </div>              
            </div>
                    
            <!-- lastpoint-->                    
            <div class="row" v-bind:class="{'px3': control != 'dashboard'}">
                <div class="col-lg-6">
                    <div class="input-group justify-content-lg-start justify-content-md-center justify-content-sm-center justify-content-center">        
                        <div class="input-group-prepend">                            
                            <select class="custom-select " v-on:change="browseRecords()" v-model="batch">                        
                                <option value="current">Active</option>
                                <option value="allexpenses">All Expenses</option>
                                <option v-for="batch in batches" :key="batch.index" :value="batch.value">{{batch.text}}</option>                        
                            </select>
                        </div>
                        <div class="input-group-prepend">                            
                            <div class="dropdown" data-toggle="tooltip" data-placement="left" title="Filter records (Active Expenses)">
                                <button class="btn btn-primary dropdown-toggle interActiveBtn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <button class="dropdown-item all" v-on:click="filterBy = '', filterRecords('','alldata')" >Select All</button>                                        
                                    <button class="dropdown-item" id="filterBy" v-bind:class="''+ payment.id" v-on:click="filterBy = payment.bill, filterRecords('', payment.id)" v-for="payment in payments" :key="payment.index" v-text="payment.bill"></button>                                    
                                </div>                                        
                            </div>
                        </div>                                    
                        <div class="input-group-append">                                        
                            <div class="dropdown" data-toggle="tooltip" title="Export data to PDF">
                                <button class="btn btn-danger dropdown-toggle no-border interActiveBtn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-file-export"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a href="/dynamic_pdf/pdf/all" class="dropdown-item"><i class="fas fa-file-pdf"></i>Save as PDF {{moment().format('MMMM Y')}}</a>                                        
                                    <a @click='checkFilter($event)' id="filter" class="dropdown-item hand-cursor"><i class="fas fa-file-alt"></i>Save as PDF {{moment().format('MMMM Y')}} by Filter</a>                                               
                                    <a @click='checkFilter($event)' id="selected" class="dropdown-item hand-cursor"><i class="fas fa-file-alt"></i>Save as PDF by Selected Expenses</a>                                                                                       
                                </div>
                            </div>                                        
                        </div>                                                                        
                        <div class="input-group-append">
                            <button title="Clear Edit Form" class="btn btn-secondary cancelGlobal" @click="cancelEditForm"><i class="fas fa-redo"></i></button>                            
                        </div>                            
                        <div class="input-group-append">
                            <button title="Add New Expenses" data-toggle="tooltip" class="btn btn-primary interActiveBtn" @click="addItemForm('show')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>   
                    </div>
                </div>

                <div class="col-lg-6 mt-lg-0 mt-2">
                    <div class="input-group justify-content-lg-end justify-content-md-center justify-content-sm-center justify-content-center">
                        <div class="input-group-prepend">
                            <span class="input-group-text no-border" id="basic-addon1">Show Entries:</span>
                        </div>                    
                        <select v-model="size.default" v-on:change="pageView($event)">                        
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>                        
                            <option value="30">30</option>                                               
                        </select>                                                
                        <div class="input-group-append navNextPrevious">
                            <button id="previous" class="btn btn-outline-secondary interActiveBtn" 
                                :disabled="pageNumber === 0" 
                                @click="prevPage">
                                <i class="fas fa-angle-double-left"></i>
                            </button>                
                            <div class="input-group-prepend">
                                <span class="input-group-text no-border" v-if="paginatedData.length <= 0">Page 0 of 0</span>
                                <span class="input-group-text no-border" v-if="paginatedData.length >= 1">Page <span class="mx-1" v-text="pageNumber+1"></span> of <span class="ml-1" v-text="pageCount"></span></span>
                            </div>
                            <button id="next" class="btn btn-outline-secondary interActiveBtn" 
                                :disabled="pageNumber >= pageCount-1" 
                                @click="nextPage">
                                <i class="fas fa-angle-double-right"></i>
                            </button>
                        </div>                                    
                    </div>
                </div>                                    
            </div>            
         
            <!-- table -->
            <div class="row table-content" v-bind:class="{'px3': control != 'dashboard'}">
                <div class="col-lg-12 mt-3">               
                    <div class="table-responsive">
                        <table class="table globalTable table-striped table-bordered  table-hover" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true">
                            <caption class="text-primary">
                                <strong>Columns</strong> [Payment, Cost, Purchased] are interactive. Click each cell to show "Quick Edit" and "Change Payment" forms.
                            </caption>
                            
                            <thead class="thead-dark thead-header">
                                <tr>
                                    <th class="align-middle">    
                                        Payment
                                        <span v-on:click="sort('payment', '.payment_order')" class="float-right sorter">
                                            <div class="payment_order ml-1"> <img src="https://icongr.am/octicons/kebab-vertical.svg?color=ffffff&size=16" alt="arrow"></div>                                        
                                        </span>                                    
                                    </th>
                                    <th class="align-middle">Cost
                                        <span v-on:click="sort('cost', '.cost_order')" class="float-right sorter">
                                            <div class="cost_order ml-1">
                                                <!-- <img src="https://icongr.am/octicons/kebab-vertical.svg?color=ffffff&size=16" alt="arrow">-->
                                                <img src="https://icongr.am/octicons/kebab-vertical.svg?color=ffffff&size=16" alt="arrow">
                                            </div>                                        
                                        </span>
                                    </th>
                                    <th class="d-none d-sm-table-cell align-middle">Category
                                        <span v-on:click="sort('category', '.category_order')" class="float-right sorter">
                                            <div class="category_order ml-1"> <img src="https://icongr.am/octicons/kebab-vertical.svg?color=ffffff&size=16" alt="arrow"></div>                                        
                                        </span>
                                    </th>
                                    <th class="d-none d-md-table-cell align-middle" width="30%">Notes</th>
                                    <th class="d-none d-md-table-cell align-middle">
                                        Date Purchased
                                        <span v-on:click="sort('purchase', '.purchase_order')" class="float-right sorter">
                                            <div class="purchase_order ml-1"> <img src="https://icongr.am/octicons/kebab-vertical.svg?color=ffffff&size=16" alt="arrow"></div>                                        
                                        </span>
                                    </th>
                                    <th class="align-middle text-center">
                                    <div class="form-check">                                        
                                            <input class="form-check-input" type="checkbox" id="autoclose" @click="editAutoClose($event)">
                                            <label class="form-check-label" for="autoclose" title="(Auto-close Feature)">
                                                Action
                                            </label>
                                        </div>
                                    </th>
                                    <th class="align-middle text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="noconfirmation" @click="deleteNoConfirmation($event)">
                                            <label class="form-check-label" for="noconfirmation" title="Delete items w/out confirmation">
                                                Delete
                                            </label>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>                            
                                <tr v-if="loading">
                                    <td colspan="6" class="text-center">
                                        <div class="spinner-border spinner-border-md text-primary" role="status">                                                                                
                                        </div>
                                        <div class="mt-1">Please wait while loading data...</div>
                                    </td>                            
                                </tr>
                                
                                <tr v-if="paginatedData.length <=0 && !loading">                               
                                    <td colspan="6" class="text-center">
                                        <div>There are no records to display.</div>                                    
                                    </td>
                                </tr>
                                <!-- ctr is for setting the rowIndex when deleting item with confirmation, highlight the active cell row -->
                                <tr v-else v-for="(item, ctr) in paginatedData" :key="item.index">                                               
                                    <td data-toggle="tooltip" data-container="body" data-placement="top" title="Change payment...">                                                      
                                        <div v-on:click="triggerEdit($event,item.id, item.amount, item.bill_id, ++ctr, item.created_at )" 
                                            class="doubleClick" v-bind:id="'select' +item.id">                                                                                        
                                            {{item.bill.bill}}                                            
                                        </div>
                                    </td>
                                    <td>
                                        <div v-on:click="triggerEdit($event,item.id)" class="doubleClick" title="Edit value..." v-bind:id="'text' +item.id" v-text="'$' + digits(item.amount)">
                                        </div>                                                                        
                                        <div>                                        
                                            <input type="text"                                         
                                                v-bind:class="{'is-invalid' : amountError2}"                                             
                                                v-on:keypress="updatekeypress($event, item.id)"                                             
                                                v-on:keyup="inputMonitor($event, item.id)"                                                                                         
                                                v-bind:id="'amount' +item.id"                                                            
                                                max-length="12"                                                                          
                                                class="form-control hidden-input" v-bind:value="item.amount">                                                                                                                           
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">                                        
                                        <div v-on:click="triggerEdit($event, item.id)" class="doubleClick" title="Edit category..." v-bind:id="'cat' +item.id" v-text="item.category"></div>
                                        <select class="custom-select inputSelect" v-bind:id="'category' + item.id" v-bind:value="item.category">
                                            <option value="Groceries">Groceries</option>
                                            <option value="Restaurant">Restaurant</option>
                                            <option value="Online Purchases">Online Purchases</option>
                                            <option value="Gas">Gas</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div v-on:click="triggerEdit($event, item.id)" class="doubleClick" title="Edit note..." v-bind:id="'desc' +item.id" v-text="item.description"></div>                                    
                                        <div>                                        
                                            <input type="text" 
                                                v-on:keypress="updatekeypress($event, item.id)" 
                                                v-on:keyup="inputMonitor($event, item.id)"                                             
                                                class="form-control" v-bind:id="'note' +item.id" v-bind:value="item.description">                                            
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div v-on:click="triggerEdit($event, item.id)" class="doubleClick" 
                                                title="Change date..." v-bind:id="'date' +item.id" 
                                                v-text="moment(item.created_at).format('MMMM D, dddd, YYYY h:mm:ss A')"></div>                                                                            
                                        <div class="inputSelect" v-bind:id="'datepicker' + item.id">
                                            <Datepicker                                                 
                                                    :format="customFormatter"                                             
                                                    :value="item.created_at"  
                                                    v-bind:id="'datepicker_input' + item.id"
                                                    input-class="customInput text-center form-control">
                                            </Datepicker>            
                                        </div>                                 
                                        
                                    </td>
                                    <td class="text-center">                                                    
                                        <actions
                                                v-bind:ids="ids" 
                                                v-bind:pageNumber="pageNumber" 
                                                v-bind:pageCount="pageCount" 
                                                v-bind:item="item"      
                                                v-bind:batch_source="batch_source"                                          
                                                v-on:edit-item="editItem" 
                                                v-on:edit-mode="editState"                                            
                                        />
                                    </td>
                                    <td class="text-center">                                    
                                        <todelete v-bind:item="item" v-bind:index="++ctr" v-on:del-item="deleteConfirmation"/>
                                    </td>
                                </tr>                         
                            </tbody>
                        </table> 
                        <table class="table" style="margin-top: -16px;">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Total Expenses: ${{digits(total_expenses)}}</th>                                                            
                                </tr>
                            </thead>                    
                        </table>
                    </div>               
                </div>            
            </div>
            <!-- /.table -->           
        </div>        

        <!-- deleteConfirm modal -->
        <transition name="modal" v-if="deleteConfirm">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container">

                    <div class="modal-header">                    
                        Confirmation                    
                    </div>

                    <div class="modal-body text-center">
                        <slot name="body">
                            Are you sure you want to delete this expense?
                        </slot>
                    </div>
                    
                    <div class="text-center mb-3">                           
                        <button class="btn btn-danger" @click="deleteItem">
                            Proceed
                        </button>
                        <button class="btn btn-outline-primary" @click="cancelDelete">
                            Cancel
                        </button>                    
                    </div>
                    </div>
                </div>
            </div>
        </transition>

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
                            <button class="btn btn-outline-primary" @click="globalModal = false">
                                Close
                            </button>                    
                    </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Add Item Modal -->
        <transition name="modal" v-if="addItem">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container">

                        <!-- header -->
                        <div class="modal-header">                                  
                            <span><i class="fas fa-pencil-alt"></i> Add Expenses</span>                                                                              
                            <span v-if="itemSuccess" class="text-success float-right">New expenses was added!</span>
                        </div>

                        <!-- contents -->
                        <div v-if="activePayments == 0">
                            <div class="my-3 text-center">Sorry, you can no longer add expenses since you don't have any active payments.</div>                            
                            <div class="my-3 text-center"><button @click.prevent="addItem = false, filterRecords('','alldata')" class="btn btn-secondary">Close</button></div>
                        </div>


                        <form v-if="activePayments.length > 0">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="form-group">
                                        <label for="selectAdd">Payment</label>
                                        <select id="selectAdd" v-on:change="setFilter" v-model="selectAdd" class="custom-select">
                                            <option v-for="payment in activePayments" :key="payment.id" v-bind:value="payment.id">                                                
                                                {{payment.bill}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="inputAdd">Cost</label>
                                        <input type="text" maxlength="12" ref="newCost" id="newItem"                                                 
                                                v-on:keypress="updatekeypress($event, null)"                                             
                                                v-model="newItem.cost" 
                                                v-bind:class="{'is-invalid': itemError.cost }"
                                                class="form-control addInput" placeholder="0.00">
                                    </div>
                                </div>          
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="desc">Description</label>
                                        <select v-model="newItem.desc" class="custom-select">
                                            <option value="Groceries">Groceries</option>
                                            <option value="Restaurant">Restaurant</option>
                                            <option value="Online Purchases">Online Purchases</option>
                                            <option value="Gas">Gas</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Other">Other</option>                                            
                                        </select>
                                    </div>
                                    
                                </div>                      
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="inputAdd">Note</label>
                                        <input type="text" v-model="newItem.note" class="form-control addInput" placeholder="...">
                                    </div>
                                </div>
                            </div>                             
                        </div>
                                            
                        <!-- close -->
                        <div class="text-center mb-4" style="margin-top: -20px;">  
                            <button class="btn btn-success globalAddItem" @click.prevent="addItemForm('verify')">
                                Add
                            </button>                                                      
                            
                            <button class="btn btn-secondary" @click.prevent="addItem = false, filterRecords('','alldata') ">
                                Close
                            </button>                                                                        
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Toast Notification -->
        <div class="toast-container">
            <div class="toast" role="alert" data-autohide="false">
                <div class="toast-header">                
                    <strong class="mr-auto">Notification</strong>                    
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">                    
                </div>
            </div>
        </div>

        <!-- Side Bar -->
        <div class="right-sidebar">
            <changepayment :payments="payments" :id="sidebar.id" :amount="sidebar.cost" 
                           :payment_text="sidebar.payment_text"
                           :old_payment="sidebar.old_payment" 
                           :purchased="sidebar.purchased"
                           :rowIndex="sidebar.rowIndex"
                           v-on:update-payment="editItem"   
                           v-on:cancel-edit="editMode = false"                        
            />                     
        </div>

    </div>
</template>

<script>
    var moment = require('moment');
    import actions from './actions.vue';    
    import todelete from './todelete.vue';    
    import changepayment from './changepayment.vue';
    import allexpenses from './allexpenses.vue';    

    // Date picker
    import Datepicker from 'vuejs-datepicker';

    export default {     
        props:['origin', 'control'],        
        name: 'myexpenses',
        components:{
            actions,           
            todelete,     
            changepayment,   
            Datepicker,      
            allexpenses,          
        },        
        data(){            
            return{  
                batches:[],
                batch: 'current',    
                batch_source: false,      
                sidebar:{
                    old_payment: '',
                    new_payment: '',
                    cost: '',
                    purchased: '',
                    payment_text: '',    
                    rowIndex: '',                    
                },                
                moment: moment,
                api_token: $('#api_token').val(),    
                id: $('#id').val(),
                to_delete: '',
                to_delete_amount: 0,
                to_delete_bill_id: '',
                expenses: [],
                payments: [],
                total_expenses: '',
                filterBy: '',
                filterRecord: false,
                filteredRecord: [],
                filterDisable: true,
                bank: '',
                sorting:{
                    payment: false,
                    cost: false,
                    purchase: false,
                    category: false,
                },
                pageNumber: 0,
                size:{
                    default: 10,
                },
                deleteConfirm: false,                
                updatedItem: false,
                timer1: '',
                timer2: '',
                amountError2: false,
                newAmount: '',
                loading: true,
                perPage: 10, 
                editMode: false,      
                rowIndex: '', 
                actions:{
                    autoclose: false,
                    noconfirmation: false,
                },
                globalModal: false,     
                modal:{
                    header: '',
                    body: '',
                },
                ids: [],  
                addItem: false,
                newItem:{
                    payment: '',
                    cost: '',
                    note: '',
                    desc: 'Groceries',
                    bill_id: '',
                },
                selectAdd: '',
                itemSuccess: false,
                itemError:{
                    cost: false,
                },
                lockAutoClose: false,       
                newData: {},         
            }
        },
        mounted(){                                    
         
            $(function () {
                $('[data-toggle="tooltip"]').tooltip({
                    container : 'body'
                })
            })

            $("td").tooltip({container:'body'});

            /**
             * Note: 
             * this.control is the main loading form source.
             * 
             */
            this.paymentsList();  
            this.prepareExpenses();
            this.prepareBatches();
                        
            // Set Filter                         
            if(typeof this.origin !== "undefined"){                
                this.filterBy = this.origin;                                
                this.filterRecords('', this.origin);             
            }                                        
        },
        created: function(){           
            let $this = this;
            eventBus.$on('activePayments', function(payload){                
                $this.paymentsList();
            })

            eventBus.$on('globalModal', function(payload){        
                $this.globalModal = true;
                $this.modal.header = payload.header;
                $this.modal.body = payload.body;                
            })
        },       
        methods:{              
            prepareBatches:function(){
                axios.get(`/batches`).then((response) => {
                    // console.log(response.data)                    
                    this.batches = response.data;                  
                })
            },   
            browseRecords:function(){                
                // Reset page number
                this.pageNumber = 0;
                
                // Cancel form
                this.cancelEditForm();
                
                // Remove Change Payment Form                
                $('.right-sidebar').stop().animate({
                    'right' : -400
                }, 300, 'easeInOutExpo')

                // Highlight selected table                
                $('.table tbody tr').removeClass('selRow');
                
                let src = this.batch;   
                
                // Notify expenses source came from batch method
                this.batch_source = src === 'current' ? false : true;

                axios.get(`/batches/${src}`).then( (response) =>{
                    // console.log(response.data)
                    this.expenses = response.data.records;
                    this.filteredRecord = response.data.records;                
                    this.total_expenses = parseFloat(response.data.total).toFixed(2);
                })
            },
            prepareExpenses:function(){
                let request = axios.get(`/api/expenses/${this.id}/${this.api_token}`)
                // auth: let request = axios.get(`/allexpenses`)            
                .then( (response) => {                                                            
                    // Get total expenses
                    this.total_expenses = parseFloat(response.data.total_expenses).toFixed(2);
                    // console.log(response.data)
                    return response.data;
                })
                .catch(err => console.log(err))

                request.then(result=>{                    
                    this.loading = false;
                    this.expenses = result.expenses;
                })            
            },                                    
            customFormatter(date) {
                return moment(date).format('YYYY-MM-DD hh:mm:ss');            
            },
            checkFilter:function(e){       
                let src = e.target.id;
                // Export to PDF by Selecting Expenses
                if(src === "selected"){                                        
                    //<a href="/dynamic_pdf/pdf/all" class="dropdown-item"><i class="fas fa-file-pdf"></i>Save as PDF {{moment().format('MMMM Y')}}</a>                                        

                    if(this.batch == "current"){
                        window.location.href = "/dynamic_pdf/pdf/all"
                    }
                    else{
                        window.location.href = "/dynamic_pdf/expenses/pdf/" + this.batch;
                    }   

                    return;
                }
                
                if(this.filterBy == ""){
                    alert('You must filter your data first before exporting!')
                }else{
                    window.location.href = "/dynamic_pdf/pdf/" + this.filterBy;
                }                
            },
            cancelEditForm(){
                this.ids = [];
                $('.globalCancel').trigger("click");                 
                $('button, select').prop('disabled', false);
                $('.sidebar-blocker').css({
                    'display': 'none',
                    'z-index': '-1'
                });  

                // pageNumber & pageCount
                let prev = this.pageNumber === 0 ? true : false;
                $('#previous').prop('disabled', prev)
                
                let next = this.pageNumber >= this.pageCount-1 ? true : false;
                $('#next').prop('disabled', next);    
            },     
            setFilter(){
                // Set filter 
                this.filterBy = this.selectAdd;
                this.filterRecords('', this.selectAdd);

                this.$refs.newCost.focus();
              
                // Set bill id to be added in the "AddItemForm" method
                for(let i=0; i < this.payments.length; i++){
                    let d = this.payments[i];
                    if(d.id == this.filterBy){
                        this.newItem.bill_id = d.payment_id;
                    }                    
                }
            },
            addItemForm:function(state){                
                switch (state){
                    case "show":
                                            
                        this.addItem = true;
                        let $this = this;
                   
                        setTimeout(function(){
                            $('#selectAdd option').eq(0).prop('selected', true);
                            $this.selectAdd = $('#selectAdd').val();
                            
                            // Set filter
                            $this.filterBy = $this.selectAdd;                            
                            $this.filterRecords('', $this.selectAdd);

                            // Autofocus
                            if($this.activePayments.length > 0){
                                $this.$refs.newCost.focus();                            
                            }                            
                        }, 1)
                    break;

                    case "verify": 
                                        
                        axios.post(`/expenses`,{                            
                            //payment: this.newItem.payment,
                            'cost': this.newItem.cost,
                            'desc': this.newItem.desc,
                            'note': this.newItem.note,
                            'bill_id': this.selectAdd,
                        }).then( (response)=>{
                            let d = response.data;                                                       

                            // debug
                            // console.log(d)
                            
                            if(typeof d.cost === "undefined"){   
                                                            
                                // Trigger and eventBus inside "todopayment.vue"
                                eventBus.$emit('updateOrder', 'update data')

                                // Trigger and eventBus inside "paymentorder.vue"
                                eventBus.$emit('updateChart', 'update chart');
                                                                                                                                 
                                // Update total expenses
                                this.total_expenses = parseFloat(this.total_expenses) + parseFloat(this.newItem.cost);                            
                                this.total_expenses = this.total_expenses.toFixed(2);

                                // Push data object from user's input
                                // #this.filteredRecord = d.filtered;                                
                                // #this.expenses = d.newdata;     
                                                                
                                this.newData = {
                                    id: d.data.id,
                                    amount: this.newItem.cost,
                                    bill: {
                                        bill: d.filteredPayment,
                                    },
                                    description: d.data.description,
                                    created_at: d.data.created_at
                                }
                                                                                                
                                // ES6 Push                          
                                this.expenses = [...this.expenses, this.newData];                                                                    
                                this.filteredRecord = [...this.filteredRecord, this.newData];

                                // Sort
                                this.expenses.sort((a,b) => a.bill > b.bill ? 1 : -1);
                                this.filteredRecord.sort((a,b) => a.bill > b.bill ? 1 : -1);                                

                                // Manually pushing data
                                // this.filteredRecord.push({payment: { payment: d.filterPayment }, amount: this.newItem.cost, description: this.newItem.note, created_at: '' })
                                // this.expenses.push({payment: { payment: d.filterPayment }, amount: this.newItem.cost, description: this.newItem.note, created_at: '' })
                                
                                // Clear form
                                this.itemError.cost = false;
                                this.newItem.cost = "";
                                this.newItem.note = "";
                                this.$refs.newCost.focus();
                             
                                // Set quick delay
                                clearTimeout(this.timer2);
                                $('.globalAddItem').prop('disabled', true);
                                this.timer2 = setTimeout(function(){
                                    $('.globalAddItem').prop('disabled', false);
                                }, 500);
                                
                                // Show success
                                this.itemSuccess = true;                            
                                clearInterval(this.timer1);
                                let $this = this;
                                this.timer1 = setTimeout(function(){
                                    $this.itemSuccess = false;
                                }, 5000)
                            }else{
                                this.itemError.cost = true;
                                this.newItem.cost = "";
                                this.$refs.newCost.focus();
                            }
                        }).catch(err => console.log(err))
                    break;
                }
            },                     
            digits(nStr){
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
            filterRecords:function(e, data){                                               
                // this.filterBy = $('#selectAdd option:selected').text().trim();

                // Set batch to active and set batch_source to false
                this.batch = "current";
                this.batch_source = false;
                 
                // Reset icons
                $('.payment_order, .cost_order, .purchase_order').html(' <img src="https://icongr.am/octicons/kebab-vertical.svg?color=ffffff&size=16" alt="arrow">');                        
                $('.payment_order, .cost_order, .purchase_order').removeClass("rotate");
                $('.payment_order, .cost_order, .purchase_order').removeClass("rotate_reverse");   
                       
                // Allow sorting                       
                this.sorting.cost = true;                

                // Reset pageNumber to zero
                this.pageNumber = 0;

                // Get source 
                let src ='';                                  
                if(typeof this.origin !== "undefined"){                    
                    src = this.origin;
                    
                    // since this.origin has already been set, if data is not equal to src, then, change it
                    if(data !== src){
                        src = data;
                    }
                }else{       
                    // data === alldata -> get all expenses             
                    src = data == "" ? 'alldata' : data;                
                }
                                                             
                if(src === "" || src === "alldata"){
                    this.filterRecord = false;
                }else{
                    this.filterRecord = true;
                }       
     
                // If filtering originated from "Add Expenses", do not set filterRecord
                if(e == 'column-payment'){
                  //  this.filterRecord = false;
                }
                                
                /**
                 * Note:
                 *  Filtering records must be originated from the SERVER to give an accurate data
                 */                           
                
                axios.get(`/payments/${src}`)
                     .then( (response) => {           
                        // debug                     
                        // console.log(response.data)                                                 
                        if(this.filterRecord){
                            this.filteredRecord = response.data.expenses;                 
                        }else{
                            this.expenses = response.data.expenses;
                        }
                        
                        this.total_expenses = parseFloat(response.data.total);                        
                        this.total_expenses = this.total_expenses.toFixed(2);
                      })
                      .catch(err => console.log(err)); 

            },
            paymentsList:function(){
                // load payments from HomeController             
                axios.get(`/payments/getalldata`)
                     .then( (response) => {
                        let d= response.data;
                        // console.log(d)

                        this.payments = d.payments;                          
                      })
                      .catch(err => console.log(err));
            },
            triggerEdit:function(e, id, amount, tender, notes, purchased){                            
                //$event,item.id, item.amount, item.payment_id, ++ctr, item.created_at )" 
                // Focus on selected cell   
                
                if(e.target.id.indexOf("text") >= 0){
                    $('#editBtn' + id).trigger('click');
                    $('#amount' + id).focus().select();                    
                }else if(e.target.id.indexOf('cat') >= 0){
                    $('#editBtn' + id).trigger('click');
                    $('#category' + id).focus().select();  
                }else if(e.target.id.indexOf('date') >= 0){                    
                    $('#editBtn' + id).trigger('click');
                    $('#datepicker_input' + id).focus();
                }else if(e.target.id.indexOf('select') >= 0){  
                    
                    if(this.batch_source){
                        this.globalModal = true;                          
                        this.modal.header = '<i class="fas fa-info font18 text-info"></i> Demo Only';
                        this.modal.body = 'You cannot update this record anymore since it is no longer an active billing expenses.';
                        return;
                    }
                    this.sidebar.id = id;
                    this.sidebar.cost = amount;
                    this.sidebar.old_payment = tender;
                    this.sidebar.payment_text = $('#select' +id).text();                    
                    this.sidebar.purchased =  moment(purchased).format('MMMM d, Y');

                    // Do not allow table to be sorted
                    this.editMode = true;

                    /**
                     * Note:
                     * notes variable was used to get the selected index
                     */
                    this.sidebar.rowIndex = notes;
                    $('.right-sidebar').stop().animate({
                        'right' : 10
                    }, 800, 'easeInOutExpo')

                    // Highlight selected table
                    this.rowIndex = notes-1;
                    $('.table tbody tr').removeClass('selRow');
                    $('.table tbody tr:nth-child(' + this.rowIndex + ')').addClass('selRow');
                    $('.interActiveBtn').prop('disabled', true);     
                    return;
                    
                    // Cancel all quick forms through SPA method                    
                    // Push router                    
                    // let source = typeof this.control === "undefined" ? 'myexpenses' : 'dashboard';                    
                    // this.$router.push({ name: 'edit', params: { id: id, amount: amount, tender: tender, notes: notes, purchased:purchased, origin: this.filterBy, source: source  } })                    
                    // $('#tender' + id).focus().select();
                }else{
                    $('#editBtn' + id).trigger('click');
                    $('#note' + id).focus().select();
                }             
            },
            inputMonitor(e, id){                
                // Enter
                if(e.keyCode === 13){                       
                    $('#saveBtn' + id).trigger('click');
                }

                // Esc
                if(e.keyCode === 27){
                    $('#cancelButton').trigger('click');                    
                }
            },
            updatekeypress(e,id){                                    
                // prevent alpha input
                if(e.target.id.includes('amount') || e.target.id == 'newItem'){
                    let charCode = (e.which) ? e.which : e.keyCode;
                    if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                        e.preventDefault();;
                    } else {
                        return true;
                    }       
                }                                           
            },            
            setAutoClose(){
                $('#autoclose').prop("checked", true);
                
                if(!this.lockAutoClose){
                    this.globalModal = true;
                    this.modal.header = '<i class="fas fa-info font18 text-info"></i> Auto-Close Mode';
                    this.modal.body = 'Since you are modifying the <strong>Payment</strong> column, <strong>Auto-Close</strong> feature has been locked.';
                    this.actions.autoclose = true;
                    this.lockAutoClose = true;
                }

                // Lock it
                $('#autoclose').prop("disabled", true)
            },
            editAutoClose(e){
                let src = e.target.id;
            
                this.actions.autoclose = $('#' +src).is(":checked") ? true : false;    
                
                // Show info
                if(this.actions.autoclose){
                    this.globalModal = true;
                    this.modal.header = '<i class="fas fa-info font18 text-info"></i> Tips';
                    this.modal.body = 'This will automatically close the "Quick Edit" form once you saved your work.' +
                                      '<br><br>' +
                                      '<strong>Quick Tips:</strong><br/><div class="text-left mt-2">' +
                                      '- Press "ENTER" to save your work.<br/>' +
                                      '- Press "ESC" to close or cancel edit form.<br/>' +
                                      '- Click cells under Payment, Cost & Notes to show Quick Edit form.</div>'
                }                
            },            
            editItem:function(data){                
                // debug                                            
                /////////////////////////////////////
                // UPDATE PAYMENT
                if(data.change_payment){                    
                    axios.patch(`/update/expenses/${data.id}`, {                        
                        new_bill_id: data.new_bill_id,
                        change_payment: data.change_payment,                        
                    }).then((response) =>{         
                        // debug
                        // console.log(response.data)               

                        if(response.data.success){
                            // Trigger and eventBus inside "todopayment.vue"
                            eventBus.$emit('updateOrder', 'update data')
                            eventBus.$emit('updateChart', 'update chart')                            

                            // update table list
                            for(var i=0; i < this.expenses.length; i++){
                                let x = this.expenses[i];                   
                                let current_item = this.expenses[i].bill.bill;
                            
                                if(x.id == data.id){                                                                             
                                    /**
                                     * original_tender source is from changepayment.vue
                                     */
                                    x.bill.bill = x.bill.bill.replace(current_item, data.new_payment_text)                                    
                                }                                         
                            }
                        }
                    }).catch(err => console.log(err))                    
                    return;
                }

                /////////////////////////////////////
                // UPDATE EXPENSES
                // if user empty field and save           
                if(data.amount == "" || data.amount <= 0){                    
                    alert('You need to enter a value for this field.')
                    setTimeout(function(){
                        $('#amount' + data.id).val('0.00');                        
                    }, 1)                    
                    return;
                }                                
                  
                let request = axios.patch(`/update/expenses/${data.id}`, {                    
                    'amount': data.amount,                       
                    'note': data.notes, 
                    'desc': data.desc,   
                    'created': data.created,
                }).then( (response) =>{      
                    // debug                
                    // console.log(response.data)                                     

                    return response.data;                    
                }).catch(err => console.log(err))

                request.then(result=>{  

                    // Show notification
                    this.updatedItem = true;                         

                    if(result.success){         
                        
                        // Success
                        this.amountError = false;
                        this.amountError2 = false;
                        this.success = true;

                        // Update total expenses  
                        this.total_expenses = parseFloat(result.total_sum).toFixed(2);
                        
                        // Update this.expenses
                        const updatedRecord = {
                            'user_id' : id,
                            'amount': data.amount,
                            'payment_id': data.tender,
                            'description' : data.notes                                                        
                        };  
                  
                        // Trigger and eventBus inside "todopayment.vue"
                        eventBus.$emit('updateOrder', 'update data')                        
                        eventBus.$emit('updateChart', 'update chart')
                        
                        // Show toast success for 5 seconds                        
                        $('.toast-container').css('display', 'block');
                        $('.toast-body').html('<i class="fas fa-check"></i> Item has been successfully updated!');
                        $('.toast').toast('show')        
                                                                                        
                        // Autoclose                        
                        if(this.actions.autoclose){
                            $('#cancelButton').trigger('click');

                            // auto-focus and select on first available input
                            $("input:text:visible:first").focus().select();
                        }
                    }else{
                        // Error                        
                        this.success = false;
                        this.amountError = true;
                        this.amountError2 = true;
                        this.newAmount = 0;

                        $('.toast-body').text('You entered an invalid number!');
                        $('.toast').toast('show')                                                                                               
                    }
                        clearTimeout(this.timer1);
                        this.timer1 = setTimeout(function(){
                            $('.toast').toast('hide');
                            $('.toast-container').css('display', 'none');
                        }, 5000)
                })            
            },
            cancelDelete(){
                this.deleteConfirm = false;
                $('.table tbody tr:nth-child(' + this.rowIndex + ')').removeClass('selRow');
            },
            deleteNoConfirmation(e){
                /**
                 * If checked, delete items without confirmation
                 */
                let src = e.target.id;
                this.actions.noconfirmation = $('#' +src).is(":checked") ? true : false;    

                // Show warning
                if(this.actions.noconfirmation){
                    this.globalModal = true;
                    this.modal.header = '<i class="fas fa-exclamation-triangle font18 text-danger"></i> Warning';
                    this.modal.body = 'Checking this will delete selected items without confirmation but there will be a quick milliseconds delay disable to prevent server error.';
                }
            },
            deleteConfirmation(id, amount, index, bill_id){                              
                // Prepare data
                this.to_delete = id;                
                this.to_delete_amount = parseFloat(amount);
                this.to_delete_bill_id = bill_id;                

                // Delete without confirmation                
                if(this.actions.noconfirmation){                    
                    // To avoid server error, give a quick delay
                    this.deleteItem();
                    $('.globalDelete').prop('disabled', true);

                    clearTimeout(this.timer1);                    
                    this.timer1 = setTimeout(function(){
                        $('.globalDelete').prop('disabled', false);
                    }, 500)
                    
                    return;
                }

                // Delete with confirmation
                // Show delete confirmation box
                this.deleteConfirm = true;
                
                // Highlight selected table
                this.rowIndex = index;
                $('.table tbody tr:nth-child(' + this.rowIndex + ')').addClass('selRow');
            },
            deleteItem(){                                               
                let request = axios.post(`/expenses/delete/${this.to_delete}`, {
                    amount: this.to_delete_amount,
                    bill_id: this.to_delete_bill_id,
                }).then( (response) =>{                    
                    return response.data;
                }).catch(err => console.log(err))

                request.then(result=>{
                    let d = result.success;                    
                    if(d){                                                
                        // Update computed data                        
                        if(!this.filterRecord){                            
                            this.expenses = this.expenses.filter(expenses => expenses.id !== this.to_delete);                            
                        }else{                                                   
                            this.filteredRecord = this.filteredRecord.filter(expenses => expenses.id !== this.to_delete);
                        }                
                        
                        // Trigger and eventBus inside "todopayment.vue"
                        eventBus.$emit('updateOrder', 'update data')                        
                        eventBus.$emit('updateChart', 'update chart')                        

                        // Close delete confirmation box
                        this.deleteConfirm = false;

                        // Remove selectedRow
                        if(!this.actions.noconfirmation){
                            $('.table tbody tr:nth-child(' + this.rowIndex + ')').removeClass('selRow');
                        }                
                                              
                        // Re-compute total expenses                        
                        this.total_expenses = parseFloat(this.total_expenses) - this.to_delete_amount;   
                        this.total_expenses = this.total_expenses.toFixed(2);                                
                        
                        // If current paginatedData === 0, set pageNumber to 0                        
                        if(this.paginatedData.length <= 0){                            
                            this.pageNumber = 0;                            
                        }                        
                    }
                })
            },
            nextPage(){
                this.pageNumber++;                   
            },
            prevPage(){                
                this.pageNumber--;                
            },
            pageView(e){
                // Reset pageNumber
                this.pageNumber = 0;
                let count = e.target.value;                          
                this.size.default = parseInt(count);
            },
            changeIcon(el, value){
                $(el).removeClass(value)
                $(el).addClass(value)
            },
            editState(state){                
                this.editMode = state;
            },
            sort:function(column, el){                       
                // If editMode is TRUE, no sorting is allowed                
                if(this.editMode){                    
                    return false;
                }
                            
                switch (column){
                    case "payment":    
                        // if filter is enabled
                        if(this.filterRecord){
                            return false;
                        }                        

                        $(el).html('<i class="fas fa-arrow-down"></i>');  
                        if(!this.sorting.payment){
                            this.expenses.sort((a,b) => a.bill.bill > b.bill.bill ? 1 : -1);                            
                            this.sorting.payment = true;
                            $(el).removeClass('rotate_reverse')
                            $(el).addClass('rotate')
                        }else{
                            this.expenses.sort((a,b) => a.bill.bill < b.bill.bill ? 1 : -1);                                                        
                            this.sorting.payment = false;
                            $(el).removeClass('rotate')
                            $(el).addClass('rotate_reverse')
                        }                        
                    break;

                    case "cost":           
                        if(!this.filterRecord){                                      
                            $(el).html('<i class="fas fa-arrow-down"></i>');  
                        }else{
                            $(el).html('<i class="fas fa-arrow-up"></i>'); 
                        }                   

                        if(!this.sorting.cost){                              
                            if(!this.filterRecord){
                                // Filter All
                                this.expenses.sort((a,b) => parseFloat(a.amount) > parseFloat(b.amount) ? 1 : -1);                            
                            }else{
                                // Filter by selection                                
                                this.filteredRecord.sort((a,b) => parseFloat(a.amount) < parseFloat(b.amount) ? 1 : -1);                            
                            }
                            
                            this.sorting.cost = true;  
                            $(el).removeClass('rotate_reverse')
                            $(el).addClass('rotate')                         
                        }else{                                                              
                            if(!this.filterRecord){
                                // Filter All
                                this.expenses.sort((a,b) => parseFloat(a.amount) < parseFloat(b.amount) ? 1 : -1);                            
                            }else{
                                // Filter by selection                                
                                this.filteredRecord.sort((a,b) => parseFloat(a.amount) > parseFloat(b.amount) ? 1 : -1);                            
                            }
                            
                            this.sorting.cost = false;
                            $(el).removeClass('rotate')
                            $(el).addClass('rotate_reverse')
                        }
                    break;

                    case "purchase": 
                        if(!this.filterRecord){                                      
                            $(el).html('<i class="fas fa-arrow-down"></i>');  
                        }else{
                            $(el).html('<i class="fas fa-arrow-up"></i>'); 
                        }         
                        
                        if(!this.sorting.purchase){    
                            if(!this.filterRecord){
                                // Filter All
                                this.expenses.sort((a,b) => new Date(a.created_at) > new Date(b.created_at) ? 1 : -1);                            
                            }else{
                                // Filter by selection
                                this.filteredRecord.sort((a,b) => new Date(a.created_at) < new Date(b.created_at) ? 1 : -1);                            
                            }
                            
                            this.sorting.purchase = true;  
                            $(el).removeClass('rotate_reverse')
                            $(el).addClass('rotate')                         
                        }else{
                            if(!this.filterRecord){
                                // Filter All
                                this.expenses.sort((a,b) => new Date(a.created_at) < new Date(b.created_at) ? 1 : -1);                            
                            }else{
                                // Filter by selection
                                this.filteredRecord.sort((a,b) => new Date(a.created_at) > new Date(b.created_at) ? 1 : -1);                            
                            }                            
                            this.sorting.purchase = false;
                            $(el).removeClass('rotate')
                            $(el).addClass('rotate_reverse')
                        }
                    break;

                    case 'category':
                        if(!this.filterRecord){                                      
                            $(el).html('<i class="fas fa-arrow-down"></i>');  
                        }else{
                            $(el).html('<i class="fas fa-arrow-up"></i>'); 
                        }                   

                        if(!this.sorting.category){                              
                            if(!this.filterRecord){
                                // Filter All
                                this.expenses.sort((a,b) => a.category > b.category ? 1 : -1);                            
                            }else{
                                // Filter by selection                                
                                this.filteredRecord.sort((a,b) => a.category < b.category ? 1 : -1);                            
                            }
                            
                            this.sorting.category = true;  
                            $(el).removeClass('rotate_reverse')
                            $(el).addClass('rotate')                         
                        }else{                                                              
                            if(!this.filterRecord){
                                // Filter All
                                this.expenses.sort((a,b) => a.category < b.category ? 1 : -1);                            
                            }else{
                                // Filter by selection                                
                                this.filteredRecord.sort((a,b) => a.category > b.category ? 1 : -1);                            
                            }
                            
                            this.sorting.category = false;
                            $(el).removeClass('rotate')
                            $(el).addClass('rotate_reverse')
                        }
                    break;
                }

                // Set element


            },
        },
        computed:{       
            activePayments(){                
                return this.payments.filter(el => el.payment_status.match('0'))                
            },
            rows() {
                return this.paginatedData.length;
            },
            filteredExpenses(){
                /**
                 * This will be used to show cart data in V-FOR
                 * E.g.
                 * v-for="items in filteredCart"
                 */          
                
                //let filter = new RegExp(this.filterBy, 'i')              
                //return this.expenses.filter(el => el.payment.payment.match(filter))
            }, 
            pageCount(){                    
                let l = "";

                if(this.filterRecord){
                    // true                                  
                    l = parseInt(this.filteredRecord.length);         
                    //console.log(this.filteredRecord.length)                               
                }else{
                    // false                                       
                    l = parseInt(this.expenses.length);                                   
                    //console.log(this.expenses.length)                                        
                }
                            
                let s = parseInt(this.size.default);
                                              
                // Math.ceil because we want to display 17/10 === 2
                return Math.ceil(l/s);    
            },
            paginatedData(){                                     
                const start = this.pageNumber * this.size.default,
                        end = start + this.size.default;                        
                                
                // Slice and Filter                   
                /**
                 * Note: Filtering is ignored since all the data is being pulled thru server
                 */

                let filterBy = $("#filterBy option:selected").text().trim();                
                if(filterBy == "Select All"){
                   filterBy = "";
                }
                                                 
                let filter = new RegExp(this.filterBy, 'i');                
                                
                if(!this.filterRecord){   
                    // Default data                                           
                    // FILTERED: return this.expenses.slice(start,end).filter(el => el.bill.bill.match(filter));                
                    // console.log('Unfiltered data')
                    return this.expenses.slice(start,end);
                }else{             
                    // Filtered data                                               
                    // FILTERED: return this.filteredRecord.slice(start,end).filter(el => el.bill.bill.match(filter));
                    // console.log('Filtered data')
                    return this.filteredRecord.slice(start,end);
                }                
            }   
        }
    }
    
</script>

<style lang="scss" scoped>

table{
    font-weight: 300;    
}
.thead-dark tr th{
    color:white!important;
    font-weight: 300!important;
}
.description{
    min-width: 300px;
    height: 100px;
    font-size: 12px;
}

/* Sort arrows */
.sorter{
    cursor: pointer;
}
input, .inputSelect{
    border-radius: 0;
    font-size: 13px;    
    display:none;    
}

select{
    border-radius: 0;
}

.input-sidebar{
    display:block;
}

.form-check-input{
    display:inline-block!important;
}

.doubleClick{
    cursor: pointer;
}

.toast-container{
    position: fixed;
    top:20px;
    right: 10px;    
    z-index: 9998;    
    display:none;
}

.toast-header{
    background-color: dodgerblue;
    color:white;
}
.toast-body{
    font-size: 14px;    
    padding: 20px;
}

.payment_order{
    font-size: 14px;
}

.rotate{
    animation: rotateIcon .75s;
    transform: rotate(180deg);
}
@keyframes rotateIcon{
    0%{
        transform: rotate(0);
    }
    100%{
        transform: rotate(180deg);
    }
}

.rotate_reverse{
    animation: rotateIcon_Rev .75s;
    transform: rotate(0deg);
}

@keyframes rotateIcon_Rev{
    0%{
        transform: rotate(180deg);
    }
    100%{
        transform: rotate(0deg);
    }
}

/* row 1 */
.table .thead-header th:nth-child(1){        
    width: 15%;
}

/* row 2 */
.table .thead-header th:nth-child(2){        
    width: 10%;
}

@media(max-width: 414px){
    .table .thead-header th:nth-child(-n+5){
        width: auto;
    }
}

/* selRow */
.selRow{
    background-color:crimson!important;
    color:white!important;
    transition: 1s ease-in-out;
}
.no-border{
    border-radius: 0;
}
.right-border{
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.dropdown-item{
    font-size: 12px;
}
.fa-file-pdf, .fa-file-alt{
    font-size: 14px;
    margin-right: 5px;
    color: crimson;
}

.form-check-label{
    cursor: pointer;
}

/* Add Item */
.addInput{
    display:block;
}
button{
    border-radius: 0;
}
th{
    vertical-align: baseline;    
}
td{    
    vertical-align: baseline;    
}

.nav-header{
    position:relative;
} 

td{    
    padding:6px;
}

/* remove caret  */
.dropdown-toggle::after {
    display: none;
}

.input-group-append, .input-group{
    button, select{
        font-size: 12px;
    }
}
.input-group-text{
    background-color: #04091e;
    color:white;
    border:1px solid black;
    font-size: 11px;
}


/* right-sidbar */
.right-sidebar{
    position: fixed;
    right:-400px;
    top:60px;
    width: 330px;    
    background-color:#04091e;
    color:white;
    padding: 30px;
    z-index: 5000;
}

.hand-cursor{
    cursor: pointer;
}

.header_bg{
    background-color: #dc3545!important;
}

.px3{
    padding:0 20px;    
}
</style>