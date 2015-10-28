<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Batch_Renderer {

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'product_batch/product_batch_engine');
        //</editor-fold>
    }

    public static function modal_product_batch_render($app, $modal) {
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title' => Lang::get('Product'), 'icon' => 'fa fa-cogs'));
        $modal->width_set('95%');
        $components = self::product_batch_components_render($app, $modal, true);
        //</editor-fold>
    }

    public static function product_batch_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Batch_Engine::path_get();

        $id_prefix = Product_Batch_Engine::$prefix_id;

        $id = $data['id'];
        $components = self::product_batch_components_render($app, $form, false);
        $back_href = $path->index;

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

    public static function product_batch_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Batch_Engine::path_get();
        $components = array();
        $db = new DB();

        $id_prefix = Product_Batch_Engine::$prefix_id;

        $components['id'] = $form->input_add()->input_set('id', $id_prefix . '_id')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_add()->input_set('id', $id_prefix . '_method')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_select_add()
                ->input_select_set('label', 'Product')
                ->input_select_set('icon', APP_ICON::product())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_product')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('disable_all', true)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', 'Unit')
                ->input_select_set('icon', APP_ICON::unit())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_unit')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('disable_all', true)
        ;
        
        $form->input_add()->input_set('label', Lang::get('Batch Number'))
                ->input_set('id', $id_prefix . '_batch_number')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;

        $form->input_add()->input_set('label', Lang::get('Expired Date'))
                ->input_set('id', $id_prefix . '_expired_date')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
        ;

        $form->input_select_add()
                ->input_select_set('label', 'Status')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_product_batch_status')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true)
        ;
        
        $form->table_input_add()->table_input_set('id', $id_prefix . '_tbl_product_stock')
                ->main_div_set('class', 'form-group hide_all ')
                ->label_set('value', '')
                ->table_input_set('class','table fixed-table sm-text')
                ->table_input_set('style','')
                
                ->table_input_set('columns', array(
                    'col_name' => 'warehouse',
                    'th' => array('val' => 'Warehouse', 'visible' => true,'col_style'=>''),
                    'td' => array('val' => '', 'tag' => 'div', 
                        'attr' => array(), 
                        'class' => '', 'visible' => true
                    ),
                ))                
                ->table_input_set('columns', array(
                    'col_name' => 'qty',
                    'th' => array('val' => Lang::get('Available Qty'), 'visible' => true,'col_style'=>'width:150px;text-align:right'),
                    'td' => array('val' => '', 'tag' => 'div','col_style'=>'text-align:right' ,
                        'attr' => array(), 
                        'class' => '', 'visible' => true
                    ),
                ))
                
        ;

        $form->textarea_add()->textarea_set('label', 'Notes')
                ->textarea_set('id', $id_prefix . '_notes')
                ->textarea_set('value', '')
                ->textarea_set('hide_all', true)
                ->textarea_set('disable_all', true)
        ;

        $form->hr_add()->hr_set('class', '');

        $form->button_add()->button_set('value', 'Submit')
                ->button_set('id', $id_prefix . '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;

        $param = array(
            'ajax_url' => $path->index . 'ajax_search/'
            , 'index_url' => $path->index
            , 'detail_tab' => '#detail_tab'
            , 'view_url' => $path->index . 'view/'
            , 'window_scroll' => 'body'
            , 'data_support_url' => $path->index . 'data_support/'
            , 'common_ajax_listener' => ICES_Engine::$app['app_base_url'] . 'common_ajax_listener/'
            , 'component_prefix_id' => $id_prefix
        );

        if ($is_modal) {
            $param['detail_tab'] = '#modal_product_batch .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_product_batch';
        }

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'product_batch/product_batch_product_stock_js', $param, TRUE);
        $app->js_set($js);
        
        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'product_batch/product_batch_basic_function_js', $param, TRUE);
        $app->js_set($js);
        

        return $components;
        //</editor-fold>
    }

    public static function product_batch_status_log_render($app, $form, $data, $path) {
        //<editor-fold defaultstate="collapsed">
        $config = array(
            'module_name' => 'product_batch',
            'module_engine' => 'product_batch_engine',
            'id' => $data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
        //</editor-fold>
    }

    public static function product_stock_log_render($app, $form, $data, $path) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_engine'));
        
        $path = Product_Batch_Engine::path_get();
        $id_prefix = 'psl';
        
        //$form->custom_component_add()->load_view(false)->innerHTML_set('<div class="form-group"><br/></div>');
        
         $form->input_add()->input_set('label', Lang::get(''))
                ->input_set('id', $id_prefix . '_product_batch_id')
                ->input_set('icon', '')
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('hide', true)
                ->input_set('value',$data['id'])
        ;
        
        $warehouse_list = array(
            array('id'=>'all','text'=>'All Warehouse'),
        );
        $warehouse_list = array_merge($warehouse_list, Warehouse_Data_Support::input_select_warehouse_list_get());
        $form->input_select_add()
                ->input_select_set('label', 'Warehouse')
                ->input_select_set('icon', APP_ICON::product())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_warehouse')
                ->input_select_set('data_add', $warehouse_list)
                ->input_select_set('value', $warehouse_list[0])
                ->input_select_set('hide_all', true)
                ->input_select_set('disable_all', true)
                ->input_select_set('allow_empty', false)
        ;
        
        $cols = array(
            array('name' => 'warehouse_text', 'label' => Lang::get('Warehouse'), 'data_type' => 'text'),
            array('name' => 'moddate', 'label' => Lang::get('Date'), 'data_type' => 'text'),
            array('name' => 'old_qty', 'label' => Lang::get(array('Old Qty')), 'data_type' => 'text', 'attribute' => array('style' => 'text-align:right'), 'row_attrib' => array('style' => 'text-align:right')),
            array('name' => 'qty', 'label' => Lang::get(array('&Delta; Qty')), 'data_type' => 'text', 'attribute' => array('style' => 'text-align:right'), 'row_attrib' => array('style' => 'text-align:right')),
            array('name' => 'new_qty', 'label' => Lang::get(array('New Qty')), 'data_type' => 'text', 'attribute' => array('style' => 'text-align:right'), 'row_attrib' => array('style' => 'text-align:right')),
            array('name' => 'description', 'label' => Lang::get('Description'), 'data_type' => 'text'),
        );
        
        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', $id_prefix.'_product_stock_table')
                ->table_ajax_set('lookup_url', $path->index . 'ajax_search/product_stock')
                ->table_ajax_set('columns', $cols)
                ->table_ajax_set('key_exists', false)
                ->filter_set(
                    array(
                        array('id'=>'psl_product_batch_id','field'=>'product_batch_id','type'=>'input')
                        ,array('id'=>'psl_warehouse','field'=>'warehouse_id','type'=>'select2')
                    )
                )

                ;
        ;
        
        $js = '
            $("#psl_warehouse").on("change",function(){
                psl_product_stock_table.methods.data_show(1);
            });
        ';
        
        $app->js_set($js);
        
        //</editor-fold>
    }

}

?>