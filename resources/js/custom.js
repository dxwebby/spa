
$(document).ready(function () {
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({
            container : 'body'
        })        
    })


    // Sidebar Toggle
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');

        // navbar Toggler
        $('.navToggle .space-bar:nth-child(1)').toggleClass('pointx')
        $('.navToggle .space-bar:nth-child(2)').toggleClass('pointx')
        $('.navToggle .space-bar:nth-child(3)').toggleClass('pointx')

        $('.tooltip').tooltip('hide')
    });

    // Global cancelButton from 'actions.vue;
    $('html, body').keyup(function(e){
        // ESC        
        if(e.keyCode === 27){                                  
            if($('.actionButton').prop('disabled')){
            //    $('.globalCancel').trigger("click");                
            }
        }
    })      
    
    
    $('.navLink').click(function(e){
        // console.log('navLink clicked')
        $('.globalCancel').trigger("click");        
        $('.interActiveBtn').prop('disabled', false);
        e.preventDefault();        
        
    })
});


