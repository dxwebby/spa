<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <activeComponent v-bind:active="'Dashboard / Unlisted Expenses /'"/>                
            </div>
        </div>

        <div class="row mt-4">             
            <div class="col-lg-12">
                <div class="card col-lg-10 offset-lg-1 p-1">                    
                    <div class="card-body text-center">
                        <div class="header">
                            <h5 class="card-title my-auto"><i class="fas fa-unlink font18 mr-2"></i> Unlisted Expenses</h5>
                        </div>                            
                        <p class="card-text">
                            <table class="table table-striped">
                                <thead class="thead-light">
                                    <tr>                
                                        <th>#</th>                        
                                        <th>Original Payment</th>                                        
                                        <th class="hide_me">Total Records</th>
                                        <th>Assign Payment To</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>                                
                                    <tr v-for="list in unlisted" :key="list.index">                                        
                                        <td>{{list.index}}</td>
                                        <td>{{list.bill}}</td>                                        
                                        <td class="hide_me">{{list.total}}</td>
                                        <td>
                                            <select v-bind:id="'selectAdd' + list.index" class="custom-select selectAdd">                                            
                                                <option v-for="payment in activePayments" :key="payment.id" v-bind:value="payment.id">                                                
                                                    {{payment.bill}}
                                                </option>
                                            </select>
                                        </td>    
                                        <td>
                                            <unlistedactions
                                                :id="list.id"
                                                :index="list.index"
                                                v-on:assign="assignPayment"
                                                v-on:delete="deleteExpenses"
                                            />
                                        </td>                                    
                                    </tr>
                                </tbody>
                            </table>
                        </p>                        
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>

<script>
    import activeComponent from './activeComponent.vue';
    import unlistedactions from './unlistedactions.vue';

    export default {
        components:{
            activeComponent,
            unlistedactions,
        },
        data(){
            return{
                payments: [],
                selectAdd: '',
                unlisted: [],
                unkown: 0,
            }
        },
        mounted(){
            this.paymentsList();
            this.unlistedExpenses();
        },
        computed:{
            activePayments(){                
                // returns active payments that haven't mark as paid yet
                return this.payments;
                //return this.payments.filter(el => el.payment_status.match('0'))                
            },
        },
        methods:{
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
            unlistedExpenses:function(){
                // get unlisted expenses                
                axios.get(`/unlistedexpenses`).then( (response) => {                        
                        this.unlisted = response.data.unlisted;        
                        // console.log(response.data)                  
                }).catch(err => console.log(err))
            },
            assignPayment:function(id, index){                
                let selectAdd = $('#selectAdd' + index).val();                
                axios.put(`/unlisted/action`, {
                    'id' : id,
                    'assign_to': selectAdd,
                }).then((response) =>{
                    // console.log(response.data)
                    this.unlisted = this.unlisted.filter(el => el.id !== id)
                })
            },
            deleteExpenses:function(id){                
                axios.delete(`/unlistedexpenses/delete/${id}`).then((response) =>{
                     //console.log(response.data)
                    this.unlisted = this.unlisted.filter(el => el.id !== id)
                })
            }
        }
        
    }
</script>

<style lang="scss" scoped>
    .card-title{
        font-size: 14px;
        font-weight: 600;
    }

    @media(max-width: 567px){
        .selectAdd{
            font-size: 11px;
        }
        .hide_me{
            display:none;
        }
    }

    .header{
        background-color: dodgerblue;
        color:white;
        padding: 12px;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
    }

    .table{
        th, td{
            vertical-align: baseline;
        }
    }

    select{
        border-radius: 0;
    }
</style>