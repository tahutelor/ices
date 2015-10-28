<script>
    var employee_u_group_methods = {
        load_u_group: function(iparam) {
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            employee_tbl_u_group_method.reset();
            employee_tbl_u_group_method.head_generate();
            $.each(iparam.u_group, function(lidx, lrow) {
                employee_tbl_u_group_method.input_row_generate(lrow);
            });
            employee_tbl_u_group_method.input_row_generate({});
        }
    }

    var employee_u_group_bind_event = function() {
        var lparent_pane = employee_parent_pane;
        var lprefix_id = employee_component_prefix_id;
        var lajax_url = '<?php echo $data_support_url . 'u_group_get'; ?>';
        var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, {}).response;
        var lu_group = [];
        $.each(lresponse.u_group, function(lidx, lrow) {
            lu_group.push(lrow);
        });
        
        var lu_group_get = function(){
            var lresult = [];
            var lu_group_id_div = $(lparent_pane).find(lprefix_id+'_tbl_u_group tbody [col_name="u_group_id"] div');
            
                
            $.each(lu_group,function(lidx, lrow){    
                var lexists = false;
                $.each(lu_group_id_div, function(lidx2, lrow2){
                    if(lrow.id === $(lrow2)[0].innerHTML) lexists = true;     
                });
                if(!lexists) lresult.push(lrow);
            });
                
            
            return lresult;
        };
        
        employee_tbl_u_group_method.setting.func_new_row_validation = function(iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            <?php // --- validation select employee category---  ?>
            var lu_group = $(lrow).find('[col_name="u_group"] input[original]');
            var la = $(lrow).find('[col_name="u_group"] .select2-container>a');
            var lval = lu_group.select2('val');
            if (lval === '') {
                success = 0;                
                $(la).css('border-color', APP_COLOR.red);
                $(lu_group).on('select2-open',function(){
                    $(la).css('border-color','');
                });
                $(lu_group).on('select2-close',function(){
                    $(la).css('border-color',APP_COLOR.red);
                });
            }

            lresult.success = success;
            return lresult;
        };
        employee_tbl_u_group_method.setting.func_get_data_table = function() {
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            var lresult = {u_group: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();
            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_u_group tbody')[0];
            $.each($(ltbody).find('tr'), function(lidx, lrow) {
                var lu_group_id = $(lrow).find('[col_name="u_group_id"] div')[0].innerHTML;            
                if (lu_group_id !== '') {
                    lresult.u_group.push({id:lu_group_id});
                }

            });
            return lresult;
        };
        employee_tbl_u_group_method.setting.func_row_bind_event = function(iopt) {
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            var lrow = iopt.tr;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
<?php // --- Show and Hide phase ---                   ?>
            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_u_group')[0];
            if (lmethod === 'add') {

            }
            else if (lmethod === 'view') {

            }
<?php // --- End Of Show and Hide phase ---                   ?>

            if (Object.keys(ldata_row).length === 0) {
                $(lrow).find('[col_name ="u_group"] input[original]').select2({
                    allowClear: true,
                    query:function(query){
                        var data={results:[]};
                        data.results = lu_group_get();
                        query.callback(data);
                    }
                });
                $(lrow).find('[col_name ="u_group"] input[original]').on('change', function() {
                    $(lrow).find('[col_name ="u_group_id"] div')[0].innerHTML = "";
                    var lval = $(this).select2('val');
                    if (lval !== '') {
                        $(lrow).find('[col_name ="u_group_id"] div')[0].innerHTML = lval;
                    }
                });
            }
        }

        employee_tbl_u_group_method.setting.func_row_transform_comp_on_new_row = function(iopt) {
            var lrow = iopt.tr;
            var lu_group = $(lrow).find('[col_name="u_group"] input[original]').select2('data');
            $(lrow).find('[col_name="u_group"]')[0].innerHTML = '<div>' + lu_group.text + '</div>';
        }

        employee_tbl_u_group_method.setting.func_row_data_assign = function(iopt) {
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            switch (lmethod) {
                case 'add':
                case 'view':
                    if (Object.keys(ldata_row).length > 0) {
                        $(lrow).find('[col_name="u_group_id"]')[0].innerHTML = '<div>' + ldata_row.id + '</div>';
                        $(lrow).find('[col_name="u_group"]')[0].innerHTML = '<div>' + ldata_row.text + '</div>';
                        employee_tbl_u_group_method.components.trash_set(iopt);
                    }
                    break;
            }


        }

    }
</script>