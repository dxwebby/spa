<template>
    <div>               
        <div class="row">
            <div class="col-lg-12">
                <activeComponent v-bind:active="'Dashboard /'"/>
            </div>
        </div>
        
        <div class='row'>
            <div class="col-lg-12">
                <paymentorder/>                        
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12">    
                <div class="card p-0">                
                    <div class="card-body">
                        <todopayment/>                            
                    </div>
                </div>                            
            </div>
        </div>        
        
        <div class="row mt-4">
            <!-- v-bind:control is the source of redirection -->
            <div class="col-lg-12">    
                 <div class="card p-0">                
                    <div class="card-body">
                        <myexpenses 
                            v-bind:control="'dashboard'" 
                            v-bind:origin="origin"                                             
                        />
                    </div>
                </div>                 
            </div>
        </div>        
    </div>
</template>

<script>    
    import activeComponent from './activeComponent.vue';
    import paymentorder from './paymentorder.vue';
    import myexpenses from './myexpenses.vue';
    import todopayment from './todopayment.vue';
    
    export default {
        name:'home',
        props: ['origin'],
        components:{
            activeComponent,
            myexpenses,
            paymentorder,   
            todopayment,                  
        },
        data(){
            return{        
            }
        },
        mounted(){
            
        },
        methods:{                                     
        }
    }
</script>

<style lang="scss" scoped>

.pink{
    background-color:pink;

}


</style>