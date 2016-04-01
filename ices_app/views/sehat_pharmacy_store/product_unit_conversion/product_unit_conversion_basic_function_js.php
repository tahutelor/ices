<script>
    var product_unit_conversion_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var product_unit_conversion_ajax_url = null;
    var product_unit_conversion_index_url = null;
    var product_unit_conversion_view_url = null;
    var product_unit_conversion_window_scroll = null;
    var product_unit_conversion_data_support_url = null;
    var product_unit_conversion_common_ajax_listener = null;
    var product_unit_conversion_component_prefix_id = '';

    var product_unit_conversion_init = function () {
        var parent_pane = product_unit_conversion_parent_pane;

        product_unit_conversion_ajax_url = '<?php echo $ajax_url ?>';
        product_unit_conversion_index_url = '<?php echo $index_url ?>';
        product_unit_conversion_view_url = '<?php echo $view_url ?>';
        product_unit_conversion_window_scroll = '<?php echo $window_scroll; ?>';
        product_unit_conversion_data_support_url = '<?php echo $data_support_url; ?>';
        product_unit_conversion_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        product_unit_conversion_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var product_unit_conversion_data = {
        unit_default:<?php echo json_encode($unit_default); ?>,
    }

    var product_unit_conversion_methods = {
        hide_all: function () {
            var lparent_pane = product_unit_conversion_parent_pane;
            var lprefix_id = product_unit_conversion_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = product_unit_conversion_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = product_unit_conversion_parent_pane;
            var lprefix_id = product_unit_conversion_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            product_unit_conversion_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_qty').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_unit').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_qty2').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_unit2').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_product_unit_conversion_status').closest('div [class*="form-group"]').show();
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
            var lparent_pane = product_unit_conversion_parent_pane;
            var lprefix_id = product_unit_conversion_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            product_unit_conversion_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_qty").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_unit').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_qty2").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_product_unit_conversion_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_unit2').select2('enable');
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = product_unit_conversion_parent_pane;
            var lprefix_id = product_unit_conversion_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_qty').val('1').blur();
            $(lparent_pane).find(lprefix_id+'_unit').select2('data',product_unit_conversion_data.unit_default);
            $(lparent_pane).find(lprefix_id + '_qty2').val('1').blur();
            $(lparent_pane).find(lprefix_id+'_unit2').select2('data',product_unit_conversion_data.unit_default);
            APP_FORM.status.default_status_set(
                    'product_unit_conversion',
                    $(lparent_pane).find(lprefix_id + '_product_unit_conversion_status')
                    );
            
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = product_unit_conversion_parent_pane;
            var lprefix_id = product_unit_conversion_component_prefix_id;
            var lajax_url = product_unit_conversion_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var product_unit_conversion_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                product_unit_conversion: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.product_unit_conversion.product_id = $(lparent_pane).find(lprefix_id + "_product_id").val();
                    json_data.product_unit_conversion.qty = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_qty").val());
                    json_data.product_unit_conversion.unit_id = $(lparent_pane).find(lprefix_id + "_unit").select2('val');
                    json_data.product_unit_conversion.qty2 = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_qty2").val());
                    json_data.product_unit_conversion.unit_id2 = $(lparent_pane).find(lprefix_id + "_unit2").select2('val');
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'product_unit_conversion_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_product_unit_conversion_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + product_unit_conversion_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            
            return lresult;
            
        },
        
        
    }

    var product_unit_conversion_bind_event = function () {
        var lparent_pane = product_unit_conversion_parent_pane;
        var lprefix_id = product_unit_conversion_component_prefix_id;

        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: product_unit_conversion_methods,
            view_url: product_unit_conversion_view_url,
            prefix_id:lprefix_id,
            window_scroll:product_unit_conversion_window_scroll,
        });
        
        APP_COMPONENT.input.numeric($(lparent_pane).find(lprefix_id+'_qty'),{reset:true,min_val:1});
        APP_COMPONENT.input.numeric($(lparent_pane).find(lprefix_id+'_qty2'),{reset:true,min_val:1});
    }

    var product_unit_conversion_components_prepare = function () {
        var lparent_pane = product_unit_conversion_parent_pane;
        var lprefix_id = product_unit_conversion_component_prefix_id;
        var method = $(product_unit_conversion_parent_pane).find(lprefix_id + "_method").val();

        var product_unit_conversion_data_set = function () {
            var lparent_pane = product_unit_conversion_parent_pane;
            var lprefix_id = product_unit_conversion_component_prefix_id;
            switch (method) {
                case "add":
                    product_unit_conversion_methods.reset_all();
                    break;
                case "view":
                    var product_unit_conversion_id = $(product_unit_conversion_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: product_unit_conversion_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(product_unit_conversion_data_support_url + "product_unit_conversion_get", json_data).response;
                    if (lresponse !== []) {
                        var lproduct_unit_conversion = lresponse.product_unit_conversion;
                        
                        $(lparent_pane).find(lprefix_id + '_qty')
                                .val(APP_CONVERTER._float(lproduct_unit_conversion.qty)).blur();
                        $(lparent_pane).find(lprefix_id + '_unit')
                                .select2('data', lproduct_unit_conversion.unit);
                        $(lparent_pane).find(lprefix_id + '_qty2')
                                .val(APP_CONVERTER._float(lproduct_unit_conversion.qty2)).blur();
                       
                        $(lparent_pane).find(lprefix_id + '_unit2')
                                .select2('data', lproduct_unit_conversion.unit2);
                        
                        $(lparent_pane).find(lprefix_id + '_product_unit_conversion_status')
                                .select2('data', lproduct_unit_conversion.product_unit_conversion_status).change();

                        $(lparent_pane).find(lprefix_id + '_product_unit_conversion_status')
                                .select2({data: lresponse.product_unit_conversion_status_list});

                    }
                    ;
                    break;
            }
        }

        product_unit_conversion_methods.enable_disable();
        product_unit_conversion_methods.show_hide();
        product_unit_conversion_data_set();
    }

</script>