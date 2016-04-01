<script>
    var product_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var product_ajax_url = null;
    var product_index_url = null;
    var product_view_url = null;
    var product_window_scroll = null;
    var product_data_support_url = null;
    var product_common_ajax_listener = null;
    var product_component_prefix_id = '';

    var product_init = function () {
        var parent_pane = product_parent_pane;

        product_ajax_url = '<?php echo $ajax_url ?>';
        product_index_url = '<?php echo $index_url ?>';
        product_view_url = '<?php echo $view_url ?>';
        product_window_scroll = '<?php echo $window_scroll; ?>';
        product_data_support_url = '<?php echo $data_support_url; ?>';
        product_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        product_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var product_data = {
        unit_default:<?php echo json_encode($unit_default); ?>,
    }

    var product_methods = {
        hide_all: function () {
            var lparent_pane = product_parent_pane;
            var lprefix_id = product_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = product_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = product_parent_pane;
            var lprefix_id = product_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            product_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_barcode').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_purchase_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_sales_formula').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_sales_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_product_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_product_category').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_unit').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_unit_sales').closest('div [class*="form-group"]').show();
                    break;
            }

            switch (lmethod) {
                case 'add':

                    break;
                case 'view':
                    
                    break;
            }
        },
        enable_disable: function () {
            var lparent_pane = product_parent_pane;
            var lprefix_id = product_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            product_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_barcode").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_purchase_amount").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_sales_formula").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_print_product_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_product_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_unit').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_unit_sales').select2('enable');
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = product_parent_pane;
            var lprefix_id = product_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            $(lparent_pane).find(lprefix_id + '_notes').val('');
            $(lparent_pane).find(lprefix_id + '_purchase_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_sales_formula').val('');
            $(lparent_pane).find(lprefix_id + '_sales_amount').val('0.00');

            APP_FORM.status.default_status_set(
                    'product',
                    $(lparent_pane).find(lprefix_id + '_product_status')
                    );
            $(lparent_pane).find(lprefix_id+'_unit').select2('data',product_data.unit_default).change();
            $(lparent_pane).find(lprefix_id+'_product_category').select2('data',null);
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = product_parent_pane;
            var lprefix_id = product_component_prefix_id;
            var lajax_url = product_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var product_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                product: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.product.product_category_id = $(lparent_pane).find(lprefix_id + "_product_category").select2('val');
                    json_data.product.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.product.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.product.barcode = $(lparent_pane).find(lprefix_id + "_barcode").val();
                    json_data.product.sales_formula = $(lparent_pane).find(lprefix_id + "_sales_formula").val();
                    json_data.product.unit_id = $(lparent_pane).find(lprefix_id + "_unit").select2('val');
                    json_data.product.unit_sales_id = $(lparent_pane).find(lprefix_id + "_unit_sales").select2('val');
                    json_data.product.purchase_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_purchase_amount").val());
                    json_data.product.product_status = $(lparent_pane).find(lprefix_id + "_product_status").select2('val');
                    json_data.product.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'product_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_product_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + product_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
        sales_amount_set:function(){
            var lparent_pane = product_parent_pane;
            var lprefix_id = product_component_prefix_id;
            
            var lformula = $(lparent_pane).find(lprefix_id+'_sales_formula').val();
            var lpurchase_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id+'_purchase_amount').val()).toString();
            lformula = lformula.replace(/c/,lpurchase_amount);
            var lsales_amount = '0';
            try{
                lsales_amount = Math.ceil(APP_CONVERTER._float(eval(lformula))/APP_CONVERTER._float(500))*500; 
            }
            catch(e){}
            
            $(lparent_pane).find(lprefix_id+'_sales_amount').val(APP_CONVERTER.thousand_separator(lsales_amount));
        },
        
    }

    var product_bind_event = function () {
        var lparent_pane = product_parent_pane;
        var lprefix_id = product_component_prefix_id;

        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: product_methods,
            view_url: product_view_url,
            prefix_id:lprefix_id,
            window_scroll:product_window_scroll,
        });
        
        APP_COMPONENT.input.math_func(lprefix_id+'_sales_formula',{});
        
        $(lparent_pane).find(lprefix_id+'_sales_formula').on('keyup',function(){
            product_methods.sales_amount_set();
        });
        
        
        $(lparent_pane).find(lprefix_id+'_unit').on('change',function(){
            var lis_unit_sales = $(lparent_pane).find(lprefix_id+'_unit_sales');
            var ldata = $(this).select2('data');
            if(ldata !== null){
                $(lis_unit_sales).select2('data',ldata);
            }
        });
    }

    var product_components_prepare = function () {
        var lparent_pane = product_parent_pane;
        var lprefix_id = product_component_prefix_id;
        var method = $(product_parent_pane).find(lprefix_id + "_method").val();

        var product_data_set = function () {
            var lparent_pane = product_parent_pane;
            var lprefix_id = product_component_prefix_id;
            switch (method) {
                case "add":
                    product_methods.reset_all();
                    break;
                case "view":
                    var product_id = $(product_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: product_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(product_data_support_url + "product_get", json_data).response;
                    if (lresponse !== []) {
                        var lproduct = lresponse.product;
                        $(lparent_pane).find(lprefix_id + '_code').val(lproduct.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lproduct.name);
                        $(lparent_pane).find(lprefix_id + '_barcode').val(lproduct.barcode);
                        $(lparent_pane).find(lprefix_id + '_sales_formula').val(lproduct.sales_formula);
                        $(lparent_pane).find(lprefix_id + '_purchase_amount').val(APP_CONVERTER.thousand_separator(lproduct.purchase_amount));
                        $(lparent_pane).find(lprefix_id + '_sales_amount').val(APP_CONVERTER.thousand_separator(lproduct.sales_amount));
                        $(lparent_pane).find(lprefix_id + '_notes').val(lproduct.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_unit')
                                .select2('data', lproduct.unit);
                        
                        $(lparent_pane).find(lprefix_id + '_unit_sales')
                                .select2('data', lproduct.unit_sales);
                        
                        $(lparent_pane).find(lprefix_id + '_product_category')
                                .select2('data', lproduct.product_category);
                        
                        $(lparent_pane).find(lprefix_id + '_product_status')
                                .select2('data', lproduct.product_status).change();

                        $(lparent_pane).find(lprefix_id + '_product_status')
                                .select2({data: lresponse.product_status_list});

                    }
                    ;
                    break;
            }
        }

        product_methods.enable_disable();
        product_methods.show_hide();
        product_data_set();
    }

</script>