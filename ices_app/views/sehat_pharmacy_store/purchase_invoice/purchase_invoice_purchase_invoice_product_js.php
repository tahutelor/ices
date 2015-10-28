<script>
    var purchase_invoice_purchase_invoice_product_methods = {
        load_product: function(iparam) {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;

            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            purchase_invoice_tbl_purchase_invoice_product_method.reset();
            purchase_invoice_tbl_purchase_invoice_product_method.head_generate();
            
            $.each(iparam.purchase_invoice_product, function(lidx, lrow) {
                purchase_invoice_tbl_purchase_invoice_product_method.input_row_generate(lrow);
            });
            
            if(lmethod === 'add'){
                purchase_invoice_tbl_purchase_invoice_product_method.input_row_generate({});
            }

        }
    };

    var purchase_invoice_purchase_invoice_product_bind_event = function() {
        var lparent_pane = purchase_invoice_parent_pane;
        var lprefix_id = purchase_invoice_component_prefix_id;

        purchase_invoice_tbl_purchase_invoice_product_method.setting.func_new_row_validation = function(iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            var lc = iopt.comp;

            
            <?php //validation replace space or empty sting ?>
            
            APP_COMPONENT.input_select.mark($(lc.product).find('input[original]'),{mark_type:'valid'});
            APP_COMPONENT.input.mark($(lc.qty).find('input'),{mark_type:'valid'});
            APP_COMPONENT.input.mark($(lc.amount).find('input'),{mark_type:'valid'});
            APP_COMPONENT.input.mark($(lc.expired_date).find('input'),{mark_type:'valid'});
            
            
            if ($(lc.product_id).find('div')[0].innerHTML === '') {
                success = 0;
                APP_COMPONENT.input_select.mark($(lc.product),{mark_type:'invalid'});
            }
            
            if(APP_CONVERTER._float($(lc.qty).find('input').val()) <= APP_CONVERTER._float(0) ){
                success = 0;
                APP_COMPONENT.input.mark($(lc.qty).find('input'),{mark_type:'invalid'});
            }
            
            if(APP_CONVERTER._float($(lc.amount).find('input').val()) < APP_CONVERTER._float(0) ){
                success = 0;
                APP_COMPONENT.input.mark($(lc.amount).find('input'),{mark_type:'invalid'});
            }
                        
            if($(lc.expired_date).find('input').val() === ''){
                success = 0;
                APP_COMPONENT.input.mark($(lc.expired_date).find('input'),{mark_type:'invalid'});
            }
            else if (APP_CONVERTER._date($(lc.expired_date).find('input').val(),'Y-m-d H:i:s') < APP_CONVERTER._date(null,'Y-m-d H:i:s')){
                success = 0;
                APP_COMPONENT.input.mark($(lc.expired_date).find('input'),{mark_type:'invalid'});
            }
            
            
            lresult.success = success;
            return lresult;
        };

        purchase_invoice_tbl_purchase_invoice_product_method.setting.func_get_data_table = function() {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            var lresult = {purchase_invoice_product: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();

            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_purchase_invoice_product tbody')[0];
            $.each($(ltbody).find('tr'), function(lidx, lrow) {
                var lproduct_id = $(lrow).find('[col_name="product_id"] div')[0].innerHTML;
                
                if(lproduct_id !== ''){
                    var ltemp = {
                        product_type: $(lrow).find('[col_name="product_type"] div')[0].innerHTML,
                        product_id:lproduct_id,
                        unit_id: $(lrow).find('[col_name="unit_id"] div')[0].innerHTML
                    };
                    
                    if(lidx !== ($(ltbody).find('tr').length - 1)){
                        ltemp.qty = APP_CONVERTER._float($(lrow).find('[col_name="qty"] div')[0].innerHTML);
                        ltemp.amount = APP_CONVERTER._float($(lrow).find('[col_name="amount"] div')[0].innerHTML);
                        var lexpired_date = $(lrow).find('[col_name="expired_date"] div')[0].innerHTML;
                        lexpired_date = lexpired_date === ''?null:(new Date(lexpired_date)).format('Y-m-d H:i:s');
                        ltemp.expired_date = lexpired_date;
                    }
                    else{
                        ltemp.qty = APP_CONVERTER._float($(lrow).find('[col_name="qty"] input').val());
                        ltemp.amount = APP_CONVERTER._float($(lrow).find('[col_name="amount"] input').val());
                        var lexpired_date = $(lrow).find('[col_name="expired_date"] input').val();
                        lexpired_date = lexpired_date === ''?null:(new Date(lexpired_date)).format('Y-m-d H:i:s');
                        ltemp.expired_date = lexpired_date;
                    }
                    
                    lresult.purchase_invoice_product.push(ltemp);
                }

            });
            return lresult;
        };

        purchase_invoice_tbl_purchase_invoice_product_method.setting.func_row_bind_event = function(iopt) {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_purchase_invoice_product')[0];
            
            var lsubtotal_amount_set = function(){
                var lamount = $(lc.amount).find('input').val();
                var lqty = $(lc.qty).find('input').val();
                var lsubtotal_amount = APP_CONVERTER._float(lamount) * APP_CONVERTER._float(lqty);
                $(lc.subtotal_amount).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator(lsubtotal_amount);
                purchase_invoice_methods.all_amount_set();
            }
            
            if (lmethod === 'add') {                

                APP_COMPONENT.input_select.set($(lc.product).find('input'),
                {
                    min_input_length:0,
                    place_holder:'Type something to search',
                    allow_clear:true,
                    ajax_url:purchase_invoice_ajax_url+'input_select_product_search',
                    exceptional_data_func:function(){return [];}
                },
                function(){
                    
                });
                
                $(lc.expired_date).find('input').datetimepicker({
                    value:'',
                    format:'F d, Y H:i'
                });
                
                $(lc.qty).find('input').css('text-align','right');
                APP_COMPONENT.input.numeric($(lc.qty).find('input'),{min_val:0});
                
                $(lc.amount).find('input').css('text-align','right');
                APP_COMPONENT.input.numeric($(lc.amount).find('input'),{min_val:0});
                
                $(lc.subtotal_amount).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator('0');
                
                $(lc.product).find('input').on('change',function(){
                    $(lc.product_type).find('div')[0].innerHTML = '';
                    $(lc.product_id).find('div')[0].innerHTML = '';
                    $(lc.expired_date).find('input').val('');
                    $(lc.qty).find('input').val('0').blur();                    
                    $(lc.unit_id).find('div')[0].innerHTML = '';
                    $(lc.unit).find('div')[0].innerHTML = '';
                    $(lc.amount).find('input').val('0').blur();
                    $(lc.subtotal_amount).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator('0');
                
                    if($(this).select2('val')!== ''){
                        var ldata = $(this).select2('data');
                        $(lc.product_type).find('div')[0].innerHTML = ldata.product_type;
                        $(lc.product_id).find('div')[0].innerHTML = ldata.id;
                        $(lc.unit_id).find('div')[0].innerHTML = ldata.unit[0].id;
                        $(lc.unit).find('div')[0].innerHTML = ldata.unit[0].text;
                    }
                });
                
                $(lc.amount).find('input').on('blur',function(){
                    lsubtotal_amount_set();
                });
                
                $(lc.qty).find('input').on('blur',function(){
                    lsubtotal_amount_set();
                });
                
            }
            else if (lmethod === 'view') {

            }

<?php // --- Show and Hide phase ---             ?>
<?php // --- End Of Show and Hide phase ---             ?>
            
            if (Object.keys(ldata_row).length === 0) {
            }

        };

        purchase_invoice_tbl_purchase_invoice_product_method.setting.func_row_transform_comp_on_new_row = function(iopt) {
            var lrow = iopt.tr;
            var lc = iopt.comp;
            $(lc.product)[0].innerHTML = '<div>'+$(lc.product).find('input').select2('data').text+'</div>';
            $(lc.expired_date)[0].innerHTML = '<div>'+$(lc.expired_date).find('input').val()+'</div>';
            $(lc.qty)[0].innerHTML = '<div>'+$(lc.qty).find('input').val()+'</div>';
            $(lc.amount)[0].innerHTML = '<div>'+$(lc.amount).find('input').val()+'</div>';
        };

        purchase_invoice_tbl_purchase_invoice_product_method.setting.func_row_data_assign = function(iopt) {
            var lparent_pane = purchase_invoice_parent_pane;
            var lprefix_id = purchase_invoice_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            switch (lmethod) {
                case 'add':
                
                    break;
                case 'view':
                    
                    break;
            }
            
            if (Object.keys(ldata_row).length > 0) {
                $(lc.product_type)[0].innerHTML = '<div>'+ldata_row.product_type+'</div>';
                $(lc.product)[0].innerHTML = '<div>'+ldata_row.product_text+'</div>';
                $(lc.expired_date)[0].innerHTML = '<div>'+APP_CONVERTER._date(ldata_row.expired_date,'F d, Y H:i')+'</div>';
                $(lc.qty)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.qty)+'</div>';
                $(lc.unit)[0].innerHTML = '<div>'+ldata_row.unit_text+'</div>';
                $(lc.amount)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.amount)+'</div>';
                $(lc.subtotal_amount)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.subtotal_amount)+'</div>';
                $(lrow).find('[col_name="action"]')[0].innerHTML = '';
            }

        };

    };
</script>