<script>
    var unit_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var unit_ajax_url = null;
    var unit_index_url = null;
    var unit_view_url = null;
    var unit_window_scroll = null;
    var unit_data_support_url = null;
    var unit_common_ajax_listener = null;
    var unit_component_prefix_id = '';

    var unit_init = function () {
        var parent_pane = unit_parent_pane;

        unit_ajax_url = '<?php echo $ajax_url ?>';
        unit_index_url = '<?php echo $index_url ?>';
        unit_view_url = '<?php echo $view_url ?>';
        unit_window_scroll = '<?php echo $window_scroll; ?>';
        unit_data_support_url = '<?php echo $data_support_url; ?>';
        unit_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        unit_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var unit_data = {
        unit_type_default:null,
        
    }

    var unit_methods = {
        hide_all: function () {
            var lparent_pane = unit_parent_pane;
            var lprefix_id = unit_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = unit_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = unit_parent_pane;
            var lprefix_id = unit_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            unit_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_unit_status').closest('div [class*="form-group"]').show();
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
            var lparent_pane = unit_parent_pane;
            var lprefix_id = unit_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            unit_methods.disable_all();

            switch (lmethod) {
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_name").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_unit_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = unit_parent_pane;
            var lprefix_id = unit_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('');
            $(lparent_pane).find(lprefix_id + '_name').val('');
            
            APP_FORM.status.default_status_set(
                    'unit',
                    $(lparent_pane).find(lprefix_id + '_unit_status')
                    );
            
            $(lparent_pane).find(lprefix_id + '_notes').val('');

        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = unit_parent_pane;
            var lprefix_id = unit_component_prefix_id;
            var lajax_url = unit_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var unit_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                unit: {},
                message_session: true
            };

            switch (lmethod) {
                case 'add':
                case 'view':
                    json_data.unit.code = $(lparent_pane).find(lprefix_id + "_code").val();
                    json_data.unit.name = $(lparent_pane).find(lprefix_id + "_name").val();
                    json_data.unit.unit_status = $(lparent_pane).find(lprefix_id + "_unit_status").select2('val');
                    json_data.unit.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'unit_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_unit_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + unit_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
    }

    var unit_bind_event = function () {
        var lparent_pane = unit_parent_pane;
        var lprefix_id = unit_component_prefix_id;
       
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: unit_methods,
            view_url: unit_view_url,
            prefix_id:lprefix_id,
            window_scroll:unit_window_scroll,
        });
    }

    var unit_components_prepare = function () {
        var lparent_pane = unit_parent_pane;
        var lprefix_id = unit_component_prefix_id;
        var method = $(unit_parent_pane).find(lprefix_id + "_method").val();

        var unit_data_set = function () {
            var lparent_pane = unit_parent_pane;
            var lprefix_id = unit_component_prefix_id;
            switch (method) {
                case "add":
                    unit_methods.reset_all();
                    break;
                case "view":
                    var unit_id = $(unit_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: unit_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(unit_data_support_url + "unit_get", json_data).response;
                    if (lresponse !== []) {
                        var lunit = lresponse.unit;
                        $(lparent_pane).find(lprefix_id + '_code').val(lunit.code);
                        $(lparent_pane).find(lprefix_id + '_name').val(lunit.name);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lunit.notes);
                        
                        $(lparent_pane).find(lprefix_id + '_unit_status')
                                .select2('data', lunit.unit_status).change();

                        $(lparent_pane).find(lprefix_id + '_unit_status')
                                .select2({data: lresponse.unit_status_list});

                    }
                    ;
                    break;
            }
        }

        unit_methods.enable_disable();
        unit_methods.show_hide();
        unit_data_set();
    }

</script>