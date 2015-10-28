<script>
    var ref_id = '<?php echo $ref_id ?>';
    var ref_type = '<?php echo $ref_type ?>';
    var ref_text = '<?php echo $ref_text ?>';
    
    sales_receipt_init();
    sales_receipt_bind_event();
    
    sales_receipt_methods.after_submit = function(){
        $('#modal_sales_receipt').modal('hide');
        window.location.href = APP_WINDOW.current_url();
    }
    
    var sales_receipt_sales_receipt_init = function(){
        var parent_pane = $('#modal_sales_receipt')[0];
        sales_receipt_components_prepare();
        $('#modal_sales_receipt').find('#sales_receipt_reference').select2('disable');
    }
    
    $('#sales_receipt_new').on('click',function(){
        var parent_pane = $('#modal_sales_receipt')[0];
        $(parent_pane).find('#sales_receipt_method').val('add');
        $(parent_pane).find('#sales_receipt_id').val('');
        sales_receipt_sales_receipt_init();
        $(parent_pane).find('#sales_receipt_reference')
            .select2('data',{id:ref_id,text:ref_text,ref_type:ref_type}).change(); 
       $(parent_pane).find('#sales_receipt_reference').select2('disable');
    });
    
    var llinks = $('#sales_receipt_table').find('a');
    $.each(llinks, function(key, val){
        
        $(val).off('click');
        $(val).on('click',function(e){
            e.preventDefault();
            var lid = $(val).attr('href');
            var parent_pane = $('#modal_sales_receipt')[0];
            $(parent_pane).find('#sales_receipt_method').val('view');
            $(parent_pane).find('#sales_receipt_id').val(lid);
            sales_receipt_sales_receipt_init();
            $(parent_pane).modal('show');
        });

    });
    
</script>