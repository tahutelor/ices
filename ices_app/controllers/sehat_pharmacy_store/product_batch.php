<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Batch extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Product Batch'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'product_batch/product_batch_engine');
        $this->path = Product_Batch_Engine::path_get();
        $this->title_icon = APP_ICON::product_batch();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('product_batch'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Product Batch', 'List'))->form_set('span', '12');

        $cols = array(
            array('name' => 'batch_number', 'label' => Lang::get('Batch Number'), 'data_type' => 'text', 'is_key' => true),
            array('name' => 'product_text', 'label' => Lang::get('Product'), 'data_type' => 'text'),
            array('name' => 'expired_date', 'label' => Lang::get(array('Expired Date')), 'data_type' => 'text'),
            array('name' => 'qty', 'label' => Lang::get(array('Qty')), 'data_type' => 'text', 'attribute' => array('style' => 'text-align:right'), 'row_attrib' => array('style' => 'text-align:right')),
            array('name' => 'purchase_amount', 'label' => Lang::get(array('Purchase Amount')), 'data_type' => 'text', 'attribute' => array('style' => 'text-align:right'), 'row_attrib' => array('style' => 'text-align:right')),
            array('name' => 'product_batch_status', 'label' => Lang::get('Status'), 'data_type' => 'text'),
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/product_batch')
                ->table_ajax_set('columns', $cols);
        $app->render();
        //</editor-fold>
    }

    public function view($id = "", $method = "view") {
        //<editor-fold defaultstate="collapsed">
        $this->load->helper($this->path->product_batch_data_support);
        $this->load->helper($this->path->product_batch_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Product_Batch_Data_Support::product_batch_get($id)) > 0) {
                    Message::set('error', array("Data doesn't exist"));
                    $cont = false;
                }
            }
        }

        if ($cont) {
            if ($method == 'add')
                $id = '';
            $data = array(
                'id' => $id
            );

            $app = new App();
            $app->set_title($this->title);
            $app->set_menu('collapsed', true);
            $app->set_breadcrumb($this->title, strtolower('product_batch'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Product_Batch_Renderer::product_batch_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Product_Batch_Renderer::product_batch_status_log_render($app, $history_pane, array("id" => $id), $this->path);

                $history_stock_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#product_stock_log_tab', "value" => "Product Stock History"));
                $history_stock_pane = $history_tab->div_add()->div_set('id', 'product_stock_log_tab')->div_set('class', 'tab-pane');
                Product_Batch_Renderer::product_stock_log_render($app, $history_stock_pane, array("id" => $id), $this->path);
            }

            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function product_batch_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'product_batch_add', 'primary_data_key' => 'product_batch', 'data_post' => $post);
            SI::data_submit()->submit('product_batch_engine', $param);
        }
    }

    public function product_batch_active($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'product_batch_active', 'primary_data_key' => 'product_batch', 'data_post' => $post);
            SI::data_submit()->submit('product_batch_engine', $param);
        }
    }

    public function product_batch_inactive($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'product_batch_inactive', 'primary_data_key' => 'product_batch', 'data_post' => $post);
            $result = SI::data_submit()->submit('product_batch_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Batch_Engine::path_get();
        get_instance()->load->helper($path->product_batch_data_support);
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array('response' => array());
        $response = array();
        switch ($method) {
            case 'product_batch':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select pb.*
                                    ,p.name product_name
                                    ,p.code product_code
                                    ,concat(p.name, p.code) product_text
                                from product_batch pb
                                LEFT OUTER JOIN product p on pb.product_id = p.id and pb.product_type = "registered_product"                           
                                LEFT OUTER JOIN unit u on pb.unit_id = u.id
                                where pb.status>0                      
                        ',
                        'where' => '
                            and (
                                pb.expired_date LIKE ' . $lookup_str . '  
                                or pb.batch_number LIKE ' . $lookup_str . '       
                                or p.name LIKE ' . $lookup_str . '  
                                or p.code LIKE ' . $lookup_str . '  
                            )
                        ',
                        'group' => '
                            )tf
                        ',
                        'order' => 'order by cast(batch_number as decimal(20,5)) desc'
                    ),
                );
                $temp_result = SI::form_data()->ajax_table_search($config, $data, array('output_type' => 'object'));
                $t_data = $temp_result->data;
                foreach ($t_data as $i => $row) {
                    $row->product_batch_status = SI::get_status_attr(
                                    SI::type_get('Product_Batch_Engine', $row->product_batch_status, '$status_list')['text']
                    );
                    $row->product_text = '<a href="' . ICES_Engine::$app['app_base_url']
                            . 'product/view/' . $row->product_id . '">'
                            . Tools::html_tag('strong', $row->product_code)
                            . ' ' . $row->product_name
                            . '</a>'
                    ;
                    $row->expired_date = Tools::_date($row->expired_date, 'F d, Y H:i:s');
                    $row->qty = Tools::thousand_separator($row->qty);
                    $row->purchase_amount = Tools::thousand_separator($row->purchase_amount);
                }
                $temp_result = json_decode(json_encode($temp_result), true);
                $result = $temp_result;
                //</editor-fold>
                break;
            case 'product_stock':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $warehouse_id = Tools::_str(isset($data['additional_filter']['warehouse_id'])?
                    $data['additional_filter']['warehouse_id']:'')
                ;
                
                $q_warehouse = $warehouse_id === 'all'?'':
                    'and psg.warehouse_id = '.$db->escape($warehouse_id);
                ;
                
                $product_batch_id = Tools::_str(isset($data['additional_filter']['product_batch_id'])?
                    $data['additional_filter']['product_batch_id']:'')
                ;
                
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select distinct psgql.*
                                    ,w.code warehouse_code
                                    ,w.name warehouse_name
                                    ,concat(w.code, w.name) warehouse_text
                                from product_stock_good_qty_log psgql
                                inner join product_stock_good psg on psgql.product_stock_good_id = psg.id
                                inner join warehouse w on psg.warehouse_id = w.id
                                where 1 = 1
                                    and psg.product_batch_id = '.$db->escape($product_batch_id).'
                                    '.$q_warehouse.'
                        ',
                        'where' => '
                            and (
                                psg.status > 0
                            )
                        ',
                        'group' => '
                            )tf
                        ',
                        'order' => 'order by id desc'
                    ),
                );
                $temp_result = SI::form_data()->ajax_table_search($config, $data, array('output_type' => 'object'));
                $t_data = $temp_result->data;
                foreach ($t_data as $i => $row) {
                    $row->old_qty = Tools::thousand_separator($row->old_qty);
                    $row->qty = Tools::thousand_separator($row->qty);
                    $row->new_qty = Tools::thousand_separator($row->new_qty);
                    $row->warehouse_text = Tools::html_tag('strong',$row->warehouse_code)
                        .' '.$row->warehouse_name
                    ;
                    $row->description = SI::form_data()->log_description_translate($row->description);
                }
                $temp_result = json_decode(json_encode($temp_result), true);
                $result = $temp_result;
                //</editor-fold>
                break;
        }
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Batch_Engine::path_get();
        get_instance()->load->helper($path->product_batch_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'product_batch_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $product_batch_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Product_Batch_Data_Support::product_batch_get($product_batch_id);
                if (count($temp) > 0) {
                    $product_batch = $temp['product_batch'];
                    $product_batch['product'] = array(
                        'id'=>$product_batch['product_id'],
                        'text'=>Tools::html_tag('strong',$product_batch['product_code'])
                            .' '.$product_batch['product_name'],
                    );
                    $product_batch['unit'] = array(
                        'id'=>$product_batch['unit_id'],
                        'text'=>Tools::html_tag('strong',$product_batch['unit_code'])
                            .' '.$product_batch['unit_name'],
                    );
                    $product_batch['product_batch_status'] = array(
                        'id' => $product_batch['product_batch_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('product_batch_engine', $product_batch['product_batch_status'], '$status_list'
                                )['text']
                        )
                    );

                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('product_batch_engine', $product_batch['product_batch_status']['id']
                    );
                    
                    $product_stock = $temp['product_stock'];
                    foreach($product_stock as $idx=>$row){
                        $product_stock[$idx]['warehouse_text'] = Tools::html_tag('strong',$row['warehouse_code'])
                            .' '.$row['warehouse_name'];
                    }

                    $response['product_batch'] = $product_batch;
                    $response['product_batch_status_list'] = $next_allowed_status_list;
                    $response['product_stock'] = $product_stock;
                }
                //</editor-fold>
                break;
        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }

}
