<template>
    <div>
        <form>
            <div class="form-group">
                <table class="table table-sidebar">                        
                    <tbody>
                        <tr>
                            <td>Current Payment</td>
                            <td class="text-right">{{renderPayment}}</td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="form-group">                    
                <select class="custom-select" id="sidebar_payment" v-model="new_payment">                    
                    <option v-for="payment in listPayments" :key="payment.index" v-bind:value="payment.id" v-text="payment.bill"></option>
                </select>                    
            </div>
            <div class="form-group">                    
                <table class="table table-sidebar">                        
                    <tbody>
                        <tr>
                            <td>Amount</td>
                            <td class="text-right">${{amount}}</td>
                        </tr>
                        <tr>
                            <td>Date Purchased</td>
                            <td class="text-right">{{purchased}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group text-center">
                <button class="btn btn-success" @click.prevent="updatePayment">Change Payment</button>
                <button class="btn btn-secondary" @click.prevent="closePaymentForm">Close</button>
            </div>

        </form>
    </div>
</template>

<script>
    export default {
        props:['payments', 'id', 'amount', 'payment_text', 'old_payment', 'purchased', 'rowIndex'],
        name: 'changepayment',
        data(){
            return{
                new_payment: '',  
                renderPayment: '',              
            }
        },        
        computed:{
            listPayments:function(){                          
                this.new_payment = this.old_payment;                 
                this.renderPayment = this.payment_text;               
                return this.payments;                
            }
        },
        methods:{      
            closePaymentForm(){                
                $('.right-sidebar').stop().animate({
                        'right' : -400
                }, 800, 'easeInOutExpo')                

                // Remove row selection
                $('.globalTable tbody tr:nth-child(' + parseInt(this.rowIndex-1) + ')').removeClass('selRow');

                // Allow sorting
                this.$emit('cancel-edit');

                // Allow buttons 
                if(!$('.hidden-input').is(":visible")){  
                    $('.interActiveBtn').prop('disabled', false);     
                }
                
            },   
            updatePayment(){           
                let payment_text  = $('#sidebar_payment option:selected').text().trim();                

                // Get selected payment bill_id                   
                let new_bill_id = "";                
                for(let i=0; i < this.payments.length; i++){
                    let x = this.payments[i];
                    
                    if(x.id == this.new_payment){
                        new_bill_id = x.id;
                    }
                }
                
                const newPayment = {
                    "id" : this.id,                    
                    "new_bill_id" : this.new_payment,
                    "new_payment_text": payment_text,
                    "change_payment" : true,
                }

                this.$emit('update-payment', newPayment);
                this.renderPayment = payment_text;
            },
        }
    }
</script>

<style scoped>

input, select, button{
    border-radius: 0;
}
.table-sidebar{
    color:white;
}
td{
    padding-left: 0;
    padding-right:0;
}
</style>