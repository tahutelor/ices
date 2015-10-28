<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Sales_Renderer {

    public static function modal_rpt_sales_render($app,$modal){
        $modal->header_set(array('title'=>'System Investigation Report','icon'=>App_Icon::report()));
        $components = self::rpt_sales_components_render($app, $modal,true);
    }

    public static function rpt_sales_render($app,$form,$data,$path,$method){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_engine'));
        $id_prefix = Rpt_Sales_Engine::$prefix_id;
        $path = Rpt_Sales_Engine::path_get();
        $id = $data['id'];
        $components = self::rpt_sales_components_render($app, $form,false);
        $back_href = $path->index;

        $js = '
            <script>
                $("#rpt_sales_method").val("'.$method.'");
                $("#rpt_sales_id").val("'.$id.'");
            </script>
        ';             
        $app->js_set($js);

        $js = '                
                rpt_sales_init();
                rpt_sales_bind_event();
                rpt_sales_components_prepare(); 
        ';
        $app->js_set($js);
        //</editor-fold>
    }

    public static function rpt_sales_components_render($app,$form,$is_modal){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_engine'));
        $path = Rpt_Sales_Engine::path_get();            
        $components = array();
        $db = new DB();

        $id_prefix = Rpt_Sales_Engine::$prefix_id;

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

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_sales/'.$id_prefix.'_basic_function_js',$param,TRUE);
        $app->js_set($js);
        return $components;
        //</editor-fold>
    }

    public static function form_render($module_name,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_engine'));
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_data_support'));
        
        $result = array('html'=>'','script'=>'');        
        if(method_exists('Rpt_Sales_Renderer', $module_name.'_render')){
            $result = eval('return self::'.$module_name.'_render(false,$data);');
        }
        return $result;
        //</editor-fold>
    }
    
    static function sales_invoice_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_data_support'));
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_engine'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Sales_Engine::path_get();
        $id_prefix = Rpt_Sales_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $main_div->datetimepicker_add()->datetimepicker_set('label',Lang::get('Start Date'))
            ->datetimepicker_set('id',$id_prefix.'_start_date')
            ->datetimepicker_set('value',Tools::_date(Date('Y-m-01'),'F d, Y H:i')) 
        ;
        
        $main_div->datetimepicker_add()->datetimepicker_set('label',Lang::get('End Date'))
            ->datetimepicker_set('id',$id_prefix.'_end_date')
            ->datetimepicker_set('value',Tools::_date(Date('Y-m-t 23:59:59'),'F d, Y H:i')) 
        ;
        
        $sales_invoice_status_list = array(
            array('id'=>'all_status','text'=>'<strong>ALL STATUS</strong>'),
            array('id'=>'invoiced','text'=>SI::get_status_attr('INVOICED')),
            array('id'=>'X','text'=>SI::get_status_attr('CANCELED')),
        );
        
        
        $main_div->input_select_add()
                ->input_select_set('label', Lang::get('Status'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_sales_invoice_status')
                ->input_select_set('data_add', $sales_invoice_status_list)
                ->input_select_set('value', $sales_invoice_status_list[0])
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
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
        
        
        $js = str_replace(array('<script>','</script>'),'',get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_sales/sales_invoice_js',$param,true));
        $result['script'].=$js;
        
        
        
        return $result;
        //</editor-fold>
    }

    static function sales_invoice_rpt_preview_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_data_support'));
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_engine'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Sales_Engine::path_get();
        $id_prefix = Rpt_Sales_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $tbl = $main_div->table_add();
        $tbl->table_set('class','table');
        $tbl->table_set('id',$id_prefix.'_tbl_sales_invoice_preview');
        $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $tbl->table_set('columns',array("name"=>"code","label"=>Lang::get("Code"),'col_attrib'=>array('style'=>''),'is_key'=>true));        
        $tbl->table_set('columns',array("name"=>"sales_invoice_date","label"=>Lang::get(array("Sales Invoice","Date")),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"customer_name","label"=>Lang::get("Customer"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('columns',array("name"=>"grand_total_amount","label"=>Lang::get("Grand Total Amount "),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"outstanding_grand_total_amount","label"=>Lang::get("Outstanding Grand Total Amount "),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"sales_invoice_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('data key','id');
        
        
        
        $param = array(
            'sales_invoice_status'=>Tools::_str(isset($data['sales_invoice_status'])?$data['sales_invoice_status']:''),
            'start_date'=>Tools::_str(isset($data['start_date'])?$data['start_date']:''),
            'end_date'=>Tools::_str(isset($data['end_date'])?$data['end_date']:''),
        );
        $sales_invoice = Rpt_Sales_Data_Support::sales_invoice_get($param);
        if(count($sales_invoice) > 500) $sales_invoice = array_splice($sales_invoice,0,499);
        $footer = array(
            'grand_total_amount'=>Tools::_float(0),
            'outstanding_grand_total_amount'=>Tools::_float(0),
        );
        foreach($sales_invoice as $idx=>$row){            
            $sales_invoice[$idx]['row_num'] = $idx+1;
            $sales_invoice[$idx]['code'] = '<a href="'.ICES_Engine::$app['app_base_url'].'sales_invoice/view/'.$row['id'].'" target="_blank">'.$sales_invoice[$idx]['code'].'</a>';
            $sales_invoice[$idx]['grand_total_amount'] = Tools::thousand_separator($sales_invoice[$idx]['grand_total_amount']);
            $sales_invoice[$idx]['outstanding_grand_total_amount'] = Tools::thousand_separator($sales_invoice[$idx]['outstanding_grand_total_amount']);
            $sales_invoice[$idx]['sales_invoice_status'] = SI::get_status_attr(
                SI::type_get('sales_invoice_engine',$sales_invoice[$idx]['sales_invoice_status'],'$status_list')['text']
            );
            $footer['grand_total_amount'] += Tools::_float($row['grand_total_amount']);
            $footer['outstanding_grand_total_amount'] += Tools::_float($row['outstanding_grand_total_amount']);
        }
        if(count($sales_invoice)>0){
            $footer['grand_total_amount'] = '<strong>'.Tools::thousand_separator($footer['grand_total_amount']).'</strong>';
            $footer['outstanding_grand_total_amount'] = '<strong>'.Tools::thousand_separator($footer['outstanding_grand_total_amount']).'</strong>';
            $sales_invoice[] = $footer;
        }
        
        $tbl->table_set('data',$sales_invoice);
        
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
    
    static function sales_receipt_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_data_support'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Sales_Engine::path_get();
        $id_prefix = Rpt_Sales_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $main_div->datetimepicker_add()->datetimepicker_set('label',Lang::get('Start Date'))
            ->datetimepicker_set('id',$id_prefix.'_start_date')
            ->datetimepicker_set('value',Tools::_date(Date('Y-m-01'),'F d, Y H:i')) 
        ;
        
        $main_div->datetimepicker_add()->datetimepicker_set('label',Lang::get('End Date'))
            ->datetimepicker_set('id',$id_prefix.'_end_date')
            ->datetimepicker_set('value',Tools::_date(Date('Y-m-t 23:59:59'),'F d, Y H:i')) 
        ;
        
        $sales_invoice_status_list = array(
            array('id'=>'all_status','text'=>'<strong>ALL STATUS</strong>'),
            array('id'=>'invoiced','text'=>SI::get_status_attr('INVOICED')),
            array('id'=>'X','text'=>SI::get_status_attr('CANCELED')),
        );
        
        
        $main_div->input_select_add()
                ->input_select_set('label', Lang::get('Status'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_sales_receipt_status')
                ->input_select_set('data_add', $sales_invoice_status_list)
                ->input_select_set('value', $sales_invoice_status_list[0])
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
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
        
        
        $js = str_replace(array('<script>','</script>'),'',get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_sales/sales_receipt_js',$param,true));
        $result['script'].=$js;
        
        
        
        return $result;
        //</editor-fold>
    }
    static function sales_receipt_rpt_preview_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_data_support'));
        SI::module()->load_class(array('module'=>'sales_receipt','class_name'=>'sales_receipt_engine'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Sales_Engine::path_get();
        $id_prefix = Rpt_Sales_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $tbl = $main_div->table_add();
        $tbl->table_set('class','table');
        $tbl->table_set('id',$id_prefix.'_tbl_sales_receipt_preview');
        $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $tbl->table_set('columns',array("name"=>"code","label"=>Lang::get("Code"),'col_attrib'=>array('style'=>''),'is_key'=>true));        
        $tbl->table_set('columns',array("name"=>"sales_receipt_date","label"=>Lang::get(array("Sales Receipt","Date")),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"sales_invoice_code","label"=>Lang::get("Sales Invoice Code"),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"customer_name","label"=>Lang::get("Customer"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('columns',array("name"=>"amount","label"=>Lang::get("Amount "),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"sales_receipt_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('data key','id');
        
        
        
        $param = array(
            'sales_receipt_status'=>Tools::_str(isset($data['sales_receipt_status'])?$data['sales_receipt_status']:''),
            'start_date'=>Tools::_str(isset($data['start_date'])?$data['start_date']:''),
            'end_date'=>Tools::_str(isset($data['end_date'])?$data['end_date']:''),
        );
        $sales_receipt = Rpt_Sales_Data_Support::sales_receipt_get($param);
        if(count($sales_receipt) > 500) $sales_receipt = array_splice($sales_receipt,0,499);
        $footer = array(
            'amount'=>Tools::_float(0),
        );
        foreach($sales_receipt as $idx=>$row){            
            $sales_receipt[$idx]['row_num'] = $idx+1;
            $sales_receipt[$idx]['code'] = '<a href="'.ICES_Engine::$app['app_base_url'].'sales_receipt/view/'.$row['id'].'" target="_blank">'.$sales_receipt[$idx]['code'].'</a>';
            $sales_receipt[$idx]['amount'] = Tools::thousand_separator($sales_receipt[$idx]['amount']);
            $sales_receipt[$idx]['sales_receipt_status'] = SI::get_status_attr(
                SI::type_get('sales_receipt_engine',$sales_receipt[$idx]['sales_receipt_status'],'$status_list')['text']
            );
            $footer['amount'] += Tools::_float($row['amount']);            
        }
        
        if(count($sales_receipt)>0){
            $footer['amount'] = '<strong>'.Tools::thousand_separator($footer['amount']).'</strong>';
            $sales_receipt[] = $footer;
        }
        
        $tbl->table_set('data',$sales_receipt);
        
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
    
    static function sales_return_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_data_support'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Sales_Engine::path_get();
        $id_prefix = Rpt_Sales_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $main_div->datetimepicker_add()->datetimepicker_set('label',Lang::get('Start Date'))
            ->datetimepicker_set('id',$id_prefix.'_start_date')
            ->datetimepicker_set('value',Tools::_date(Date('Y-m-01'),'F d, Y H:i')) 
        ;
        
        $main_div->datetimepicker_add()->datetimepicker_set('label',Lang::get('End Date'))
            ->datetimepicker_set('id',$id_prefix.'_end_date')
            ->datetimepicker_set('value',Tools::_date(Date('Y-m-t 23:59:59'),'F d, Y H:i')) 
        ;
        
        $sales_invoice_status_list = array(
            array('id'=>'all_status','text'=>'<strong>ALL STATUS</strong>'),
            array('id'=>'returned','text'=>SI::get_status_attr('RETURNED')),
            array('id'=>'X','text'=>SI::get_status_attr('CANCELED')),
        );
        
        
        $main_div->input_select_add()
                ->input_select_set('label', Lang::get('Status'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_sales_return_status')
                ->input_select_set('data_add', $sales_invoice_status_list)
                ->input_select_set('value', $sales_invoice_status_list[0])
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
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
        
        
        $js = str_replace(array('<script>','</script>'),'',get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_sales/sales_return_js',$param,true));
        $result['script'].=$js;
        
        
        
        return $result;
        //</editor-fold>
    }
    static function sales_return_rpt_preview_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_data_support'));
        SI::module()->load_class(array('module'=>'sales_return','class_name'=>'sales_return_engine'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Sales_Engine::path_get();
        $id_prefix = Rpt_Sales_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $tbl = $main_div->table_add();
        $tbl->table_set('class','table');
        $tbl->table_set('id',$id_prefix.'_tbl_sales_return_preview');
        $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $tbl->table_set('columns',array("name"=>"code","label"=>Lang::get("Code"),'col_attrib'=>array('style'=>''),'is_key'=>true));        
        $tbl->table_set('columns',array("name"=>"sales_return_date","label"=>Lang::get(array("Sales Receipt","Date")),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"sales_invoice_code","label"=>Lang::get("Sales Invoice Code"),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"customer_name","label"=>Lang::get("Customer"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('columns',array("name"=>"grand_total_amount","label"=>Lang::get("Grand Total Amount"),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"sales_return_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('data key','id');
        
        
        
        $param = array(
            'sales_return_status'=>Tools::_str(isset($data['sales_return_status'])?$data['sales_return_status']:''),
            'start_date'=>Tools::_str(isset($data['start_date'])?$data['start_date']:''),
            'end_date'=>Tools::_str(isset($data['end_date'])?$data['end_date']:''),
        );
        $sales_return = Rpt_Sales_Data_Support::sales_return_get($param);
        if(count($sales_return) > 500) $sales_return = array_splice($sales_return,0,499);
        $footer = array(
            'grand_total_amount'=>Tools::_float(0),
        );
        foreach($sales_return as $idx=>$row){            
            $sales_return[$idx]['row_num'] = $idx+1;
            $sales_return[$idx]['code'] = '<a href="'.ICES_Engine::$app['app_base_url'].'sales_return/view/'.$row['id'].'" target="_blank">'.$sales_return[$idx]['code'].'</a>';
            $sales_return[$idx]['grand_total_amount'] = Tools::thousand_separator($sales_return[$idx]['grand_total_amount']);
            $sales_return[$idx]['sales_return_status'] = SI::get_status_attr(
                SI::type_get('sales_return_engine',$sales_return[$idx]['sales_return_status'],'$status_list')['text']
            );
            $footer['grand_total_amount'] += Tools::_float($row['grand_total_amount']);            
        }
        
        if(count($sales_return)>0){
            $footer['grand_total_amount'] = '<strong>'.Tools::thousand_separator($footer['grand_total_amount']).'</strong>';
            $sales_return[] = $footer;
        }
        
        $tbl->table_set('data',$sales_return);
        
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