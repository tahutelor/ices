<script>
    var customer_type_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var customer_type_ajax_url = null;
    var customer_type_index_url = null;
    var customer_type_view_url = null;
    var customer_type_window_scroll = null;
    var customer_type_data_support_url = null;
    var customer_type_common_ajax_listener = null;
    var customer_type_component_prefix_id = '';

    var customer_type_init = function () {
        var parent_pane = customer_type_parent_pane;

        customer_type_ajax_url = '<?php echo $ajax_url ?>';
        customer_type_index_url = '<?php echo $index_url ?>';
        customer_type_view_url = '<?php echo $view_url ?>';
        customer_type_window_scroll = '<?php echo $window_scroll; ?>';
        customer_type_data_support_url = '<?php echo $data_support_url; ?>';
        customer_type_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        customer_type_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var customer_type_data = {
        bool_arr:<?php echo json_encode($bool_arr); ?>
    }
    
    var customer_type_prnt_customer_type_extra_param_get = function(){
        var lparent_pane = customer_type_parent_pane;
        var lprefix_id = customer_type_component_prefix_id;
        
        var lresult = {customer_type_id:$(lparent_pane).find(lprefix_id+'_id').val()};
        
        return lresult;
    }
    
    var customer_type_methods = {
        hide_all: function () {
            var lparent_pane = customer_type_parent_pane;
            var lprefix_id = customer_type_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = customer_type_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = customer_type_parent_pane;
            var lprefix_id = customer_type_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            customer_type_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notes').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer_type_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notif_si_outstanding').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer_type_default').closest('div [class*="form-group"]').show();
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
            var lparent_pane = customer_type_parent_pane;
            var lprefix_id = customer_type_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            customer_type_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_customer_type_default').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_notif_si_outstanding').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_customer_type_status').select2('enable');
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = customer_type_parent_pane;
            var lprefix_id = customer_type_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            $(lparent_pane).find(lprefix_id + '_notes').val('');

            APP_FORM.status.default_status_set(
                    'customer_type',
                    $(lparent_pane).find(lprefix_id + '_customer_type_status')
                    );
            
            $(lparent_pane).find(lprefix_id+'_notif_si_outstanding').select2('data',customer_type_data.bool_arr[0]);
            $(lparent_pane).find(lprefix_id+'_customer_type_default').select2('data',customer_type_data.bool_arr[1]);
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = customer_type_parent_pane;
            var lprefix_id = customer_type_component_prefix_id;
            var lajax_url = customer_type_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var customer_type_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                customer_type: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    
                    json_data.customer_type.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.customer_type.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.customer_type.notif_si_outstanding = $(lparent_pane).find(lprefix_id + "_notif_si_outstanding").select2('val');
                    json_data.customer_type.customer_type_default = $(lparent_pane).find(lprefix_id + "_customer_type_default").select2('val');
                    json_data.customer_type.customer_type_status = $(lparent_pane).find(lprefix_id + "_customer_type_status").select2('val');                    
                    json_data.customer_type.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'customer_type_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_customer_type_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + customer_type_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
        
    }

    var customer_type_bind_event = function () {
        var lparent_pane = customer_type_parent_pane;
        var lprefix_id = customer_type_component_prefix_id;

        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: customer_type_methods,
            view_url: customer_type_view_url,
            prefix_id:lprefix_id,
            window_scroll:customer_type_window_scroll,
        });
        
    }

    var customer_type_components_prepare = function () {
        var lparent_pane = customer_type_parent_pane;
        var lprefix_id = customer_type_component_prefix_id;
        var method = $(customer_type_parent_pane).find(lprefix_id + "_method").val();

        var customer_type_data_set = function () {
            var lparent_pane = customer_type_parent_pane;
            var lprefix_id = customer_type_component_prefix_id;
            switch (method) {
                case "add":
                    customer_type_methods.reset_all();
                    break;
                case "view":
                    var customer_type_id = $(customer_type_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: customer_type_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(customer_type_data_support_url + "customer_type_get", json_data).response;
                    if (lresponse !== []) {
                        var lcustomer_type = lresponse.customer_type;
                        $(lparent_pane).find(lprefix_id + '_code').val(lcustomer_type.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lcustomer_type.name);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lcustomer_type.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_notif_si_outstanding')
                                .select2('data', lcustomer_type.notif_si_outstanding);

                        $(lparent_pane).find(lprefix_id + '_customer_type_default')
                                .select2('data', lcustomer_type.customer_type_default);


                        $(lparent_pane).find(lprefix_id + '_customer_type_status')
                                .select2('data', lcustomer_type.customer_type_status).change();

                        $(lparent_pane).find(lprefix_id + '_customer_type_status')
                                .select2({data: lresponse.customer_type_status_list});

                    }
                    ;
                    break;
            }
        }

        customer_type_methods.enable_disable();
        customer_type_methods.show_hide();
        customer_type_data_set();
    }

</script>