<script>
    var sales_receipt_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var sales_receipt_ajax_url = null;
    var sales_receipt_index_url = null;
    var sales_receipt_view_url = null;
    var sales_receipt_window_scroll = null;
    var sales_receipt_data_support_url = null;
    var sales_receipt_common_ajax_listener = null;
    var sales_receipt_component_prefix_id = '';

    var sales_receipt_init = function () {
        var parent_pane = sales_receipt_parent_pane;

        sales_receipt_ajax_url = '<?php echo $ajax_url ?>';
        sales_receipt_index_url = '<?php echo $index_url ?>';
        sales_receipt_view_url = '<?php echo $view_url ?>';
        sales_receipt_window_scroll = '<?php echo $window_scroll; ?>';
        sales_receipt_data_support_url = '<?php echo $data_support_url; ?>';
        sales_receipt_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        sales_receipt_component_prefix_id = '#<?php echo $component_prefix_id; ?>';

    }

    var sales_receipt_data = {
        outstanding_grand_total_amount: '0'
    }

    var sales_receipt_methods = {
        hide_all: function () {
            var lparent_pane = sales_receipt_parent_pane;
            var lprefix_id = sales_receipt_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all: function () {
            var lparent_pane = sales_receipt_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        show_hide: function () {
            var lparent_pane = sales_receipt_parent_pane;
            var lprefix_id = sales_receipt_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            sales_receipt_methods.hide_all();

            switch (lmethod) {
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id + '_store').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_reference').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_payment_type').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_sales_receipt_date').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_bos_bank_account').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_customer_bank_account').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_sales_receipt_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id + '_amount').closest('div [class*="form-group"]').show();
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
            var lparent_pane = sales_receipt_parent_pane;
            var lprefix_id = sales_receipt_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            sales_receipt_methods.disable_all();

            switch (lmethod) {
                case "add":                
                    $(lparent_pane).find(lprefix_id + '_store').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_code").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + '_reference').select2('enable');
                    $(lparent_pane).find(lprefix_id + '_payment_type').select2('disable');
                    $(lparent_pane).find(lprefix_id + "_sales_receipt_date").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + '_bos_bank_account').select2('disable');
                    $(lparent_pane).find(lprefix_id + '_sales_receipt_status').select2('enable');
                    $(lparent_pane).find(lprefix_id + "_amount").prop("disabled", false);
                    $(lparent_pane).find(lprefix_id + "_change_amount").prop("disabled", true);
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
                case 'view':
                    $(lparent_pane).find(lprefix_id + "_notes").prop("disabled", false);
                    break;
            }
        },
        reset_all: function () {
            var lparent_pane = sales_receipt_parent_pane;
            var lprefix_id = sales_receipt_component_prefix_id;

            $(lparent_pane).find(lprefix_id + '_code').val('[AUTO GENERATE]');
            
            $(lparent_pane).find(lprefix_id+'_reference').select2('data',null);
            $(lparent_pane).find(lprefix_id+'_reference').change();
            
            $(lparent_pane).find(lprefix_id+'_sales_receipt_date').datetimepicker({
                value:APP_GENERATOR.CURR_DATETIME('minute', 10,'F d, Y H:i')
            });
            APP_FORM.status.default_status_set(
                'sales_receipt',
                $(lparent_pane).find(lprefix_id + '_sales_receipt_status')
            );
            
            APP_COMPONENT.input_select.default_set($(lparent_pane).find(lprefix_id+'_store'));
            $(lparent_pane).find(lprefix_id + '_notes').val('');
            
        },
        after_submit: function(){
            
        },
        submit: function () {
            var lparent_pane = sales_receipt_parent_pane;
            var lprefix_id = sales_receipt_component_prefix_id;
            var lajax_url = sales_receipt_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id + "_method").val();
            var sales_receipt_id = $(lparent_pane).find(lprefix_id + "_id").val();
            var json_data = {
                ajax_post: true,
                sales_receipt: {},
                message_session: true
            };

            switch(lmethod){
                case 'add':
                case 'view':                    
                    json_data.sales_receipt.sales_receipt_status = $(lparent_pane).find(lprefix_id + "_sales_receipt_status").select2('val');
                    json_data.sales_receipt.notes = $(lparent_pane).find(lprefix_id + "_notes").val();
                    break;
            
            }

            switch (lmethod) {
                case 'add':
                    var lref_data = $(lparent_pane).find(lprefix_id+'_reference').select2('data');
                    if(lref_data === null) {lref_data = {id:'',ref_type:''}};
                    json_data.sales_receipt.ref_type = lref_data.ref_type;
                    json_data.sales_receipt.ref_id = lref_data.id;
                    json_data.sales_receipt.store_id = $(lparent_pane).find(lprefix_id + "_store").select2('val');
                    json_data.sales_receipt.payment_type_id = $(lparent_pane).find(lprefix_id + "_payment_type").select2('val');
                    json_data.sales_receipt.bos_bank_account_id = $(lparent_pane).find(lprefix_id + "_bos_bank_account").select2('val');
                    json_data.sales_receipt.customer_bank_account = $(lparent_pane).find(lprefix_id + "_customer_bank_account").val();
                    json_data.sales_receipt.amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_amount").val());
                    json_data.sales_receipt.change_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id + "_change_amount").val());
                    
                    
                    break;
                case 'view':
                    
                    break;
            }
            var lajax_method = '';
            switch (lmethod) {
                case 'add':
                    lajax_method = 'sales_receipt_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id + '_sales_receipt_status').select2('data').method;
                    break;
            }
            lajax_url += lajax_method + '/' + sales_receipt_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
            
        },
        reference_dependency_set:function(){
            var lparent_pane = sales_receipt_parent_pane;
            var lprefix_id = sales_receipt_component_prefix_id;
            var lajax_url = sales_receipt_data_support_url+'reference_dependency_get/';
            var ldata = $(lparent_pane).find(lprefix_id+'_reference').select2('data');
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();
            
            
            $(lparent_pane).find(lprefix_id + '_payment_type').select2('disable');
            
            $(lparent_pane).find(lprefix_id+'_payment_type').select2('data',null).change();
                        
            APP_COMPONENT.reference_detail.empty($(lparent_pane).find(lprefix_id+'_reference_detail'));
            if($(lparent_pane).find(lprefix_id+'_reference').select2('val')!=''){
                var ljson_data = {ref_type:ldata.ref_type,ref_id:ldata.id};
                var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, ljson_data).response;
                if(lmethod === 'add'){
                    $(lparent_pane).find(lprefix_id + '_payment_type').select2('enable');      
                    
                }
                
                sales_receipt_data.outstanding_grand_total_amount = lresponse.outstanding_grand_total_amount;
                
                APP_COMPONENT.reference_detail.extra_info_set(
                    $(lparent_pane).find(lprefix_id+'_reference_detail'),
                    lresponse.reference_detail,
                    {reset:true}
                );
                
                if(lmethod === 'add'){
                    var lpayment_type_list = JSON.parse(atob($(lparent_pane).find(lprefix_id+'_payment_type').attr('select2_data_list')));
                    
                    $.each(lpayment_type_list, function(lidx, lrow){
                        if(lrow.default){
                            $(lparent_pane).find(lprefix_id+'_payment_type').select2('data',{id:lrow.id}).change();
                        }
                    });
                
                }
                
                
                
                
                
                
        
            }
            
        },
        payment_type_dependency_set:function(){
            var lparent_pane = sales_receipt_parent_pane;
            var lprefix_id = sales_receipt_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();
            
            $(lparent_pane).find(lprefix_id + '_bos_bank_account').select2('disable');
            $(lparent_pane).find(lprefix_id + '_customer_bank_account').prop('disabled',true);
            $(lparent_pane).find(lprefix_id + '_change_amount').prop('disabled',true);

            $(lparent_pane).find(lprefix_id + '_bos_bank_account').select2('data',null);                
            $(lparent_pane).find(lprefix_id + '_change_amount').val('0.00');
            $(lparent_pane).find(lprefix_id + '_customer_bank_account').val('');
            $(lparent_pane).find(lprefix_id + '_amount').val('0.00');
            
            
            if($(lparent_pane).find(lprefix_id + '_payment_type').select2('val') !=='' ){
                var ldata = $(lparent_pane).find(lprefix_id + '_payment_type').select2('data');
                if(lmethod == 'add'){
                    var loutstanding_grand_total_amount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id+'_reference_detail_outstanding_grand_total_amount')[0].innerHTML);
                    $(lparent_pane).find(lprefix_id+'_amount').val(APP_CONVERTER.thousand_separator(loutstanding_grand_total_amount));

                    if(ldata.change_amount === '1'){
                        $(lparent_pane).find(lprefix_id + '_change_amount').prop('disabled',false);
                    }                
                    if(ldata.customer_bank_account === '1'){
                        $(lparent_pane).find(lprefix_id + '_customer_bank_account').prop('disabled',false);
                    }
                    if(ldata.bos_bank_account_id_default !== null){
                        $(lparent_pane).find(lprefix_id + '_bos_bank_account').select2('enable');
                        $(lparent_pane).find(lprefix_id + '_bos_bank_account').select2('data',{id:ldata.bos_bank_account_id_default}).change();
                    }
                }
            }
        }
    }

    var sales_receipt_bind_event = function () {
        var lparent_pane = sales_receipt_parent_pane;
        var lprefix_id = sales_receipt_component_prefix_id;
        
        $(lparent_pane).find(lprefix_id + '_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id + '_btn_submit'), {
            parent_pane: lparent_pane,
            module_method: sales_receipt_methods,
            view_url: sales_receipt_view_url,
            prefix_id:lprefix_id,
            window_scroll:sales_receipt_window_scroll,
        });
        
        $(lparent_pane).find(lprefix_id+'_reference').on('change',function(){
            sales_receipt_methods.reference_dependency_set();            
        });
        
        $(lparent_pane).find(lprefix_id+'_payment_type').on('change',function(lidx, lrow){
            sales_receipt_methods.payment_type_dependency_set();
        });
        
        $(lparent_pane).find(lprefix_id+'_amount').on('blur',function(){
            var lpt_data = $(lparent_pane).find(lprefix_id+'_payment_type').select2('data');
            var lref_data = $(lparent_pane).find(lprefix_id+'_reference').select2('data');
            $(lparent_pane).find(lprefix_id+'_change_amount').val('0').blur();
            if(lpt_data !== null && lref_data !== null){
                var loutstanding_amount  = APP_CONVERTER._float(sales_receipt_data.outstanding_grand_total_amount);
                var lamount = APP_CONVERTER._float($(lparent_pane).find(lprefix_id+'_amount').val());
                if(lpt_data.change_amount === '1'){
                    var ldiff = APP_CONVERTER._float(lamount) - APP_CONVERTER._float(loutstanding_amount);
                    if(ldiff > APP_CONVERTER._float('0')){
                        $(lparent_pane).find(lprefix_id+'_change_amount').val(ldiff).blur();
                    }
                }
            }
        });
    }

    var sales_receipt_components_prepare = function () {
        var lparent_pane = sales_receipt_parent_pane;
        var lprefix_id = sales_receipt_component_prefix_id;
        var method = $(sales_receipt_parent_pane).find(lprefix_id + "_method").val();

        var sales_receipt_data_set = function () {
            var lparent_pane = sales_receipt_parent_pane;
            var lprefix_id = sales_receipt_component_prefix_id;
            switch (method) {
                case "add":
                    sales_receipt_methods.reset_all();
                    break;
                case "view":
                    var sales_receipt_id = $(sales_receipt_parent_pane).find(lprefix_id + "_id").val();
                    var json_data = {data: sales_receipt_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(sales_receipt_data_support_url + "sales_receipt_get", json_data).response;
                    if (lresponse !== []) {
                        var lsales_receipt = lresponse.sales_receipt;
                        $(lparent_pane).find(lprefix_id + '_store').select2('data',{id:lsales_receipt.store_id}).change();
                        $(lparent_pane).find(lprefix_id + '_code').val(lsales_receipt.code);
                        $(lparent_pane).find(lprefix_id + '_reference').select2('data',lsales_receipt.reference).change();
                        $(lparent_pane).find(lprefix_id + '_payment_type').select2('data',{id:lsales_receipt.payment_type_id}).change();                        
                        $(lparent_pane).find(lprefix_id + '_customer_bank_account').val(lsales_receipt.customer_bank_account);
                        $(lparent_pane).find(lprefix_id + '_notes').val(lsales_receipt.notes);
                        $(lparent_pane).find(lprefix_id + '_amount').val(APP_CONVERTER.thousand_separator(lsales_receipt.amount));
                        $(lparent_pane).find(lprefix_id + '_change_amount').val(APP_CONVERTER.thousand_separator(lsales_receipt.change_amount));
                        $(lparent_pane).find(lprefix_id + '_bos_bank_account').select2('data',{id:lsales_receipt.bos_bank_account_id}).change();
                        
                        
                        $(lparent_pane).find(lprefix_id + '_sales_receipt_status')
                                .select2('data', lsales_receipt.sales_receipt_status).change();

                        $(lparent_pane).find(lprefix_id + '_sales_receipt_status')
                                .select2({data: lresponse.sales_receipt_status_list});
                        
                        $(lparent_pane).find(lprefix_id + '_sales_receipt_cancellation_reason')
                                .val(lsales_receipt.cancellation_reason);

                    }
                    ;
                    break;
            }
        }

        sales_receipt_methods.enable_disable();
        sales_receipt_methods.show_hide();
        sales_receipt_data_set();
    }

</script>