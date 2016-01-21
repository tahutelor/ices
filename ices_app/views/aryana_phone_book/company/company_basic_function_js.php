<script>
    var company_parent_pane = $('<?php echo $detail_tab; ?>')[0];
    var company_ajax_url = null;
    var company_index_url = null;
    var company_view_url = null;
    var company_window_scroll = null;
    var company_data_support_url = null;
    var company_common_ajax_listener = null;
    var company_component_prefix_id = '';
    
    var company_init = function(){
        var parent_pane = company_parent_pane;

        company_ajax_url = '<?php echo $ajax_url ?>';
        company_index_url = '<?php echo $index_url ?>';
        company_view_url = '<?php echo $view_url ?>';
        company_window_scroll = '<?php echo $window_scroll; ?>';
        company_data_support_url = '<?php echo $data_support_url; ?>';
        company_common_ajax_listener = '<?php echo $common_ajax_listener; ?>';
        company_component_prefix_id = '#<?php echo $component_prefix_id; ?>';
        
    }
    var company_data ={
        
    }
    
    var company_methods = {
        
        hide_all:function(){
            var lparent_pane = company_parent_pane;
            var lprefix_id = company_component_prefix_id;
            $(lparent_pane).find('.hide_all').hide();
        },
        disable_all:function(){
            var lparent_pane = company_parent_pane;
            var lcomponents = $(lparent_pane).find('.disable_all');
            APP_COMPONENT.disable_all(lparent_pane);
        },
        
        show_hide: function(){
            var lparent_pane = company_parent_pane;
            var lprefix_id = company_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();            
            
            company_methods.hide_all();
            
            switch(lmethod){
                case 'add':
                case 'view':
                    $(lparent_pane).find(lprefix_id+'_code').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_name').closest('div [class*="form-group"]').show();
                    $(lparent_pane).find(lprefix_id+'_company_status').closest('div [class*="form-group"]').show();
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
            var lparent_pane = company_parent_pane;
            var lprefix_id = company_component_prefix_id;
            var lmethod = $(lparent_pane).find(lprefix_id+'_method').val();  
            company_methods.disable_all();
            
            switch(lmethod){
                case "add":
                case 'view':
                    $(lparent_pane).find(lprefix_id+"_name").prop("disabled",false);
                    $(lparent_pane).find(lprefix_id+"_code").prop("disabled",false);
                    $(lparent_pane).find(lprefix_id+'_company_status').select2('enable');
                    $(lparent_pane).find(lprefix_id+"_notes").prop("disabled",false);
                    break;
            }
        },
        reset_all:function(){
            var lparent_pane = company_parent_pane;
            var lprefix_id = company_component_prefix_id;
            
            $(lparent_pane).find(lprefix_id+'_code').val('');
            $(lparent_pane).find(lprefix_id+'_name').val('');
            $(lparent_pane).find(lprefix_id+'_notes').val('');
            
            APP_FORM.status.default_status_set(
                'company',
                $(lparent_pane).find(lprefix_id+'_company_status')
            );
           
        },
        after_submit: function(){
            
        },
        submit:function(){
            var lparent_pane = company_parent_pane;
            var lprefix_id = company_component_prefix_id;
            var lajax_url = company_index_url;
            var lmethod = $(lparent_pane).find(lprefix_id+"_method").val();
            var company_id = $(lparent_pane).find(lprefix_id+"_id").val();        
            var json_data = {
                ajax_post:true,
                company:{},
                message_session:true
            };

            switch(lmethod){
                case 'add':
                case 'view':
                    json_data.company.code = $(lparent_pane).find(lprefix_id+"_code").val();
                    json_data.company.name = $(lparent_pane).find(lprefix_id+"_name").val();
                    json_data.company.company_status = $(lparent_pane).find(lprefix_id+"_company_status").select2('val');
                    json_data.company.notes = $(lparent_pane).find(lprefix_id+"_notes").val();
                    break;
            }
            
            var lajax_method='';
            switch(lmethod){
                case 'add':
                    lajax_method = 'company_add';
                    break;
                case 'view':
                    lajax_method = $(lparent_pane).find(lprefix_id+'_company_status').select2('data').method;
                    break;
            }
            lajax_url +=lajax_method+'/'+company_id;

            var lresult = {
                json_data:json_data,
                ajax_url:lajax_url
            };
            return lresult;
        },
    }

    var company_bind_event = function(){
        var lparent_pane = company_parent_pane;
        var lprefix_id = company_component_prefix_id;
        
        $(lparent_pane).find(lprefix_id+'_btn_submit').off('click');
        APP_COMPONENT.button.submit.set($(lparent_pane).find(lprefix_id+'_btn_submit'),{
            parent_pane: lparent_pane,
            module_method: company_methods,
            view_url: company_view_url,
            prefix_id:lprefix_id,
            window_scroll:company_window_scroll,
        });
        
        
    }
    
    var company_components_prepare= function(){
        var lparent_pane = company_parent_pane;
        var lprefix_id = company_component_prefix_id;
        var method = $(company_parent_pane).find(lprefix_id+"_method").val();
                
        var company_data_set = function(){
            var lparent_pane = company_parent_pane;
            var lprefix_id = company_component_prefix_id;
            switch(method){
                case "add":
                    company_methods.reset_all();
                    break;
                case "view":
                    var company_id = $(company_parent_pane).find(lprefix_id+"_id").val();
                    var json_data={data:company_id};
                    var lresponse = APP_DATA_TRANSFER.ajaxPOST(company_data_support_url+"company_get",json_data).response;
                    if(lresponse != []){
                        var lcompany = lresponse.company;
                        $(lparent_pane).find(lprefix_id+'_code').val(lcompany.code);
                        $(lparent_pane).find(lprefix_id+'_name').val(lcompany.name);
                        $(lparent_pane).find(lprefix_id+'_method_name').val(lcompany.method);
                        
                        $(lparent_pane).find(lprefix_id+'_company_status')
                            .select2('data',lcompany.company_status).change();
                            
                        $(lparent_pane).find(lprefix_id+'_company_status')
                            .select2({data:lresponse.company_status_list});                        
                        
                        $(lparent_pane).find(lprefix_id+'_notes').val(lcompany.notes);
                    };
                    break;            
            }
        }
    
        company_methods.enable_disable();
        company_methods.show_hide();
        company_data_set();
    }
    
</script>