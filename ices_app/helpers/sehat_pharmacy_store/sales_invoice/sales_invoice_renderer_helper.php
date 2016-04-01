<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_Invoice_Renderer {

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'sales_invoice/sales_invoice_engine');
        //</editor-fold>
    }

    public static function modal_sales_invoice_render($app, $modal) {
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title' => Lang::get('Sales Invoice'), 'icon' => APP_ICON::html_get(APP_ICON::sales_invoice())));
        $modal->width_set('95%');
        $modal->footer_attr_set(array('style'=>'display:none'));
        $components = self::sales_invoice_components_render($app, $modal, true);
        //</editor-fold>
    }

    public static function sales_invoice_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Invoice_Engine::path_get();
        SI::module()->load_class(array('module'=>'customer','class_name'=>'customer_renderer'));
        
        $id_prefix = Sales_Invoice_Engine::$prefix_id;

        $id = $data['id'];
        $components = self::sales_invoice_components_render($app, $form, false);
        $back_href = $path->index;

        $modal_customer = $app->engine->modal_add()->id_set('modal_customer');
        Customer_Renderer::modal_customer_render($app, $modal_customer);
        
        $form->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('href', $back_href)
                ->button_set('class', 'btn btn-default')
        ;

        $js = '
            <script>
                $("#' . $id_prefix . '_method").val("' . $method . '");
                $("#' . $id_prefix . '_id").val("' . $id . '");
            </script>
        ';
        $app->js_set($js);

        $js = '                
                ' . $id_prefix . '_init();
                ' . $id_prefix . '_bind_event();
                ' . $id_prefix . '_components_prepare(); 
        ';
        $app->js_set($js);
        //</editor-fold>
    }

    public static function sales_invoice_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Invoice_Engine::path_get();
        SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
        SI::module()->load_class(array('module'=>'customer','class_name'=>'customer_data_support'));
        $components = array();
        $db = new DB();

        $id_prefix = Sales_Invoice_Engine::$prefix_id;

        $form->input_add()->input_set('id', $id_prefix . '_id')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_add()->input_set('id', $id_prefix . '_method')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_select_add()
                ->input_select_set('label', Lang::get('Store'))
                ->input_select_set('icon', APP_ICON::store())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_store')
                ->input_select_set('data_add', Store_Data_Support::input_select_store_list_get())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('disable_all', true)
                ->input_select_set('allow_empty', false)
        ;
        
        $form->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('id', $id_prefix . '_code')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $form->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('Sales Invoice','Date')))
                ->datetimepicker_set('id', $id_prefix . '_sales_invoice_date')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
                ->datetimepicker_set('allow_empty',false)
        ;
                
        $form->input_select_detail_add()
            ->input_select_set('icon',App_Icon::customer())
            ->input_select_set('label',' Customer')
            ->input_select_set('id',$id_prefix.'_customer')
            ->input_select_set('min_length','0')
            ->input_select_set('data_add',array())
            ->input_select_set('value',array())
            ->input_select_set('ajax_url',$path->ajax_search.'/input_select_customer_search/')
            ->input_select_set('disable_all',true)
            ->input_select_set('hide_all',true)
            ->detail_set('rows',array())
            ->detail_set('id',$id_prefix.'_customer_detail')
            ->detail_set('ajax_url','')
            ->detail_set('button_new',true)
            ->detail_set('button_new_id',$id_prefix.'_btn_customer_new')
            ->detail_set('button_new_class','btn btn-primary btn-sm')
        ;
        
        $form->table_input_add()->table_input_set('id', $id_prefix . '_tbl_si_product')
                ->main_div_set('class', 'form-group hide_all ')
                ->label_set('value', '')
                ->table_input_set('class','table fixed-table')
                ->table_input_set('style','')
                ->table_input_set('columns', array(
                    'col_name' => 'product_type',
                    'th' => array('val' => '', 'visible' => false,'col_style'=>''),
                    'td' => array('val' => '', 'tag' => 'div', 
                        'attr' => array(), 
                        'class' => '', 'visible' => false
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_id_exists' => true,
                    'col_name' => 'product',
                    'th' => array('val' => 'Product', 'visible' => true,'col_style'=>'min-width:400px'),
                    'td' => array('val' => '', 'tag' => 'input', 
                        'attr' => array('original' => ''), 
                        'class' => '', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_id_exists' => true,
                    'col_name' => 'unit',
                    'th' => array('val' => Lang::get('Unit'), 'visible' => false,'col_style'=>'width:75px'),
                    'td' => array('val' => '', 'tag' => 'div', 
                        'attr' => array(), 
                        'class' => '', 'visible' => false
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'unit_id_sales',
                    'th' => array('val' => Lang::get(''), 'visible' => false,'col_style'=>'width:75px'),
                    'td' => array('val' => '', 'tag' => 'div', 
                        'attr' => array(), 
                        'class' => '', 'visible' => false
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'unit_sales',
                    'th' => array('val' => Lang::get('Unit'), 'visible' => true,'col_style'=>'width:75px'),
                    'td' => array('val' => '', 'tag' => 'div', 
                        'attr' => array(), 
                        'class' => '', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'stock_qty',
                    'th' => array('val' => Lang::get('Stock Qty'), 'visible' => true,'col_style'=>'width:150px;text-align:right'),
                    'td' => array('val' => '', 'tag' => 'div','col_style'=>'text-align:right' ,
                        'attr' => array(), 
                        'class' => '', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'qty',
                    'th' => array('val' => 'Qty', 'visible' => true,'col_style'=>'width:150px;text-align:right'),
                    'td' => array('val' => '', 'tag' => 'input','col_style'=>'text-align:right' ,
                        'attr' => array(), 
                        'class' => 'form-control', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'amount',
                    'th' => array('val' => 'Amount', 'visible' => true,'col_style'=>'width:150px;text-align:right'),
                    'td' => array('val' => '', 'tag' => 'div','col_style'=>'text-align:right' ,
                        'attr' => array(), 
                        'class' => '', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'subtotal_amount',
                    'th' => array('val' => 'Subtotal Amount', 'visible' => true,'col_style'=>'width:150px;text-align:right'),
                    'td' => array('val' => '', 'tag' => 'div','col_style'=>'width:150px;text-align:right',
                        'attr' => array(), 
                        'class' => '', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'constant_sales',
                    'th' => array('val' => '', 'visible' => false,'col_style'=>''),
                    'td' => array('val' => '', 'tag' => 'div','col_style'=>'text-align:right' ,
                        'attr' => array(), 
                        'class' => '', 'visible' => false
                    ),
                ))
                
        ;
        
        $form->input_add()->input_set('label', Lang::get('Total Amount'))
                ->input_set('id', $id_prefix . '_total_amount')
                ->input_set('icon', APP_ICON::dollar())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
                ->input_set('is_numeric',true)
        ;
        
        $form->input_add()->input_set('label', Lang::get('Total Disc. Amount'))
                ->input_set('id', $id_prefix . '_total_discount_amount')
                ->input_set('icon', APP_ICON::dollar())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
                ->input_set('is_numeric',true)
        ;
        
        $form->input_add()->input_set('label', Lang::get('Grand Total Amount'))
                ->input_set('id', $id_prefix . '_grand_total_amount')
                ->input_set('icon', APP_ICON::dollar())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
                ->input_set('is_numeric',true)
        ;
        
        $form->input_add()->input_set('label', Lang::get('Outstanding Grand Total Amount'))
                ->input_set('id', $id_prefix . '_outstanding_grand_total_amount')
                ->input_set('icon', APP_ICON::dollar())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
                ->input_set('is_numeric',true)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', Lang::get('Status'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_sales_invoice_status')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true);
        
        $form->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id',$id_prefix.'_notes')
                ->textarea_set('value','')
                ->textarea_set('hide_all',true)
                ->textarea_set('disable_all',true)
            ;

        $form->hr_add()->hr_set('class', '');

        $form->button_add()->button_set('value', 'Submit')
                ->button_set('id', $id_prefix . '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;

        $form->button_add()->button_set('value', 'Print')
                ->button_set('id', $id_prefix . '_btn_print')
                ->button_set('icon', App_Icon::btn_print())
                ->button_set('class','btn btn-default pull-right hide_all')
                
        ;
        
        $param = array(
            'ajax_url' => $path->index . 'ajax_search/',
            'index_url' => $path->index,
            'detail_tab' => '#detail_tab',
            'view_url' => $path->index . 'view/',
            'window_scroll' => 'body',
            'data_support_url' => $path->index . 'data_support/',
            'common_ajax_listener' => ICES_Engine::$app['app_base_url'] . 'common_ajax_listener/',
            'component_prefix_id' => $id_prefix,
            'si_customer_default'=>  Customer_Data_Support::input_select_si_customer_default_get()
            
            
        );

        if ($is_modal) {
            $param['detail_tab'] = '#modal_sales_invoice .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_sales_invoice';
        }
        
        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'sales_invoice/sales_invoice_basic_function_js', $param, TRUE);
        $app->js_set($js);
        
         $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'sales_invoice/sales_invoice_si_product_js', $param, TRUE);
        $app->js_set($js);
        
        return $components;
        //</editor-fold>
    }

    public static function sales_invoice_status_log_render($app, $form, $data, $path) {
        $config = array(
            'module_name' => 'sales_invoice',
            'module_engine' => 'sales_invoice_engine',
            'id' => $data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
    }

    public static function product_mov_qty_render($app,$form,$data,$path){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
        $id = $data['id'];
        $db = new DB();
        $temp = Sales_Invoice_Data_Support::sales_invoice_get($id);
        if(count($temp)>0) {
            $sales_invoice = $temp['sales_invoice'];
            $si_product_mov_qty = $temp['si_product_mov_qty'];
            
                        
            $tbl = $form->table_add();
            $tbl->table_set('class','table');
            $tbl->table_set('id','si_product_mov_qty_tbl');
            $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
            $tbl->table_set('columns',array("name"=>"product_text","label"=>Lang::get("Product"),'col_attrib'=>array('style'=>'')));
            $tbl->table_set('columns',array("name"=>"batch_number","label"=>Lang::get("Batch Number"),'col_attrib'=>array('style'=>'')));
            //$tbl->table_set('columns',array("name"=>"warehouse_text","label"=>Lang::get("Warehouse"),'col_attrib'=>array('style'=>'')));
            $tbl->table_set('columns',array("name"=>"qty","label"=>Lang::get("Qty"),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
            $tbl->table_set('columns',array("name"=>"unit_text","label"=>Lang::get("Unit"),'col_attrib'=>array('style'=>'')));
            $tbl->table_set('data key','id');
            $tbl->table_set('key_exists',false);

            
            foreach($si_product_mov_qty as $idx=>$row){
                $si_product_mov_qty[$idx]['row_num'] = $idx+1;
                $si_product_mov_qty[$idx]['batch_number'] = Tools::html_tag('strong',$row['batch_number'])
                    .' '.Tools::_date($row['expired_date'],'F d, Y H:i:s')
                ;
                $si_product_mov_qty[$idx]['product_text'] = Tools::html_tag('strong',$row['product_code'])
                    .' '.$row['product_name']
                ;
                $si_product_mov_qty[$idx]['warehouse_text'] = Tools::html_tag('strong',$row['warehouse_code'])
                    .' '.$row['warehouse_name']
                ;
                $si_product_mov_qty[$idx]['qty'] = Tools::thousand_separator($row['qty']);
                $si_product_mov_qty[$idx]['unit_text'] = Tools::html_tag('strong',$row['unit_code'])
                    .' '.$row['unit_name']
                ;
            };
            
            $tbl->table_set('data',$si_product_mov_qty);

        }
        //</editor-fold>
    }
    
    public static function sales_receipt_render($app,$form,$data,$path){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
        SI::module()->load_class(array('module'=>'sales_receipt','class_name'=>'sales_receipt_renderer'));
        $id = $data['id'];
        $db = new DB();
        $temp = Sales_Invoice_Data_Support::sales_invoice_get($id);
        if(count($temp)>0) {
            $sales_invoice = $temp['sales_invoice'];
            
            
            if($sales_invoice['sales_invoice_status'] != 'X' 
                && Tools::_float($sales_invoice['outstanding_grand_total_amount']) > Tools::_float('0')){
                if(Security_Engine::get_controller_permission(ICES_Engine::$app['val'],User_Info::get()['user_id'],'sales_receipt','add')){
                    $form->form_group_add()->custom_component_add()->load_view(false)->innerHTML_set('<br/>');
                $form->button_add()->button_set('class','primary')
                        ->button_set('value',Lang::get(array('New','Sales Receipt')))
                        ->button_set('icon','fa fa-plus')
                        ->button_set('attrib',array(
                            'data-toggle'=>"modal" 
                            ,'data-target'=>"#modal_sales_receipt"
                        ))
                        ->button_set('disable_after_click',false)
                        ->button_set('id','sales_receipt_new')
                    ;
                }
            }
            
            $tbl = $form->table_add();
            $tbl->table_set('class','table');
            $tbl->table_set('id','sales_receipt_table');
            $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
            $tbl->table_set('columns',array("name"=>"code","label"=>Lang::get("Code"),'col_attrib'=>array('style'=>''),'is_key'=>true));
            $tbl->table_set('columns',array("name"=>"sales_receipt_date","label"=>Lang::get("Sales Receipt Date"),'col_attrib'=>array('style'=>'')));
            $tbl->table_set('columns',array("name"=>"amount","label"=>Lang::get("Amount "),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
            $tbl->table_set('columns',array("name"=>"sales_receipt_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
            $tbl->table_set('data key','id');

            $q = '
                select distinct NULL row_num
                    ,pr.*
                from sales_receipt pr
                where pr.status>0
                    and pr.ref_type="sales_invoice" and pr.ref_id = '.$db->escape($id).'
                order by pr.id desc

            ';
            $rs = $db->query_array($q);
            for($i = 0;$i<count($rs);$i++){
                $rs[$i]['row_num'] = $i+1;
                $rs[$i]['amount'] = Tools::thousand_separator($rs[$i]['amount'],2,true);
                $rs[$i]['sales_receipt_status'] = SI::get_status_attr(
                    SI::status_get('Sales_Receipt_Engine', $rs[$i]['sales_receipt_status'])['text']
                );
                $rs[$i]['sales_receipt_date'] = Tools::_date($rs[$i]['sales_receipt_date'],'F d, Y H:i:s');
            }
            $tbl->table_set('data',$rs);

            
            $modal_sales_receipt = $app->engine->modal_add()->id_set('modal_sales_receipt');

            Sales_Receipt_Renderer::modal_sales_receipt_render(
                    $app
                    ,$modal_sales_receipt
                );


            $param = array(
                'index_url'=>$path->index
                ,'ajax_search'=>$path->ajax_search
                ,'ref_text'=>Tools::html_tag('strong',$sales_invoice['code'])
                    .' '.'Grand Total Amount: '.Tools::thousand_separator($sales_invoice['grand_total_amount'])
                ,'ref_type'=>'sales_invoice'
                ,'ref_id'=>$sales_invoice['id']
            );

            $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'sales_invoice/sales_receipt_js',$param,TRUE);
            $app->js_set($js);
            

        }
        //</editor-fold>
    }
    
    
    
}

?>