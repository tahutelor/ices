<script>
    var sales_invoice_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var sales_invoice_ajax_url = null;
    var sales_invoice_index_url = null;
    var sales_invoice_view_url = null;
    var sales_invoice_window_scroll = null;
    var sales_invoice_data_support_url = null;
    var sales_invoice_common_ajax_listener = null;
    var sales_invoice_component_prefix_id = '';

    var sales_invoice_init = function () {
        var parent_pane = sales_invoice_parent_pane;

        sales_invoice_ajax_url = '<?php echo $ajax_url ?>';
        sales_invoice_index_url = '<?php echo $index_url ?>';
        sales_invoice_view_url = '<?php echo $view_url ?>';
        sales_invoice_window_scroll = '<?php echo $window_scroll; ?>';
        sales_invoice_data_support_url = '<?php echo $data_support_url; ?>';
        sales_invoice_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        sales_invoice_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var sales_invoice_data = {
        customer_default:<?php echo json_encode($si_customer_default); ?>,
    }

    var sales_invoice_methods = {
        hide_all: function () {
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
            $(lparent_pane).find(lprefix_id+'_customer_detail '+lprefix_id+'_btn_customer_new').hide();
        },
        disable_all: function () {
            var lparent_pane = sales_invoice_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            sales_invoice_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_store').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_sales_invoice_date').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer_si_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_sales_invoice_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_total_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_total_discount_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_grand_total_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_outstanding_grand_total_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notes').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_si_product').closest('div [class*="form-group"]').show();
                    break;
            }

            switch (lmethod) {
                case 'add':
                    $(lparent_pane).find(lprefix_id+'_customer_detail '+lprefix_id+'_btn_customer_new').show();
                    break;
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_btn_print').show();
                    break;
            }
        },
        enable_disable: function () {
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            sales_invoice_methods.disable_all();

            switch (lmethod) {
                case "add":                
                    $(lparent_pane).find(lprefix_id + '_store').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_sales_invoice_date").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + '_customer').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_customer_si_code").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + '_sales_invoice_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_total_amount").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_total_discount_amount").prop("disabled", false);
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
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id+'_sales_invoice_date').datetimepicker({
                value:APP_GENERATOR.CURR_DATETIME('minute', 10,'F d, Y H:i')
            });
            APP_FORM.status.default_status_set(
                'sales_invoice',
                $(lparent_pane).find(lprefix_id + '_sales_invoice_status')
            );
            
            APP_COMPONENT.input_select.default_set($(lparent_pane).find(lprefix_id+'_store'));
            
            $(lparent_pane).find(lprefix_id+'_customer').select2('data',sales_invoice_data.customer_default).change();
            $(lparent_pane).find(lprefix_id + '_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_total_discount_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_grand_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_outstanding_grand_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_notes').val('');
            
            sales_invoice_si_product_methods.load_product({si_product:[]});
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            var lajax_url = sales_invoice_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var sales_invoice_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                sales_invoice: {},
                si_product:[],
                message_session: true
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    json_data.sales_invoice.sales_invoice_status = $(lparent_pane).find(lprefix_id + "_sales_invoice_status").select2('val');
                    json_data.sales_invoice.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    break;
            
            }

            switch (lmethod) {
                case 'add':
                    json_data.sales_invoice.store_id = $(lparent_pane).find(lprefix_id + "_store").select2('val');
                    json_data.sales_invoice.customer_id = $(lparent_pane).find(lprefix_id + "_customer").select2('val');
                    json_data.sales_invoice.total_discount_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_total_discount_amount").val());
                    
                    json_data.si_product = sales_invoice_tbl_si_product_method.setting.func_get_data_table().si_product;
                    break;
                case 'view':
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'sales_invoice_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_sales_invoice_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + sales_invoice_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
        customer_dependency_set:function(){
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            var lajax_url = sales_invoice_data_support_url+'customer_dependency_get/';
            var ljson_data = {customer_id:$(lparent_pane).find(lprefix_id+'_customer').select2('val')};
            var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, ljson_data).response;
            
            APP_COMPONENT.reference_detail.extra_info_set(
                $(lparent_pane).find(lprefix_id+'_customer_detail'),
                lresponse.customer_detail,
                {reset:true}
            );
        },
        all_amount_set: function(){
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            var ltr_arr = $(lparent_pane).find(lprefix_id+'_tbl_si_product tbody tr');
            var ltotal_amount = APP_CONVERTER._float(0);
            var ltotal_discount_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id+('_total_discount_amount')).val());
            $.each(ltr_arr,function(lidx, lrow){
                ltotal_amount+= APP_CONVERTER._float($(lrow).find('[col_name="subtotal_amount"] div')[0].innerHTML);
            });
            var lgrand_total_amount = ltotal_amount - ltotal_discount_amount;
            $(lparent_pane).find(lprefix_id+'_total_amount').val(APP_CONVERTER.thousand_separator(ltotal_amount));
            $(lparent_pane).find(lprefix_id+'_grand_total_amount').val(APP_CONVERTER.thousand_separator(lgrand_total_amount));
            $(lparent_pane).find(lprefix_id+'_outstanding_grand_total_amount').val(APP_CONVERTER.thousand_separator(lgrand_total_amount));
        }
    }

    var sales_invoice_bind_event = function () {
        var lparent_pane = sales_invoice_parent_pane;
        var lprefix_id = sales_invoice_component_prefix_id;
               
        $(lparent_pane).find(lprefix_id+'_btn_print').off('click');
        $(lparent_pane).find(lprefix_id+'_btn_print').on('click',function(e){
            e.preventDefault();
            var lsales_invoice_id = $(lparent_pane).find(lprefix_id+'_id').val();
            modal_print.init();
            var ljson_data = {sales_invoice_id:lsales_invoice_id};
            ljson_data = decodeURIComponent(JSON.stringify(ljson_data));
            modal_print.menu.add('<?php echo Lang::get('Sales Invoice')?>',sales_invoice_index_url+'sales_invoice_print/'+ljson_data+'');
            modal_print.show();
            
        });
               
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: sales_invoice_methods,
            view_url: sales_invoice_view_url,
            prefix_id:lprefix_id,
            window_scroll:sales_invoice_window_scroll,
        });
        
        $(lparent_pane).find(lprefix_id+'_customer').on('change',function(){
            sales_invoice_methods.customer_dependency_set();            
        });
        
        $(lparent_pane).find(lprefix_id+'_total_discount_amount').on('blur',function(){
            sales_invoice_methods.all_amount_set();
        });
        
        $(lparent_pane).find(lprefix_id+"_btn_customer_new").on("click",function(){
            $("#modal_customer").find("#customer_method").val("add");
            customer_init();
            customer_bind_event();
            customer_components_prepare();
            $('#modal_customer').modal('show');
            customer_methods.after_submit = function(){
                var lcustomer_id = $("#modal_customer").find("#customer_id").val();
                var lcustomer_name = $("#modal_customer").find("#customer_name").val();
                var laddress = $('#modal_customer').find('#customer_tbl_address tr:eq(0) [col_name="address"] textarea').val();
                $(lparent_pane).find(lprefix_id+"_customer").select2("data",
                    {id:lcustomer_id,text:lcustomer_name}
                ).change();
                $('#modal_customer').modal('hide');
            }
        });
        
        sales_invoice_si_product_bind_event();
    }

    var sales_invoice_components_prepare = function () {
        var lparent_pane = sales_invoice_parent_pane;
        var lprefix_id = sales_invoice_component_prefix_id;
        var method = $(sales_invoice_parent_pane).find(lprefix_id + "_method").val();

        var sales_invoice_data_set = function () {
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            switch (method) {
                case "add":
                    sales_invoice_methods.reset_all();
                    break;
                case "view":
                    var sales_invoice_id = $(sales_invoice_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: sales_invoice_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(sales_invoice_data_support_url + "sales_invoice_get", json_data).response;
                    if (lresponse !== []) {
                        var lsales_invoice = lresponse.sales_invoice;
                        $(lparent_pane).find(lprefix_id + '_store').select2('data',{id:lsales_invoice.store.id}).change();
                        $(lparent_pane).find(lprefix_id + '_code').val(lsales_invoice.code);
                        $(lparent_pane).find(lprefix_id + '_customer_si_code').val(lsales_invoice.customer_si_code);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lsales_invoice.notes);
                        $(lparent_pane).find(lprefix_id + '_method_name').val(lsales_invoice.method);
                        $(lparent_pane).find(lprefix_id + '_total_amount').val(APP_CONVERTER.thousand_separator(lsales_invoice.total_amount));
                        $(lparent_pane).find(lprefix_id + '_total_discount_amount').val(APP_CONVERTER.thousand_separator(lsales_invoice.total_discount_amount));
                        $(lparent_pane).find(lprefix_id + '_grand_total_amount').val(APP_CONVERTER.thousand_separator(lsales_invoice.grand_total_amount));
                        $(lparent_pane).find(lprefix_id + '_outstanding_grand_total_amount').val(APP_CONVERTER.thousand_separator(lsales_invoice.outstanding_grand_total_amount));
                        $(lparent_pane).find(lprefix_id + '_customer').select2('data',lsales_invoice.customer).change();

                        sales_invoice_si_product_methods.load_product({si_product:lresponse.si_product});
                        
                        $(lparent_pane).find(lprefix_id + '_sales_invoice_status')
                                .select2('data', lsales_invoice.sales_invoice_status).change();

                        $(lparent_pane).find(lprefix_id + '_sales_invoice_status')
                                .select2({data: lresponse.sales_invoice_status_list});
                        
                        $(lparent_pane).find(lprefix_id + '_sales_invoice_cancellation_reason')
                                .val(lsales_invoice.cancellation_reason);

                    }
                    ;
                    break;
            }
        }

        sales_invoice_methods.enable_disable();
        sales_invoice_methods.show_hide();
        sales_invoice_data_set();
    }

</script>