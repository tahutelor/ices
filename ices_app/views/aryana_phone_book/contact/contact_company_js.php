<script>
    var company_data = {
        company:[]
    }
    
    var contact_company_methods = {
        load_company: function(iparam) {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            contact_tbl_company_method.reset();
            contact_tbl_company_method.head_generate();
            $.each(iparam.company, function(lidx, lrow) {
                contact_tbl_company_method.input_row_generate(lrow);
            });
            contact_tbl_company_method.input_row_generate({});
        },
        company_get:function(ilookup_str){
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lresult = [];
            var lcompany_id_div = $(lparent_pane).find(lprefix_id+'_tbl_company tbody [col_name="company_id"] div');
            
            var lajax_url = '<?php echo $ajax_url . 'company_search/'; ?>';
        
            company_data.company = [];
            var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, {data: ilookup_str}).response;
            $.each(lresponse, function(lidx, lrow) {
                company_data.company.push(lrow);
            });
            
            $.each(company_data.company,function(lidx, lrow){    
                var lexists = false;
                $.each(lcompany_id_div, function(lidx2, lrow2){
                    if(lrow.id === $(lrow2)[0].innerHTML) lexists = true;     
                });
                if(!lexists){
                        lresult.push(lrow);
                }
                
            });
            return lresult;
        }
    }

    var contact_company_bind_event = function() {
        var lparent_pane = contact_parent_pane;
        var lprefix_id = contact_component_prefix_id;
        
        
        contact_tbl_company_method.setting.func_new_row_validation = function(iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            <?php // --- validation select contact category---  ?>
            var lcompany = $(lrow).find('[col_name="company"] input[original]');
            var la = $(lrow).find('[col_name="company"] .select2-container>a');
            var lval = lcompany.select2('val');
            if (lval === '') {
                success = 0;                
                $(la).css('border-color', APP_COLOR.red);
                $(lcompany).on('select2-open',function(){
                    $(la).css('border-color','');
                });
                $(lcompany).on('select2-close',function(){
                    $(la).css('border-color',APP_COLOR.red);
                });
            }

            lresult.success = success;
            return lresult;
        };
        contact_tbl_company_method.setting.func_get_data_table = function() {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lresult = {company_id: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();
            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_company tbody')[0];
            $.each($(ltbody).find('tr'), function(lidx, lrow) {
                var lcompany_id = $(lrow).find('[col_name="company_id"] div')[0].innerHTML;
                if (lcompany_id !== "") {
                    lresult.company_id.push(lcompany_id);
                }

            });
            return lresult;
        };
        contact_tbl_company_method.setting.func_row_bind_event = function(iopt) {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lrow = iopt.tr;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
<?php // --- Show and Hide phase ---                   ?>
            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_company')[0];
            if (lmethod === 'add') {

            }
            else if (lmethod === 'view') {

            }
<?php // --- End Of Show and Hide phase ---                   ?>

            if (Object.keys(ldata_row).length === 0) {
                var company_timeout;
                $(lrow).find('[col_name ="company"] input[original]').select2({
                    allowClear: true,
                    query:function(query){
                        window.clearTimeout(company_timeout);
                        company_timeout = window.setTimeout(function(){
                            var data={results:[]};
                            var llookup_str = query.term.toLowerCase().trim();
                            data.results = contact_company_methods.company_get(llookup_str);
                            query.callback(data);
                        },250);
                        
                    }
                });
                
                $(lrow).find('[col_name ="company"] input[original]').on('change', function() {
                    $(lrow).find('[col_name ="company_id"] div')[0].innerHTML = "";
                    var lval = $(this).select2('val');
                    if (lval !== '') {
                        $(lrow).find('[col_name ="company_id"] div')[0].innerHTML = lval;
                    }
                });
            }
        }

        contact_tbl_company_method.setting.func_row_transform_comp_on_new_row = function(iopt) {
            var lrow = iopt.tr;
            var lcompany = $(lrow).find('[col_name="company"] input[original]').select2('data');
            $(lrow).find('[col_name="company"]')[0].innerHTML = '<div>' + lcompany.text + '</div>';
        }

        contact_tbl_company_method.setting.func_row_data_assign = function(iopt) {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            switch (lmethod) {
                case 'add':
                case 'view':
                    if (Object.keys(ldata_row).length > 0) {
                        $(lrow).find('[col_name="company_id"]')[0].innerHTML = '<div>' + ldata_row.id + '</div>';
                        $(lrow).find('[col_name="company"]')[0].innerHTML = '<div>' + ldata_row.text + '</div>';
                        contact_tbl_company_method.components.trash_set(iopt);
                    }
                    break;
            }


        }

    }
</script>