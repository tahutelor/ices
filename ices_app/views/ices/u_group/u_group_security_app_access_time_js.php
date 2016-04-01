<script>
    var security_app_access_time_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var security_app_access_time_ajax_url = null;
    var security_app_access_time_index_url = null;
    var security_app_access_time_view_url = null;
    var security_app_access_time_window_scroll = null;
    var security_app_access_time_data_support_url = null;
    var security_app_access_time_common_ajax_listener = null;
    var security_app_access_time_component_prefix_id = '';
    
    
    var security_app_access_time_init = function(){
        var parent_pane = security_app_access_time_parent_pane;

        security_app_access_time_ajax_url = '<?php echo $ajax_url ?>';
        security_app_access_time_index_url = '<?php echo $index_url ?>';
        security_app_access_time_view_url = '<?php echo $view_url ?>';
        security_app_access_time_window_scroll = '<?php echo $window_scroll; ?>';
        security_app_access_time_data_support_url = '<?php echo $data_support_url; ?>';
        security_app_access_time_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        security_app_access_time_component_prefix_id = '#<?php echo $component_prefix_id; ?>';
        
    }

    var security_app_access_time_after_submit = function(){

    }
    
    var security_app_access_time_data ={
        
    }
    
    var security_app_access_time_methods = {
        
        hide_all:function(){
            var lparent_pane = security_app_access_time_parent_pane;
            var lprefix_id = security_app_access_time_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all:function(){
            var lparent_pane = security_app_access_time_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        
        show_hide: function(){
            var lparent_pane = security_app_access_time_parent_pane;
            var lprefix_id = security_app_access_time_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();            
            
            security_app_access_time_methods.hide_all();
            
            switch(lmethod){
                case 'add':
                case 'view':
                    break;
            }
            
            switch(lmethod){
                case 'add':
                    
                    break;
                case 'view':
                    break;
            }
        },        
        enable_disable: function(){
            var lparent_pane = security_app_access_time_parent_pane;
            var lprefix_id = security_app_access_time_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();  
            security_app_access_time_methods.disable_all();
            
            switch(lmethod){
                case "add":
                case 'view':
                    break;
            }
        },
        reset_all:function(){
            var lparent_pane = security_app_access_time_parent_pane;
            var lprefix_id = security_app_access_time_component_prefix_id;
            
            
        },
        submit:function(){
            var lparent_pane = security_app_access_time_parent_pane;
            var lprefix_id = security_app_access_time_component_prefix_id;
            var ajax_url = security_app_access_time_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id+"_method").val();
            var security_app_access_time_id = $(lparent_pane).find(lprefix_id+"_id").val();        
            var json_data = {
                ajax_post:true,
                dummy:{},
                security_app_access_time:[],
                u_group:{},
                message_session:true
            };
            
            json_data.u_group.id = $('#u_group_id').val();
            json_data.security_app_access_time = [];
            var lsecurity_app_access_time_inpt = $(lparent_pane).find('.panel input[type="checkbox"][id*="security_app_access_time"]');
            $.each($(lsecurity_app_access_time_inpt),function(lidx, lrow){
                if($(lrow).is(':checked')){
                    json_data.security_app_access_time.push({id:$(lrow).attr('id').replace(/[security_app_access_time]/g,'')});
                }
            });
            
            var lajax_method='security_app_access_time_save';
            
            ajax_url +=lajax_method+'/';
            
            var lresult = {
                json_data:json_data,
                ajax_url:ajax_url,
            };
            return lresult;
        },
    }

    var security_app_access_time_bind_event = function(){
        var lparent_pane = security_app_access_time_parent_pane;
        var lprefix_id = security_app_access_time_component_prefix_id;
        
        $(lparent_pane).find(lprefix_id+'_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id+'_btn_submit'),{
            parent_pane: lparent_pane,
            module_method: security_app_access_time_methods,
            view_url: security_app_access_time_view_url,
            prefix_id:lprefix_id,
            window_scroll:security_app_access_time_window_scroll,
        });
        
        $("#security_app_access_time_check_all").on('ifChecked',function(){
            var checkboxes = $("[type=checkbox]") ;
            checkboxes.each(function(key,val){
                if(val.id.indexOf('security_app_access_time')!= -1){
                     $('#'+val.id).iCheck('check');
                }
            });        
        });

        $("#security_app_access_time_check_all").on('ifUnchecked',function(){

           var checkboxes = $("[type=checkbox]") ;
           checkboxes.each(function(key,val){
               if(val.id.indexOf('security_app_access_time')!= -1){
                    $('#'+val.id).iCheck('uncheck');
               }
           });        
        });
        
    }
    
    var security_app_access_time_components_prepare= function(){
        var lparent_pane = security_app_access_time_parent_pane;
        var lprefix_id = security_app_access_time_component_prefix_id;
        var method = $(security_app_access_time_parent_pane).find(lprefix_id+"_method").val();
                
        var security_app_access_time_data_set = function(){
            var lparent_pane = security_app_access_time_parent_pane;
            var lprefix_id = security_app_access_time_component_prefix_id;
            switch(method){
                case "add":
                    security_app_access_time_methods.reset_all();
                    break;
                case "view":
                    
                    break;            
            }
        }
        
        security_app_access_time_methods.enable_disable();
        security_app_access_time_methods.show_hide();
        security_app_access_time_data_set();
    }
    
</script>