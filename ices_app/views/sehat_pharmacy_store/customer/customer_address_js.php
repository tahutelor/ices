<script>
    var customer_address_methods = {
        load_address: function(iparam) {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;

            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            customer_tbl_address_method.reset();
            customer_tbl_address_method.head_generate();

            $.each(iparam.address, function(lidx, lrow) {
                customer_tbl_address_method.input_row_generate(lrow);
            });
            customer_tbl_address_method.input_row_generate({});

        }
    };

    var customer_address_bind_event = function() {
        var lparent_pane = customer_parent_pane;
        var lprefix_id = customer_component_prefix_id;

        customer_tbl_address_method.setting.func_new_row_validation = function(iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            var laddress = $(lrow).find('[col_name="address"] textarea');
            var lval = laddress.val();
            
            <?php //validation replace space or empty sting ?>
            if (lval.replace(/[ \n\r]/g,'') === '') {
                success = 0;
                $(laddress).css('border-color', APP_COLOR.red);
                $(laddress).val('');
            }

            lresult.success = success;
            return lresult;
        };

        customer_tbl_address_method.setting.func_get_data_table = function() {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var lresult = {address: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();

            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_address tbody')[0];
            $.each($(ltbody).find('tr'), function(lidx, lrow) {
                var laddress = $(lrow).find('[col_name="address"] div').length > 0 ?
                        $(lrow).find('[col_name="address"] div')[0].innerHTML :
                        $(lrow).find('[col_name="address"] textarea').val();
                                
                if (laddress !== "") {
                    lresult.address.push(laddress);
                }

            });
            return lresult;
        };

        customer_tbl_address_method.setting.func_row_bind_event = function(iopt) {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var lrow = iopt.tr;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

<?php // --- Show and Hide phase ---             ?>
            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_address')[0];
            if (lmethod === 'add') {

            }
            else if (lmethod === 'view') {

            }


<?php // --- End Of Show and Hide phase ---             ?>

            if (Object.keys(ldata_row).length === 0) {
            }

        };

        customer_tbl_address_method.setting.func_row_transform_comp_on_new_row = function(iopt) {
            var lrow = iopt.tr;
            var l_address = $(lrow).find('[col_name="address"] textarea').val();
            $(lrow).find('[col_name="address"]')[0].innerHTML = '<div style="white-space:pre-wrap;">' + l_address + '</div>';
        };

        customer_tbl_address_method.setting.func_row_data_assign = function(iopt) {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            switch (lmethod) {
                case 'add':
                case 'view':
                    if (Object.keys(ldata_row).length > 0) {
                        var abc = $(lrow).find('[col_name="address"]')[0].innerHTML = '<div>' + ldata_row.address + '</div>';
                        customer_tbl_address_method.components.trash_set(iopt);
                    }
                    break;
            }


        };

    };
</script>