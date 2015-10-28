<script>
    var product_batch_product_stock_methods = {
        load_product_stock: function (iparam) {
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;

            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            product_batch_tbl_product_stock_method.reset();
            product_batch_tbl_product_stock_method.head_generate();

            $.each(iparam.product_stock, function (lidx, lrow) {
                product_batch_tbl_product_stock_method.input_row_generate(lrow);
            });

            if (lmethod === 'add') {

            }

        }
    };

    var product_batch_product_stock_bind_event = function () {
        var lparent_pane = product_batch_parent_pane;
        var lprefix_id = product_batch_component_prefix_id;

        product_batch_tbl_product_stock_method.setting.func_new_row_validation = function (iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            var lc = iopt.comp;


<?php //validation replace space or empty sting  ?>


            lresult.success = success;
            return lresult;
        };

        product_batch_tbl_product_stock_method.setting.func_get_data_table = function () {
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;
            var lresult = {product_stock: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();

            return lresult;
        };

        product_batch_tbl_product_stock_method.setting.func_row_bind_event = function (iopt) {
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_product_stock')[0];

<?php // --- Show and Hide phase ---              ?>
            if (lmethod === 'add') {

            }
            else if (lmethod === 'view') {

            }

        };

        product_batch_tbl_product_stock_method.setting.func_row_transform_comp_on_new_row = function (iopt) {
            var lrow = iopt.tr;
            var lc = iopt.comp;
        };

        product_batch_tbl_product_stock_method.setting.func_row_data_assign = function (iopt) {
            var lparent_pane = product_batch_parent_pane;
            var lprefix_id = product_batch_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lc = iopt.comp;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            switch (lmethod) {
                case 'add':
                    break;
                case 'view':
                    $(lc.warehouse).find('div')[0].innerHTML = '<div>' + ldata_row.warehouse_text + '</div>';
                    $(lc.qty).find('div')[0].innerHTML = '<div>' + APP_CONVERTER.thousand_separator(ldata_row.qty) + '</div>';
                    $(lrow).find('[col_name="action"]')[0].innerHTML = '';
                    break;
            }

            if (Object.keys(ldata_row).length > 0) {

            }

        };

    };
</script>