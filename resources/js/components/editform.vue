<template>
    <div>
        <input type="text" v-model="cost">
    </div>
</template>

<script>
    export default {
        props:['id', 'amount'],
        data(){
            return{
                cost: '',
            }
        },
        mounted(){
            this.cost = this.amount;
        },
        methods:{

        }
    }
</script>

<style scoped>

</style>