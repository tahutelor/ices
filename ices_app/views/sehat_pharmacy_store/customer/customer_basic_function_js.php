<script>
    var customer_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var customer_ajax_url = null;
    var customer_index_url = null;
    var customer_view_url = null;
    var customer_window_scroll = null;
    var customer_data_support_url = null;
    var customer_common_ajax_listener = null;
    var customer_component_prefix_id = '';

    var customer_init = function () {
        var parent_pane = customer_parent_pane;

        customer_ajax_url = '<?php echo $ajax_url ?>';
        customer_index_url = '<?php echo $index_url ?>';
        customer_view_url = '<?php echo $view_url ?>';
        customer_window_scroll = '<?php echo $window_scroll; ?>';
        customer_data_support_url = '<?php echo $data_support_url; ?>';
        customer_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        customer_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var customer_data = {
        customer_type_default:null,
        bool_arr:<?php echo json_encode($bool_arr); ?>
    }

    var customer_methods = {
        hide_all: function () {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = customer_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            customer_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer_type').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_si_customer_default').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_birthdate').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_mail_address').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_address').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_phone_number').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer_debit_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer_credit_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notes').closest('div [class*="form-group"]').show();
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
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            customer_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_customer_type').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_si_customer_default').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_customer_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_birthdate").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_customer_debit_amount').val('0.00');
                    $(lparent_pane).find(lprefix_id + '_customer_credit_amount').val('0.00');
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            $(lparent_pane).find(lprefix_id + '_birthdate').val(null);
            
            APP_FORM.status.default_status_set(
                    'customer',
                    $(lparent_pane).find(lprefix_id + '_customer_status')
                    );
            
            $(lparent_pane).find(lprefix_id+'_customer_type').select2('data',customer_data.customer_type_default);
            $(lparent_pane).find(lprefix_id+'_si_customer_default').select2('data',customer_data.bool_arr[1]);
            
            $(lparent_pane).find(lprefix_id + '_notes').val('');
                    
            customer_mail_address_methods.load_mail_address({mail_address: []});
            customer_address_methods.load_address({address: []});
            customer_phone_number_methods.load_phone_number({phone_number: []});

        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var lajax_url = customer_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var customer_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                customer: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.customer.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.customer.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.customer.customer_type_id = $(lparent_pane).find(lprefix_id + "_customer_type").select2('val');
                    json_data.customer.si_customer_default = $(lparent_pane).find(lprefix_id + "_si_customer_default").select2('val');
                    json_data.customer.customer_status = $(lparent_pane).find(lprefix_id + "_customer_status").select2('val');
                    json_data.customer.birthdate = $(lparent_pane).find(lprefix_id + "_birthdate").val() === ''?null:(new Date($(lparent_pane).find(lprefix_id + "_birthdate").val())).format('Y-m-d H:i:s');
                    json_data.customer.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    json_data.mail_address = customer_tbl_mail_address_method.setting.func_get_data_table().mail_address;
                    json_data.address = customer_tbl_address_method.setting.func_get_data_table().address;
                    json_data.phone_number = customer_tbl_phone_number_method.setting.func_get_data_table().phone_number;
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'customer_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_customer_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + customer_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
        customer_type_list_set:function(){
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var lajax_url = customer_data_support_url+'customer_type_list_get/';
            var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url,{}).response;
            var lis_customer_type = $(lparent_pane).find(lprefix_id+'_customer_type');
            APP_COMPONENT.input_select.empty($(lis_customer_type));
            $(lis_customer_type).select2({data:lresponse,placeholder:'Type something to search',allowClear:false});
        },
        customer_type_default_set:function(){
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var lajax_url = customer_data_support_url+'customer_type_default_get/';
            var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url,{}).response;
            customer_data.customer_type_default = lresponse;
        }
    }

    var customer_bind_event = function () {
        var lparent_pane = customer_parent_pane;
        var lprefix_id = customer_component_prefix_id;

        customer_methods.customer_type_list_set();
        customer_methods.customer_type_default_set();
       
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: customer_methods,
            view_url: customer_view_url,
            prefix_id:lprefix_id,
            window_scroll:customer_window_scroll,
        });

        customer_mail_address_bind_event();
        customer_address_bind_event();
        customer_phone_number_bind_event();
        
    }

    var customer_components_prepare = function () {
        var lparent_pane = customer_parent_pane;
        var lprefix_id = customer_component_prefix_id;
        var method = $(customer_parent_pane).find(lprefix_id + "_method").val();

        var customer_data_set = function () {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            switch (method) {
                case "add":
                    customer_methods.reset_all();
                    break;
                case "view":
                    var customer_id = $(customer_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: customer_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(customer_data_support_url + "customer_get", json_data).response;
                    if (lresponse !== []) {
                        var lcustomer = lresponse.customer;
                        $(lparent_pane).find(lprefix_id + '_code').val(lcustomer.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lcustomer.name);
                        $(lparent_pane).find(lprefix_id + '_customer_debit_amount').val(APP_CONVERTER.thousand_separator(lcustomer.customer_debit_amount));
                        $(lparent_pane).find(lprefix_id + '_customer_credit_amount').val(APP_CONVERTER.thousand_separator(lcustomer.customer_credit_amount));
                        $(lparent_pane).find(lprefix_id + '_notes').val(lcustomer.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_customer_type')
                                .select2('data', lcustomer.customer_type);
                        
                        $(lparent_pane).find(lprefix_id + '_si_customer_default')
                                .select2('data', lcustomer.si_customer_default);
                        
                        $(lparent_pane).find(lprefix_id + '_customer_status')
                                .select2('data', lcustomer.customer_status).change();

                        $(lparent_pane).find(lprefix_id + '_customer_status')
                                .select2({data: lresponse.customer_status_list});

                        $(lparent_pane).find(lprefix_id + '_birthdate').val(lcustomer.birthdate!== null?APP_CONVERTER._date(lcustomer.birthdate, 'F d, Y H:i'):null);
                        customer_mail_address_methods.load_mail_address({mail_address: lresponse.mail_address});
                        customer_address_methods.load_address({address: lresponse.address});
                        customer_phone_number_methods.load_phone_number({phone_number: lresponse.phone_number});
                        
                        
                    }
                    ;
                    break;
            }
        }

        customer_methods.enable_disable();
        customer_methods.show_hide();
        customer_data_set();
    }

</script>