<script>
    var phone_number_type_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var phone_number_type_ajax_url = null;
    var phone_number_type_index_url = null;
    var phone_number_type_view_url = null;
    var phone_number_type_window_scroll = null;
    var phone_number_type_data_support_url = null;
    var phone_number_type_common_ajax_listener = null;
    var phone_number_type_component_prefix_id = '';
    
    var phone_number_type_init = function(){
        var parent_pane = phone_number_type_parent_pane;

        phone_number_type_ajax_url = '<?php echo $ajax_url ?>';
        phone_number_type_index_url = '<?php echo $index_url ?>';
        phone_number_type_view_url = '<?php echo $view_url ?>';
        phone_number_type_window_scroll = '<?php echo $window_scroll; ?>';
        phone_number_type_data_support_url = '<?php echo $data_support_url; ?>';
        phone_number_type_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        phone_number_type_component_prefix_id = '#<?php echo $component_prefix_id; ?>';
        
    }

    var phone_number_type_data ={
        
    }
    
    var phone_number_type_methods = {
        
        hide_all:function(){
            var lparent_pane = phone_number_type_parent_pane;
            var lprefix_id = phone_number_type_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all:function(){
            var lparent_pane = phone_number_type_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        
        show_hide: function(){
            var lparent_pane = phone_number_type_parent_pane;
            var lprefix_id = phone_number_type_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();            
            
            phone_number_type_methods.hide_all();
            
            switch(lmethod){
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_phone_number_type_status').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_notes').closest('div [class*="form-group"]').show();
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
            var lparent_pane = phone_number_type_parent_pane;
            var lprefix_id = phone_number_type_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();  
            phone_number_type_methods.disable_all();
            
            switch(lmethod){
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id+"_name").prop("disabled",false);
                    $(lparent_pane).find(lprefix_id+"_code").prop("disabled",false);
                    $(lparent_pane).find(lprefix_id+'_phone_number_type_status').select2('enable');
                    $(lparent_pane).find(lprefix_id+"_notes").prop("disabled",false);
                    break;
            }
        },
        reset_all:function(){
            var lparent_pane = phone_number_type_parent_pane;
            var lprefix_id = phone_number_type_component_prefix_id;
            
            $(lparent_pane).find(lprefix_id+'_code').val('');
            $(lparent_pane).find(lprefix_id+'_name').val('');
            $(lparent_pane).find(lprefix_id+'_notes').val('');
            
            APP_FORM.status.default_status_set(
                'phone_number_type',
                $(lparent_pane).find(lprefix_id+'_phone_number_type_status')
            );
           
        },
        after_submit: function(){
            
        },
        submit:function(){
            var lparent_pane = phone_number_type_parent_pane;
            var lprefix_id = phone_number_type_component_prefix_id;
            var lajax_url = phone_number_type_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id+"_method").val();
            var phone_number_type_id = $(lparent_pane).find(lprefix_id+"_id").val();        
            var json_data = {
                ajax_post:true,
                phone_number_type:{},
                message_session:true
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    json_data.phone_number_type.code = $(lparent_pane).find(lprefix_id+"_code").val();
                    json_data.phone_number_type.name = $(lparent_pane).find(lprefix_id+"_name").val();
                    json_data.phone_number_type.phone_number_type_status = $(lparent_pane).find(lprefix_id+"_phone_number_type_status").select2('val');
                    json_data.phone_number_type.notes = $(lparent_pane).find(lprefix_id+"_notes").val();
                    break;
            }
            
            var lajax_method='';
            switch(lmethod){
                case 'add':
                    lajax_method = 'phone_number_type_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id+'_phone_number_type_status').select2('data').method;
                    break;
            }
            lajax_url +=lajax_method+'/'+phone_number_type_id;
            
            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
        },
    }

    var phone_number_type_bind_event = function(){
        var lparent_pane = phone_number_type_parent_pane;
        var lprefix_id = phone_number_type_component_prefix_id;
        
        $(lparent_pane).find(lprefix_id+'_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id+'_btn_submit'),{
            parent_pane: lparent_pane,
            module_method: phone_number_type_methods,
            view_url: phone_number_type_view_url,
            prefix_id:lprefix_id,
            window_scroll:phone_number_type_window_scroll,
        });
        
        
    }
    
    var phone_number_type_components_prepare= function(){
        var lparent_pane = phone_number_type_parent_pane;
        var lprefix_id = phone_number_type_component_prefix_id;
        var method = $(phone_number_type_parent_pane).find(lprefix_id+"_method").val();
                
        var phone_number_type_data_set = function(){
            var lparent_pane = phone_number_type_parent_pane;
            var lprefix_id = phone_number_type_component_prefix_id;
            switch(method){
                case "add":
                    phone_number_type_methods.reset_all();
                    break;
                case "view":
                    var phone_number_type_id = $(phone_number_type_parent_pane).find(lprefix_id+"_id").val();
                    var json_data={data:phone_number_type_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(phone_number_type_data_support_url+"phone_number_type_get",json_data).response;
                    if(lresponse != []){
                        var lphone_number_type = lresponse.phone_number_type;
                        $(lparent_pane).find(lprefix_id+'_code').val(lphone_number_type.code);
                        $(lparent_pane).find(lprefix_id+'_name').val(lphone_number_type.name);
                        $(lparent_pane).find(lprefix_id+'_method_name').val(lphone_number_type.method);
                        
                        $(lparent_pane).find(lprefix_id+'_phone_number_type_status')
                            .select2('data',lphone_number_type.phone_number_type_status).change();
                            
                        $(lparent_pane).find(lprefix_id+'_phone_number_type_status')
                            .select2({data:lresponse.phone_number_type_status_list});                        
                        
                        $(lparent_pane).find(lprefix_id+'_notes').val(lphone_number_type.notes);
                    };
                    break;            
            }
        }
    
        phone_number_type_methods.enable_disable();
        phone_number_type_methods.show_hide();
        phone_number_type_data_set();
    }
    
</script>