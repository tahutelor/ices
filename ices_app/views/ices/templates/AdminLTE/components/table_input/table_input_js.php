<script>
    var <?php echo $table_id ?>_method = {
        setting:{
            table_col_get:function(){
            return <?php echo json_encode($table_cols); ?>;
            },
            table_get:function(){
                return $('#<?php echo $table_id; ?>')[0];
            },
            func_row_bind_event:function(iopt){
                <?php // Func bind after render row ?>
                var lrow = iopt.tr;
                var ltbody = iopt.tbody;

            },
            func_row_data_assign:function(iopt){
                <?php // Func assign data after render row ?>
            },
            func_row_transform_comp_on_new_row:function(iopt){
                <?php // Func transform component on new row ?>

            },
            func_new_row_validation:function(iopt){
                <?php // Func data row validation before add more row ?>
                var lrow = iopt.tr;
                var ltbody = iopt.tbody;
                var lresult = {success:1,msg:[]};
                
                return lresult;
            },
            func_get_data_table:function(){
                <?php // Func get data table ?>
            },
            
        }
        ,reset:function(){
            var lmethod = <?php echo $table_id ?>_method;
            var ltable = lmethod.setting.table_get();
            $(ltable).find('thead').empty();
            $(ltable).find('tbody').empty();
            $(ltable).find('tfoot').empty();
        }
        
        ,head_generate:function(){
            var lmethod = <?php echo $table_id ?>_method;
            var ltable = lmethod.setting.table_get();
            var lcol_arr = lmethod.setting.table_col_get();
            
            var ltr = document.createElement('tr');
            var fast_draw = APP_COMPONENT.table_fast_draw;
            
            <?php if($row_num){ ?>
                fast_draw.col_add(ltr,{tag:'th',class:'table-row-num',col_name:'row_num',style:'',val:'<span>#</span>',type:'text',});
            <?php }?>
            
            $.each(lcol_arr, function(i, lcol){
                fast_draw.col_add(ltr,{tag:'th',class:lcol.th.class,col_name:lcol.col_name,col_style:lcol.th.col_style,val:lcol.th.val,type:lcol.th.type,visible:lcol.th.visible});
            });
            
            <?php if($new_row){ ?>
            fast_draw.col_add(ltr,{tag:'th',class:'table-action',col_name:'action',style:'vertical-align:middle',val:'',type:'text'});
            
            <?php }?>
            
            $(ltable).find('thead').append(ltr);
        }
        
        ,input_row_generate:function(idata_row){
            var lmethod = <?php echo $table_id ?>_method;
            var ltable = lmethod.setting.table_get();
            var lcol_arr = lmethod.setting.table_col_get();
            
            var ltr = document.createElement('tr');
            var fast_draw = APP_COMPONENT.table_fast_draw;
            var lcomp = {};
            
            <?php if($row_num){ ?>
            fast_draw.col_add(ltr,{tag:'td',class:'table-row-num',col_name:'row_num',style:'',val:$(ltable).find('tbody tr').length+1,type:'span',});
            lcomp.row_num = $(ltr).find('[col_name="row_num"]');
            <?php } ?>
                
            $.each(lcol_arr, function(i, lcol){
                if(lcol.col_id_exists){
                    fast_draw.col_add(ltr,{tag:'td',class:lcol.td.class,col_name:lcol.col_name+'_id',style:lcol.td.style,val:'',type:'div',visible:false});
                    lcomp[lcol.col_name+'_id'] = $(ltr).find('[col_name="'+lcol.col_name+'_id"]');
                }
                fast_draw.col_add(ltr,{
                    tag:'td',class:lcol.td.class,col_name:lcol.col_name, col_style:lcol.td.col_style
                    ,style:lcol.td.style,val:lcol.td.val,type:lcol.td.tag,visible:lcol.td.visible
                    ,comp_attr:lcol.td.attr
                });
                lcomp[lcol.col_name] = $(ltr).find('[col_name="'+lcol.col_name+'"]');
            });
            
            var lparam = {tr:ltr,comp:lcomp,data_row:idata_row}
            //lmethod.setting.func_comp_set(lparam);
            lcomp = lparam.comp;
            
            <?php if($new_row){ ?>
            var laction = fast_draw.col_add(ltr,{tag:'td',col_name:'action',style:'vertical-align:middle',val:'',type:'text'});
            var lnew_row = APP_COMPONENT.new_row();    
            laction.appendChild(lnew_row);
            
            $(lnew_row).on('click',function(){
                var lmethod = <?php echo $table_id ?>_method;
                var ltbody = $(this).closest('tbody')[0];
                var lrow = $(this).closest('tr')[0];
                var lopt = {tbody:ltbody, tr:lrow, comp:lcomp};
                var lresult = lmethod.setting.func_new_row_validation(lopt);
                
                if(lresult.success === 1){
                    lmethod.components.trash_set(lopt);
                    lmethod.setting.func_row_transform_comp_on_new_row(lopt);                    
                    $(ltbody).append(lmethod.input_row_generate({}));
                }                    
            });
            
            <?php }?>
                
                
            
                
            $(ltable).find('tbody').append(ltr);
            var lparam = {tr:ltr,tbody:$(ltr).closest('tbody'),data_row:idata_row,comp:lcomp};
            lmethod.setting.func_row_bind_event(lparam);
            lmethod.setting.func_row_data_assign(lparam);
        },
        components:{
            trash_set:function(iopt){
                var lrow = iopt.tr;
                var ltbody = $(lrow).closest('tbody');
                var ltrash = APP_COMPONENT.trash();
                $(lrow).find('[col_name="action"]').empty();
                $(lrow).find('[col_name="action"]')[0].appendChild(ltrash);
                
            }
        }
        ,get_data_table:function(){
            var lmethod = <?php echo $table_id ?>_method;
            var lresult = [];
            lresult = lmethod.setting.func_get_data_table();
            return lresult;
        }
    };
    
    
    
</script>