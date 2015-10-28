<script>
    var warehouse_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var warehouse_ajax_url = null;
    var warehouse_index_url = null;
    var warehouse_view_url = null;
    var warehouse_window_scroll = null;
    var warehouse_data_support_url = null;
    var warehouse_common_ajax_listener = null;
    var warehouse_component_prefix_id = '';

    var warehouse_init = function () {
        var parent_pane = warehouse_parent_pane;

        warehouse_ajax_url = '<?php echo $ajax_url ?>';
        warehouse_index_url = '<?php echo $index_url ?>';
        warehouse_view_url = '<?php echo $view_url ?>';
        warehouse_window_scroll = '<?php echo $window_scroll; ?>';
        warehouse_data_support_url = '<?php echo $data_support_url; ?>';
        warehouse_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        warehouse_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var warehouse_data = {
    }

    var warehouse_methods = {
        hide_all: function () {
            var lparent_pane = warehouse_parent_pane;
            var lprefix_id = warehouse_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = warehouse_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = warehouse_parent_pane;
            var lprefix_id = warehouse_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            warehouse_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_warehouse_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_address').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_phone_number').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notes').closest('div [class*="form-group"]').show();
                    break;
            }

            switch (lmethod) {
                case 'add':

                    break;
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_btn_delete').show();
                    break;
            }
        },
        enable_disable: function () {
            var lparent_pane = warehouse_parent_pane;
            var lprefix_id = warehouse_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            warehouse_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_warehouse_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_birthdate").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = warehouse_parent_pane;
            var lprefix_id = warehouse_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            $(lparent_pane).find(lprefix_id + '_notes').val('');

            APP_FORM.status.default_status_set(
                    'warehouse',
                    $(lparent_pane).find(lprefix_id + '_warehouse_status')
                    );

            warehouse_address_methods.load_address({address: []});
            warehouse_phone_number_methods.load_phone_number({phone_number: []});
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = warehouse_parent_pane;
            var lprefix_id = warehouse_component_prefix_id;
            var lajax_url = warehouse_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var warehouse_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                warehouse: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.warehouse.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.warehouse.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.warehouse.warehouse_status = $(lparent_pane).find(lprefix_id + "_warehouse_status").select2('val');
                    json_data.warehouse.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    json_data.address = warehouse_tbl_address_method.setting.func_get_data_table().address;
                    json_data.phone_number = warehouse_tbl_phone_number_method.setting.func_get_data_table().phone_number;
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'warehouse_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_warehouse_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + warehouse_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
    }

    var warehouse_bind_event = function () {
        var lparent_pane = warehouse_parent_pane;
        var lprefix_id = warehouse_component_prefix_id;

        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: warehouse_methods,
            view_url: warehouse_view_url,
            prefix_id:lprefix_id,
            window_scroll:warehouse_window_scroll,
        });

        warehouse_address_bind_event();
        warehouse_phone_number_bind_event();
    }

    var warehouse_components_prepare = function () {
        var lparent_pane = warehouse_parent_pane;
        var lprefix_id = warehouse_component_prefix_id;
        var method = $(warehouse_parent_pane).find(lprefix_id + "_method").val();

        var warehouse_data_set = function () {
            var lparent_pane = warehouse_parent_pane;
            var lprefix_id = warehouse_component_prefix_id;
            switch (method) {
                case "add":
                    warehouse_methods.reset_all();
                    break;
                case "view":
                    var warehouse_id = $(warehouse_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: warehouse_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(warehouse_data_support_url + "warehouse_get", json_data).response;
                    if (lresponse !== []) {
                        var lwarehouse = lresponse.warehouse;
                        $(lparent_pane).find(lprefix_id + '_code').val(lwarehouse.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lwarehouse.name);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lwarehouse.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_warehouse_status')
                                .select2('data', lwarehouse.warehouse_status).change();

                        $(lparent_pane).find(lprefix_id + '_warehouse_status')
                                .select2({data: lresponse.warehouse_status_list});

                        warehouse_address_methods.load_address({address: lresponse.address});
                        warehouse_phone_number_methods.load_phone_number({phone_number: lresponse.phone_number});
                    }
                    ;
                    break;
            }
        }

        warehouse_methods.enable_disable();
        warehouse_methods.show_hide();
        warehouse_data_set();
    }

</script>