<script>
    var contact_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var contact_ajax_url = null;
    var contact_index_url = null;
    var contact_view_url = null;
    var contact_window_scroll = null;
    var contact_data_support_url = null;
    var contact_common_ajax_listener = null;
    var contact_component_prefix_id = '';

    var contact_init = function () {
        var parent_pane = contact_parent_pane;

        contact_ajax_url = '<?php echo $ajax_url ?>';
        contact_index_url = '<?php echo $index_url ?>';
        contact_view_url = '<?php echo $view_url ?>';
        contact_window_scroll = '<?php echo $window_scroll; ?>';
        contact_data_support_url = '<?php echo $data_support_url; ?>';
        contact_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        contact_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var contact_data = {
    }

    var contact_methods = {
        hide_all: function () {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = contact_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            contact_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_contact_category').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_company').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_contact_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_birthdate').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_mail_address').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_address').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_phone_number').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_keyword').closest('div [class*="form-group"]').show();
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
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            contact_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_contact_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_birthdate").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            $(lparent_pane).find(lprefix_id + '_birthdate').val('');

            APP_FORM.status.default_status_set(
                    'contact',
                    $(lparent_pane).find(lprefix_id + '_contact_status')
                    );
            
            $(lparent_pane).find(lprefix_id + '_notes').val('');
                    
            contact_mail_address_methods.load_mail_address({mail_address: []});
            contact_keyword_methods.load_keyword({keyword: []});
            contact_address_methods.load_address({address: []});
            contact_phone_number_methods.load_phone_number({phone_number: []});
            contact_contact_category_methods.load_contact_category({contact_category: []});
            contact_company_methods.load_company({company: []});
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lajax_url = contact_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var contact_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                contact: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.contact.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.contact.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.contact.contact_status = $(lparent_pane).find(lprefix_id + "_contact_status").select2('val');
                    json_data.contact.birthdate = $(lparent_pane).find(lprefix_id + "_birthdate").val() === ''?null:(new Date($(lparent_pane).find(lprefix_id + "_birthdate").val())).format('Y-m-d H:i:s');
                    json_data.contact.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    json_data.contact_category_id = contact_tbl_contact_category_method.setting.func_get_data_table().contact_category_id;
                    json_data.company_id = contact_tbl_company_method.setting.func_get_data_table().company_id;
                    json_data.mail_address = contact_tbl_mail_address_method.setting.func_get_data_table().mail_address;
                    json_data.keyword = contact_tbl_keyword_method.setting.func_get_data_table().keyword;
                    json_data.address = contact_tbl_address_method.setting.func_get_data_table().address;
                    json_data.phone_number = contact_tbl_phone_number_method.setting.func_get_data_table().phone_number;
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'contact_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_contact_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + contact_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
    }

    var contact_bind_event = function () {
        var lparent_pane = contact_parent_pane;
        var lprefix_id = contact_component_prefix_id;

        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: contact_methods,
            view_url: contact_view_url,
            prefix_id:lprefix_id,
            window_scroll:contact_window_scroll,
        });

        contact_mail_address_bind_event();
        contact_keyword_bind_event();
        contact_address_bind_event();
        contact_phone_number_bind_event();
        contact_contact_category_bind_event();
        contact_company_bind_event();
    }

    var contact_components_prepare = function () {
        var lparent_pane = contact_parent_pane;
        var lprefix_id = contact_component_prefix_id;
        var method = $(contact_parent_pane).find(lprefix_id + "_method").val();

        var contact_data_set = function () {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            switch (method) {
                case "add":
                    contact_methods.reset_all();
                    break;
                case "view":
                    var contact_id = $(contact_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: contact_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(contact_data_support_url + "contact_get", json_data).response;
                    if (lresponse !== []) {
                        var lcontact = lresponse.contact;
                        $(lparent_pane).find(lprefix_id + '_code').val(lcontact.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lcontact.name);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lcontact.notes);
                        $(lparent_pane).find(lprefix_id + '_method_name').val(lcontact.method);

                        $(lparent_pane).find(lprefix_id + '_contact_status')
                                .select2('data', lcontact.contact_status).change();

                        $(lparent_pane).find(lprefix_id + '_contact_status')
                                .select2({data: lresponse.contact_status_list});

                        $(lparent_pane).find(lprefix_id + '_birthdate').val(lcontact.birthdate!== null?APP_CONVERTER._date(lcontact.birthdate, 'F d, Y H:i'):null);
                        contact_mail_address_methods.load_mail_address({mail_address: lresponse.mail_address});
                        contact_keyword_methods.load_keyword({keyword: lresponse.keyword});
                        contact_address_methods.load_address({address: lresponse.address});
                        contact_phone_number_methods.load_phone_number({phone_number: lresponse.phone_number});
                        contact_contact_category_methods.load_contact_category({contact_category: lresponse.contact_category});
                        contact_company_methods.load_company({company: lresponse.company});
                        
                    }
                    ;
                    break;
            }
        }

        contact_methods.enable_disable();
        contact_methods.show_hide();
        contact_data_set();
    }

</script>