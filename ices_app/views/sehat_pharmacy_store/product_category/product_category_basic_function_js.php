<script>
    var product_category_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var product_category_ajax_url = null;
    var product_category_index_url = null;
    var product_category_view_url = null;
    var product_category_window_scroll = null;
    var product_category_data_support_url = null;
    var product_category_common_ajax_listener = null;
    var product_category_component_prefix_id = '';

    var product_category_init = function () {
        var parent_pane = product_category_parent_pane;

        product_category_ajax_url = '<?php echo $ajax_url ?>';
        product_category_index_url = '<?php echo $index_url ?>';
        product_category_view_url = '<?php echo $view_url ?>';
        product_category_window_scroll = '<?php echo $window_scroll; ?>';
        product_category_data_support_url = '<?php echo $data_support_url; ?>';
        product_category_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        product_category_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var product_category_data = {
    }
    
    var product_category_prnt_product_category_extra_param_get = function(){
        var lparent_pane = product_category_parent_pane;
        var lprefix_id = product_category_component_prefix_id;
        
        var lresult = {product_category_id:$(lparent_pane).find(lprefix_id+'_id').val()};
        
        return lresult;
    }
    
    var product_category_methods = {
        hide_all: function () {
            var lparent_pane = product_category_parent_pane;
            var lprefix_id = product_category_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = product_category_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = product_category_parent_pane;
            var lprefix_id = product_category_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            product_category_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notes').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_product_category_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_prnt_product_category').closest('div [class*="form-group"]').show();
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
            var lparent_pane = product_category_parent_pane;
            var lprefix_id = product_category_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            product_category_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_print_product_category_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_product_category_status').select2('enable');
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = product_category_parent_pane;
            var lprefix_id = product_category_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            $(lparent_pane).find(lprefix_id + '_notes').val('');

            APP_FORM.status.default_status_set(
                    'product_category',
                    $(lparent_pane).find(lprefix_id + '_product_category_status')
                    );
            $(lparent_pane).find(lprefix_id+'_prnt_product_category').select2('data',null);
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = product_category_parent_pane;
            var lprefix_id = product_category_component_prefix_id;
            var lajax_url = product_category_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var product_category_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                product_category: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.product_category.prnt_product_category_id = $(lparent_pane).find(lprefix_id + "_prnt_product_category").select2('val');
                    json_data.product_category.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.product_category.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.product_category.product_category_status = $(lparent_pane).find(lprefix_id + "_product_category_status").select2('val');
                    json_data.product_category.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'product_category_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_product_category_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + product_category_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
        
    }

    var product_category_bind_event = function () {
        var lparent_pane = product_category_parent_pane;
        var lprefix_id = product_category_component_prefix_id;

        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: product_category_methods,
            view_url: product_category_view_url,
            prefix_id:lprefix_id,
            window_scroll:product_category_window_scroll,
        });
        
    }

    var product_category_components_prepare = function () {
        var lparent_pane = product_category_parent_pane;
        var lprefix_id = product_category_component_prefix_id;
        var method = $(product_category_parent_pane).find(lprefix_id + "_method").val();

        var product_category_data_set = function () {
            var lparent_pane = product_category_parent_pane;
            var lprefix_id = product_category_component_prefix_id;
            switch (method) {
                case "add":
                    product_category_methods.reset_all();
                    break;
                case "view":
                    var product_category_id = $(product_category_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: product_category_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(product_category_data_support_url + "product_category_get", json_data).response;
                    if (lresponse !== []) {
                        var lproduct_category = lresponse.product_category;
                        $(lparent_pane).find(lprefix_id + '_code').val(lproduct_category.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lproduct_category.name);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lproduct_category.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_prnt_product_category')
                                .select2('data', lproduct_category.prnt_product_category);
                        
                        $(lparent_pane).find(lprefix_id + '_product_category_status')
                                .select2('data', lproduct_category.product_category_status).change();

                        $(lparent_pane).find(lprefix_id + '_product_category_status')
                                .select2({data: lresponse.product_category_status_list});

                    }
                    ;
                    break;
            }
        }

        product_category_methods.enable_disable();
        product_category_methods.show_hide();
        product_category_data_set();
    }

</script>