<script>
    var contact_keyword_methods = {
        load_keyword: function(iparam) {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;

            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            contact_tbl_keyword_method.reset();
            contact_tbl_keyword_method.head_generate();

            $.each(iparam.keyword, function(lidx, lrow) {
                contact_tbl_keyword_method.input_row_generate(lrow);
            });
            contact_tbl_keyword_method.input_row_generate({});

        }
    };

    var contact_keyword_bind_event = function() {
        var lparent_pane = contact_parent_pane;
        var lprefix_id = contact_component_prefix_id;

        contact_tbl_keyword_method.setting.func_new_row_validation = function(iopt) {
            var lresult = {success: 1, msg: []};
            var success = 1;
            var lrow = iopt.tr;
            var lkeyword = $(lrow).find('[col_name="keyword"] input');
            var lval = lkeyword.val();
            
            <?php //validation replace space or empty sting ?>
            if (lval.replace(/[ \n\r]/g,'') === '') {
                success = 0;
                $(lkeyword).css('border-color', APP_COLOR.red);
                $(lkeyword).val('');
            }

            lresult.success = success;
            return lresult;
        };

        contact_tbl_keyword_method.setting.func_get_data_table = function() {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lresult = {keyword: []};
            var lreference_type = $(lparent_pane).find(lprefix_id + '_type').val();

            var ltbody = $(lparent_pane).find(lprefix_id + '_tbl_keyword tbody')[0];
            $.each($(ltbody).find('tr'), function(lidx, lrow) {
                var lkeyword = $(lrow).find('[col_name="keyword"] div').length > 0 ?
                        $(lrow).find('[col_name="keyword"] div')[0].innerHTML :
                        $(lrow).find('[col_name="keyword"] input').val();
                                
                if (lkeyword !== "") {
                    lresult.keyword.push(lkeyword);
                }

            });
            return lresult;
        };

        contact_tbl_keyword_method.setting.func_row_bind_event = function(iopt) {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var lrow = iopt.tr;
            var ltbody = iopt.tbody;
            var ldata_row = iopt.data_row;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

<?php // --- Show and Hide phase ---             ?>
            var ltable = $(lparent_pane).find(lprefix_id + '_tbl_keyword')[0];
            if (lmethod === 'add') {

            }
            else if (lmethod === 'view') {

            }


<?php // --- End Of Show and Hide phase ---             ?>

            if (Object.keys(ldata_row).length === 0) {
            }

        };

        contact_tbl_keyword_method.setting.func_row_transform_comp_on_new_row = function(iopt) {
            var lrow = iopt.tr;
            var l_keyword = $(lrow).find('[col_name="keyword"] input').val();
            $(lrow).find('[col_name="keyword"]')[0].innerHTML = '<div style="white-space:pre-wrap;">' + l_keyword + '</div>';
        };

        contact_tbl_keyword_method.setting.func_row_data_assign = function(iopt) {
            var lparent_pane = contact_parent_pane;
            var lprefix_id = contact_component_prefix_id;
            var ldata_row = iopt.data_row;
            var lrow = iopt.tr;
            var lmethod = $(lparent_pane).find(lprefix_id + '_method').val();

            switch (lmethod) {
                case 'add':
                case 'view':
                    if (Object.keys(ldata_row).length > 0) {
                        var abc = $(lrow).find('[col_name="keyword"]')[0].innerHTML = '<div>' + ldata_row.keyword + '</div>';
                        contact_tbl_keyword_method.components.trash_set(iopt);
                    }
                    break;
            }


        };

    };
</script>