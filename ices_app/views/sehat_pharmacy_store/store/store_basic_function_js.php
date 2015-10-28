<script>
    var store_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var store_ajax_url = null;
    var store_index_url = null;
    var store_view_url = null;
    var store_window_scroll = null;
    var store_data_support_url = null;
    var store_common_ajax_listener = null;
    var store_component_prefix_id = '';

    var store_init = function () {
        var parent_pane = store_parent_pane;

        store_ajax_url = '<?php echo $ajax_url ?>';
        store_index_url = '<?php echo $index_url ?>';
        store_view_url = '<?php echo $view_url ?>';
        store_window_scroll = '<?php echo $window_scroll; ?>';
        store_data_support_url = '<?php echo $data_support_url; ?>';
        store_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        store_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var store_data = {
    }

    var store_methods = {
        hide_all: function () {
            var lparent_pane = store_parent_pane;
            var lprefix_id = store_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = store_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = store_parent_pane;
            var lprefix_id = store_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            store_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_store_status').closest('div [class*="form-group"]').show();
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
            var lparent_pane = store_parent_pane;
            var lprefix_id = store_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            store_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':

                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_store_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_birthdate").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = store_parent_pane;
            var lprefix_id = store_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            $(lparent_pane).find(lprefix_id + '_notes').val('');

            APP_FORM.status.default_status_set(
                    'store',
                    $(lparent_pane).find(lprefix_id + '_store_status')
                    );

            store_address_methods.load_address({address: []});
            store_phone_number_methods.load_phone_number({phone_number: []});
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = store_parent_pane;
            var lprefix_id = store_component_prefix_id;
            var lajax_url = store_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var store_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                store: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.store.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.store.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.store.store_status = $(lparent_pane).find(lprefix_id + "_store_status").select2('val');
                    json_data.store.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    json_data.address = store_tbl_address_method.setting.func_get_data_table().address;
                    json_data.phone_number = store_tbl_phone_number_method.setting.func_get_data_table().phone_number;
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'store_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_store_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + store_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
    }

    var store_bind_event = function () {
        var lparent_pane = store_parent_pane;
        var lprefix_id = store_component_prefix_id;

        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: store_methods,
            view_url: store_view_url,
            prefix_id:lprefix_id,
            window_scroll:store_window_scroll,
        });

        store_address_bind_event();
        store_phone_number_bind_event();
    }

    var store_components_prepare = function () {
        var lparent_pane = store_parent_pane;
        var lprefix_id = store_component_prefix_id;
        var method = $(store_parent_pane).find(lprefix_id + "_method").val();

        var store_data_set = function () {
            var lparent_pane = store_parent_pane;
            var lprefix_id = store_component_prefix_id;
            switch (method) {
                case "add":
                    store_methods.reset_all();
                    break;
                case "view":
                    var store_id = $(store_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: store_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(store_data_support_url + "store_get", json_data).response;
                    if (lresponse !== []) {
                        var lstore = lresponse.store;
                        $(lparent_pane).find(lprefix_id + '_code').val(lstore.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lstore.name);
                        $(lparent_pane).find(lprefix_id + '_method_name').val(lstore.method);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lstore.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_store_status')
                                .select2('data', lstore.store_status).change();

                        $(lparent_pane).find(lprefix_id + '_store_status')
                                .select2({data: lresponse.store_status_list});

                        store_address_methods.load_address({address: lresponse.address});
                        store_phone_number_methods.load_phone_number({phone_number: lresponse.phone_number});
                    }
                    ;
                    break;
            }
        }

        store_methods.enable_disable();
        store_methods.show_hide();
        store_data_set();
    }

</script>