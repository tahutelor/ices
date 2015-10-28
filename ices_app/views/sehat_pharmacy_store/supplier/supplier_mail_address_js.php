<script>
    var supplier_mail_address_methods = {
        load_mail_address: function (iparam) {
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;

            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            supplier_tbl_mail_address_method.reset();
            supplier_tbl_mail_address_method.head_generate();

            $.each(iparam.mail_address, function (lidx, lrow) {
                supplier_tbl_mail_address_method.input_row_generate(lrow);
            });
            supplier_tbl_mail_address_method.input_row_generate({});

        }
    };

    var supplier_mail_address_bind_event = function () {
        var lparent_pane = supplier_parent_pane;
        var lprefix_id = supplier_component_prefix_id;

        supplier_tbl_mail_address_method.setting.func_new_row_validation = function (iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            var lmail_address = $(lrow).find('[col_name="mail_address"] input');
            var lval = lmail_address.val();
            
            <?php //validation replace space or empty string   ?>
            if (lval.replace(/[ \n\r]/g, '') === '' || !APP_VALIDATOR.mail_address(lval)) {
                success = 0;
                $(lmail_address).css('border-color', APP_COLOR.red);
                $(lmail_address).val('');
            }


            lresult.success = success;
            return lresult;
        };

        supplier_tbl_mail_address_method.setting.func_get_data_table = function () {
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;
//            var lresult = [];
            var lresult = {mail_address: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();

            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_mail_address tbody')[0];
            $.each($(ltbody).find('tr'), function (lidx, lrow) {
                var lmail_address = $(lrow).find('[col_name="mail_address"] div').length > 0 ?
                        $(lrow).find('[col_name="mail_address"] div')[0].innerHTML :
                        $(lrow).find('[col_name="mail_address"] input').val();

                if (lmail_address !== "") {
                    lresult.mail_address.push(lmail_address);
                }
            });
            return lresult;
        };

        supplier_tbl_mail_address_method.setting.func_row_bind_event = function (iopt) {
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;
            var lrow = iopt.tr;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

<?php // --- Show and Hide phase ---                  ?>
            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_mail_address')[0];
            if (lmethod === 'add') {

            }
            else if (lmethod === 'view') {

            }


<?php // --- End Of Show and Hide phase ---                  ?>

            if (Object.keys(ldata_row).length === 0) {

            }

        };

        supplier_tbl_mail_address_method.setting.func_row_transform_comp_on_new_row = function (iopt) {
            var lrow = iopt.tr;
            var lmail_address = $(lrow).find('[col_name="mail_address"] input').val();
            $(lrow).find('[col_name="mail_address"]')[0].innerHTML = '<div>' + lmail_address + '</div>';
        };

        supplier_tbl_mail_address_method.setting.func_row_data_assign = function (iopt) {
            var lparent_pane = supplier_parent_pane;
            var lprefix_id = supplier_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            switch (lmethod) {
                case 'add':
                case 'view':
                    if (Object.keys(ldata_row).length > 0) {
                        $(lrow).find('[col_name="mail_address"]')[0].innerHTML = '<div>' + ldata_row.mail_address + '</div>';
                        supplier_tbl_mail_address_method.components.trash_set(iopt);
                    }
                    break;

            }


        };

    };
</script>