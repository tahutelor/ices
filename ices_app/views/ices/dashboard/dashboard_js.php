<script>
    var dashboard_refresh_every_ms=300000;
    var dashboard = {
        number_of_dashboard:0,
        dashboard_draw: function(irow){
            $(irow.target_data).empty();
            var loverlay = $(irow.target_data).closest('[dashboard_component]').find('[id*="_overlay"]');
            var lloading = $(irow.target_data).closest('[dashboard_component]').find('[id*="_loading"]');
            $(loverlay).addClass('overlay');
            $(lloading).addClass('loading-img');
            $(irow.target_data).hide();
            APP_COMPONENT.attach($(irow.target_data),irow.data);
            $(irow.target_data).show();
            setTimeout(function(){
                $(loverlay).removeClass('overlay');
                $(lloading).removeClass('loading-img');
            },500);
            //$(irow.target_data)[0].innerHTML = irow.data;        
            
        },
        dashboard_get:function(imodule_arr){
            var ldata = {
                module:imodule_arr
            };
            
            var lajax_url = "<?php echo ICES_Engine::$app['app_base_url'];?>"+'dashboard'+
                '/'+'data_support/data_get';

            var lresult = APP_DATA_TRANSFER.ajaxPOST(lajax_url,ldata);
            if(lresult.success === 1){
                $.each(lresult.response, function(ldashboard_idx, lrow){
                    dashboard.dashboard_draw(lrow);
                });
            }
        },
        refresh:function(imodule){
            imodule = typeof imodule === 'undefined'? 
                [imodule] : ['weekly_sales_invoice'];
            dashboard.dashboard_get(imodule);
        }
    }
    
    $('[dashboard_component] [id*="_refresh"]').on('click',function(){
        
        var ldiv = $(this).closest('.box.box-primary')[0];
        $(ldiv).find('[id*="_overlay"]').addClass('overlay');
        $(ldiv).find('[id*="_loading"]').addClass('loading-img');
        setTimeout(function(){
            $(ldiv).find('[id*="_overlay"]').removeClass('overlay');
            $(ldiv).find('[id*="_loading"]').removeClass('loading-img');
        },500);
            
        dashboard.refresh($(ldiv).attr('module_name'));
    });
    
    $('[dashboard_component] [id*="_minus"]').on('click',function(){
        var lprefix_id = '#'+$(this).closest('[module_name]').attr('module_name');
        if($(lprefix_id+'_div').find('[class="slimScrollDiv"]').height()>0){
            $(lprefix_id+'_div').find('.box-body').attr('original_height',APP_CONVERTER._float($(lprefix_id+'_div').find('.box-body').css('height')));
            $(lprefix_id+'_div').find('[class="slimScrollDiv"]').height(0);
            
        }
        else{
            $(lprefix_id+'_div').find('[class="slimScrollDiv"]').height($(lprefix_id+'_div').find('.box-body').attr('original_height'));
            $(lprefix_id+'_div').find('[id*="_refresh"]').click();
        }
        
    });
    
    
</script>