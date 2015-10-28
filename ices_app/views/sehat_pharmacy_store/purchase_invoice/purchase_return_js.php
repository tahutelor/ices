<script>
    var ref_id = '<?php echo $ref_id ?>';
    var ref_type = '<?php echo $ref_type ?>';
    var ref_text = '<?php echo $ref_text ?>';
    
    purchase_return_init();
    purchase_return_bind_event();
    
    purchase_return_methods.after_submit = function(){
        $('#modal_purchase_return').modal('hide');
        window.location.href = APP_WINDOW.current_url();
    }
    
    var purchase_return_purchase_return_init = function(){
        var parent_pane = $('#modal_purchase_return')[0];
        purchase_return_components_prepare();
        $('#modal_purchase_return').find('#purchase_return_reference').select2('disable');
    }
    
    $('#purchase_return_new').on('click',function(){
        var parent_pane = $('#modal_purchase_return')[0];
        $(parent_pane).find('#purchase_return_method').val('add');
        $(parent_pane).find('#purchase_return_id').val('');
        purchase_return_purchase_return_init();
        $(parent_pane).find('#purchase_return_reference')
            .select2('data',{id:ref_id,text:ref_text,ref_type:ref_type}).change(); 
       $(parent_pane).find('#purchase_return_reference').select2('disable');
    });
    
    var llinks = $('#purchase_return_table').find('a');
    $.each(llinks, function(key, val){
        
        $(val).off('click');
        $(val).on('click',function(e){
            e.preventDefault();
            var lid = $(val).attr('href');
            var parent_pane = $('#modal_purchase_return')[0];
            $(parent_pane).find('#purchase_return_method').val('view');
            $(parent_pane).find('#purchase_return_id').val(lid);
            purchase_return_purchase_return_init();
            $(parent_pane).modal('show');
        });

    });
    
</script>