<script>
    var customer_phone_number_methods = {
        load_phone_number: function (iparam) {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;

            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            customer_tbl_phone_number_method.reset();
            customer_tbl_phone_number_method.head_generate();

            $.each(iparam.phone_number, function (lidx, lrow) {
                customer_tbl_phone_number_method.input_row_generate(lrow);
            });
            customer_tbl_phone_number_method.input_row_generate({});

        }
    }

    var customer_phone_number_bind_event = function () {
        var lparent_pane = customer_parent_pane;
        var lprefix_id = customer_component_prefix_id;
        var lajax_url = '<?php echo $data_support_url . 'phone_number_type_get'; ?>';
        var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, {}).response;
        var lphone_number_type = [];
        $.each(lresponse, function (lidx, lrow) {
            lphone_number_type.push(lrow);
        });
        customer_tbl_phone_number_method.setting.func_new_row_validation = function (iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            var lphone_number = $(lrow).find('[col_name="phone_number"] input');
            var lval = lphone_number.val();
<?php //validation replace space or empty sting         ?>
            if (lval.replace(/[ \n\r]/g, '') === '') {
                success = 0;
                $(lphone_number).css('border-color', APP_COLOR.red);
                $(lphone_number).val('');

                var lphone_number_type = $(lrow).find('[col_name="phone_number_type"] input[original]');
                APP_COMPONENT.input_select.mark($(lphone_number_type),{mark_type:'invalid'});
            }

            lresult.success = success;
            return lresult;
        };

        customer_tbl_phone_number_method.setting.func_get_data_table = function () {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
//            var lresult = [];
            var lresult = {phone_number: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();

            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_phone_number tbody')[0];
            $.each($(ltbody).find('tr'), function (lidx, lrow) {
                var lphone_number = $(lrow).find('[col_name="phone_number"] div').length > 0 ?
                        $(lrow).find('[col_name="phone_number"] div')[0].innerHTML :
                        $(lrow).find('[col_name="phone_number"] input').val();

                var lphone_number_type_id = $(lrow).find('[col_name="phone_number_type_id"] div')[0].innerHTML;

                if (lphone_number !== "") {
                    lresult.phone_number.push({phone_number: lphone_number, phone_number_type_id: lphone_number_type_id});
                }

            });
            return lresult;
        };

        customer_tbl_phone_number_method.setting.func_row_bind_event = function (iopt) {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var lrow = iopt.tr;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

<?php // --- Show and Hide phase ---                 ?>
            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_phone_number')[0];
            if (lmethod === 'add') {

            }
            else if (lmethod === 'view') {

            }


<?php // --- End Of Show and Hide phase ---                 ?>

            if (Object.keys(ldata_row).length === 0) {
                $(lrow).find('[col_name ="phone_number"] input').inputmask("+999999999999999", {"placeholder": ""});
                $(lrow).find('[col_name ="phone_number_type"] input[original]').select2({
                    allowClear: true,
                    query: function (query) {
                        var data = {results: []};
                        data.results = lphone_number_type;
                        query.callback(data);
                    }
                });
                $(lrow).find('[col_name ="phone_number_type"] input[original]').on('change', function () {
                    $(lrow).find('[col_name ="phone_number_type_id"] div')[0].innerHTML = "";
                    var lval = $(this).select2('val');
                    if (lval !== '') {
                        $(lrow).find('[col_name ="phone_number_type_id"] div')[0].innerHTML = lval;
                    }
                });
            }

        }

        customer_tbl_phone_number_method.setting.func_row_transform_comp_on_new_row = function (iopt) {
            var lrow = iopt.tr;
            var l_phone_number = $(lrow).find('[col_name="phone_number"] input').val();
            $(lrow).find('[col_name="phone_number"]')[0].innerHTML = '<div>' + l_phone_number + '</div>';

            var lphone_number_type = $(lrow).find('[col_name="phone_number_type"] input[original]').select2('data');
            $(lrow).find('[col_name="phone_number_type"]')[0].innerHTML = '<div>' + lphone_number_type.text + '</div>';
        }

        customer_tbl_phone_number_method.setting.func_row_data_assign = function (iopt) {
            var lparent_pane = customer_parent_pane;
            var lprefix_id = customer_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            switch (lmethod) {
                case 'add':
                case 'view':
                    if (Object.keys(ldata_row).length > 0) {
                        $(lrow).find('[col_name="phone_number"]')[0].innerHTML = '<div>+' + ldata_row.phone_number + '</div>';
                        $(lrow).find('[col_name="phone_number_type_id"]')[0].innerHTML = '<div>' + ldata_row.phone_number_type_id + '</div>';
                        $(lrow).find('[col_name="phone_number_type"]')[0].innerHTML = '<div>' + ldata_row.phone_number_type_name + '</div>';
                        customer_tbl_phone_number_method.components.trash_set(iopt);
                    }
                    break;

            }


        }

    }
</script>