<script>
    var purchase_invoice_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var purchase_invoice_ajax_url = null;
    var purchase_invoice_index_url = null;
    var purchase_invoice_view_url = null;
    var purchase_invoice_window_scroll = null;
    var purchase_invoice_data_support_url = null;
    var purchase_invoice_common_ajax_listener = null;
    var purchase_invoice_component_prefix_id = '';

    var purchase_invoice_init = function () {
        var parent_pane = purchase_invoice_parent_pane;

        purchase_invoice_ajax_url = '<?php echo $ajax_url ?>';
        purchase_invoice_index_url = '<?php echo $index_url ?>';
        purchase_invoice_view_url = '<?php echo $view_url ?>';
        purchase_invoice_window_scroll = '<?php echo $window_scroll; ?>';
        purchase_invoice_data_support_url = '<?php echo $data_support_url; ?>';
        purchase_invoice_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        purchase_invoice_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var purchase_invoice_data = {
    }

    var purchase_invoice_methods = {
        hide_all: function () {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
            $(lparent_pane).find(lprefix_id+'_supplier_detail '+lprefix_id+'_btn_supplier_new').hide();
        },
        disable_all: function () {
            var lparent_pane = purchase_invoice_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            purchase_invoice_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_store').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_purchase_invoice_date').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_supplier').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_supplier_si_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_purchase_invoice_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_total_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_total_discount_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_additional_cost_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_grand_total_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_outstanding_grand_total_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notes').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_purchase_invoice_product').closest('div [class*="form-group"]').show();
                    break;
            }

            switch (lmethod) {
                case 'add':
                    $(lparent_pane).find(lprefix_id+'_supplier_detail '+lprefix_id+'_btn_supplier_new').show();
                    break;
                case 'view':
                    
                    break;
            }
        },
        enable_disable: function () {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            purchase_invoice_methods.disable_all();

            switch (lmethod) {
                case "add":                
                    $(lparent_pane).find(lprefix_id + '_store').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_purchase_invoice_date").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + '_supplier').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_supplier_si_code").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_purchase_invoice_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_total_amount").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_total_discount_amount").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_additional_cost_amount").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_grand_total_amount").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_outstanding_grand_total_amount").prop("disabled", true);                    
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id+'_purchase_invoice_date').datetimepicker({
                value:APP_GENERATOR.CURR_DATETIME('minute', 10,'F d, Y H:i')
            });
            APP_FORM.status.default_status_set(
                'purchase_invoice',
                $(lparent_pane).find(lprefix_id + '_purchase_invoice_status')
            );
            
            APP_COMPONENT.input_select.default_set($(lparent_pane).find(lprefix_id+'_store'));
            
            $(lparent_pane).find(lprefix_id+'_supplier').select2('data',null).change();
            $(lparent_pane).find(lprefix_id + '_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_total_discount_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_additional_cost_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_grand_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_outstanding_grand_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_notes').val('');
            
            purchase_invoice_purchase_invoice_product_methods.load_product({purchase_invoice_product:[]});
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            var lajax_url = purchase_invoice_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var purchase_invoice_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                purchase_invoice: {},
                purchase_invoice_product:[],
                message_session: true
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    json_data.purchase_invoice.purchase_invoice_status = $(lparent_pane).find(lprefix_id + "_purchase_invoice_status").select2('val');
                    json_data.purchase_invoice.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    break;
            
            }

            switch (lmethod) {
                case 'add':
                    json_data.purchase_invoice.store_id = $(lparent_pane).find(lprefix_id + "_store").select2('val');
                    json_data.purchase_invoice.supplier_id = $(lparent_pane).find(lprefix_id + "_supplier").select2('val');
                    json_data.purchase_invoice.supplier_si_code = $(lparent_pane).find(lprefix_id + "_supplier_si_code").val();
                    json_data.purchase_invoice.total_discount_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_total_discount_amount").val());
                    json_data.purchase_invoice.additional_cost_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_additional_cost_amount").val());
                    
                    json_data.purchase_invoice_product = purchase_invoice_tbl_purchase_invoice_product_method.setting.func_get_data_table().purchase_invoice_product;
                    break;
                case 'view':
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'purchase_invoice_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_purchase_invoice_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + purchase_invoice_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
        supplier_dependency_set:function(){
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            var lajax_url = purchase_invoice_data_support_url+'supplier_dependency_get/';
            var ljson_data = {supplier_id:$(lparent_pane).find(lprefix_id+'_supplier').select2('val')};
            var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, ljson_data).response;
            
            APP_COMPONENT.reference_detail.extra_info_set(
                $(lparent_pane).find(lprefix_id+'_supplier_detail'),
                lresponse.supplier_detail,
                {reset:true}
            );
        },
        all_amount_set: function(){
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            var ltr_arr = $(lparent_pane).find(lprefix_id+'_tbl_purchase_invoice_product tbody tr');
            var ltotal_amount = APP_CONVERTER._float(0);
            var ltotal_discount_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id+('_total_discount_amount')).val());
            var ladditional_cost_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id+('_additional_cost_amount')).val());
            $.each(ltr_arr,function(lidx, lrow){
                ltotal_amount+= APP_CONVERTER._float($(lrow).find('[col_name="subtotal_amount"] div')[0].innerHTML);
            });
            var lgrand_total_amount = ltotal_amount - ltotal_discount_amount + ladditional_cost_amount;
            $(lparent_pane).find(lprefix_id+'_total_amount').val(APP_CONVERTER.thousand_separator(ltotal_amount));
            $(lparent_pane).find(lprefix_id+'_grand_total_amount').val(APP_CONVERTER.thousand_separator(lgrand_total_amount));
            $(lparent_pane).find(lprefix_id+'_outstanding_grand_total_amount').val(APP_CONVERTER.thousand_separator(lgrand_total_amount));
        }
    }

    var purchase_invoice_bind_event = function () {
        var lparent_pane = purchase_invoice_parent_pane;
        var lprefix_id = purchase_invoice_component_prefix_id;
               
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: purchase_invoice_methods,
            view_url: purchase_invoice_view_url,
            prefix_id:lprefix_id,
            window_scroll:purchase_invoice_window_scroll,
        });
        
        $(lparent_pane).find(lprefix_id+'_supplier').on('change',function(){
            purchase_invoice_methods.supplier_dependency_set();            
        });
        
        $(lparent_pane).find(lprefix_id+'_total_discount_amount').on('blur',function(){
            purchase_invoice_methods.all_amount_set();
        });
        
        $(lparent_pane).find(lprefix_id+'_additional_cost_amount').on('blur',function(){
            purchase_invoice_methods.all_amount_set();
        });
        
        $(lparent_pane).find(lprefix_id+"_btn_supplier_new").on("click",function(){
            $("#modal_supplier").find("#supplier_method").val("add");
            supplier_init();
            supplier_bind_event();
            supplier_components_prepare();
            $('#modal_supplier').modal('show');
            supplier_methods.after_submit = function(){
                var lsupplier_id = $("#modal_supplier").find("#supplier_id").val();
                var lsupplier_name = $("#modal_supplier").find("#supplier_name").val();
                var laddress = $('#modal_supplier').find('#supplier_tbl_address tr:eq(0) [col_name="address"] textarea').val();
                $(lparent_pane).find(lprefix_id+"_supplier").select2("data",
                    {id:lsupplier_id,text:lsupplier_name}
                ).change();
                $('#modal_supplier').modal('hide');
            }
        });
        
        purchase_invoice_purchase_invoice_product_bind_event();
    }

    var purchase_invoice_components_prepare = function () {
        var lparent_pane = purchase_invoice_parent_pane;
        var lprefix_id = purchase_invoice_component_prefix_id;
        var method = $(purchase_invoice_parent_pane).find(lprefix_id + "_method").val();

        var purchase_invoice_data_set = function () {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            switch (method) {
                case "add":
                    purchase_invoice_methods.reset_all();
                    break;
                case "view":
                    var purchase_invoice_id = $(purchase_invoice_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: purchase_invoice_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(purchase_invoice_data_support_url + "purchase_invoice_get", json_data).response;
                    if (lresponse !== []) {
                        var lpurchase_invoice = lresponse.purchase_invoice;
                        $(lparent_pane).find(lprefix_id + '_store').select2('data',{id:lpurchase_invoice.store.id}).change();
                        $(lparent_pane).find(lprefix_id + '_code').val(lpurchase_invoice.code);
                        $(lparent_pane).find(lprefix_id + '_supplier_si_code').val(lpurchase_invoice.supplier_si_code);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lpurchase_invoice.notes);
                        $(lparent_pane).find(lprefix_id + '_total_amount').val(APP_CONVERTER.thousand_separator(lpurchase_invoice.total_amount));
                        $(lparent_pane).find(lprefix_id + '_total_discount_amount').val(APP_CONVERTER.thousand_separator(lpurchase_invoice.total_discount_amount));
                        $(lparent_pane).find(lprefix_id + '_grand_total_amount').val(APP_CONVERTER.thousand_separator(lpurchase_invoice.grand_total_amount));
                        $(lparent_pane).find(lprefix_id + '_additional_cost_amount').val(APP_CONVERTER.thousand_separator(lpurchase_invoice.additional_cost_amount));
                        $(lparent_pane).find(lprefix_id + '_outstanding_grand_total_amount').val(APP_CONVERTER.thousand_separator(lpurchase_invoice.outstanding_grand_total_amount));
                        $(lparent_pane).find(lprefix_id + '_supplier').select2('data',lpurchase_invoice.supplier).change();
                        
                        purchase_invoice_purchase_invoice_product_methods.load_product({purchase_invoice_product:lresponse.pi_product});
                        
                        $(lparent_pane).find(lprefix_id + '_purchase_invoice_status')
                                .select2('data', lpurchase_invoice.purchase_invoice_status).change();

                        $(lparent_pane).find(lprefix_id + '_purchase_invoice_status')
                                .select2({data: lresponse.purchase_invoice_status_list});
                        
                        $(lparent_pane).find(lprefix_id + '_purchase_invoice_cancellation_reason')
                                .val(lpurchase_invoice.cancellation_reason);

                    }
                    ;
                    break;
            }
        }

        purchase_invoice_methods.enable_disable();
        purchase_invoice_methods.show_hide();
        purchase_invoice_data_set();
    }

</script>