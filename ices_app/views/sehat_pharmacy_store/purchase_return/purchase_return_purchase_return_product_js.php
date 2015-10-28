<script>
    var purchase_return_purchase_return_product_methods = {
        load_product: function(iparam) {
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;

            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            purchase_return_tbl_pr_product_method.reset();
            purchase_return_tbl_pr_product_method.head_generate();
            
            if(lmethod === 'add'){
                $(lparent_pane).find(lprefix_id+'_tbl_pr_product th[col_name="available_qty"]').show();
            }
            else if (lmethod === 'view'){
                $(lparent_pane).find(lprefix_id+'_tbl_pr_product th[col_name="available_qty"]').hide();
            }
            
            $.each(iparam.pr_product, function(lidx, lrow) {
                purchase_return_tbl_pr_product_method.input_row_generate(lrow);
            });
            
            if(lmethod === 'add'){
                
            }

        }
    };

    var purchase_return_purchase_return_product_bind_event = function() {
        var lparent_pane = purchase_return_parent_pane;
        var lprefix_id = purchase_return_component_prefix_id;

        purchase_return_tbl_pr_product_method.setting.func_new_row_validation = function(iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            var lc = iopt.comp;

            
            <?php //validation replace space or empty sting ?>
            
            
            lresult.success = success;
            return lresult;
        };

        purchase_return_tbl_pr_product_method.setting.func_get_data_table = function() {
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            var lresult = {pr_product: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();

            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_pr_product tbody')[0];
            $.each($(ltbody).find('tr'), function(lidx, lrow) {
                var lqty = APP_CONVERTER._float($(lrow).find('[col_name="qty"] input').val());
                
                if(APP_CONVERTER._float(lqty) > APP_CONVERTER._float('0')){
                    var ltemp = {
                        ref_type: $(lrow).find('[col_name="ref_type"] div')[0].innerHTML,
                        ref_id: $(lrow).find('[col_name="ref_id"] div')[0].innerHTML,
                        qty: APP_CONVERTER._float($(lrow).find('[col_name="qty"] input').val()),
                    };
                    
                    lresult.pr_product.push(ltemp);
                }

            });
            return lresult;
        };

        purchase_return_tbl_pr_product_method.setting.func_row_bind_event = function(iopt) {
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_pr_product')[0];
            
            var lsubtotal_amount_set = function(){
                var lamount = $(lc.amount).find('div')[0].innerHTML;
                var lqty = $(lc.qty).find('input').val();
                var lsubtotal_amount = APP_CONVERTER._float(lamount) * APP_CONVERTER._float(lqty);
                $(lc.subtotal_amount).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator(lsubtotal_amount);
                purchase_return_methods.all_amount_set();
            }
            
            <?php // --- Event Preparation phase ---             ?>
            
            if (lmethod === 'add') {                
                $(lc.qty).find('input').css('text-align','right');
                APP_COMPONENT.input.numeric($(lc.qty).find('input'),{min_val:0,max_val:ldata_row.available_qty});
                
                $(lc.qty).find('input').on('blur',function(){
                    lsubtotal_amount_set();
                });
                
            }
            else if (lmethod === 'view') {
                
            }

            
            
            <?php // --- Show and Hide phase ---             ?>
            if(lmethod === 'add'){
                
            }
            else if(lmethod === 'view'){
                $(lc.available_qty).closest('td').hide();                
            }

        };

        purchase_return_tbl_pr_product_method.setting.func_row_transform_comp_on_new_row = function(iopt) {
            var lrow = iopt.tr;
            var lc = iopt.comp;
        };

        purchase_return_tbl_pr_product_method.setting.func_row_data_assign = function(iopt) {
            var lparent_pane = purchase_return_parent_pane;
            var lprefix_id = purchase_return_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            switch (lmethod) {
                case 'add':                    
                    $(lc.ref_type)[0].innerHTML = '<div>'+ldata_row.ref_type+'</div>';
                    $(lc.ref_id)[0].innerHTML = '<div>'+ldata_row.ref_id+'</div>';
                    $(lc.product_type)[0].innerHTML = '<div>'+ldata_row.product_type+'</div>';
                    $(lc.product)[0].innerHTML = '<div>'+ldata_row.product_text+'</div>';
                    $(lc.expired_date)[0].innerHTML = '<div>'+APP_CONVERTER._date(ldata_row.expired_date,'F d, Y H:i')+'</div>';
                    $(lc.available_qty)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.available_qty)+'</div>';
                    $(lc.qty).find('input').val('0').blur();
                    $(lc.unit)[0].innerHTML = '<div>'+ldata_row.unit_text+'</div>';
                    $(lc.amount)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.amount)+'</div>';
                    $(lc.subtotal_amount)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator('0')+'</div>';
                    $(lrow).find('[col_name="action"]')[0].innerHTML = '';
                    break;
                case 'view':
                    $(lc.product_type)[0].innerHTML = '<div>'+ldata_row.product_type+'</div>';
                    $(lc.product)[0].innerHTML = '<div>'+ldata_row.product_text+'</div>';
                    $(lc.expired_date)[0].innerHTML = '<div>'+APP_CONVERTER._date(ldata_row.expired_date,'F d, Y H:i')+'</div>';
                    $(lc.available_qty)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.available_qty)+'</div>';
                    $(lc.qty)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.qty)+'</div>';
                    $(lc.unit)[0].innerHTML = '<div>'+ldata_row.unit_text+'</div>';
                    $(lc.amount)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.amount)+'</div>';
                    $(lc.subtotal_amount)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.subtotal_amount)+'</div>';
                    $(lrow).find('[col_name="action"]')[0].innerHTML = '';
                    break;
            }
            
            if (Object.keys(ldata_row).length > 0) {
                
            }

        };

    };
</script>