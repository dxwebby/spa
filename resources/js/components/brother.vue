<template>
    <div>
        <h1>BROTHER</h1>
        <button @click="messageSister" class="btn btn-primary">Message Sister</button>
        <div v-if="messageson" class="mt-3 alert alert-secondary" v-html="messageson"></div>
    </div>
</template>

<script>
    export default {
        props: ['messageson'], 
        data() {
            return {
                thecardtitle: 'Child Component!'
            }
        }, 
        methods: {
            messageSister() {
                this.$emit('brothersaid', 'Mom said do your homework!')
            }
        }   
    }
</script>

<style lang="scss" scoped>

</style>