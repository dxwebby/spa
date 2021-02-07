<template>
    <div>
        <!-- PUSH -->
        <div class="my-5">
            <h5>Test Data:</h5>
            <input type="text" v-model="userName" @keyup.enter="addItem">
            <button class="btn btn-success" @click.prevent="addItem">Add</button>
            <div v-for="item in items" :key="item.index">
                {{item.name}}, {{item.age}} from: {{item.other.address}}
            </div>
        </div>            
        <!-- /.PUSH -->


    </div>
</template>

<script>
    export default {
        data(){
            return{
                items:[
                    {
                        name: 'john',
                        age: 25, 
                        other:
                            {
                                address: 'balagan',
                            }
                        
                    },
                    {
                        name: 'jenny',
                        age: 45,
                        other:
                            {
                                address: 'binmaley'
                            }
                         
                    },                    
                ],
                userName: '',
            }
        },
        mounted(){

        },
        methods:{
            addItem:function(){
                try{
                    this.items.push({name: this.userName, age: this.randomInt(), other: {address: 'new address'} });
                }catch(error){
                    console.log(error)                    
                }                
            },
            randomInt:function(){
                return Math.floor((Math.random() * 100) + 30);
            }
        }
    }
</script>

<style scoped>

</style>