<script>
    var contact_category_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var contact_category_ajax_url = null;
    var contact_category_index_url = null;
    var contact_category_view_url = null;
    var contact_category_window_scroll = null;
    var contact_category_data_support_url = null;
    var contact_category_common_ajax_listener = null;
    var contact_category_component_prefix_id = '';
    
    var contact_category_init = function(){
        var parent_pane = contact_category_parent_pane;

        contact_category_ajax_url = '<?php echo $ajax_url ?>';
        contact_category_index_url = '<?php echo $index_url ?>';
        contact_category_view_url = '<?php echo $view_url ?>';
        contact_category_window_scroll = '<?php echo $window_scroll; ?>';
        contact_category_data_support_url = '<?php echo $data_support_url; ?>';
        contact_category_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        contact_category_component_prefix_id = '#<?php echo $component_prefix_id; ?>';
        
    }
    var contact_category_data ={
        
    }
    
    var contact_category_methods = {
        
        hide_all:function(){
            var lparent_pane = contact_category_parent_pane;
            var lprefix_id = contact_category_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all:function(){
            var lparent_pane = contact_category_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        
        show_hide: function(){
            var lparent_pane = contact_category_parent_pane;
            var lprefix_id = contact_category_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();            
            
            contact_category_methods.hide_all();
            
            switch(lmethod){
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_contact_category_status').closest('div [class*="form-group"]').show();
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
            var lparent_pane = contact_category_parent_pane;
            var lprefix_id = contact_category_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();  
            contact_category_methods.disable_all();
            
            switch(lmethod){
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id+"_name").prop("disabled",false);
                    $(lparent_pane).find(lprefix_id+"_code").prop("disabled",false);
                    $(lparent_pane).find(lprefix_id+'_contact_category_status').select2('enable');
                    $(lparent_pane).find(lprefix_id+"_notes").prop("disabled",false);
                    break;
            }
        },
        reset_all:function(){
            var lparent_pane = contact_category_parent_pane;
            var lprefix_id = contact_category_component_prefix_id;
            
            $(lparent_pane).find(lprefix_id+'_code').val('');
            $(lparent_pane).find(lprefix_id+'_name').val('');
            $(lparent_pane).find(lprefix_id+'_notes').val('');
            
            APP_FORM.status.default_status_set(
                'contact_category',
                $(lparent_pane).find(lprefix_id+'_contact_category_status')
            );
           
        },
        after_submit: function(){
            
        },
        submit:function(){
            var lparent_pane = contact_category_parent_pane;
            var lprefix_id = contact_category_component_prefix_id;
            var lajax_url = contact_category_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id+"_method").val();
            var contact_category_id = $(lparent_pane).find(lprefix_id+"_id").val();        
            var json_data = {
                ajax_post:true,
                contact_category:{},
                message_session:true
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    json_data.contact_category.code = $(lparent_pane).find(lprefix_id+"_code").val();
                    json_data.contact_category.name = $(lparent_pane).find(lprefix_id+"_name").val();
                    json_data.contact_category.contact_category_status = $(lparent_pane).find(lprefix_id+"_contact_category_status").select2('val');
                    json_data.contact_category.notes = $(lparent_pane).find(lprefix_id+"_notes").val();
                    break;
            }
            
            var lajax_method='';
            switch(lmethod){
                case 'add':
                    lajax_method = 'contact_category_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id+'_contact_category_status').select2('data').method;
                    break;
            }
            lajax_url +=lajax_method+'/'+contact_category_id;

            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
        },
    }

    var contact_category_bind_event = function(){
        var lparent_pane = contact_category_parent_pane;
        var lprefix_id = contact_category_component_prefix_id;
        
        $(lparent_pane).find(lprefix_id+'_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id+'_btn_submit'),{
            parent_pane: lparent_pane,
            module_method: contact_category_methods,
            view_url: contact_category_view_url,
            prefix_id:lprefix_id,
            window_scroll:contact_category_window_scroll,
        });
        
        
    }
    
    var contact_category_components_prepare= function(){
        var lparent_pane = contact_category_parent_pane;
        var lprefix_id = contact_category_component_prefix_id;
        var method = $(contact_category_parent_pane).find(lprefix_id+"_method").val();
                
        var contact_category_data_set = function(){
            var lparent_pane = contact_category_parent_pane;
            var lprefix_id = contact_category_component_prefix_id;
            switch(method){
                case "add":
                    contact_category_methods.reset_all();
                    break;
                case "view":
                    var contact_category_id = $(contact_category_parent_pane).find(lprefix_id+"_id").val();
                    var json_data={data:contact_category_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(contact_category_data_support_url+"contact_category_get",json_data).response;
                    if(lresponse != []){
                        var lcontact_category = lresponse.contact_category;
                        $(lparent_pane).find(lprefix_id+'_code').val(lcontact_category.code);
                        $(lparent_pane).find(lprefix_id+'_name').val(lcontact_category.name);
                        $(lparent_pane).find(lprefix_id+'_method_name').val(lcontact_category.method);
                        
                        $(lparent_pane).find(lprefix_id+'_contact_category_status')
                            .select2('data',lcontact_category.contact_category_status).change();
                            
                        $(lparent_pane).find(lprefix_id+'_contact_category_status')
                            .select2({data:lresponse.contact_category_status_list});                        
                        
                        $(lparent_pane).find(lprefix_id+'_notes').val(lcontact_category.notes);
                    };
                    break;            
            }
        }
    
        contact_category_methods.enable_disable();
        contact_category_methods.show_hide();
        contact_category_data_set();
    }
    
</script>