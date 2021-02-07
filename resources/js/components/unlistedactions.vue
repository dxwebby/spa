<template>
    <div>
        <button class="btn btn-success" title="Assign these expenses to a new payment" @click="$emit('assign', id, index)">Assign</button>
        <button class="btn btn-danger" title="Delete all these expenses" @click="confirmDelete(id)">Delete</button>


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
                            <button class="btn btn-danger" @click="$emit('delete', selected_id), globalModal = false">Yes</button>                                                
                            <button class="btn btn-outline-primary" @click="globalModal = false">
                                Close
                            </button>                    
                    </div>
                    </div>
                </div>
            </div>
        </transition>

    </div>
</template>

<script>
    export default {
        name: 'unlistedactions',
        props: ['id', 'index'],
        data(){
            return{
                selected_id: '',
                globalModal: false,     
                modal:{
                    header: '',
                    body: '',
                },
            }
        },        
        methods:{
            confirmDelete:function(id){
                this.selected_id = id;
                this.globalModal = true;
                this.modal.header = '<i class="fas fa-trash font16 text-danger"></i> Delete Confirmation';
                this.modal.body = 'Are you sure you want to delete all of these expenses?'
            },
        }
    }
</script>

<style lang="scss" scoped>
    button{
        border-radius: 0;
    }
</style>