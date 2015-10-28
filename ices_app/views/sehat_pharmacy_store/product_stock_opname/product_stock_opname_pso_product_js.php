<script>
    var product_stock_opname_pso_product_methods = {
        load_product: function(iparam) {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;

            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            product_stock_opname_tbl_pso_product_method.reset();
            product_stock_opname_tbl_pso_product_method.head_generate();
            
            $.each(iparam.pso_product, function(lidx, lrow) {
                product_stock_opname_tbl_pso_product_method.input_row_generate(lrow);
            });
            
            if(lmethod === 'add'){
                product_stock_opname_tbl_pso_product_method.input_row_generate({});
            }

        },
        mark_qty:function(icomp){
            var lval = APP_CONVERTER._float($(icomp)[0].innerHTML);
            $(icomp).removeClass('text-red');
            if(lval < APP_CONVERTER._float('0')){
                $(icomp).addClass('text-red');
            }
        }
    };

    var product_stock_opname_pso_product_bind_event = function() {
        var lparent_pane = product_stock_opname_parent_pane;
        var lprefix_id = product_stock_opname_component_prefix_id;

        product_stock_opname_tbl_pso_product_method.setting.func_new_row_validation = function(iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            var lc = iopt.comp;

            
            <?php //validation replace space or empty sting ?>
            
            APP_COMPONENT.input_select.mark($(lc.product).find('input'),{mark_type:'valid'});
            APP_COMPONENT.input_select.mark($(lc.product_batch).find('input'),{mark_type:'valid'});
            APP_COMPONENT.input.mark($(lc.new_qty),{mark_type:'valid'});
            
            if ($(lc.product_id).find('div')[0].innerHTML === '') {
                success = 0;
                APP_COMPONENT.input_select.mark($(lc.product).find('input[original]'),{mark_type:'invalid'});
            }
            
            if ($(lc.product_batch_id).find('div')[0].innerHTML === '') {
                success = 0;
                APP_COMPONENT.input_select.mark($(lc.product_batch).find('input[original]'),{mark_type:'invalid'});
            }
            
            if(APP_CONVERTER._float($(lc.qty).find('div')[0].innerHTML) == APP_CONVERTER._float(0) ){
                success = 0;
                APP_COMPONENT.input.mark($(lc.new_qty).find('input'),{mark_type:'invalid'});
            }
                       
            
            lresult.success = success;
            return lresult;
        };

        product_stock_opname_tbl_pso_product_method.setting.func_get_data_table = function() {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;
            var lresult = {pso_product: []};
            
            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_pso_product tbody')[0];
            $.each($(ltbody).find('tr'), function(lidx, lrow) {
                var lproduct_batch_id = $(lrow).find('[col_name="product_batch_id"] div')[0].innerHTML;
                var lqty = APP_CONVERTER._float($(lrow).find('[col_name="qty"] div')[0].innerHTML);
                
                if(lproduct_batch_id !== '' && APP_CONVERTER._float(lqty) !== APP_CONVERTER._float('0')){
                    var ltemp = {
                        product_batch_id: $(lrow).find('[col_name="product_batch_id"] div')[0].innerHTML,
                        qty: lqty,
                    };
                    lresult.pso_product.push(ltemp);
                }

            });
            return lresult;
        };

        product_stock_opname_tbl_pso_product_method.setting.func_row_bind_event = function(iopt) {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_pso_product')[0];
            
            if (lmethod === 'add') {
                
                var ldiff_qty_set = function(){
                    var ldiff = APP_CONVERTER._float('0');
                    var lold_qty = APP_CONVERTER._float($(lc.old_qty).find('div')[0].innerHTML);
                    var lnew_qty = APP_CONVERTER._float($(lc.new_qty).find('input').val());
                    ldiff = lnew_qty - lold_qty;
                    $(lc.qty).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator(ldiff);
                    product_stock_opname_pso_product_methods.mark_qty($(lc.qty).find('div'));
                    
                }

                APP_COMPONENT.input_select.set($(lc.product).find('input[original]'),
                {
                    min_input_length:0,
                    place_holder:'Type something to search',
                    allow_clear:true,
                    ajax_url:product_stock_opname_ajax_url+'input_select_product_search',
                    exceptional_data_func:function(){return [];}
                },
                function(){
                    
                });
                
                $(lc.product).find('input[original]').on('change',function(){
                    var ldata = $(this).select2('data');
                    $(lc.product_batch).find('input[original]').select2('data',null);
                    $(lc.product_batch).find('input[original]').change();
                    $(lc.product_id).find('div')[0].innerHTML = '';
                    $(lc.unit_id).find('div')[0].innerHTML = '';
                    $(lc.unit).find('div')[0].innerHTML = '';
                    
                    if(ldata !== null){
                        $(lc.product_id).find('div')[0].innerHTML = ldata.id;
                        $(lc.unit_id).find('div')[0].innerHTML = ldata.unit[0].id;
                        $(lc.unit).find('div')[0].innerHTML = ldata.unit[0].text;
                    }
                });
                
                APP_COMPONENT.input_select.set($(lc.product_batch).find('input[original]'),
                {
                    min_input_length:0,
                    place_holder:'Type something to search',
                    allow_clear:true,
                    ajax_url:product_stock_opname_ajax_url+'input_select_product_batch_search',
                    exceptional_data_func:function(){
                        var ltrs = $(ltable).find('tbody tr:not(:last)');
                        var lresult = [];
                        $.each(ltrs, function(lidx, lrow){
                            lresult.push({id:$(lrow).find('[col_name="product_batch_id"] div')[0].innerHTML});
                        });
                        return lresult;
                    }
                },
                function(){
                    var lparent_pane = product_stock_opname_parent_pane;
                    var lprefix_id = product_stock_opname_component_prefix_id;
                    var lresult = {};
                    var lproduct_id = $(lc.product_id).find('div')[0].innerHTML;
                    var lunit_id = $(lc.unit_id).find('div')[0].innerHTML;
                    var lwarehouse_id = $(lparent_pane).find(lprefix_id+'_warehouse').select2('val');
                    lresult.product_id = lproduct_id;
                    lresult.unit_id = lunit_id;
                    lresult.warehouse_id = lwarehouse_id;
                    return lresult;
                });
                
                $(lc.product_batch).find('input[original]').on('change',function(){
                    var ldata = $(this).select2('data');
                    $(lc.old_qty).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator('0');
                    $(lc.new_qty).find('input').val('0').blur();
                    $(lc.product_batch_id).find('div')[0].innerHTML = '';
                    if(ldata !== null){
                        $(lc.old_qty).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator(ldata.qty);
                        $(lc.new_qty).find('input').val(ldata.qty).blur();
                        $(lc.product_batch_id).find('div')[0].innerHTML = ldata.id;
                    }
                });
                
                
                $(lc.new_qty).find('input').css('text-align','right');
                APP_COMPONENT.input.numeric($(lc.new_qty).find('input'),{min_val:0});        
                $(lc.new_qty).find('input').on('blur',function(){
                    ldiff_qty_set();
                });
                
            }
            else if (lmethod === 'view') {

            }

<?php // --- Show and Hide phase ---             ?>
<?php // --- End Of Show and Hide phase ---             ?>
            
            if (Object.keys(ldata_row).length === 0) {
            }

        };

        product_stock_opname_tbl_pso_product_method.setting.func_row_transform_comp_on_new_row = function(iopt) {
            var lrow = iopt.tr;
            var lc = iopt.comp;
            $(lc.product)[0].innerHTML = '<div>'+$(lc.product).find('input[original]').select2('data').text+'</div>';
            $(lc.product_batch)[0].innerHTML = '<div>'+$(lc.product_batch).find('input[original]').select2('data').text+'</div>';
            $(lc.new_qty)[0].innerHTML = '<div>'+$(lc.new_qty).find('input').val()+'</div>';
        };

        product_stock_opname_tbl_pso_product_method.setting.func_row_data_assign = function(iopt) {
            var lparent_pane = product_stock_opname_parent_pane;
            var lprefix_id = product_stock_opname_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            switch (lmethod) {
                case 'add':
                    $(lc.old_qty).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator('0');
                    $(lc.qty).find('div')[0].innerHTML = APP_CONVERTER.thousand_separator('0');
                    $(lc.new_qty).find('input').val('0').blur();
                    $(lc.product_batch_id).find('div')[0].innerHTML = '';
                    $(lc.product_id).find('div')[0].innerHTML = '';
                    $(lc.unit_id).find('div')[0].innerHTML = '';
                    break;
                case 'view':
                    
                    break;
            }
            
            if (Object.keys(ldata_row).length > 0) {
                
                $(lc.product)[0].innerHTML = '<div>'+ldata_row.product_text+'</div>';
                $(lc.unit)[0].innerHTML = '<div>'+ldata_row.unit_text+'</div>';
                $(lc.product_batch)[0].innerHTML = '<div>'+ldata_row.product_batch_text+'</div>';
                $(lc.old_qty)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.old_qty)+'</div>';
                $(lc.qty)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.qty)+'</div>';
                
                $(lc.new_qty)[0].innerHTML = '<div>'+APP_CONVERTER.thousand_separator(ldata_row.new_qty)+'</div>';
                $(lrow).find('[col_name="action"]')[0].innerHTML = '';
                product_stock_opname_pso_product_methods.mark_qty($(lc.qty).find('div'));
            }
            

        };

    };
</script>