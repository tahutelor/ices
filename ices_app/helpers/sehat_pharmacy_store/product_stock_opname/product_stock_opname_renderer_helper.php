<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Stock_Opname_Renderer {

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'product_stock_opname/product_stock_opname_engine');
        //</editor-fold>
    }

    public static function modal_product_stock_opname_render($app, $modal) {
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title' => Lang::get('Product Stock Opname'), 'icon' => APP_ICON::html_get(APP_ICON::product_stock_opname())));
        $modal->width_set('95%');
        $modal->footer_attr_set(array('style'=>'display:none'));
        $components = self::product_stock_opname_components_render($app, $modal, true);
        //</editor-fold>
    }

    public static function product_stock_opname_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Stock_Opname_Engine::path_get();
        
        $back_href = $path->index;
        $id_prefix = Product_Stock_Opname_Engine::$prefix_id;

        $id = $data['id'];
        $components = self::product_stock_opname_components_render($app, $form, false);
        
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

    public static function product_stock_opname_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Stock_Opname_Engine::path_get();
        SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        $components = array();
        $db = new DB();

        $id_prefix = Product_Stock_Opname_Engine::$prefix_id;

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
        
        $form->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('Product Stock Opname','Date')))
                ->datetimepicker_set('id', $id_prefix . '_product_stock_opname_date')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
                ->datetimepicker_set('allow_empty',false)
        ;
        
        $form->input_select_add()
            ->input_select_set('icon',App_Icon::warehouse())
            ->input_select_set('label',' Warehouse')
            ->input_select_set('id',$id_prefix.'_warehouse')
            ->input_select_set('min_length','0')
            ->input_select_set('data_add',Warehouse_Data_Support::input_select_warehouse_list_get())
            ->input_select_set('value',array())
            ->input_select_set('ajax_url','')
            ->input_select_set('disable_all',true)
            ->input_select_set('hide_all',true)
            ->input_select_set('allow_empty', false)           
        ;
        
        $form->input_add()->input_set('label', Lang::get('Checker'))
                ->input_set('id', $id_prefix . '_checker')
                ->input_set('icon', APP_ICON::user())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
                
        ;
        
        $form->input_select_add()
                ->input_select_set('label', Lang::get('Status'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_product_stock_opname_status')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true);
        
        
        
        $form->table_input_add()->table_input_set('id', $id_prefix . '_tbl_pso_product')
                ->main_div_set('class', 'form-group hide_all ')
                ->label_set('value', '')
                ->table_input_set('class','table fixed-table sm-text')
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
                    'th' => array('val' => 'Product', 'visible' => true,'col_style'=>'min-width:400px;width:400px'),
                    'td' => array('val' => '', 'tag' => 'input', 
                        'attr' => array('original' => ''), 
                        'class' => '', 'visible' => true
                    ),
                ))
                
                ->table_input_set('columns', array(
                    'col_id_exists' => true,
                    'col_name' => 'product_batch',
                    'th' => array('val' => Lang::get('Batch Number'), 'visible' => true,'col_style'=>'width:280px'),
                    'td' => array('val' => '', 'tag' => 'input', 
                        'attr' => array('original' => ''),
                        'class' => '', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'old_qty',
                    'th' => array('val' => Lang::get(array('Old','Qty')), 'visible' => true,'col_style'=>'width:150px;text-align:right'),
                    'td' => array('val' => '', 'tag' => 'div','col_style'=>'text-align:right' ,
                        'attr' => array(), 
                        'class' => '', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'qty',
                    'th' => array('val' =>"&Delta;".'Qty', 'visible' => true,'col_style'=>'width:150px;text-align:right'),
                    'td' => array('val' => '', 'tag' => 'div','col_style'=>'text-align:right' ,
                        'attr' => array(), 
                        'class' => '', 'visible' => true
                    ),
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'new_qty',
                    'th' => array('val' => Lang::get(array('New','Qty')), 'visible' => true,'col_style'=>'width:150px;text-align:right'),
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
                
                      
        ;
                
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
            $param['detail_tab'] = '#modal_product_stock_opname .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_product_stock_opname';
        }

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'product_stock_opname/product_stock_opname_pso_product_js', $param, TRUE);
        $app->js_set($js);
        
        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'product_stock_opname/product_stock_opname_basic_function_js', $param, TRUE);
        $app->js_set($js);
        
        return $components;
        //</editor-fold>
    }

    public static function product_stock_opname_status_log_render($app, $form, $data, $path) {
        $config = array(
            'module_name' => 'product_stock_opname',
            'module_engine' => 'product_stock_opname_engine',
            'id' => $data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
    }

    
}

?>