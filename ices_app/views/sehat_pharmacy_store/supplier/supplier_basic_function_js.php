<script>
    var supplier_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var supplier_ajax_url = null;
    var supplier_index_url = null;
    var supplier_view_url = null;
    var supplier_window_scroll = null;
    var supplier_data_support_url = null;
    var supplier_common_ajax_listener = null;
    var supplier_component_prefix_id = '';

    var supplier_init = function () {
        var parent_pane = supplier_parent_pane;

        supplier_ajax_url = '<?php echo $ajax_url ?>';
        supplier_index_url = '<?php echo $index_url ?>';
        supplier_view_url = '<?php echo $view_url ?>';
        supplier_window_scroll = '<?php echo $window_scroll; ?>';
        supplier_data_support_url = '<?php echo $data_support_url; ?>';
        supplier_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        supplier_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var supplier_data = {
        supplier_type_default:null,
        
    }

    var supplier_methods = {
        hide_all: function () {
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = supplier_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            supplier_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_supplier_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_birthdate').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_mail_address').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_address').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_phone_number').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_supplier_debit_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_supplier_credit_amount').closest('div [class*="form-group"]').show();
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
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            supplier_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_supplier_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_birthdate").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            $(lparent_pane).find(lprefix_id + '_supplier_debit_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_supplier_credit_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_birthdate').val(null);
            
            APP_FORM.status.default_status_set(
                    'supplier',
                    $(lparent_pane).find(lprefix_id + '_supplier_status')
                    );
            
            $(lparent_pane).find(lprefix_id + '_notes').val('');
                    
            supplier_mail_address_methods.load_mail_address({mail_address: []});
            supplier_address_methods.load_address({address: []});
            supplier_phone_number_methods.load_phone_number({phone_number: []});

        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;
            var lajax_url = supplier_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var supplier_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                supplier: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.supplier.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.supplier.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.supplier.supplier_status = $(lparent_pane).find(lprefix_id + "_supplier_status").select2('val');
                    json_data.supplier.birthdate = $(lparent_pane).find(lprefix_id + "_birthdate").val() === ''?null:(new Date($(lparent_pane).find(lprefix_id + "_birthdate").val())).format('Y-m-d H:i:s');
                    json_data.supplier.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    json_data.mail_address = supplier_tbl_mail_address_method.setting.func_get_data_table().mail_address;
                    json_data.address = supplier_tbl_address_method.setting.func_get_data_table().address;
                    json_data.phone_number = supplier_tbl_phone_number_method.setting.func_get_data_table().phone_number;
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'supplier_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_supplier_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + supplier_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
    }

    var supplier_bind_event = function () {
        var lparent_pane = supplier_parent_pane;
        var lprefix_id = supplier_component_prefix_id;
        
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: supplier_methods,
            view_url: supplier_view_url,
            prefix_id:lprefix_id,
            window_scroll:supplier_window_scroll,
        });

        supplier_mail_address_bind_event();
        supplier_address_bind_event();
        supplier_phone_number_bind_event();
        
    }

    var supplier_components_prepare = function () {
        var lparent_pane = supplier_parent_pane;
        var lprefix_id = supplier_component_prefix_id;
        var method = $(supplier_parent_pane).find(lprefix_id + "_method").val();

        var supplier_data_set = function () {
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;
            switch (method) {
                case "add":
                    supplier_methods.reset_all();
                    break;
                case "view":
                    var supplier_id = $(supplier_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: supplier_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(supplier_data_support_url + "supplier_get", json_data).response;
                    if (lresponse !== []) {
                        var lsupplier = lresponse.supplier;
                        $(lparent_pane).find(lprefix_id + '_code').val(lsupplier.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lsupplier.name);
                        $(lparent_pane).find(lprefix_id + '_supplier_debit_amount').val(APP_CONVERTER.thousand_separator(lsupplier.supplier_debit_amount));
                        $(lparent_pane).find(lprefix_id + '_supplier_credit_amount').val(APP_CONVERTER.thousand_separator(lsupplier.supplier_credit_amount));
                        $(lparent_pane).find(lprefix_id + '_notes').val(lsupplier.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_supplier_type')
                                .select2('data', lsupplier.supplier_type);
                        
                        $(lparent_pane).find(lprefix_id + '_si_supplier_default')
                                .select2('data', lsupplier.si_supplier_default);
                        
                        $(lparent_pane).find(lprefix_id + '_supplier_status')
                                .select2('data', lsupplier.supplier_status).change();

                        $(lparent_pane).find(lprefix_id + '_supplier_status')
                                .select2({data: lresponse.supplier_status_list});

                        $(lparent_pane).find(lprefix_id + '_birthdate').val(lsupplier.birthdate!== null?APP_CONVERTER._date(lsupplier.birthdate, 'F d, Y H:i'):null);
                        supplier_mail_address_methods.load_mail_address({mail_address: lresponse.mail_address});
                        supplier_address_methods.load_address({address: lresponse.address});
                        supplier_phone_number_methods.load_phone_number({phone_number: lresponse.phone_number});
                        
                        
                    }
                    ;
                    break;
            }
        }

        supplier_methods.enable_disable();
        supplier_methods.show_hide();
        supplier_data_set();
    }

</script>