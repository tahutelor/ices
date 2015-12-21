<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Product_Renderer {

    public static function modal_rpt_product_render($app,$modal){
        $modal->header_set(array('title'=>'Report Product','icon'=>App_Icon::report()));
        $components = self::rpt_product_components_render($app, $modal,true);
    }

    public static function rpt_product_render($app,$form,$data,$path,$method){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_product','class_name'=>'rpt_product_engine'));
        $id_prefix = Rpt_Product_Engine::$prefix_id;
        $path = Rpt_Product_Engine::path_get();
        $id = $data['id'];
        $components = self::rpt_product_components_render($app, $form,false);
        $back_href = $path->index;

        
        
        $js = '
            <script>
                $("#rpt_product_method").val("'.$method.'");
                $("#rpt_product_id").val("'.$id.'");
            </script>
        ';             
        $app->js_set($js);

        $js = '                
                rpt_product_init();
                rpt_product_bind_event();
                rpt_product_components_prepare(); 
        ';
        $app->js_set($js);
        //</editor-fold>
    }

    public static function rpt_product_components_render($app,$form,$is_modal){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_product','class_name'=>'rpt_product_engine'));
        $path = Rpt_Product_Engine::path_get();            
        $components = array();
        $db = new DB();

        $id_prefix = Rpt_Product_Engine::$prefix_id;

        $components['id'] = $form->input_add()->input_set('id',$id_prefix.'_id')
                ->input_set('hide',true)
                ->input_set('value','')
                ;

        $form->input_add()->input_set('id',$id_prefix.'_method')
                ->input_set('hide',true)
                ->input_set('value','')
                ;            
        $db = new DB();
        

        $form->input_select_add()
            ->input_select_set('label',Lang::get('Module Name'))
            ->input_select_set('icon',App_Icon::info())
            ->input_select_set('min_length','0')
            ->input_select_set('id',$id_prefix.'_module_name')
            ->input_select_set('data_add',array())
            ->input_select_set('value',array())
            ->input_select_set('disable_all',true)
            ->input_select_set('hide_all',true)
            ->input_select_set('ajax_url',$path->data_support.'/input_select_module_name/')
            ->input_select_set('allow_empty',false)

        ;

        $form->div_add()
            ->div_set('id',$id_prefix.'_report_div')
            ->div_set('class','')
        ;
        
        
        
        $form_group = $form->form_group_add()->attrib_set(array('style'=>'height:34px;text-align:right'));
        
        $form_group->button_add()->button_set('value', 'Preview')
                ->button_set('icon', App_Icon::btn_preview())
                ->button_set('href', '')
                ->button_set('id', $id_prefix.'_btn_preview')
                ->button_set('class', 'btn btn-default hide_all ')
                ->button_set('style','margin-right:5px')
                
        ; 
        
        $form_group->button_group_add()
            ->button_group_set('icon',App_Icon::btn_save())
            ->button_group_set('value','Download')
            ->button_group_set('div_class','btn-group hide_all')
            ->button_group_set('item_list_add',array('id'=>$id_prefix.'_save_excel','label'=>'Excel','class'=>'fa fa-file-excel-o'))
            ;
        
               
        
        $form->div_add()
            ->div_set('id',$id_prefix.'_report_preview_div')
            ->div_set('class','')
        ;
        
        
        $param = array(
            'ajax_url'=>$path->index.'ajax_search/'
            ,'index_url'=>$path->index
            ,'detail_tab'=>'#'.$id_prefix
            ,'view_url'=>$path->index.'view/'
            ,'window_scroll'=>'body'
            ,'form_render_url'=>$path->index.'form_render/'
            ,'data_support_url'=>$path->index.'data_support/'
            ,'common_ajax_listener'=>ICES_Engine::$app['app_base_url'].'common_ajax_listener/'
            ,'component_prefix_id'=>$id_prefix
        );
        


        if($is_modal){
            $param['detail_tab'] = '#modal_'.$id_prefix.' .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_'.$id_prefix;
        }

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_product/'.$id_prefix.'_basic_function_js',$param,TRUE);
        $app->js_set($js);
        return $components;
        //</editor-fold>
    }

    public static function form_render($module_name,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_product','class_name'=>'rpt_product_engine'));
        SI::module()->load_class(array('module'=>'rpt_product','class_name'=>'rpt_product_data_support'));
        
        $result = array('html'=>'','script'=>'');        
        if(method_exists('Rpt_Product_Renderer', $module_name.'_render')){
            $result = eval('return self::'.$module_name.'_render(false,$data);');
        }
        return $result;
        //</editor-fold>
    }
    
    static function product_stock_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_product','class_name'=>'rpt_product_data_support'));
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Product_Engine::path_get();
        $id_prefix = Rpt_Product_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
                
        $main_div->input_select_add()
                ->input_select_set('label', Lang::get('Warehouse'))
                ->input_select_set('icon', App_Icon::warehouse())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_warehouse')
                ->input_select_set('data_add', Warehouse_Data_Support::input_select_warehouse_list_get())
                ->input_select_set('value', Warehouse_Data_Support::input_select_warehouse_list_get()[0])
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
        ;
        
        $product_status_list = array(
            array('id'=>'all_status','text'=>'<strong>ALL STATUS</strong>'),
            array('id'=>'active','text'=>SI::get_status_attr('ACTIVE')),
            array('id'=>'inactive','text'=>SI::get_status_attr('INACTIVE')),
        );
                
        $main_div->input_select_add()
                ->input_select_set('label', Lang::get('Status'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_product_status')
                ->input_select_set('data_add', $product_status_list)
                ->input_select_set('value', $product_status_list[0])
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
        ;
        
        $param_yes_no = array(
            array('id'=>'all','text'=>'<strong>All</strong>'),
            array('id'=>'1','text'=>SI::get_status_attr('TRUE')),
            array('id'=>'0','text'=>SI::get_status_attr('FALSE')),
            
        );
        
        $main_div->input_select_add()
                ->input_select_set('label', Lang::get('Product Batch Expired'))
                ->input_select_set('icon', 'fa fa-calendar')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_product_batch_expired')
                ->input_select_set('data_add', $param_yes_no)
                ->input_select_set('value', $param_yes_no[0])
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
        ;
        
        $main_div->input_add()->input_set('label', Lang::get('Keyword'))
                ->input_set('id', $id_prefix . '_keyword')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
        ;
        
        $result['html'] = $main_div->render();
        $result['script'] = $main_div->scripts_get();
        
        $param = array(
            'ajax_url'=>$path->index.'ajax_search/'
            ,'index_url'=>$path->index
            ,'detail_tab'=>'#'.$id_prefix
            ,'view_url'=>$path->index.'view/'
            ,'window_scroll'=>'body'
            ,'form_render_url'=>$path->index.'form_render/'
            ,'data_support_url'=>$path->index.'data_support/'
            ,'common_ajax_listener'=>ICES_Engine::$app['app_base_url'].'common_ajax_listener/'
            ,'component_prefix_id'=>$id_prefix
        );
        
        
        $js = str_replace(array('<script>','</script>'),'',get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_product/product_stock_js',$param,true));
        $result['script'].=$js;
        
        
        
        return $result;
        //</editor-fold>
    }

    static function product_stock_rpt_preview_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_product','class_name'=>'rpt_product_data_support'));
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_engine'));
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_data_support'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Product_Engine::path_get();
        $id_prefix = Rpt_Product_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $tbl = $main_div->table_add();
        $tbl->table_set('class','table');
        $tbl->table_set('id',$id_prefix.'_tbl_product_preview');
        $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $tbl->table_set('columns',array("name"=>"product_category_text","label"=>Lang::get("Category"),'col_attrib'=>array('style'=>''),'is_key'=>true));        
        $tbl->table_set('columns',array("name"=>"product_text","label"=>Lang::get("Product"),'col_attrib'=>array('style'=>''),'is_key'=>true));        
        $tbl->table_set('columns',array("name"=>"qty","label"=>Lang::get("Qty"),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"unit_code","label"=>Lang::get("Unit"),'col_attrib'=>array('style'=>'')));
        $tbl->table_set('columns',array("name"=>"purchase_amount","label"=>Lang::get("Purchase Amount"),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"sales_amount","label"=>Lang::get("Sales Amount"),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"product_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('data key','id');
        
        $param = array(
            'product_status'=>Tools::_str(isset($data['product_status'])?$data['product_status']:''),
            'warehouse_id'=>Tools::_str(isset($data['warehouse_id'])?$data['warehouse_id']:''),
            'keyword'=>Tools::_str(isset($data['keyword'])?$data['keyword']:''),
            'product_batch_expired'=>Tools::_str(isset($data['product_batch_expired'])?$data['product_batch_expired']:''),
        );
        $product_stock = Rpt_Product_Data_Support::product_stock_get($param);
        if(count($product_stock) > 500) $product_stock = array_splice($product_stock,0,499);
        
        $footer = array(
            
        );
        foreach($product_stock as $idx=>$row){            
            $product_stock[$idx]['row_num'] = $idx+1;
            $product_stock[$idx]['product_category_text'] = '<strong>'.$row['product_category_code'].'</strong>'. ' '.$row['product_category_name'];
            $product_stock[$idx]['product_text'] = '<a href="'.ICES_Engine::$app['app_base_url'].'product/view/'.$row['product_id'].'" target="_blank">'
                .'<strong>'.$row['product_code'].'</strong>'.' '.$row['product_name'].'</a>';
            $product_stock[$idx]['product_status'] = SI::get_status_attr(
                SI::type_get('product_engine',$product_stock[$idx]['product_status'],'$status_list')['text']
            );
            $product_stock[$idx]['qty'] = Tools::thousand_separator($row['qty']);
            $product_stock[$idx]['purchase_amount'] = Tools::thousand_separator($row['purchase_amount']);
            $product_stock[$idx]['sales_amount'] = Tools::thousand_separator(Product_Data_Support::sales_amount_get($row['sales_amount']));
            
        }
        if(count($product_stock)>0){
            
        }
        
        $tbl->table_set('data',$product_stock);
        
        $result['html'] = $main_div->render();
        $result['script'] = $main_div->scripts_get();
        
        $param = array(
            'ajax_url'=>$path->index.'ajax_search/'
            ,'index_url'=>$path->index
            ,'detail_tab'=>'#'.$id_prefix
            ,'view_url'=>$path->index.'view/'
            ,'window_scroll'=>'body'
            ,'form_render_url'=>$path->index.'form_render/'
            ,'data_support_url'=>$path->index.'data_support/'
            ,'common_ajax_listener'=>ICES_Engine::$app['app_base_url'].'common_ajax_listener/'
            ,'component_prefix_id'=>$id_prefix
        );
        
        $js = '';
        $result['script'].=$js;
        
        
        
        return $result;
        //</editor-fold>
    }
    
}
    
?>