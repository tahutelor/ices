<script>
    var sales_invoice_si_product_methods = {
        load_product: function(iparam) {
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;

            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            sales_invoice_tbl_si_product_method.reset();
            sales_invoice_tbl_si_product_method.head_generate();
            
            $(lparent_pane).find(lprefix_id+'_tbl_si_product th[col_name="stock_qty"]').hide();
            if(lmethod === 'add'){
                $(lparent_pane).find(lprefix_id+'_tbl_si_product th[col_name="stock_qty"]').show();
            }
            
            
            $.each(iparam.si_product, function(lidx, lrow) {
                sales_invoice_tbl_si_product_method.input_row_generate(lrow);
            });
            
            if(lmethod === 'add'){
                sales_invoice_tbl_si_product_method.input_row_generate({});
            }

        }
    };

    var sales_invoice_si_product_bind_event = function() {
        var lparent_pane = sales_invoice_parent_pane;
        var lprefix_id = sales_invoice_component_prefix_id;

        sales_invoice_tbl_si_product_method.setting.func_new_row_validation = function(iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            
            <?php //validation replace space or empty sting ?>
            
            APP_COMPONENT.input_select.mark($(lc.product).find('input[original]'),{mark_type:'valid'});
            APP_COMPONENT.input.mark($(lc.qty).find('input'),{mark_type:'valid'});
            
                        
            
            if ($(lc.product_id).find('div')[0].innerHTML === '') {
                success = 0;
                APP_COMPONENT.input_select.mark($(lc.product).find('input[original]'),{mark_type:'invalid'});
            }
            
            if(APP_CONVERTER._float($(lc.qty).find('input').val()) <= APP_CONVERTER._float(0) ){
                success = 0;
                APP_COMPONENT.input.mark($(lc.qty).find('input'),{mark_type:'invalid'});
            }
            
            if(APP_CONVERTER._float($(lc.amount).find('div')[0].innerHTML) < APP_CONVERTER._float(0)){
                success = 0;
            }           
            
            lresult.success = success;
            return lresult;
        };

        sales_invoice_tbl_si_product_method.setting.func_get_data_table = function() {
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            var lresult = {si_product: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();

            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_si_product tbody')[0];
            $.each($(ltbody).find('tr'), function(lidx, lrow) {
                var lproduct_id = $(lrow).find('[col_name="product_id"] div')[0].innerHTML;
                var lconstant_sales = $(lrow).find('[col_name="constant_sales"] div')[0].innerHTML;
                
                if(lproduct_id !== ''){
                    var ltemp = {
                        product_type: $(lrow).find('[col_name="product_type"] div')[0].innerHTML,
                        product_id:lproduct_id,
                        unit_id: $(lrow).find('[col_name="unit_id"] div')[0].innerHTML,
                        unit_id_sales: $(lrow).find('[col_name="unit_id_sales"] div')[0].innerHTML
                    };
                    
                    if(lidx !== ($(ltbody).find('tr').length - 1)){
                        var lqty = APP_CONVERTER._float($(lrow).find('[col_name="qty"] div')[0].innerHTML)
                            /  APP_CONVERTER._float(lconstant_sales)
                        ;
                        ltemp.qty = lqty;
                    }
                    else{
                        var lqty = APP_CONVERTER._float($(lrow).find('[col_name="qty"] input').val())
                            /  APP_CONVERTER._float(lconstant_sales)
                        ;
                        ltemp.qty = lqty;
                    }
                    
                    if(APP_CONVERTER._float(ltemp.qty) > APP_CONVERTER._float('0')){
                        lresult.si_product.push(ltemp);
                    }
                }

            });
            return lresult;
        };

        sales_invoice_tbl_si_product_method.setting.func_row_bind_event = function(iopt) {
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_si_product')[0];
            
            var lsubtotal_amount_set = function(){
                var lamount = APP_CONVERTER._float($(lc.amount).find('div')[0].innerHTML);
                var lqty = APP_CONVERTER._float($(lc.qty).find('input').val());
                var lsubtotal_amount = APP_CONVERTER._float(lamount) * APP_CONVERTER._float(lqty);
                $(lc.subtotal_amount).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator(lsubtotal_amount);
                sales_invoice_methods.all_amount_set();
            }
            
            if (lmethod === 'add') {                

                APP_COMPONENT.input_select.set($(lc.product).find('input[original]'),
                {
                    min_input_length:0,
                    place_holder:'Type something to search',
                    allow_clear:true,
                    ajax_url:sales_invoice_ajax_url+'input_select_product_search',
                    exceptional_data_func:function(){
                        var lresult = [];
                        var ltrs = $(lparent_pane).find(lprefix_id+'_tbl_si_product tbody tr:not(:last)');
                        $.each(ltrs,function(lidx, ltr){
                            lresult.push({
                                id:$(ltr).find('[col_name="product_id"] div')[0].innerHTML
                            });
                        });    
        
                        return lresult;
                    }
                },
                function(){
                    
                });
                
                $(lc.expired_date).datetimepicker({
                    value:'',
                    format:'F d, Y H:i'
                });
                
                $(lc.qty).find('input').css('text-align','right');
                APP_COMPONENT.input.numeric($(lc.qty).find('input'),{min_val:0,max_val:0});
                
                $(lc.product).find('input[original]').on('change',function(){
                    APP_COMPONENT.input.numeric($(lc.qty).find('input'),{min_val:0,max_val:0,reset:true});
                    
                    $(lc.qty).find('input').on('blur',function(){
                        lqty_event_on_blur();
                    });
                    
                    $(lc.product_type).find('div')[0].innerHTML = '';
                    $(lc.product_id).find('div')[0].innerHTML = '';
                    $(lc.qty).find('input').val('0').blur();                    
                    $(lc.unit_id).find('div')[0].innerHTML = '';
                    $(lc.unit).find('div')[0].innerHTML = '';
                    $(lc.amount).find('div')[0].innerHTML = '0.00';
                    $(lc.subtotal_amount).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator('0');
                    $(lc.stock_qty).find('div').removeClass('text-red');
                    $(lc.stock_qty).find('div')[0].innerHTML = '0.00';
                    $(lc.amount).find('div').removeClass('text-red');
                    $(lc.constant_sales).find('div')[0].innerHTML = '0';
                
                    if($(this).select2('val')!== ''){
                        var ldata = $(this).select2('data');
                        var lconstant_sales = ldata.unit[0].unit_sales[0].constant_sales;
                        $(lc.constant_sales).find('div')[0].innerHTML = lconstant_sales;
                        
                        var lstock_qty = APP_CONVERTER._float(ldata.unit[0].qty) * APP_CONVERTER._float($(lc.constant_sales).find('div')[0].innerHTML);
                        var lamount  = APP_CONVERTER._float(ldata.unit[0].sales_amount) / APP_CONVERTER._float($(lc.constant_sales).find('div')[0].innerHTML);
                        
                        APP_COMPONENT.input.numeric($(lc.qty).find('input'),{
                            min_val:0,
                            max_val:lstock_qty,
                            reset:true
                        });
                        $(lc.qty).find('input').on('blur',function(){
                            lqty_event_on_blur();
                        });
                        
                        
                        $(lc.product_type).find('div')[0].innerHTML = ldata.product_type;
                        $(lc.product_id).find('div')[0].innerHTML = ldata.id;
                        $(lc.unit_id).find('div')[0].innerHTML = ldata.unit[0].id;
                        $(lc.unit).find('div')[0].innerHTML = ldata.unit[0].text;
                        $(lc.unit_id_sales).find('div')[0].innerHTML = ldata.unit[0].unit_sales[0].id;
                        $(lc.unit_sales).find('div')[0].innerHTML = ldata.unit[0].unit_sales[0].text;
                        
                        $(lc.stock_qty).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator(lstock_qty);
                        if(APP_CONVERTER._float(lstock_qty) <= APP_CONVERTER._float(0)){
                            $(lc.stock_qty).find('div').addClass('text-red');
                        }
                        
                        $(lc.amount).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator(lamount);
                        if(APP_CONVERTER._float(eval(ldata.unit[0].sales_amount)) <= APP_CONVERTER._float(0)){
                            $(lc.amount).find('div').addClass('text-red');
                        }
                    }
                });
                
                var lqty_event_on_blur = function(){
                    lsubtotal_amount_set();
                };
                
                $(lc.qty).find('input').on('blur',function(){
                    lqty_event_on_blur();
                });
                
            }
            else if (lmethod === 'view') {

            }

<?php // --- Show and Hide phase ---             ?>
            if(lmethod === 'add'){
                $(lc.stock_qty).show();
            }
            else{
                $(lc.stock_qty).hide();
            }
<?php // --- End Of Show and Hide phase ---             ?>
            
            if (Object.keys(ldata_row).length === 0) {
            }

        };

        sales_invoice_tbl_si_product_method.setting.func_row_transform_comp_on_new_row = function(iopt) {
            var lrow = iopt.tr;
            var lc = iopt.comp;
            $(lc.product)[0].innerHTML = '<div>'+$(lc.product).find('input[original]').select2('data').text+'</div>';
            $(lc.qty)[0].innerHTML = '<div>'+$(lc.qty).find('input').val()+'</div>';
        };

        sales_invoice_tbl_si_product_method.setting.func_row_data_assign = function(iopt) {
            var lparent_pane = sales_invoice_parent_pane;
            var lprefix_id = sales_invoice_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            switch (lmethod) {
                case 'add':
                    $(lc.subtotal_amount).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator('0');
                    $(lc.amount).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator('0');
                    $(lc.stock_qty).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator('0');
                    break;
                case 'view':
                    
                    break;
            }
            
            if (Object.keys(ldata_row).length > 0) {
                var lconstant_sales = ldata_row.constant_sales;
                
                $(lc.product_type)[0].innerHTML = '<div>'+ldata_row.product_type+'</div>';
                $(lc.product)[0].innerHTML = '<div>'+ldata_row.product_text+'</div>';
                var lqty = APP_CONVERTER._float(ldata_row.qty) * APP_CONVERTER._float(ldata_row.constant_sales);
                $(lc.qty)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(lqty)+'</div>';
                $(lc.unit)[0].innerHTML = '<div>'+ldata_row.unit_text+'</div>';
                $(lc.unit_sales)[0].innerHTML = '<div>'+ldata_row.unit_text_sales+'</div>';
                $(lc.constant_sales)[0].innerHTML = '<div>'+ldata_row.constant_sales+'</div>';
                var lamount = APP_CONVERTER._float(ldata_row.amount) / APP_CONVERTER._float(ldata_row.constant_sales);
                $(lc.amount)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(lamount)+'</div>';
                $(lc.subtotal_amount)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.subtotal_amount)+'</div>';
                $(lrow).find('[col_name="action"]')[0].innerHTML = '';
            }

        };

    };
</script>