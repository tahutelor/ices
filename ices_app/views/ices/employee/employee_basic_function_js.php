<script>
    var employee_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var employee_ajax_url = null;
    var employee_index_url = null;
    var employee_view_url = null;
    var employee_window_scroll = null;
    var employee_data_support_url = null;
    var employee_common_ajax_listener = null;
    var employee_component_prefix_id = '';
    
    var employee_init = function(){
        var parent_pane = employee_parent_pane;

        employee_ajax_url = '<?php echo $ajax_url ?>';
        employee_index_url = '<?php echo $index_url ?>';
        employee_view_url = '<?php echo $view_url ?>';
        employee_window_scroll = '<?php echo $window_scroll; ?>';
        employee_data_support_url = '<?php echo $data_support_url; ?>';
        employee_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        employee_component_prefix_id = '#<?php echo $component_prefix_id; ?>';
        
    }

    var employee_after_submit = function(){

    }
    
    var employee_data ={
        
    }
    
    var employee_methods = {
        
        hide_all:function(){
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all:function(){
            var lparent_pane = employee_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        
        show_hide: function(){
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            var lmethod = $(lparent_pane).find('#employee_method').val();            
            
            employee_methods.hide_all();
            
            switch(lmethod){
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_firstname').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_lastname').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_username').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_password').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_tbl_u_group').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_employee_status').closest('div [class*="form-group"]').show();
                    break;
            }
            
            switch(lmethod){
                case 'add':
                    
                    break;
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_btn_delete').show();
                    break;
            }
        },        
        enable_disable: function(){
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();  
            employee_methods.disable_all();
            
            switch(lmethod){
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_firstname').prop('disabled',false);
                    $(lparent_pane).find(lprefix_id+"_lastname").prop("disabled",false);
                    $(lparent_pane).find(lprefix_id+"_username").prop("disabled",false);
                    $(lparent_pane).find(lprefix_id+"_password").prop("disabled",false);
                    $(lparent_pane).find(lprefix_id+'_employee_status').select2('enable');
                    break;
            }
        },
        reset_all:function(){
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            
            $(lparent_pane).find(lprefix_id+'_firstname').val('');
            $(lparent_pane).find(lprefix_id+'_lastname').val('');
            $(lparent_pane).find(lprefix_id+'_username').val('');
            $(lparent_pane).find(lprefix_id+'_password').val('');
            
            APP_FORM.status.default_status_set(
                'employee',
                $(lparent_pane).find(lprefix_id+'_employee_status')
            );
           
            employee_u_group_methods.load_u_group({u_group:[]});
        },
        after_submit: function(){
            
        },
        submit:function(){
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            var lajax_url = employee_index_url;
            var lmethod = $(lparent_pane).find("#employee_method").val();
            var employee_id = $(lparent_pane).find(lprefix_id+"_id").val();        
            var json_data = {
                ajax_post:true,
                employee:{},
                u_group:[],
                message_session:true
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    
                    json_data.employee.firstname = $(lparent_pane).find(lprefix_id+"_firstname").val();
                    json_data.employee.lastname = $(lparent_pane).find(lprefix_id+"_lastname").val();
                    json_data.employee.username = $(lparent_pane).find(lprefix_id+"_username").val();
                    json_data.employee.password = $(lparent_pane).find(lprefix_id+"_password").val();
                    json_data.employee.method = $(lparent_pane).find(lprefix_id+"_method_name").val();
                    json_data.employee.employee_status = $(lparent_pane).find(lprefix_id+"_employee_status").select2('val');
                    json_data.u_group = employee_tbl_u_group_method.setting.func_get_data_table().u_group;
                    break;
            }
            
            var lajax_method='';
            switch(lmethod){
                case 'add':
                    lajax_method = 'employee_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id+'_employee_status').select2('data').method;
                    break;
            }
            lajax_url +=lajax_method+'/'+employee_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
        },
    }

    var employee_bind_event = function(){
        var lparent_pane = employee_parent_pane;
        var lprefix_id = employee_component_prefix_id;
        
        $(lparent_pane).find(lprefix_id+'_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id+'_btn_submit'),{
            parent_pane:lparent_pane,
            module_method:employee_methods,
            view_url: employee_view_url,
            prefix_id:lprefix_id,
            window_scroll:employee_window_scroll,
        });
        
        employee_u_group_bind_event();
    }
    
    var employee_components_prepare= function(){
        var lparent_pane = employee_parent_pane;
        var lprefix_id = employee_component_prefix_id;
        var method = $(employee_parent_pane).find(lprefix_id+"_method").val();
                
        var employee_data_set = function(){
            var lparent_pane = employee_parent_pane;
            var lprefix_id = employee_component_prefix_id;
            switch(method){
                case "add":
                    employee_methods.reset_all();
                    break;
                case "view":
                    var employee_id = $(employee_parent_pane).find(lprefix_id+"_id").val();
                    var json_data={data:employee_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(employee_data_support_url+"employee_get",json_data).response;
                    if(lresponse != []){
                        var lemployee = lresponse.employee;
                        var lu_group = lresponse.u_group;
                        
                        $(lparent_pane).find(lprefix_id+'_firstname').val(lemployee.firstname);
                        $(lparent_pane).find(lprefix_id+'_lastname').val(lemployee.lastname);
                        $(lparent_pane).find(lprefix_id+'_username').val(lemployee.username);
                        $(lparent_pane).find(lprefix_id+'_password').val(lemployee.password);
                        
                        $(lparent_pane).find(lprefix_id+'_employee_status')
                        .select2('data',lemployee.employee_status
                        ).change();
                            
                        $(lparent_pane).find(lprefix_id+'_employee_status')
                        .select2({data:lresponse.employee_status_list});
                        
                        employee_u_group_methods.load_u_group({u_group:lu_group});
                        
                    };
                    break;            
            }
        }
    
        employee_methods.enable_disable();
        employee_methods.show_hide();
        employee_data_set();
    }
    
</script>