<script>
    var bos_bank_account_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var bos_bank_account_ajax_url = null;
    var bos_bank_account_index_url = null;
    var bos_bank_account_view_url = null;
    var bos_bank_account_window_scroll = null;
    var bos_bank_account_data_support_url = null;
    var bos_bank_account_common_ajax_listener = null;
    var bos_bank_account_component_prefix_id = '';

    var bos_bank_account_init = function () {
        var parent_pane = bos_bank_account_parent_pane;

        bos_bank_account_ajax_url = '<?php echo $ajax_url ?>';
        bos_bank_account_index_url = '<?php echo $index_url ?>';
        bos_bank_account_view_url = '<?php echo $view_url ?>';
        bos_bank_account_window_scroll = '<?php echo $window_scroll; ?>';
        bos_bank_account_data_support_url = '<?php echo $data_support_url; ?>';
        bos_bank_account_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        bos_bank_account_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var bos_bank_account_data = {
        bos_bank_account_type_default:null,
        
    }

    var bos_bank_account_methods = {
        hide_all: function () {
            var lparent_pane = bos_bank_account_parent_pane;
            var lprefix_id = bos_bank_account_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = bos_bank_account_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = bos_bank_account_parent_pane;
            var lprefix_id = bos_bank_account_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            bos_bank_account_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_bank_account').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_bos_bank_account_status').closest('div [class*="form-group"]').show();
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
            var lparent_pane = bos_bank_account_parent_pane;
            var lprefix_id = bos_bank_account_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            bos_bank_account_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_bank_account").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_bos_bank_account_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = bos_bank_account_parent_pane;
            var lprefix_id = bos_bank_account_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('');
            $(lparent_pane).find(lprefix_id + '_bank_account').val('');
            
            APP_FORM.status.default_status_set(
                    'bos_bank_account',
                    $(lparent_pane).find(lprefix_id + '_bos_bank_account_status')
                    );
            
            $(lparent_pane).find(lprefix_id + '_notes').val('');

        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = bos_bank_account_parent_pane;
            var lprefix_id = bos_bank_account_component_prefix_id;
            var lajax_url = bos_bank_account_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var bos_bank_account_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                bos_bank_account: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.bos_bank_account.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.bos_bank_account.bank_account = $(lparent_pane).find(lprefix_id + "_bank_account").val();
                    json_data.bos_bank_account.bos_bank_account_status = $(lparent_pane).find(lprefix_id + "_bos_bank_account_status").select2('val');
                    json_data.bos_bank_account.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'bos_bank_account_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_bos_bank_account_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + bos_bank_account_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
    }

    var bos_bank_account_bind_event = function () {
        var lparent_pane = bos_bank_account_parent_pane;
        var lprefix_id = bos_bank_account_component_prefix_id;
       
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: bos_bank_account_methods,
            view_url: bos_bank_account_view_url,
            prefix_id:lprefix_id,
            window_scroll:bos_bank_account_window_scroll,
        });
    }

    var bos_bank_account_components_prepare = function () {
        var lparent_pane = bos_bank_account_parent_pane;
        var lprefix_id = bos_bank_account_component_prefix_id;
        var method = $(bos_bank_account_parent_pane).find(lprefix_id + "_method").val();

        var bos_bank_account_data_set = function () {
            var lparent_pane = bos_bank_account_parent_pane;
            var lprefix_id = bos_bank_account_component_prefix_id;
            switch (method) {
                case "add":
                    bos_bank_account_methods.reset_all();
                    break;
                case "view":
                    var bos_bank_account_id = $(bos_bank_account_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: bos_bank_account_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(bos_bank_account_data_support_url + "bos_bank_account_get", json_data).response;
                    if (lresponse !== []) {
                        var lbos_bank_account = lresponse.bos_bank_account;
                        $(lparent_pane).find(lprefix_id + '_code').val(lbos_bank_account.code);
                        $(lparent_pane).find(lprefix_id + '_bank_account').val(lbos_bank_account.bank_account);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lbos_bank_account.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_bos_bank_account_status')
                                .select2('data', lbos_bank_account.bos_bank_account_status).change();

                        $(lparent_pane).find(lprefix_id + '_bos_bank_account_status')
                                .select2({data: lresponse.bos_bank_account_status_list});

                    }
                    ;
                    break;
            }
        }

        bos_bank_account_methods.enable_disable();
        bos_bank_account_methods.show_hide();
        bos_bank_account_data_set();
    }

</script>