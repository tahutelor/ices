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
        SI::module()->load_class(array('module'=>'unit','class_name'=>'unit_data_support'));
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
        $unit_list = Unit_Data_Support::input_select_unit_list_get();
        $form->input_select_add()
                ->input_select_set('label', 'Unit')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_unit')
                ->input_select_set('data_add', $unit_list)
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty', false)
        ;
        
        
        $form->input_select_add()
                ->input_select_set('label', 'Sales Unit')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_unit_sales')
                ->input_select_set('data_add', $unit_list)
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('disable_all', true)
                ->input_select_set('allow_empty', false)
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
            , 'unit_default'=>$unit_list[0]
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

    public static function product_unit_conversion_render($app, $form, $data, $path){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'product_unit_conversion','class_name'=>'product_unit_conversion_engine'));
        SI::module()->load_class(array('module'=>'product_unit_conversion','class_name'=>'product_unit_conversion_renderer'));
        $id = $data['id'];
        $t_product = Product_Data_Support::product_get($id);
        $product = $t_product['product'];

        if(Security_Engine::get_controller_permission(ICES_Engine::$app['val'],User_Info::get()['user_id'],'product_unit_conversion','product_unit_conversion_add')){
            $form->form_group_add()->custom_component_add()->load_view(false)->innerHTML_set('<br/>');
            $form->button_add()->button_set('class','primary')
                ->button_set('value',Lang::get(array('New','Product Unit Conversion')))
                ->button_set('icon','fa fa-plus')
                ->button_set('attrib',array(
                    'data-toggle'=>"modal" 
                    ,'data-target'=>"#modal_product_unit_conversion"
                ))
                ->button_set('disable_after_click',false)
                ->button_set('id','product_unit_conversion_new')
            ;
        }


        $db = new DB();
        $q = '
            select distinct null row_num
                ,puc.*
                ,u.code unit_code
                ,u2.code unit_code2
            from p_u_conversion puc 
            inner join unit u on puc.unit_id = u.id
            inner join unit u2 on puc.unit_id2 = u2.id
            where puc.product_id = '.$db->escape($id).'
            order by puc.id desc
            limit 100
        ';
        $rs = $db->query_array($q);
        for($i = 0;$i<count($rs);$i++){
            $rs[$i]['row_num'] = '<a href="'.$rs[$i]['id'].'">'.($i+1).'</a>';
            $rs[$i]['qty'] = Tools::thousand_separator($rs[$i]['qty'],2);
            $rs[$i]['qty2'] = Tools::thousand_separator($rs[$i]['qty2'],2);


        }
        $product_unit_conversion = $rs;

        $table = $form->form_group_add()->table_add();
        $table->table_set('id','product_unit_conversion_view_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"qty","label"=>"Qty",'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $table->table_set('columns',array("name"=>"unit_code","label"=>"Unit",'col_attrib'=>array('style'=>'')));
        $table->table_set('columns',array("name"=>"qty2","label"=>"Qty 2",'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $table->table_set('columns',array("name"=>"unit_code2","label"=>"Unit",'col_attrib'=>array('style'=>'')));


        $table->table_set('data',$product_unit_conversion);
        
        $modal_product_unit_conversion = $app->engine->modal_add()->id_set('modal_product_unit_conversion')->footer_attr_set(array('hidden'=>''));
        
        Product_Unit_Conversion_Renderer::modal_product_unit_conversion_render(
            $app
            ,$modal_product_unit_conversion
        );

        
        $param = array(
            'index_url'=>$path->index
            ,'ajax_search'=>$path->ajax_search
            ,'product_id'=>$product['id']
        );

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'product/product_unit_conversion_js',$param,TRUE);
        $app->js_set($js);
        
        
        //</editor-fold>
    }
    
}

?>