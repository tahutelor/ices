<script>
    var lproduct_id = '<?php echo $product_id ?>';

    product_unit_conversion_init();
    product_unit_conversion_bind_event();
    
    product_unit_conversion_methods.after_submit = function(){
        $('#modal_product_unit_conversion').modal('hide');
        window.location.href = APP_WINDOW.current_url();
    }
    
    var product_product_unit_conversion_init = function(){
        var parent_pane = $('#modal_product_unit_conversion')[0];
        product_unit_conversion_components_prepare();
    }
    
    $('#product_unit_conversion_new').on('click',function(){
        var parent_pane = $('#modal_product_unit_conversion')[0];
        $(parent_pane).find('#product_unit_conversion_method').val('add');
        $(parent_pane).find('#product_unit_conversion_id').val('');
        product_product_unit_conversion_init();
        $(parent_pane).find('#product_unit_conversion_product_id').val(lproduct_id);
        $(parent_pane).find('#product_unit_conversion_reference').select2('disable');
    });
    
    var llinks = $('#product_unit_conversion_view_table').find('a');
    $.each(llinks, function(key, val){
        
        $(val).off('click');
        $(val).on('click',function(e){
            e.preventDefault();
            var lid = $(val).attr('href');
            var parent_pane = $('#modal_product_unit_conversion')[0];
            $(parent_pane).find('#product_unit_conversion_method').val('view');
            $(parent_pane).find('#product_unit_conversion_id').val(lid);
            $(parent_pane).find('#product_unit_conversion_product_id').val(lproduct_id);
            product_product_unit_conversion_init();
            $(parent_pane).modal('show');
        });

    });
    
</script>