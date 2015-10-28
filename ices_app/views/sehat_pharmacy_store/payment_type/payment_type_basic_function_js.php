<script>
    var payment_type_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var payment_type_ajax_url = null;
    var payment_type_index_url = null;
    var payment_type_view_url = null;
    var payment_type_window_scroll = null;
    var payment_type_data_support_url = null;
    var payment_type_common_ajax_listener = null;
    var payment_type_component_prefix_id = '';

    var payment_type_init = function () {
        var parent_pane = payment_type_parent_pane;

        payment_type_ajax_url = '<?php echo $ajax_url ?>';
        payment_type_index_url = '<?php echo $index_url ?>';
        payment_type_view_url = '<?php echo $view_url ?>';
        payment_type_window_scroll = '<?php echo $window_scroll; ?>';
        payment_type_data_support_url = '<?php echo $data_support_url; ?>';
        payment_type_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        payment_type_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var payment_type_data = {
        bool_arr: <?php echo json_encode($bool_arr) ?>,

    }
    
    var payment_type_methods = {
        hide_all: function () {
            var lparent_pane = payment_type_parent_pane;
            var lprefix_id = payment_type_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = payment_type_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = payment_type_parent_pane;
            var lprefix_id = payment_type_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            payment_type_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_payment_type_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer_bank_account').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_supplier_bank_account').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_bos_bank_account_default').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_change_amount').closest('div [class*="form-group"]').show();
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
            var lparent_pane = payment_type_parent_pane;
            var lprefix_id = payment_type_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            payment_type_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_payment_type_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_customer_bank_account').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_supplier_bank_account').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_bos_bank_account_default').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_change_amount').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = payment_type_parent_pane;
            var lprefix_id = payment_type_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            
            APP_FORM.status.default_status_set(
                'payment_type',
                $(lparent_pane).find(lprefix_id + '_payment_type_status')
            );
            
            $(lparent_pane).find(lprefix_id+'_customer_bank_account').select2('data',payment_type_data.bool_arr[1]);
            $(lparent_pane).find(lprefix_id+'_supplier_bank_account').select2('data',payment_type_data.bool_arr[1]);
            $(lparent_pane).find(lprefix_id+'_change_amount').select2('data',payment_type_data.bool_arr[1]);
            $(lparent_pane).find(lprefix_id+'_bos_bank_account_default').select2('data',null);
            $(lparent_pane).find(lprefix_id + '_notes').val('');

        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = payment_type_parent_pane;
            var lprefix_id = payment_type_component_prefix_id;
            var lajax_url = payment_type_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var payment_type_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                payment_type: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.payment_type.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.payment_type.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.payment_type.payment_type_status = $(lparent_pane).find(lprefix_id + "_payment_type_status").select2('val');
                    json_data.payment_type.customer_bank_account = $(lparent_pane).find(lprefix_id + "_customer_bank_account").select2('val');
                    json_data.payment_type.supplier_bank_account = $(lparent_pane).find(lprefix_id + "_supplier_bank_account").select2('val');
                    json_data.payment_type.change_amount = $(lparent_pane).find(lprefix_id + "_change_amount").select2('val');
                    json_data.payment_type.bos_bank_account_id_default = $(lparent_pane).find(lprefix_id + "_bos_bank_account_default").select2('val');
                    json_data.payment_type.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'payment_type_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_payment_type_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + payment_type_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
    }

    var payment_type_bind_event = function () {
        var lparent_pane = payment_type_parent_pane;
        var lprefix_id = payment_type_component_prefix_id;
              
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: payment_type_methods,
            view_url: payment_type_view_url,
            prefix_id:lprefix_id,
            window_scroll:payment_type_window_scroll,
        });
    }

    var payment_type_components_prepare = function () {
        var lparent_pane = payment_type_parent_pane;
        var lprefix_id = payment_type_component_prefix_id;
        var method = $(payment_type_parent_pane).find(lprefix_id + "_method").val();

        var payment_type_data_set = function () {
            var lparent_pane = payment_type_parent_pane;
            var lprefix_id = payment_type_component_prefix_id;
            switch (method) {
                case "add":
                    payment_type_methods.reset_all();
                    break;
                case "view":
                    var payment_type_id = $(payment_type_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: payment_type_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(payment_type_data_support_url + "payment_type_get", json_data).response;
                    if (lresponse !== []) {
                        var lpayment_type = lresponse.payment_type;
                        $(lparent_pane).find(lprefix_id + '_code').val(lpayment_type.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lpayment_type.name);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lpayment_type.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_customer_bank_account')
                                .select2('data', lpayment_type.customer_bank_account);
                        
                        $(lparent_pane).find(lprefix_id + '_supplier_bank_account')
                                .select2('data', lpayment_type.supplier_bank_account);
                        
                        $(lparent_pane).find(lprefix_id + '_bos_bank_account_default')
                                .select2('data', lpayment_type.bos_bank_account_default);
                        
                        $(lparent_pane).find(lprefix_id + '_change_amount')
                                .select2('data', lpayment_type.change_amount);
                        
                        $(lparent_pane).find(lprefix_id + '_payment_type_status')
                                .select2('data', lpayment_type.payment_type_status).change();

                        $(lparent_pane).find(lprefix_id + '_payment_type_status')
                                .select2({data: lresponse.payment_type_status_list});
                        
                         

                    }
                    ;
                    break;
            }
        }

        payment_type_methods.enable_disable();
        payment_type_methods.show_hide();
        payment_type_data_set();
    }

</script>