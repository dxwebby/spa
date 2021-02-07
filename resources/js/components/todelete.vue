<template>
    <div class="buttons">
        <button class="noborder interActiveBtn globalDelete" @click="$emit('del-item', item.id, item.amount, index, item.bill_id)" title="Delete item"><i class="fas fa-eraser"></i></button>
    </div>
</template>

<script>
    export default {
        name: 'todelete',
        props:['item', 'index'],
        data(){
            return{
                counter: 0,
            }
        },
        mount(){
            
        }
    }
</script>

<style scoped>
.noborder{
    background-color: transparent;
    border:none;
    color: red;
    font-size: 14px;
}
.buttons{
    position: relative;
}
button{
    position: absolute;
    margin-top: 5px;
}
</style>