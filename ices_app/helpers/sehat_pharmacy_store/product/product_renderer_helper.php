<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Renderer {

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'product/product_engine');
        //</editor-fold>
    }

    public static function modal_product_render($app, $modal) {
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title' => Lang::get('Product'), 'icon' => 'fa fa-cogs'));
        $modal->width_set('95%');
        $components = self::product_components_render($app, $modal, true);
        //</editor-fold>
    }

    public static function product_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Engine::path_get();

        $id_prefix = Product_Engine::$prefix_id;

        $id = $data['id'];
        $components = self::product_components_render($app, $form, false);
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

    public static function product_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Engine::path_get();
        $components = array();
        $db = new DB();

        $id_prefix = Product_Engine::$prefix_id;

        $components['id'] = $form->input_add()->input_set('id', $id_prefix . '_id')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_add()->input_set('id', $id_prefix . '_method')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('id', $id_prefix . '_code')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;

        $form->input_add()->input_set('label', Lang::get('Name'))
                ->input_set('id', $id_prefix . '_name')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
        ;

        $form->input_select_add()
                ->input_select_set('label', 'Product Category')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_product_category')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty', false)
                ->input_select_set('ajax_url', $path->ajax_search.'product_category_search/')
        ;
        
        $form->input_add()->input_set('label', Lang::get('Barcode'))
                ->input_set('id', $id_prefix . '_barcode')
                ->input_set('icon', APP_ICON::barcode())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('select_on_focus',true)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', 'Unit')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '1')
                ->input_select_set('id', $id_prefix . '_unit')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty', true)
        ;
        
        $form->input_add()->input_set('label', Lang::get('Purchase Amount'))
                ->input_set('id', $id_prefix . '_purchase_amount')
                ->input_set('icon', APP_ICON::money())
                ->input_set('is_numeric',true)
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
        ;
        $form->input_add()->input_set('label', Lang::get('Sales Formula'))
                ->input_set('id', $id_prefix . '_sales_formula')
                ->input_set('icon', APP_ICON::formula())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('placeholder','Use &quot;c&quot; as constant')
        ;
        $form->input_add()->input_set('label', Lang::get('Sales Amount'))
                ->input_set('id', $id_prefix . '_sales_amount')
                ->input_set('icon', APP_ICON::money())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', 'Status')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_product_status')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true)
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
            $param['detail_tab'] = '#modal_product .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_product';
        }

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'product/product_basic_function_js', $param, TRUE);
        $app->js_set($js);
        
        return $components;
        //</editor-fold>
    }

    public static function product_status_log_render($app, $form, $data, $path) {
        //<editor-fold defaultstate="collapsed">
        $config = array(
            'module_name' => 'product',
            'module_engine' => 'product_engine',
            'id' => $data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
        //</editor-fold>
    }
    
    public static function product_batch_render($app, $form, $data, $path){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_engine'));
        $id = $data['id'];
        $db = new DB();
        $q = '
            select distinct null row_num
                ,pb.id
                ,pb.expired_date
                ,pb.batch_number
                ,w.code warehouse_code
                ,w.name warehouse_name
                ,psg.qty qty_stock
                ,pi.id purchase_invoice_id
                ,pi.code purchase_invoice_code
                ,pip.amount purchase_amount
                ,pi.purchase_invoice_date
                ,pb.product_batch_status 
            from product_batch pb
            inner join product_stock_good psg 
                on pb.id = psg.product_batch_id and psg.status > 0
            inner join warehouse w
                on psg.warehouse_id = w.id and w.status > 0
            inner join pi_product pip
                on pb.ref_type="pi_product" and pb.ref_id = pip.id
            inner join purchase_invoice pi
                on pi.id = pip.purchase_invoice_id
            where pb.status > 0
                and pb.product_type = "registered_product" 
                and pb.product_id = '.$db->escape($id).'
            order by pb.id desc
            limit 100
        ';
        $rs = $db->query_array($q);
        for($i = 0;$i<count($rs);$i++){
            $rs[$i]['row_num'] = $i+1;
            $rs[$i]['expired_date'] = Tools::_date($rs[$i]['expired_date'],'F d, Y H:i');
            $rs[$i]['qty_stock'] = Tools::thousand_separator($rs[$i]['qty_stock']);
            $rs[$i]['purchase_amount'] = Tools::thousand_separator($rs[$i]['purchase_amount']);
            $rs[$i]['purchase_invoice_date'] = SI::form_data()->log_description_translate($rs[$i]['purchase_invoice_date']);
            $rs[$i]['warehouse_text'] = Tools::html_tag('strong',$rs[$i]['warehouse_code'])
                .' '.$rs[$i]['warehouse_name'];
            $rs[$i]['batch_number'] = '<a target="_blank" href="'.ICES_Engine::$app['app_base_url'].'product_batch/view/'.$rs[$i]['id'].'">'.$rs[$i]['batch_number'].'</a>';
            $rs[$i]['product_batch_status'] = SI::get_status_attr(
                    SI::type_get('product_batch_engine', $rs[$i]['product_batch_status'],'$status_list')['text']
            );

        }
        $customer_status_log = $rs;

        $table = $form->form_group_add()->table_add();
        $table->table_set('id','supplier_debit_amount_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"batch_number","label"=>"Batch Number",'col_attrib'=>array('style'=>'')));
        $table->table_set('columns',array("name"=>"expired_date","label"=>"Expired Date",'col_attrib'=>array('style'=>'')));
        $table->table_set('columns',array("name"=>"warehouse_text","label"=>"Warehouse",'col_attrib'=>array('style'=>'')));
        $table->table_set('columns',array("name"=>"qty_stock","label"=>"Stock",'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $table->table_set('columns',array("name"=>"purchase_invoice_code","label"=>"Purchase Code",'col_attrib'=>array('style'=>'')));
        $table->table_set('columns',array("name"=>"purchase_invoice_date","label"=>"Purchase Date",'col_attrib'=>array('style'=>'')));
        $table->table_set('columns',array("name"=>"purchase_amount","label"=>"Purchase Amount",'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $table->table_set('columns',array("name"=>"product_batch_status","label"=>"Status",'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        
        $table->table_set('data',$customer_status_log);
        //</editor-fold>
    }

}

?>