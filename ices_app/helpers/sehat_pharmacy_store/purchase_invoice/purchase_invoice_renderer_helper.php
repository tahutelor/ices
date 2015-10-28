<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Purchase_Invoice_Renderer {

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'purchase_invoice/purchase_invoice_engine');
        //</editor-fold>
    }

    public static function modal_purchase_invoice_render($app, $modal) {
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title' => Lang::get('Purchase Invoice'), 'icon' => APP_ICON::html_get(APP_ICON::purchase_invoice())));
        $modal->width_set('95%');
        $modal->footer_attr_set(array('style'=>'display:none'));
        $components = self::purchase_invoice_components_render($app, $modal, true);
        //</editor-fold>
    }

    public static function purchase_invoice_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Invoice_Engine::path_get();
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_renderer'));
        
        $id_prefix = Purchase_Invoice_Engine::$prefix_id;

        $id = $data['id'];
        $components = self::purchase_invoice_components_render($app, $form, false);
        $back_href = $path->index;

        $modal_supplier = $app->engine->modal_add()->id_set('modal_supplier');
        Supplier_Renderer::modal_supplier_render($app, $modal_supplier);
        
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

    public static function purchase_invoice_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Invoice_Engine::path_get();
        SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
        $components = array();
        $db = new DB();

        $id_prefix = Purchase_Invoice_Engine::$prefix_id;

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
        
        $form->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('Purchase Invoice','Date')))
                ->datetimepicker_set('id', $id_prefix . '_purchase_invoice_date')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
                ->datetimepicker_set('allow_empty',false)
        ;
        
        $form->input_select_detail_add()
            ->input_select_set('icon',App_Icon::supplier())
            ->input_select_set('label',' Supplier')
            ->input_select_set('id',$id_prefix.'_supplier')
            ->input_select_set('min_length','0')
            ->input_select_set('data_add',array())
            ->input_select_set('value',array())
            ->input_select_set('ajax_url',$path->ajax_search.'/input_select_supplier_search/')
            ->input_select_set('disable_all',true)
            ->input_select_set('hide_all',true)
            ->detail_set('rows',array())
            ->detail_set('id',$id_prefix.'_supplier_detail')
            ->detail_set('ajax_url','')
            ->detail_set('button_new',true)
            ->detail_set('button_new_id',$id_prefix.'_btn_supplier_new')
            ->detail_set('button_new_class','btn btn-primary btn-sm')
        ;
        
        $form->input_add()->input_set('label', Lang::get('Sales Invoice').' '.Lang::get('Supplier'))
                ->input_set('id', $id_prefix . '_supplier_si_code')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
        ;
        
        $form->table_input_add()->table_input_set('id', $id_prefix . '_tbl_purchase_invoice_product')
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
                    'col_name' => 'expired_date',
                    'th' => array('val' => Lang::get('Expired Date'), 'visible' => true,'col_style'=>'width:200px'),
                    'td' => array('val' => '', 'tag' => 'input', 
                        'attr' => array(), 
                        'class' => 'form-control', 'visible' => true
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
                    'col_id_exists' => true,
                    'col_name' => 'unit',
                    'th' => array('val' => Lang::get('Unit'), 'visible' => true,'col_style'=>'width:75px'),
                    'td' => array('val' => '', 'tag' => 'div', 
                        'attr' => array(), 
                        'class' => '', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'amount',
                    'th' => array('val' => 'Amount', 'visible' => true,'col_style'=>'width:150px;text-align:right'),
                    'td' => array('val' => '', 'tag' => 'input','col_style'=>'text-align:right' ,
                        'attr' => array(), 
                        'class' => 'form-control', 'visible' => true
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
        
        $form->input_add()->input_set('label', Lang::get('Additional Cost Amount'))
                ->input_set('id', $id_prefix . '_additional_cost_amount')
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
                ->input_select_set('id', $id_prefix . '_purchase_invoice_status')
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

        $param = array(
            'ajax_url' => $path->index . 'ajax_search/',
            'index_url' => $path->index,
            'detail_tab' => '#detail_tab',
            'view_url' => $path->index . 'view/',
            'window_scroll' => 'body',
            'data_support_url' => $path->index . 'data_support/',
            'common_ajax_listener' => ICES_Engine::$app['app_base_url'] . 'common_ajax_listener/',
            'component_prefix_id' => $id_prefix,
            
            
        );

        if ($is_modal) {
            $param['detail_tab'] = '#modal_purchase_invoice .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_purchase_invoice';
        }

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'purchase_invoice/purchase_invoice_purchase_invoice_product_js', $param, TRUE);
        $app->js_set($js);
        
        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'purchase_invoice/purchase_invoice_basic_function_js', $param, TRUE);
        $app->js_set($js);
        
        return $components;
        //</editor-fold>
    }

    public static function purchase_invoice_status_log_render($app, $form, $data, $path) {
        $config = array(
            'module_name' => 'purchase_invoice',
            'module_engine' => 'purchase_invoice_engine',
            'id' => $data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
    }

    public static function purchase_receipt_render($app,$form,$data,$path){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        SI::module()->load_class(array('module'=>'purchase_receipt','class_name'=>'purchase_receipt_renderer'));
        $id = $data['id'];
        $db = new DB();
        $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($id);
        if(count($temp)>0) {
            $purchase_invoice = $temp['purchase_invoice'];
            
            
            if($purchase_invoice['purchase_invoice_status'] != 'X' 
                && Tools::_float($purchase_invoice['outstanding_grand_total_amount']) > Tools::_float('0')){
                if(Security_Engine::get_controller_permission(ICES_Engine::$app['val'],User_Info::get()['user_id'],'purchase_receipt','add')){
                    $form->form_group_add()->custom_component_add()->load_view(false)->innerHTML_set('<br/>');
                $form->button_add()->button_set('class','primary')
                        ->button_set('value',Lang::get(array('New','Purchase Receipt')))
                        ->button_set('icon','fa fa-plus')
                        ->button_set('attrib',array(
                            'data-toggle'=>"modal" 
                            ,'data-target'=>"#modal_purchase_receipt"
                        ))
                        ->button_set('disable_after_click',false)
                        ->button_set('id','purchase_receipt_new')
                    ;
                }
            }
            
            $tbl = $form->table_add();
            $tbl->table_set('class','table');
            $tbl->table_set('id','purchase_receipt_table');
            $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
            $tbl->table_set('columns',array("name"=>"code","label"=>Lang::get("Code"),'col_attrib'=>array('style'=>''),'is_key'=>true));
            $tbl->table_set('columns',array("name"=>"purchase_receipt_date","label"=>Lang::get("Purchase Receipt Date"),'col_attrib'=>array('style'=>'')));
            $tbl->table_set('columns',array("name"=>"amount","label"=>Lang::get("Amount "),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
            $tbl->table_set('columns',array("name"=>"purchase_receipt_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
            $tbl->table_set('data key','id');

            $q = '
                select distinct NULL row_num
                    ,pr.*
                from purchase_receipt pr
                where pr.status>0
                    and pr.ref_type="purchase_invoice" and pr.ref_id = '.$db->escape($id).'
                order by pr.id desc

            ';
            $rs = $db->query_array($q);
            for($i = 0;$i<count($rs);$i++){
                $rs[$i]['row_num'] = $i+1;
                $rs[$i]['amount'] = Tools::thousand_separator($rs[$i]['amount'],2,true);
                $rs[$i]['purchase_receipt_status'] = SI::get_status_attr(
                    SI::status_get('Purchase_Receipt_Engine', $rs[$i]['purchase_receipt_status'])['text']
                );
                $rs[$i]['purchase_receipt_date'] = Tools::_date($rs[$i]['purchase_receipt_date'],'F d, Y H:i:s');
            }
            $tbl->table_set('data',$rs);

            
            $modal_purchase_receipt = $app->engine->modal_add()->id_set('modal_purchase_receipt');

            Purchase_Receipt_Renderer::modal_purchase_receipt_render(
                    $app
                    ,$modal_purchase_receipt
                );


            $param = array(
                'index_url'=>$path->index
                ,'ajax_search'=>$path->ajax_search
                ,'ref_text'=>Tools::html_tag('strong',$purchase_invoice['code'])
                    .' '.'Grand Total Amount: '.Tools::thousand_separator($purchase_invoice['grand_total_amount'])
                ,'ref_type'=>'purchase_invoice'
                ,'ref_id'=>$purchase_invoice['id']
            );

            $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'purchase_invoice/purchase_receipt_js',$param,TRUE);
            $app->js_set($js);
            

        }
        //</editor-fold>
    }
    
    public static function purchase_return_render($app,$form,$data,$path){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        SI::module()->load_class(array('module'=>'purchase_return','class_name'=>'purchase_return_renderer'));
        $id = $data['id'];
        $db = new DB();
        $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($id);
        if(count($temp)>0) {
            $purchase_invoice = $temp['purchase_invoice'];
            
            
            if($purchase_invoice['purchase_invoice_status'] != 'X' 
                && Tools::_float($purchase_invoice['outstanding_grand_total_amount']) > Tools::_float('0')){
                if(Security_Engine::get_controller_permission(ICES_Engine::$app['val'],User_Info::get()['user_id'],'purchase_return','add')){
                    $form->form_group_add()->custom_component_add()->load_view(false)->innerHTML_set('<br/>');
                $form->button_add()->button_set('class','primary')
                        ->button_set('value',Lang::get(array('New','Purchase Return')))
                        ->button_set('icon','fa fa-plus')
                        ->button_set('attrib',array(
                            'data-toggle'=>"modal" 
                            ,'data-target'=>"#modal_purchase_return"
                        ))
                        ->button_set('disable_after_click',false)
                        ->button_set('id','purchase_return_new')
                    ;
                }
            }
            
            $tbl = $form->table_add();
            $tbl->table_set('class','table');
            $tbl->table_set('id','purchase_return_table');
            $tbl->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
            $tbl->table_set('columns',array("name"=>"code","label"=>Lang::get("Code"),'col_attrib'=>array('style'=>''),'is_key'=>true));
            $tbl->table_set('columns',array("name"=>"purchase_return_date","label"=>Lang::get("Purchase Return Date"),'col_attrib'=>array('style'=>'')));
            $tbl->table_set('columns',array("name"=>"grand_total_amount","label"=>Lang::get("Grand Total Amount "),'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
            $tbl->table_set('columns',array("name"=>"purchase_return_status","label"=>Lang::get("Status"),'attribute'=>'style=""','col_attrib'=>array('style'=>'')));
            $tbl->table_set('data key','id');

            $q = '
                select distinct NULL row_num
                    ,pr.*
                from purchase_return pr
                where pr.status>0
                    and pr.ref_type="purchase_invoice" and pr.ref_id = '.$db->escape($id).'
                order by pr.id desc

            ';
            $rs = $db->query_array($q);
            for($i = 0;$i<count($rs);$i++){
                $rs[$i]['row_num'] = $i+1;
                $rs[$i]['grand_total_amount'] = Tools::thousand_separator($rs[$i]['grand_total_amount'],2,true);
                $rs[$i]['purchase_return_status'] = SI::get_status_attr(
                    SI::status_get('Purchase_Return_Engine', $rs[$i]['purchase_return_status'])['text']
                );
                $rs[$i]['purchase_return_date'] = Tools::_date($rs[$i]['purchase_return_date'],'F d, Y H:i:s');
            }
            $tbl->table_set('data',$rs);

            
            $modal_purchase_return = $app->engine->modal_add()->id_set('modal_purchase_return');

            Purchase_Return_Renderer::modal_purchase_return_render(
                    $app
                    ,$modal_purchase_return
                );


            $param = array(
                'index_url'=>$path->index
                ,'ajax_search'=>$path->ajax_search
                ,'ref_text'=>Tools::html_tag('strong',$purchase_invoice['code'])
                    .' '.'Grand Total Amount: '.Tools::thousand_separator($purchase_invoice['grand_total_amount'])
                ,'ref_type'=>'purchase_invoice'
                ,'ref_id'=>$purchase_invoice['id']
            );

            $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'purchase_invoice/purchase_return_js',$param,TRUE);
            $app->js_set($js);
            

        }
        //</editor-fold>
    }
    
}

?>