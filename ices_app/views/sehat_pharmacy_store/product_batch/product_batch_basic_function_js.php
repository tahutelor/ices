<script>
    var product_batch_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var product_batch_ajax_url = null;
    var product_batch_index_url = null;
    var product_batch_view_url = null;
    var product_batch_window_scroll = null;
    var product_batch_data_support_url = null;
    var product_batch_common_ajax_listener = null;
    var product_batch_component_prefix_id = '';

    var product_batch_init = function () {
        var parent_pane = product_batch_parent_pane;

        product_batch_ajax_url = '<?php echo $ajax_url ?>';
        product_batch_index_url = '<?php echo $index_url ?>';
        product_batch_view_url = '<?php echo $view_url ?>';
        product_batch_window_scroll = '<?php echo $window_scroll; ?>';
        product_batch_data_support_url = '<?php echo $data_support_url; ?>';
        product_batch_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        product_batch_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var product_batch_data = {
    }

    var product_batch_methods = {
        hide_all: function () {
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = product_batch_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            product_batch_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_batch_number').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_product').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_unit').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_expired_date').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_product_batch_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notes').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_product_stock').closest('div [class*="form-group"]').show();
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
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            product_batch_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_batch_number').val('');
            $(lparent_pane).find(lprefix_id + '_expired_date').val('');
            $(lparent_pane).find(lprefix_id + '_notes').val('');

            APP_FORM.status.default_status_set(
                'product_batch',
                $(lparent_pane).find(lprefix_id + '_product_batch_status')
            );
            
            product_batch_methods.load_product_batch({product_batch: []});
        },
        after_submit: function () {

        },
        submit: function () {
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;
            var lajax_url = product_batch_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var product_batch_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                product_batch: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.product_batch.notes = $(lparent_pane).find(lprefix_id+'_notes').val();
                    json_data.product_batch.product_batch_status = $(lparent_pane).find(lprefix_id+'_product_batch_status').select2('val');
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'product_batch_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_product_batch_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + product_batch_id;

            var lresult = {
                json_data: json_data,
                ajax_url: lajax_url
            };
            return lresult;

        },
    }

    var product_batch_bind_event = function () {
        var lparent_pane = product_batch_parent_pane;
        var lprefix_id = product_batch_component_prefix_id;

        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: product_batch_methods,
            view_url: product_batch_view_url,
            prefix_id: lprefix_id,
            window_scroll: product_batch_window_scroll,
        });
        
        product_batch_product_stock_bind_event();
        
    }

    var product_batch_components_prepare = function () {
        var lparent_pane = product_batch_parent_pane;
        var lprefix_id = product_batch_component_prefix_id;
        var method = $(product_batch_parent_pane).find(lprefix_id + "_method").val();

        var product_batch_data_set = function () {
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;
            switch (method) {
                case "add":
                    product_batch_methods.reset_all();
                    break;
                case "view":
                    var product_batch_id = $(product_batch_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: product_batch_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(product_batch_data_support_url + "product_batch_get", json_data).response;
                    if (lresponse !== []) {
                        var lproduct_batch = lresponse.product_batch;
                        $(lparent_pane).find(lprefix_id + '_batch_number').val(lproduct_batch.batch_number);
                        $(lparent_pane).find(lprefix_id + '_product').select2('data',lproduct_batch.product);
                        $(lparent_pane).find(lprefix_id + '_unit').select2('data',lproduct_batch.unit);
                        $(lparent_pane).find(lprefix_id + '_expired_date').val(new Date(lproduct_batch.expired_date).format('F d, Y H:i:s'));
                        $(lparent_pane).find(lprefix_id + '_notes').val(lproduct_batch.notes);

                        product_batch_product_stock_methods.load_product_stock({product_stock:lresponse.product_stock});

                        $(lparent_pane).find(lprefix_id + '_product_batch_status')
                                .select2('data', lproduct_batch.product_batch_status).change();

                        $(lparent_pane).find(lprefix_id + '_product_batch_status')
                                .select2({data: lresponse.product_batch_status_list});
                        
                    }
                    ;
                    break;
            }
        }

        product_batch_methods.enable_disable();
        product_batch_methods.show_hide();
        product_batch_data_set();
    }

</script>