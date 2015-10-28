<script>
    var product_stock_opname_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var product_stock_opname_ajax_url = null;
    var product_stock_opname_index_url = null;
    var product_stock_opname_view_url = null;
    var product_stock_opname_window_scroll = null;
    var product_stock_opname_data_support_url = null;
    var product_stock_opname_common_ajax_listener = null;
    var product_stock_opname_component_prefix_id = '';

    var product_stock_opname_init = function () {
        var parent_pane = product_stock_opname_parent_pane;

        product_stock_opname_ajax_url = '<?php echo $ajax_url ?>';
        product_stock_opname_index_url = '<?php echo $index_url ?>';
        product_stock_opname_view_url = '<?php echo $view_url ?>';
        product_stock_opname_window_scroll = '<?php echo $window_scroll; ?>';
        product_stock_opname_data_support_url = '<?php echo $data_support_url; ?>';
        product_stock_opname_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        product_stock_opname_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var product_stock_opname_data = {
    }

    var product_stock_opname_methods = {
        hide_all: function () {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
            
        },
        disable_all: function () {
            var lparent_pane = product_stock_opname_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            product_stock_opname_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_store').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_product_stock_opname_date').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_warehouse').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_checker').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_product_stock_opname_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notes').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_pso_product').closest('div [class*="form-group"]').show();
                    break;
            }

            switch (lmethod) {
                case 'add':
                    $(lparent_pane).find(lprefix_id+'_supplier_detail '+lprefix_id+'_btn_supplier_new').show();
                    break;
                case 'view':
                    
                    break;
            }
        },
        enable_disable: function () {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            product_stock_opname_methods.disable_all();

            switch (lmethod) {
                case "add":                
                    $(lparent_pane).find(lprefix_id + '_store').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_product_stock_opname_date").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + '_warehouse').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_checker").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_product_stock_opname_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id+'_product_stock_opname_date').datetimepicker({
                value:APP_GENERATOR.CURR_DATETIME('minute', 10,'F d, Y H:i')
            });
            APP_FORM.status.default_status_set(
                'product_stock_opname',
                $(lparent_pane).find(lprefix_id + '_product_stock_opname_status')
            );
            
            APP_COMPONENT.input_select.default_set($(lparent_pane).find(lprefix_id+'_store'));
            APP_COMPONENT.input_select.default_set($(lparent_pane).find(lprefix_id+'_warehouse'));
            
            $(lparent_pane).find(lprefix_id + '_notes').val('');
            
             
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;
            var lajax_url = product_stock_opname_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var product_stock_opname_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                product_stock_opname: {},
                product_stock_opname_product:[],
                message_session: true
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    json_data.product_stock_opname.product_stock_opname_status = $(lparent_pane).find(lprefix_id + "_product_stock_opname_status").select2('val');
                    json_data.product_stock_opname.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    break;
            
            }

            switch (lmethod) {
                case 'add':
                    json_data.product_stock_opname.store_id = $(lparent_pane).find(lprefix_id + "_store").select2('val');
                    json_data.product_stock_opname.warehouse_id = $(lparent_pane).find(lprefix_id + "_warehouse").select2('val');
                    json_data.product_stock_opname.checker = $(lparent_pane).find(lprefix_id + "_checker").val();
                    json_data.pso_product = product_stock_opname_tbl_pso_product_method.setting.func_get_data_table().pso_product;
                    break;
                case 'view':
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'product_stock_opname_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_product_stock_opname_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + product_stock_opname_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
        
        
    }

    var product_stock_opname_bind_event = function () {
        var lparent_pane = product_stock_opname_parent_pane;
        var lprefix_id = product_stock_opname_component_prefix_id;
               
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: product_stock_opname_methods,
            view_url: product_stock_opname_view_url,
            prefix_id:lprefix_id,
            window_scroll:product_stock_opname_window_scroll,
        });
        
        $(lparent_pane).find(lprefix_id+'_warehouse').on('change',function(){
            var ldata = $(this).select2('data');
            
            product_stock_opname_tbl_pso_product_method.reset();
            product_stock_opname_tbl_pso_product_method.head_generate();
            
            if(ldata!== null){
                product_stock_opname_pso_product_methods.load_product({pso_product:[]});
            }
        });
        
        product_stock_opname_pso_product_bind_event();
    }

    var product_stock_opname_components_prepare = function () {
        var lparent_pane = product_stock_opname_parent_pane;
        var lprefix_id = product_stock_opname_component_prefix_id;
        var method = $(product_stock_opname_parent_pane).find(lprefix_id + "_method").val();

        var product_stock_opname_data_set = function () {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;
            switch (method) {
                case "add":
                    product_stock_opname_methods.reset_all();
                    break;
                case "view":
                    var product_stock_opname_id = $(product_stock_opname_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: product_stock_opname_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(product_stock_opname_data_support_url + "product_stock_opname_get", json_data).response;
                    if (lresponse !== []) {
                        var lproduct_stock_opname = lresponse.product_stock_opname;
                        $(lparent_pane).find(lprefix_id + '_store').select2('data',{id:lproduct_stock_opname.store_id}).change();
                        $(lparent_pane).find(lprefix_id + '_code').val(lproduct_stock_opname.code);
                        $(lparent_pane).find(lprefix_id + '_warehouse').select2('data',{id:lproduct_stock_opname.warehouse_id}).change();
                        $(lparent_pane).find(lprefix_id + '_notes').val(lproduct_stock_opname.notes);
                        $(lparent_pane).find(lprefix_id + '_checker').val(lproduct_stock_opname.checker);
                        
                        product_stock_opname_pso_product_methods.load_product({pso_product:lresponse.pso_product});
                        
                        $(lparent_pane).find(lprefix_id + '_product_stock_opname_status')
                                .select2('data', lproduct_stock_opname.product_stock_opname_status).change();

                        $(lparent_pane).find(lprefix_id + '_product_stock_opname_status')
                                .select2({data: lresponse.product_stock_opname_status_list});
                        
                        $(lparent_pane).find(lprefix_id + '_product_stock_opname_cancellation_reason')
                                .val(lproduct_stock_opname.cancellation_reason);

                    }
                    ;
                    break;
            }
        }

        product_stock_opname_methods.enable_disable();
        product_stock_opname_methods.show_hide();
        product_stock_opname_data_set();
    }

</script>