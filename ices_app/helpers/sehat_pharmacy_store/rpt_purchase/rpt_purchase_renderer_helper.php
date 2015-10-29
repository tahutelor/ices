<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Purchase_Renderer {

    public static function modal_rpt_purchase_render($app,$modal){
        $modal->header_set(array('title'=>'System Investigation Report','icon'=>App_Icon::report()));
        $components = self::rpt_purchase_components_render($app, $modal,true);
    }

    public static function rpt_purchase_render($app,$form,$data,$path,$method){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_engine'));
        $id_prefix = Rpt_Purchase_Engine::$prefix_id;
        $path = Rpt_Purchase_Engine::path_get();
        $id = $data['id'];
        $components = self::rpt_purchase_components_render($app, $form,false);
        $back_href = $path->index;

        $js = '
            <script>
                $("#rpt_purchase_method").val("'.$method.'");
                $("#rpt_purchase_id").val("'.$id.'");
            </script>
        ';             
        $app->js_set($js);

        $js = '                
                rpt_purchase_init();
                rpt_purchase_bind_event();
                rpt_purchase_components_prepare(); 
        ';
        $app->js_set($js);
        //</editor-fold>
    }

    public static function rpt_purchase_components_render($app,$form,$is_modal){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_engine'));
        $path = Rpt_Purchase_Engine::path_get();            
        $components = array();
        $db = new DB();

        $id_prefix = Rpt_Purchase_Engine::$prefix_id;

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

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_purchase/'.$id_prefix.'_basic_function_js',$param,TRUE);
        $app->js_set($js);
        return $components;
        //</editor-fold>
    }

    public static function form_render($module_name,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_engine'));
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_data_support'));
        
        $result = array('html'=>'','script'=>'');        
        if(method_exists('Rpt_Purchase_Renderer', $module_name.'_render')){
            $result = eval('return self::'.$module_name.'_render(false,$data);');
        }
        return $result;
        //</editor-fold>
    }
    
    static function purchase_invoice_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_data_support'));
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_engine'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Purchase_Engine::path_get();
        $id_prefix = Rpt_Purchase_Engine::$prefix_id;
        
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
        
        $purchase_invoice_status_list = array(
            array('id'=>'all_status','text'=>'<strong>ALL STATUS</strong>'),
            array('id'=>'invoiced','text'=>SI::get_status_attr('INVOICED')),
            array('id'=>'X','text'=>SI::get_status_attr('CANCELED')),
        );
        
        $supp_data = array(
            array('id'=>'all_supplier','text'=>Tools::html_tag('strong','All Supplier'))
        );
        
        $main_div->input_select_add()
            ->input_select_set('icon',App_Icon::supplier())
            ->input_select_set('label',' Supplier')
            ->input_select_set('id',$id_prefix.'_supplier')
            ->input_select_set('min_length','0')
            ->input_select_set('data_add',$supp_data)
            ->input_select_set('value',$supp_data[0])
            ->input_select_set('ajax_url',$path->ajax_search.'input_select_supplier_search')
            ->input_select_set('disable_all',true)
            ->input_select_set('hide_all',true)
            ->input_select_set('allow_empty',false)
        ;
        
        $main_div->input_select_add()
                ->input_select_set('label', Lang::get('Status'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_purchase_invoice_status')
                ->input_select_set('data_add', $purchase_invoice_status_list)
                ->input_select_set('value', $purchase_invoice_status_list[0])
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
        
        
        $js = str_replace(array('<script>','</script>'),'',get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_purchase/purchase_invoice_js',$param,true));
        $result['script'].=$js;
        
        
        
        return $result;
        //</editor-fold>
    }

    static function purchase_invoice_rpt_preview_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_data_support'));
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_engine'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Purchase_Engine::path_get();
        $id_prefix = Rpt_Purchase_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $tbl = $main_div->table_add();
        $tbl->table_set('class','table');
        $tbl->table_set('id',$id_prefix.'_tbl_purchase_invoice_preview');
        $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $tbl->table_set('columns',array("name"=>"code","label"=>Lang::get("Code"),'col_attrib'=>array('style'=>''),'is_key'=>true));        
        $tbl->table_set('columns',array("name"=>"purchase_invoice_date","label"=>Lang::get(array("Purchase Invoice","Date")),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"supplier_name","label"=>Lang::get("Supplier"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('columns',array("name"=>"grand_total_amount","label"=>Lang::get("Grand Total Amount "),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"outstanding_grand_total_amount","label"=>Lang::get("Outstanding Grand Total Amount "),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"purchase_invoice_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('data key','id');
        
        $param = array(
            'purchase_invoice_status'=>Tools::_str(isset($data['purchase_invoice_status'])?$data['purchase_invoice_status']:''),
            'start_date'=>Tools::_str(isset($data['start_date'])?$data['start_date']:''),
            'end_date'=>Tools::_str(isset($data['end_date'])?$data['end_date']:''),
            'supplier_id'=>Tools::_str(isset($data['supplier_id'])?$data['supplier_id']:''),            
        );
        $purchase_invoice = Rpt_Purchase_Data_Support::purchase_invoice_get($param);
        if(count($purchase_invoice) > 500) $purchase_invoice = array_splice($purchase_invoice,0,499);
        $footer = array(
            'grand_total_amount'=>Tools::_float(0),
            'outstanding_grand_total_amount'=>Tools::_float(0),
        );
        foreach($purchase_invoice as $idx=>$row){            
            $purchase_invoice[$idx]['row_num'] = $idx+1;
            $purchase_invoice[$idx]['code'] = '<a href="'.ICES_Engine::$app['app_base_url'].'purchase_invoice/view/'.$row['id'].'" target="_blank">'.$purchase_invoice[$idx]['code'].'</a>';
            $purchase_invoice[$idx]['grand_total_amount'] = Tools::thousand_separator($purchase_invoice[$idx]['grand_total_amount']);
            $purchase_invoice[$idx]['outstanding_grand_total_amount'] = Tools::thousand_separator($purchase_invoice[$idx]['outstanding_grand_total_amount']);
            $purchase_invoice[$idx]['purchase_invoice_status'] = SI::get_status_attr(
                SI::type_get('purchase_invoice_engine',$purchase_invoice[$idx]['purchase_invoice_status'],'$status_list')['text']
            );
            $footer['grand_total_amount'] += Tools::_float($row['grand_total_amount']);
            $footer['outstanding_grand_total_amount'] += Tools::_float($row['outstanding_grand_total_amount']);
        }
        if(count($purchase_invoice)>0){
            $footer['grand_total_amount'] = '<strong>'.Tools::thousand_separator($footer['grand_total_amount']).'</strong>';
            $footer['outstanding_grand_total_amount'] = '<strong>'.Tools::thousand_separator($footer['outstanding_grand_total_amount']).'</strong>';
            $purchase_invoice[] = $footer;
        }
        
        $tbl->table_set('data',$purchase_invoice);
        
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
    
    static function purchase_receipt_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_data_support'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Purchase_Engine::path_get();
        $id_prefix = Rpt_Purchase_Engine::$prefix_id;
        
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
        
        $purchase_invoice_status_list = array(
            array('id'=>'all_status','text'=>'<strong>ALL STATUS</strong>'),
            array('id'=>'invoiced','text'=>SI::get_status_attr('INVOICED')),
            array('id'=>'X','text'=>SI::get_status_attr('CANCELED')),
        );
        
        
        $main_div->input_select_add()
                ->input_select_set('label', Lang::get('Status'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_purchase_receipt_status')
                ->input_select_set('data_add', $purchase_invoice_status_list)
                ->input_select_set('value', $purchase_invoice_status_list[0])
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
        
        
        $js = str_replace(array('<script>','</script>'),'',get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_purchase/purchase_receipt_js',$param,true));
        $result['script'].=$js;
        
        
        
        return $result;
        //</editor-fold>
    }
    static function purchase_receipt_rpt_preview_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_data_support'));
        SI::module()->load_class(array('module'=>'purchase_receipt','class_name'=>'purchase_receipt_engine'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Purchase_Engine::path_get();
        $id_prefix = Rpt_Purchase_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $tbl = $main_div->table_add();
        $tbl->table_set('class','table');
        $tbl->table_set('id',$id_prefix.'_tbl_purchase_receipt_preview');
        $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $tbl->table_set('columns',array("name"=>"code","label"=>Lang::get("Code"),'col_attrib'=>array('style'=>''),'is_key'=>true));        
        $tbl->table_set('columns',array("name"=>"purchase_receipt_date","label"=>Lang::get(array("Purchase Receipt","Date")),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"purchase_invoice_code","label"=>Lang::get("Purchase Invoice Code"),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"supplier_name","label"=>Lang::get("Supplier"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('columns',array("name"=>"amount","label"=>Lang::get("Amount "),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"purchase_receipt_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('data key','id');
        
        
        
        $param = array(
            'purchase_receipt_status'=>Tools::_str(isset($data['purchase_receipt_status'])?$data['purchase_receipt_status']:''),
            'start_date'=>Tools::_str(isset($data['start_date'])?$data['start_date']:''),
            'end_date'=>Tools::_str(isset($data['end_date'])?$data['end_date']:''),
        );
        $purchase_receipt = Rpt_Purchase_Data_Support::purchase_receipt_get($param);
        if(count($purchase_receipt) > 500) $purchase_receipt = array_splice($purchase_receipt,0,499);
        $footer = array(
            'amount'=>Tools::_float(0),
        );
        foreach($purchase_receipt as $idx=>$row){            
            $purchase_receipt[$idx]['row_num'] = $idx+1;
            $purchase_receipt[$idx]['code'] = '<a href="'.ICES_Engine::$app['app_base_url'].'purchase_receipt/view/'.$row['id'].'" target="_blank">'.$purchase_receipt[$idx]['code'].'</a>';
            $purchase_receipt[$idx]['amount'] = Tools::thousand_separator($purchase_receipt[$idx]['amount']);
            $purchase_receipt[$idx]['purchase_receipt_status'] = SI::get_status_attr(
                SI::type_get('purchase_receipt_engine',$purchase_receipt[$idx]['purchase_receipt_status'],'$status_list')['text']
            );
            $footer['amount'] += Tools::_float($row['amount']);            
        }
        
        if(count($purchase_receipt)>0){
            $footer['amount'] = '<strong>'.Tools::thousand_separator($footer['amount']).'</strong>';
            $purchase_receipt[] = $footer;
        }
        
        $tbl->table_set('data',$purchase_receipt);
        
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
    
    static function purchase_return_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_data_support'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Purchase_Engine::path_get();
        $id_prefix = Rpt_Purchase_Engine::$prefix_id;
        
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
        
        $purchase_invoice_status_list = array(
            array('id'=>'all_status','text'=>'<strong>ALL STATUS</strong>'),
            array('id'=>'returned','text'=>SI::get_status_attr('RETURNED')),
            array('id'=>'X','text'=>SI::get_status_attr('CANCELED')),
        );
        
        
        $main_div->input_select_add()
                ->input_select_set('label', Lang::get('Status'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_purchase_return_status')
                ->input_select_set('data_add', $purchase_invoice_status_list)
                ->input_select_set('value', $purchase_invoice_status_list[0])
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
        
        
        $js = str_replace(array('<script>','</script>'),'',get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'rpt_purchase/purchase_return_js',$param,true));
        $result['script'].=$js;
        
        
        
        return $result;
        //</editor-fold>
    }
    static function purchase_return_rpt_preview_render($is_modal,$data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_data_support'));
        SI::module()->load_class(array('module'=>'purchase_return','class_name'=>'purchase_return_engine'));
        $result = array('html'=>'','script'=>'');
        $path = Rpt_Purchase_Engine::path_get();
        $id_prefix = Rpt_Purchase_Engine::$prefix_id;
        
        $app = new App();        
        
        $main_div = $app->engine->div_add();        
        
        $tbl = $main_div->table_add();
        $tbl->table_set('class','table');
        $tbl->table_set('id',$id_prefix.'_tbl_purchase_return_preview');
        $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $tbl->table_set('columns',array("name"=>"code","label"=>Lang::get("Code"),'col_attrib'=>array('style'=>''),'is_key'=>true));        
        $tbl->table_set('columns',array("name"=>"purchase_return_date","label"=>Lang::get(array("Purchase Receipt","Date")),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"purchase_invoice_code","label"=>Lang::get("Purchase Invoice Code"),'col_attrib'=>array('style'=>'')));        
        $tbl->table_set('columns',array("name"=>"supplier_name","label"=>Lang::get("Supplier"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('columns',array("name"=>"grand_total_amount","label"=>Lang::get("Grand Total Amount"),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $tbl->table_set('columns',array("name"=>"purchase_return_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
        $tbl->table_set('data key','id');
        
        
        
        $param = array(
            'purchase_return_status'=>Tools::_str(isset($data['purchase_return_status'])?$data['purchase_return_status']:''),
            'start_date'=>Tools::_str(isset($data['start_date'])?$data['start_date']:''),
            'end_date'=>Tools::_str(isset($data['end_date'])?$data['end_date']:''),
        );
        $purchase_return = Rpt_Purchase_Data_Support::purchase_return_get($param);
        if(count($purchase_return) > 500) $purchase_return = array_splice($purchase_return,0,499);
        $footer = array(
            'grand_total_amount'=>Tools::_float(0),
        );
        foreach($purchase_return as $idx=>$row){            
            $purchase_return[$idx]['row_num'] = $idx+1;
            $purchase_return[$idx]['code'] = '<a href="'.ICES_Engine::$app['app_base_url'].'purchase_return/view/'.$row['id'].'" target="_blank">'.$purchase_return[$idx]['code'].'</a>';
            $purchase_return[$idx]['grand_total_amount'] = Tools::thousand_separator($purchase_return[$idx]['grand_total_amount']);
            $purchase_return[$idx]['purchase_return_status'] = SI::get_status_attr(
                SI::type_get('purchase_return_engine',$purchase_return[$idx]['purchase_return_status'],'$status_list')['text']
            );
            $footer['grand_total_amount'] += Tools::_float($row['grand_total_amount']);            
        }
        
        if(count($purchase_return)>0){
            $footer['grand_total_amount'] = '<strong>'.Tools::thousand_separator($footer['grand_total_amount']).'</strong>';
            $purchase_return[] = $footer;
        }
        
        $tbl->table_set('data',$purchase_return);
        
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