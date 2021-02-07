<template>
    <div>           
        <span class="dropdown" v-if="!showFormButtons">
            <button class="btn btn-secondary dropdown-toggle actionButton editButton"  type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">                        
                <!-- 
                    <button class="btn btn-secondary dropdown-item no-border routerLink" @click="cancelForm(item.id, item.amount, item.payment_id, item.description, item.created_at, item.payment.payment)"><i class="far fa-edit"></i> Change Payment</button>
                -->

                <!-- 
                REFERENCE:
                <router-link                    
                    :to="{name: 'edit', params: {id: item.id, amount: item.amount, tender: item.payment_id, notes: item.description, purchased: item.created_at} }" 
                    class="btn btn-secondary dropdown-item no-border routerLink" @click="cancelForm"><i class="far fa-edit"></i> Change Payment
                </router-link>        
                -->
            
                <button v-bind:id="'editBtn' + item.id"                     
                        class="btn btn-secondary dropdown-item editButton" 
                        @click="showForm(item.id, item.bill.bill, item.bill_batch)">
                        <i class="fas fa-pen-alt"></i> Quick Edit
                </button>              
            </div>
        </span>
               
        <!-- Edit Item Sub -->
        <span v-if="showFormButtons" class="mt-2">
            <button id="cancelButton" class="btn btn-danger actionButton globalCancel editButton" @click="showForm(item.id, item.bill.bill, item.bill_batch)"><i class="fas fa-ban"></i></button>
            <button v-bind:id="'saveBtn' + item.id" 
                    v-on:mouseleave="mouseLeave('hover_save', item.id)" v-on:mouseover="mouseOver('hover_save', item.id)"
                    class="btn btn-success actionButton editButton" @click="updateItem(item.id, item.payment_id, item.amount, item.description)"><i class="fas fa-check"></i></button>            
        </span>         
           
    </div>
</template>

<script>
    var moment = require('moment')        
    export default {        
        name: 'actions',
        props:['item', 'ids', 'pageNumber', 'pageCount', 'batch_source'],                
        data(){
            return{
                moment: moment,
                saveMode: false,
                window:{
                    width:0,
                    height:0,
                },
                showFormButtons: false,                    
            }
        },     
        mounted(){
          
        },
        methods:{        
            cancelForm(id, amount, tender, notes, purchased, payment){
                
                // Cancel quick edit form
                $('.globalCancel').trigger("click");                                                             
                this.$router.push({ name: 'edit', params: { id: id, amount: amount, tender: tender, notes: notes, purchased:purchased, origin: payment, source: 'dashboard' } })                    
            },      
            mouseOver(element, id){                
                $('.'+ element +id).css('display', 'block');
            },
            mouseLeave(element, id){
                $('.' + element +id).css('display', 'none');          
            },
            updateItem(id, tender, amount, notes){                                                  
                // Update table list
                let note = $('#note'+id).val();
                let date_ = moment($('#datepicker_input' + id).val()).format('YYYY-MM-DD hh:mm:ss');                
                
                this.item.amount = amount;               
                this.item.amount = parseFloat($('#amount' +id).val()).toFixed(2);                                                            
                note = note.replace(' ', '') === "" ? "..." : note;
                this.item.description = note;
                this.item.category = $('#category' +id).val();
                this.item.created_at = date_;
               
                // Emit                                
                const updatedRecord = {
                    'id' : id,
                    'amount': $('#amount' + id).val(),
                    'notes' : $('#note' +id).val(),                    
                    'desc' : $('#category' +id).val(),          
                    'created' : date_,
                };  
                                                      
                this.$emit('edit-item', updatedRecord);                
            },            
            showForm: function(id, payment, batch){     
                //console.log(batch)
                //console.log(this.batch_source)
                let activeBatch = moment().format('MMY');
                // console.log(activeBatch)
                //if(this.batch_source){                
                if(activeBatch != batch){
                    const payload = {                        
                        'header': '<i class="fas fa-info font18 text-info"></i> Demo Only',
                        'body': 'You cannot update this record anymore since it is no longer an active billing expenses.',
                    }                    
                    eventBus.$emit('globalModal', payload)
                    return;
                }
                // Show form
                this.showFormButtons = true;
                
                // Detect wether to cancel or edit
                let mode_text = !this.saveMode ? 'none' : 'block';             
                let mode_input = !this.saveMode ? 'block' : 'none';
                                                         
                // Hide text and desc    
                $('#text' +id).css('display', mode_text);            
                $('#desc' +id).css('display', mode_text);            
                $('#cat' +id).css('display', mode_text);      
                $('#date' +id).css('display', mode_text);            
                                
                // Show selected item                
                $('#amount' +id).css('display', mode_input);
                $('#note' +id).css('display', mode_input);
                $('#category' +id).css('display', mode_input);                
                $('#datepicker' +id).css('display', mode_input);                                
                
                // Focus select
                $('#tender' + id).val(payment)

                // Focus
                $('#amount' +id).focus();
                
                // Disable all element with hidden-input until all are invisible
                if($('.hidden-input').is(":visible")){  
                                             
                    $('.interActiveBtn').prop('disabled', true);     

                    // Activate sidebar-blocker
                    //$('.sidebar-blocker').css({
                       // 'display': 'block',
                      //  'z-index': '1000000000000'
                    //});        
                }else{       
                    
                    $('.interActiveBtn').prop('disabled', false);  

                    // De-activate sidebar-blocker
                   // $('.sidebar-blocker').css({
                       // 'display': 'none',
                       // 'z-index': '-1'
                   // });   
                    
                    // pageNumber & pageCount restoration
                    let prev = this.pageNumber === 0 ? true : false;
                    $('#previous').prop('disabled', prev)
                    
                    let next = this.pageNumber >= this.pageCount-1 ? true : false;
                    $('#next').prop('disabled', next);                    
                }              
                           
                // Set mode
                this.saveMode = !this.saveMode ? true : false;  
                
                if(!this.saveMode){                                               
                    // Cancel Quick Form                   
                    $('#editBtn' + id).prop('disabled', false);                                        
                    this.showFormButtons = false;   
                    
                    // Emit to parent to notify state is Edit mode
                    this.$emit('edit-mode', false);

                    // Show Quick-edit dropdown                
                    $('#editBtn' + id).css('display', 'block');

                    // Set focus
                    $("input:text:visible:first").focus().select();             
                }else{        
                    // Show Quick Edit Form
                    
                    // Hide Quick-edit dropdown                
                    $('#editBtn' + id).css('display', 'none');                            
                    $('#tender' + id).prop('disabled', false);                                  

                    // Emit to parent to notify state is Edit mode
                    this.$emit('edit-mode', true);
                }                                       
            },
            handleResize(){
                this.window.width = window.innerWidth;
                this.window.height = window.innerHeight;
            },
        },
        created(){
            window.addEventListener('resize', this.handleResize)
            this.handleResize();
        },
        destroyed() {
            window.removeEventListener('resize', this.handleResize)
        },
    }
</script>

<style scoped>
button{
    border-radius: 0;
} 
/* remove caret */
.dropdown-toggle::after {
    display:none!important;
}
.fa-cog{
    font-size: 10px;
}
.dropdown-item{
    font-size: 12px;
}

.actionButton{
    width: 30px; 
    padding:3px 0;
    font-size: 12px;
}
.no-border{
    border-radius:0!important;
    box-shadow: none!important;
}

</style>