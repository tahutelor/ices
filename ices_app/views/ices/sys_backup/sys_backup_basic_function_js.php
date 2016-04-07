<script>

    var sys_backup_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var sys_backup_ajax_url = null;
    var sys_backup_index_url = null;
    var sys_backup_view_url = null;
    var sys_backup_window_scroll = null;
    var sys_backup_data_support_url = null;
    var sys_backup_common_ajax_listener = null;
    var sys_backup_component_prefix_id = '';
    
    var sys_backup_insert_dummy = true;

    var sys_backup_init = function(){
        var parent_pane = sys_backup_parent_pane;
        sys_backup_ajax_url = '<?php echo $ajax_url ?>';
        sys_backup_index_url = '<?php echo $index_url ?>';
        sys_backup_view_url = '<?php echo $view_url ?>';
        sys_backup_window_scroll = '<?php echo $window_scroll; ?>';
        sys_backup_data_support_url = '<?php echo $data_support_url; ?>';
        sys_backup_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        sys_backup_component_prefix_id = '#<?php echo $component_prefix_id; ?>';
        sys_backup_rma_extra_param_get = function(){
            //input select detail use this function to get extra parameter for further query
            
        };
        
        
    }
    
    var sys_backup_methods = {
        hide_all:function(){
            var lparent_pane = sys_backup_parent_pane;
            var lc_arr = $(lparent_pane).find('.hide_all').hide();
        },
        show_hide:function(){
            var lparent_pane = sys_backup_parent_pane;
            var lmethod = $(lparent_pane).find('#sys_backup_method').val();
            var lprefix_id = sys_backup_component_prefix_id;
            sys_backup_methods.hide_all();
            
            switch(lmethod){
                case 'add':   
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_module').closest('.form-group').show();
                    break;
            }
        },
        disable_all:function(){
            var lparent_pane = sys_backup_parent_pane;
            APP_COMPONENT.disable_all(lparent_pane);
        },
        enable_disable:function(){
            var lparent_pane = sys_backup_parent_pane;
            var lmethod = $(lparent_pane).find('#sys_backup_method').val();
            var lprefix_id = sys_backup_component_prefix_id;
            sys_backup_methods.disable_all();
            switch(lmethod){
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_module').select2('enable');
                    break;
            }
        },
        reset_all:function(){
            var lparent_pane = sys_backup_parent_pane;
            var lprefix_id = sys_backup_component_prefix_id;
        },
        submit:function(){
            var lparent_pane = sys_backup_parent_pane;
            var lprefix_id = sys_backup_component_prefix_id;
            var lajax_url = sys_backup_index_url;
            var lmethod = $(lparent_pane).find('#sys_backup_method').val();
            var lajax_method = $(lparent_pane).find(lprefix_id+'_module').select2('val');
            var json_data = {
                ajax_post:true,
                message_session:true,
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    
                    lajax_url +=lajax_method+'/';
                    break;
            }
            
            switch(lajax_method){
                case 'backup_db':
                case 'backup_php':
                    json_data.sys_backup = {
                        method:lajax_method,
                        phase:'initialize',
                        filename:null,
                        
                    };
                    
                    switch(lajax_method){
                        case 'backup_db':
                            json_data.sys_backup.app_name = $(lparent_pane).find(lprefix_id+'_app_name').select2('val');
                            break;
                    }
                    
                    break;
            }
            var lresult = {
                json_data:{},
                ajax_url: lajax_url+APP_CONVERTER.encodeURIComponent(JSON.stringify(json_data)),
            };
            
            return lresult;
            
        },
        after_submit: function(lparam){
            var lparent_pane = sys_backup_parent_pane;
            var lprefix_id = sys_backup_component_prefix_id;
            var lajax_url = sys_backup_index_url;
            var lmethod = $(lparent_pane).find('#sys_backup_method').val();
            var lajax_method = $(lparent_pane).find(lprefix_id+'_module').select2('val');
            var ljson_data = {
                ajax_post:true,
                message_session:true,
            };
            
            switch(lmethod){
                case 'add':
                case 'view':
                    
                    lajax_url +=lajax_method+'/';
                    break;
            }
            
            switch(lajax_method){
                case 'backup_db':
                case 'backup_php':
                    var lresult = lparam.result;
                    var lresponse = lresult.response;
                    ljson_data.sys_backup = {
                        method: lajax_method,
                        phase: 'send_file',
                        filename: lresponse.filename
                    }
                    var lajax_url_download = lajax_url+APP_CONVERTER.encodeURIComponent(JSON.stringify(ljson_data))
                    $.fileDownload(lajax_url_download)
                    .done(function () {
                        APP_MESSAGE.set('success','Download File success');
                        var ljson_data = {
                            ajax_post:true,
                            message_session:true,
                            sys_backup:{
                                method: lajax_method,
                                phase: 'finalize',
                                filename: lresponse.filename
                            }
                        }
                        var lajax_url_finalize = lajax_url+APP_CONVERTER.encodeURIComponent(JSON.stringify(ljson_data));
                        var lresult = APP_DATA_TRANSFER.ajaxPOST(lajax_url_finalize,{});
                        APP_MESSAGE.set((lresult.success === 1?'success':'error'),lresult.msg);
                    })
                    .fail(function () { APP_MESSAGE.set('error','Download failed');});
                
                    break;
            }
            
            
            /*
            if(lresult.success === 1){
                var ljson_data = {};

                var ldata_str = JSON.stringify(ljson_data, null, null);
                ldata_str = btoa(ldata_str);
                var response = $.ajax({
                    type: "POST",
                    url: lajax_url,
                    data: ldata_str,
                    dataType: 'json',
                    contentType: 'application/json;charset=utf-8',
                    global: false,
                    async: false,
                    cache: false,
                    success: function (data) {
                    }
                }).responseText;

                try {
                    response = JSON.parse(response);
                }
                catch (err) {
                    try {
                        response = response.toString();
                    }
                    catch (err2) {
                        response = '';
                    }

                }
            }
            */
        }
        
    };
    
    var sys_backup_bind_event = function(){
        var lparent_pane = sys_backup_parent_pane;
        var lprefix_id = sys_backup_component_prefix_id;

        $(lparent_pane).find(lprefix_id+'_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id+'_submit'),{
            parent_pane: lparent_pane,
            module_method: sys_backup_methods,
            view_url: '',
            prefix_id:lprefix_id,
            window_scroll:sys_backup_window_scroll,
        });
        
        $(lparent_pane).find(lprefix_id+'_module').on('change',function(){
            var ldata = $(this).select2('data');
            $(lparent_pane).find(lprefix_id+'_app_name').closest('.form-group').hide();
            $(lparent_pane).find(lprefix_id+'_app_name').select2('disable');
            var lval = ldata.id;
            
            switch(lval){
                case 'backup_db':
                    $(lparent_pane).find(lprefix_id+'_app_name').closest('.form-group').show();
                    $(lparent_pane).find(lprefix_id+'_app_name').select2('enable');
                    break;
            }
            
        });
        
    }
    
    var sys_backup_components_prepare = function(){
        

        var sys_backup_data_set = function(){
            var lparent_pane = sys_backup_parent_pane;
            var lprefix_id = sys_backup_component_prefix_id;
            var lmethod = $(lparent_pane).find('#sys_backup_method').val();
            
            switch(lmethod){
                case 'add':
                    sys_backup_methods.reset_all();
                    
                    break;
                case 'view':
                    
                    break;
            }
        }
        
        
        sys_backup_methods.enable_disable();
        sys_backup_methods.show_hide();
        sys_backup_data_set();
    }
    
    var sys_backup_after_submit = function(){
        //function that will be executed after submit 
    }
    
    var sys_backup_reference_extra_param_get = function(){
        var lresult = {};
        var lparent_pane = sys_backup_parent_pane;
        var lprefix_id = sys_backup_component_prefix_id;
        return lresult;
    }
    
    
    
    
    
</script>