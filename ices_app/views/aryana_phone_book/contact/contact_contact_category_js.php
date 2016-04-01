<script>
    var contact_category_data = {
        contact_category:[]
    }
    
    var contact_contact_category_methods = {
        load_contact_category: function(iparam) {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            contact_tbl_contact_category_method.reset();
            contact_tbl_contact_category_method.head_generate();
            $.each(iparam.contact_category, function(lidx, lrow) {
                contact_tbl_contact_category_method.input_row_generate(lrow);
            });
            contact_tbl_contact_category_method.input_row_generate({});
        },
        contact_category_get:function(ilookup_str){
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lresult = [];
            var lcontact_category_id_div = $(lparent_pane).find(lprefix_id+'_tbl_contact_category tbody [col_name="contact_category_id"] div');
            
            $.each(contact_category_data.contact_category,function(lidx, lrow){    
                var lexists = false;
                $.each(lcontact_category_id_div, function(lidx2, lrow2){
                    if(lrow.id === $(lrow2)[0].innerHTML) lexists = true;     
                });
                if(!lexists){
                    if(lrow.text.toLowerCase().trim().indexOf(ilookup_str)!== -1){
                        lresult.push(lrow);
                    }
                }
            });
            return lresult;
        }
    }

    var contact_contact_category_bind_event = function() {
        var lparent_pane = contact_parent_pane;
        var lprefix_id = contact_component_prefix_id;
        var lajax_url = '<?php echo $data_support_url . 'contact_category_get'; ?>';
        
        contact_category_data.contact_category = [];
        var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, {}).response;
        $.each(lresponse, function(lidx, lrow) {
            contact_category_data.contact_category.push(lrow);
        });
        
        contact_tbl_contact_category_method.setting.func_new_row_validation = function(iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            <?php // --- validation select contact category---  ?>
            var lcontact_category = $(lrow).find('[col_name="contact_category"] input[original]');
            var la = $(lrow).find('[col_name="contact_category"] .select2-container>a');
            var lval = lcontact_category.select2('val');
            if (lval === '') {
                success = 0;                
                $(la).css('border-color', APP_COLOR.red);
                $(lcontact_category).on('select2-open',function(){
                    $(la).css('border-color','');
                });
                $(lcontact_category).on('select2-close',function(){
                    $(la).css('border-color',APP_COLOR.red);
                });
            }

            lresult.success = success;
            return lresult;
        };
        contact_tbl_contact_category_method.setting.func_get_data_table = function() {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lresult = {contact_category_id: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();
            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_contact_category tbody')[0];
            $.each($(ltbody).find('tr'), function(lidx, lrow) {
                var lcontact_category_id = $(lrow).find('[col_name="contact_category_id"] div')[0].innerHTML;
                if (lcontact_category_id !== "") {
                    lresult.contact_category_id.push(lcontact_category_id);
                }

            });
            return lresult;
        };
        contact_tbl_contact_category_method.setting.func_row_bind_event = function(iopt) {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lrow = iopt.tr;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
<?php // --- Show and Hide phase ---                   ?>
            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_contact_category')[0];
            if (lmethod === 'add') {

            }
            else if (lmethod === 'view') {

            }
<?php // --- End Of Show and Hide phase ---                   ?>

            if (Object.keys(ldata_row).length === 0) {

                $(lrow).find('[col_name ="contact_category"] input[original]').select2({
                    allowClear: true,
                    query:function(query){
                        var data={results:[]};
                        var llookup_str = query.term.toLowerCase().trim();
                        data.results = contact_contact_category_methods.contact_category_get(llookup_str);
                        query.callback(data);
                    }
                });
                
                $(lrow).find('[col_name ="contact_category"] input[original]').on('change', function() {
                    $(lrow).find('[col_name ="contact_category_id"] div')[0].innerHTML = "";
                    var lval = $(this).select2('val');
                    if (lval !== '') {
                        $(lrow).find('[col_name ="contact_category_id"] div')[0].innerHTML = lval;
                    }
                });
            }
        }

        contact_tbl_contact_category_method.setting.func_row_transform_comp_on_new_row = function(iopt) {
            var lrow = iopt.tr;
            var lcontact_category = $(lrow).find('[col_name="contact_category"] input[original]').select2('data');
            $(lrow).find('[col_name="contact_category"]')[0].innerHTML = '<div>' + lcontact_category.text + '</div>';
        }

        contact_tbl_contact_category_method.setting.func_row_data_assign = function(iopt) {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();
            switch (lmethod) {
                case 'add':
                case 'view':
                    if (Object.keys(ldata_row).length > 0) {
                        $(lrow).find('[col_name="contact_category_id"]')[0].innerHTML = '<div>' + ldata_row.id + '</div>';
                        $(lrow).find('[col_name="contact_category"]')[0].innerHTML = '<div>' + ldata_row.text + '</div>';
                        contact_tbl_contact_category_method.components.trash_set(iopt);
                    }
                    break;
            }


        }

    }
</script>