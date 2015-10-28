<script>
    var purchase_return_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var purchase_return_ajax_url = null;
    var purchase_return_index_url = null;
    var purchase_return_view_url = null;
    var purchase_return_window_scroll = null;
    var purchase_return_data_support_url = null;
    var purchase_return_common_ajax_listener = null;
    var purchase_return_component_prefix_id = '';

    var purchase_return_init = function () {
        var parent_pane = purchase_return_parent_pane;

        purchase_return_ajax_url = '<?php echo $ajax_url ?>';
        purchase_return_index_url = '<?php echo $index_url ?>';
        purchase_return_view_url = '<?php echo $view_url ?>';
        purchase_return_window_scroll = '<?php echo $window_scroll; ?>';
        purchase_return_data_support_url = '<?php echo $data_support_url; ?>';
        purchase_return_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        purchase_return_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var purchase_return_data = {
    }

    var purchase_return_methods = {
        hide_all: function () {
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
            $(lparent_pane).find(lprefix_id+'_supplier_detail '+lprefix_id+'_btn_supplier_new').hide();
        },
        disable_all: function () {
            var lparent_pane = purchase_return_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            purchase_return_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_store').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_reference').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_purchase_return_date').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_purchase_return_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_total_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_total_discount_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_additional_cost_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_grand_total_amount').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_notes').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_tbl_pr_product').closest('div [class*="form-group"]').show();
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
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            purchase_return_methods.disable_all();

            switch (lmethod) {
                case "add":                
                    $(lparent_pane).find(lprefix_id + '_store').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_purchase_return_date").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + '_reference').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_purchase_return_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_total_amount").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_total_discount_amount").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_additional_cost_amount").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_grand_total_amount").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            $(lparent_pane).find(lprefix_id+'_purchase_return_date').datetimepicker({
                value:APP_GENERATOR.CURR_DATETIME('minute', 10,'F d, Y H:i')
            });
            APP_FORM.status.default_status_set(
                'purchase_return',
                $(lparent_pane).find(lprefix_id + '_purchase_return_status')
            );
            
            APP_COMPONENT.input_select.default_set($(lparent_pane).find(lprefix_id+'_store'));            
            $(lparent_pane).find(lprefix_id+'_reference').select2('data',null).change();
           
            $(lparent_pane).find(lprefix_id + '_notes').val('');
            
            
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            var lajax_url = purchase_return_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var purchase_return_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                purchase_return: {},
                pr_product:[],
                message_session: true
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    json_data.purchase_return.purchase_return_status = $(lparent_pane).find(lprefix_id + "_purchase_return_status").select2('val');
                    json_data.purchase_return.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    break;
            
            }

            switch (lmethod) {
                case 'add':
                    var lref_data = $(lparent_pane).find(lprefix_id+'_reference').select2('data');
                    if(lref_data === null) {lref_data = {id:'',ref_type:''}};
                    json_data.purchase_return.ref_type = lref_data.ref_type;
                    json_data.purchase_return.ref_id = lref_data.id;

                    json_data.purchase_return.store_id = $(lparent_pane).find(lprefix_id + "_store").select2('val');
                    json_data.purchase_return.total_discount_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_total_discount_amount").val());
                    json_data.purchase_return.additional_cost_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_additional_cost_amount").val());
                    
                    json_data.pr_product = purchase_return_tbl_pr_product_method.setting.func_get_data_table().pr_product;
                    break;
                case 'view':
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'purchase_return_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_purchase_return_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + purchase_return_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
        reference_dependency_set:function(){
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            var lajax_url = purchase_return_data_support_url+'reference_dependency_get/';
            var ldata = $(lparent_pane).find(lprefix_id+'_reference').select2('data');
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();
            
            $(lparent_pane).find(lprefix_id + '_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_total_discount_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_grand_total_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_outstanding_grand_total_amount').val('0.00');
            
            APP_COMPONENT.reference_detail.empty($(lparent_pane).find(lprefix_id+'_reference_detail'));
            
            purchase_return_purchase_return_product_methods.load_product({pr_product:[]});
            
            if($(lparent_pane).find(lprefix_id+'_reference').select2('val')!=''){
                var ljson_data = {ref_type:ldata.ref_type,ref_id:ldata.id};
                var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, ljson_data).response;
                
                APP_COMPONENT.reference_detail.extra_info_set(
                    $(lparent_pane).find(lprefix_id+'_reference_detail'),
                    lresponse.reference_detail,
                    {reset:true}
                );
                
                if(lmethod === 'add'){
                    purchase_return_purchase_return_product_methods.load_product({pr_product:lresponse.ref_product});
                }
                
                
            }
            
        },
        all_amount_set: function(){
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            var ltr_arr = $(lparent_pane).find(lprefix_id+'_tbl_pr_product tbody tr');
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

    var purchase_return_bind_event = function () {
        var lparent_pane = purchase_return_parent_pane;
        var lprefix_id = purchase_return_component_prefix_id;
               
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: purchase_return_methods,
            view_url: purchase_return_view_url,
            prefix_id:lprefix_id,
            window_scroll:purchase_return_window_scroll,
        });
        
        $(lparent_pane).find(lprefix_id+'_supplier').on('change',function(){
            purchase_return_methods.supplier_dependency_set();            
        });
        
        $(lparent_pane).find(lprefix_id+'_total_discount_amount').on('change',function(){
            purchase_return_methods.all_amount_set();
        });
        
         $(lparent_pane).find(lprefix_id+'_additional_cost_amount').on('change',function(){
            purchase_return_methods.all_amount_set();
        });
        
        $(lparent_pane).find(lprefix_id+'_reference').on('change',function(){
            purchase_return_methods.reference_dependency_set();            
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
        
        purchase_return_purchase_return_product_bind_event();
    }

    var purchase_return_components_prepare = function () {
        var lparent_pane = purchase_return_parent_pane;
        var lprefix_id = purchase_return_component_prefix_id;
        var method = $(purchase_return_parent_pane).find(lprefix_id + "_method").val();

        var purchase_return_data_set = function () {
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            switch (method) {
                case "add":
                    purchase_return_methods.reset_all();
                    break;
                case "view":
                    var purchase_return_id = $(purchase_return_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: purchase_return_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(purchase_return_data_support_url + "purchase_return_get", json_data).response;
                    if (lresponse !== []) {
                        var lpurchase_return = lresponse.purchase_return;
                        $(lparent_pane).find(lprefix_id + '_store').select2('data',{id:lpurchase_return.store_id}).change();
                        $(lparent_pane).find(lprefix_id + '_reference').select2('data',lpurchase_return.reference).change();
                        $(lparent_pane).find(lprefix_id + '_code').val(lpurchase_return.code);
                        $(lparent_pane).find(lprefix_id + '_supplier_si_code').val(lpurchase_return.supplier_si_code);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lpurchase_return.notes);
                        $(lparent_pane).find(lprefix_id + '_total_amount').val(APP_CONVERTER.thousand_separator(lpurchase_return.total_amount));
                        $(lparent_pane).find(lprefix_id + '_total_discount_amount').val(APP_CONVERTER.thousand_separator(lpurchase_return.total_discount_amount));
                        $(lparent_pane).find(lprefix_id + '_additional_cost_amount').val(APP_CONVERTER.thousand_separator(lpurchase_return.additional_cost_amount));
                        $(lparent_pane).find(lprefix_id + '_grand_total_amount').val(APP_CONVERTER.thousand_separator(lpurchase_return.grand_total_amount));
                        $(lparent_pane).find(lprefix_id + '_outstanding_grand_total_amount').val(APP_CONVERTER.thousand_separator(lpurchase_return.outstanding_grand_total_amount));
                        $(lparent_pane).find(lprefix_id + '_supplier').select2('data',lpurchase_return.supplier).change();

                        purchase_return_purchase_return_product_methods.load_product({pr_product:lresponse.pr_product});
                        
                        $(lparent_pane).find(lprefix_id + '_purchase_return_status')
                                .select2('data', lpurchase_return.purchase_return_status).change();

                        $(lparent_pane).find(lprefix_id + '_purchase_return_status')
                                .select2({data: lresponse.purchase_return_status_list});
                        
                        $(lparent_pane).find(lprefix_id + '_purchase_return_cancellation_reason')
                                .val(lpurchase_return.cancellation_reason);

                    }
                    ;
                    break;
            }
        }

        purchase_return_methods.enable_disable();
        purchase_return_methods.show_hide();
        purchase_return_data_set();
    }

</script>