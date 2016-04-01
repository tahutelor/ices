<script>

    var u_profile_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var u_profile_ajax_url = null;
    var u_profile_index_url = null;
    var u_profile_view_url = null;
    var u_profile_window_scroll = null;
    var u_profile_data_support_url = null;
    var u_profile_common_ajax_listener = null;
    var u_profile_component_prefix_id = '';
    
    var u_profile_insert_dummy = true;

    var u_profile_init = function(){
        var parent_pane = u_profile_parent_pane;
        u_profile_ajax_url = '<?php echo $ajax_url ?>';
        u_profile_index_url = '<?php echo $index_url ?>';
        u_profile_view_url = '<?php echo $view_url ?>';
        u_profile_window_scroll = '<?php echo $window_scroll; ?>';
        u_profile_data_support_url = '<?php echo $data_support_url; ?>';
        u_profile_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        u_profile_component_prefix_id = '#<?php echo $component_prefix_id; ?>';
        u_profile_rma_extra_param_get = function(){
            //input select detail use this function to get extra parameter for further query
            
        };
        
        
    }
    
    var u_profile_methods = {
        hide_all:function(){
            var lparent_pane = u_profile_parent_pane;
            var lc_arr = $(lparent_pane).find('.hide_all').hide();
        },
        show_hide:function(){
            var lparent_pane = u_profile_parent_pane;
            var lmethod = $(lparent_pane).find('#u_profile_method').val();
            var lprefix_id = u_profile_component_prefix_id;
            u_profile_methods.hide_all();
            
            switch(lmethod){
                case 'add':   
                case 'view':
                    
                    $(lparent_pane).find(lprefix_id+'_username').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_firstname').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_lastname').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_password').closest('div [class*="form-group"]').show();
                    break;
            }
        },
        disable_all:function(){
            var lparent_pane = u_profile_parent_pane;
            APP_COMPONENT.disable_all(lparent_pane);
        },
        enable_disable:function(){
            var lparent_pane = u_profile_parent_pane;
            var lmethod = $(lparent_pane).find('#u_profile_method').val();
            var lprefix_id = u_profile_component_prefix_id;
            u_profile_methods.disable_all();
            switch(lmethod){
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_firstname').prop('disabled',false);
                    $(lparent_pane).find(lprefix_id+'_lastname').prop('disabled',false);
                    $(lparent_pane).find(lprefix_id+'_password').prop('disabled',false);
                    break;
            }
        },
        reset_all:function(){
            var lparent_pane = u_profile_parent_pane;
            var lprefix_id = u_profile_component_prefix_id;
        },
        after_submit: function(){
            
        },
        submit:function(){
            var lparent_pane = u_profile_parent_pane;
            var lprefix_id = u_profile_component_prefix_id;
            var lajax_url = u_profile_index_url;
            var lmethod = $(lparent_pane).find('#u_profile_method').val();
            var json_data = {
                ajax_post:true,
                message_session:true,
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    json_data.u_profile = {
                        firstname:$(lparent_pane).find(lprefix_id+'_firstname').val(),
                        lastname:$(lparent_pane).find(lprefix_id+'_lastname').val(),
                        password:$(lparent_pane).find(lprefix_id+'_password').val(),
                    };
                    
                    
                    
                    break;
            }
            var u_profile_id = $(lparent_pane).find('#u_profile_id').val();
            lajax_url +='u_profile_update/'+u_profile_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
        },
        after_submit:function(){
            window.location.href = u_profile_view_url;
        }
    };
    
    var u_profile_bind_event = function(){
        var lparent_pane = u_profile_parent_pane;
        var lprefix_id = u_profile_component_prefix_id;
        
        $(lparent_pane).find(lprefix_id+'_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id+'_btn_submit'),{
            parent_pane:lparent_pane,
            module_method:u_profile_methods,
            view_url: '',
            prefix_id:lprefix_id,
            window_scroll:u_profile_window_scroll,
        });
        
    }
    
    var u_profile_components_prepare = function(){
        

        var u_profile_data_set = function(){
            var lparent_pane = u_profile_parent_pane;
            var lprefix_id = u_profile_component_prefix_id;
            var lmethod = $(lparent_pane).find('#u_profile_method').val();
            
            switch(lmethod){
                case 'add':
                    u_profile_methods.reset_all();
                case 'view':
                    
                    break;
            }
        }
        
        
        u_profile_methods.enable_disable();
        u_profile_methods.show_hide();
        u_profile_data_set();
    }
    
    var u_profile_after_submit = function(){
        //function that will be executed after submit 
    }
    
    var u_profile_reference_extra_param_get = function(){
        var lresult = {};
        var lparent_pane = u_profile_parent_pane;
        var lprefix_id = u_profile_component_prefix_id;
        var lmodule_name = $(lparent_pane).find(lprefix_id+'_module_name').select2('val');
        var lmodule_action = $(lparent_pane).find(lprefix_id+'_module_action').select2('val');
        lresult = {module_name:lmodule_name, module_action:lmodule_action};
        return lresult;
    }
    
    
    
    
    
</script>