<script>

    var rpt_sales_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var rpt_sales_ajax_url = null;
    var rpt_sales_form_render_url = null;
    var rpt_sales_index_url = null;
    var rpt_sales_view_url = null;
    var rpt_sales_window_scroll = null;
    var rpt_sales_data_support_url = null;
    var rpt_sales_common_ajax_listener = null;
    var rpt_sales_component_prefix_id = '';
    
    
    var rpt_sales_insert_dummy = true;

    var rpt_sales_init = function(){
        var lparent_pane = rpt_sales_parent_pane;
        rpt_sales_ajax_url = '<?php echo $ajax_url ?>';
        rpt_sales_index_url = '<?php echo $index_url ?>';
        rpt_sales_form_render_url = '<?php echo $form_render_url ?>';
        rpt_sales_view_url = '<?php echo $view_url ?>';
        rpt_sales_window_scroll = '<?php echo $window_scroll; ?>';
        rpt_sales_data_support_url = '<?php echo $data_support_url; ?>';
        rpt_sales_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        rpt_sales_component_prefix_id = '#<?php echo $component_prefix_id; ?>';
        
    }
    
    var rpt_sales_methods = {
        hide_all:function(){
            var lparent_pane = rpt_sales_parent_pane;
            var lc_arr = $(lparent_pane).find('.hide_all').hide();
        },
        show_hide:function(){
            var lparent_pane = rpt_sales_parent_pane;
            var lmethod = $(lparent_pane).find('#rpt_sales_method').val();
            var lprefix_id = rpt_sales_component_prefix_id;
            rpt_sales_methods.hide_all();
            
            switch(lmethod){
                case 'add':   
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_module_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_module_condition').closest('div [class*="form-group"]').show();
                    break;
            }
        },
        disable_all:function(){
            var lparent_pane = rpt_sales_parent_pane;
            APP_COMPONENT.disable_all(lparent_pane);
        },
        enable_disable:function(){
            var lparent_pane = rpt_sales_parent_pane;
            var lmethod = $(lparent_pane).find('#rpt_sales_method').val();
            var lprefix_id = rpt_sales_component_prefix_id;
            rpt_sales_methods.disable_all();
            switch(lmethod){
                case 'add':
                    break;
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_module_name').select2('enable');
                    $(lparent_pane).find(lprefix_id+'_module_condition').select2('enable');
                   
                    break;
            }
        },
        reset_all:function(){
            var lparent_pane = rpt_sales_parent_pane;
            var lprefix_id = rpt_sales_component_prefix_id;
        },
    };
    
    var rpt_sales_bind_event = function(){
        var lparent_pane = rpt_sales_parent_pane;
        var lprefix_id = rpt_sales_component_prefix_id;
        
        $(lparent_pane).find(lprefix_id+'_module_name').on('change',function(){
            var lparent_pane = rpt_sales_parent_pane;
            var lprefix_id = rpt_sales_component_prefix_id;
            
            $(lparent_pane).find(lprefix_id+'_report_div').empty();
            $(lparent_pane).find(lprefix_id+'_report_preview_div').empty();
            $(lparent_pane).find(lprefix_id+'_btn_preview').hide();
            $(lparent_pane).find(lprefix_id+'_save_excel').closest('.btn-group').hide();
            if($(this).select2('val')!== ''){
                var lmodule_name = $(lparent_pane).find(lprefix_id+'_module_name').select2('val');
                var lajax_url = rpt_sales_form_render_url+lmodule_name+'/';
                var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, {}).response;
                APP_COMPONENT.attach($(lparent_pane).find(lprefix_id+'_report_div')[0],lresponse);
                $(lparent_pane).find(lprefix_id+'_btn_preview').show();
                $(lparent_pane).find(lprefix_id+'_save_excel').closest('.btn-group').show();
            }
        });
        
       
        $(lparent_pane).find(lprefix_id+'_save_excel').off();
        $(lparent_pane).find(lprefix_id+'_save_excel').on('click',function(){
            var lmodule_name = $(lparent_pane).find(lprefix_id+'_module_name').select2('val');
            if(lmodule_name !== ''){
                var ljson_data = {};
                ljson_data = eval('rpt_sales_'+lmodule_name+'_param_get()');
                window.open(rpt_sales_index_url+'download_excel/'+lmodule_name+'/'+encodeURIComponent(JSON.stringify(ljson_data)));
            }
        });
        
        $(lparent_pane).find(lprefix_id+'_btn_preview').off();
        $(lparent_pane).find(lprefix_id+'_btn_preview').on('click',function(e){
            var lmodule_name = $(lparent_pane).find(lprefix_id+'_module_name').select2('val');
            if(lmodule_name !== ''){
                var ljson_data = {};
                ljson_data = eval('rpt_sales_'+lmodule_name+'_param_get()');
                var lmodule_name = $(lparent_pane).find(lprefix_id+'_module_name').select2('val');
                var lajax_url = rpt_sales_index_url+'rpt_preview/'+lmodule_name+'/';
                var lresponse = APP_DATA_TRANSFER.ajaxPOST(lajax_url, ljson_data).response;
                APP_COMPONENT.attach($(lparent_pane).find(lprefix_id+'_report_preview_div')[0],lresponse);
            }
            e.preventDefault();
        });
    }
    
    var rpt_sales_components_prepare = function(){
        

        var rpt_sales_data_set = function(){
            var lparent_pane = rpt_sales_parent_pane;
            var lprefix_id = rpt_sales_component_prefix_id;
            var lmethod = $(lparent_pane).find('#rpt_sales_method').val();
            
            switch(lmethod){
                case 'add':
                case 'view':
                    
                    break;
            }
        }
        
        
        rpt_sales_methods.enable_disable();
        rpt_sales_methods.show_hide();
        rpt_sales_data_set();
    }
    
    var rpt_sales_after_submit = function(){
        //function that will be executed after submit 
    }
    
    var rpt_sales_reference_extra_param_get = function(){
        return {};
    }
    
    
    
    
    
</script>