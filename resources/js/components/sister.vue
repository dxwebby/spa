<template>
    <div>
        <H1>SISTER</H1>
        <button @click="messageBrother" class="btn btn-warning">Message Brother</button>
        <div v-if="messagedaughter" class="mt-3 alert alert-secondary" v-html="messagedaughter"></div>
    </div>
</template>

<script>
    export default {
        props: ['messagedaughter'], 
        data() {
            return {
                thecardtitle: 'Child Component!'
            }
        }, 
        methods:{
            messageBrother() {
                this.$emit('sistersaid', 'Mom said do your homework!')
            }
        },
        
    }
</script>

<style lang="scss" scoped>

</style>