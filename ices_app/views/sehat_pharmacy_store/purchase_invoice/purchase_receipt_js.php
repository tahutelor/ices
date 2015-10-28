<script>
    var ref_id = '<?php echo $ref_id ?>';
    var ref_type = '<?php echo $ref_type ?>';
    var ref_text = '<?php echo $ref_text ?>';
    
    purchase_receipt_init();
    purchase_receipt_bind_event();
    
    purchase_receipt_methods.after_submit = function(){
        $('#modal_purchase_receipt').modal('hide');
        window.location.href = APP_WINDOW.current_url();
    }
    
    var purchase_receipt_purchase_receipt_init = function(){
        var parent_pane = $('#modal_purchase_receipt')[0];
        purchase_receipt_components_prepare();
        $('#modal_purchase_receipt').find('#purchase_receipt_reference').select2('disable');
    }
    
    $('#purchase_receipt_new').on('click',function(){
        var parent_pane = $('#modal_purchase_receipt')[0];
        $(parent_pane).find('#purchase_receipt_method').val('add');
        $(parent_pane).find('#purchase_receipt_id').val('');
        purchase_receipt_purchase_receipt_init();
        $(parent_pane).find('#purchase_receipt_reference')
            .select2('data',{id:ref_id,text:ref_text,ref_type:ref_type}).change(); 
       $(parent_pane).find('#purchase_receipt_reference').select2('disable');
    });
    
    var llinks = $('#purchase_receipt_table').find('a');
    $.each(llinks, function(key, val){
        
        $(val).off('click');
        $(val).on('click',function(e){
            e.preventDefault();
            var lid = $(val).attr('href');
            var parent_pane = $('#modal_purchase_receipt')[0];
            $(parent_pane).find('#purchase_receipt_method').val('view');
            $(parent_pane).find('#purchase_receipt_id').val(lid);
            purchase_receipt_purchase_receipt_init();
            $(parent_pane).modal('show');
        });

    });
    
</script>