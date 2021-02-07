<template>
    <div class="hr mb-4 mt-2">
        <i class="fas fa-tachometer-alt"></i> <span v-html="active"></span>
        <div class="line"></div>
    </div>
</template>

<script>
    export default {
        props:['active'],
    }
</script>

<style scoped>
.hr{
    position: relative;
    background:rgba(0,0,0, .04);
    padding: 5px;
}

.line{
    position: absolute;    
    left:0;
    top:-8px;
    background-color: rgba(0,0,0, .1);
    width: 100%;
    height: .1em;
}
</style>