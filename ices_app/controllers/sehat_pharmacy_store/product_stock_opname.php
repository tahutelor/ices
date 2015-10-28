<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Stock_Opname extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Product Stock Opname'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'product_stock_opname/product_stock_opname_engine');
        $this->path = Product_Stock_Opname_Engine::path_get();
        $this->title_icon = APP_ICON::product_stock_opname();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('product_stock_opname'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Product Stock Opname', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'Product Stock Opname')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'product_stock_opname/add');

        $cols = array(
            array('name' => 'code', 'label' => Lang::get('Code'), 'data_type' => 'text', 'is_key' => true),
            array('name' => 'product_stock_opname_date', 'label' => Lang::get(array('Product Stock Opname','Date')), 'data_type' => 'text'),
            array('name' => 'num_o_products', 'label' => Lang::get(array('Number of Products')), 'data_type' => 'text','attribute'=>array('style'=>'text-align:right'),'row_attrib'=>array('style'=>'text-align:right')),
            array('name' => 'product_stock_opname_status', 'label' => Lang::get('Status'), 'data_type' => 'text'),
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/product_stock_opname')
                ->table_ajax_set('columns', $cols);
        $app->render();
        //</editor-fold>
    }

    public function add() {
        //<editor-fold defaultstate="collapsed">
        $post = $this->input->post();
        $this->view('', 'add');
        //</editor-fold>
    }

    public function view($id = "", $method = "view") {
        //<editor-fold defaultstate="collapsed">
        $this->load->helper($this->path->product_stock_opname_data_support);
        $this->load->helper($this->path->product_stock_opname_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Product_Stock_Opname_Data_Support::product_stock_opname_get($id)) > 0) {
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
            $app->set_menu('collapsed', false);
            $app->set_breadcrumb($this->title, strtolower('product_stock_opname'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Product_Stock_Opname_Renderer::product_stock_opname_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Product_Stock_Opname_Renderer::product_stock_opname_status_log_render($app, $history_pane, array("id" => $id), $this->path);
                
            }


            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function product_stock_opname_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'product_stock_opname_add', 'primary_data_key' => 'product_stock_opname', 'data_post' => $post);
            SI::data_submit()->submit('product_stock_opname_engine', $param);
        }
    }

    public function product_stock_opname_done($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'product_stock_opname_done', 'primary_data_key' => 'product_stock_opname', 'data_post' => $post);
            SI::data_submit()->submit('product_stock_opname_engine', $param);
        }
    }

    public function product_stock_opname_canceled($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'product_stock_opname_canceled', 'primary_data_key' => 'product_stock_opname', 'data_post' => $post);
            $result = SI::data_submit()->submit('product_stock_opname_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Stock_Opname_Engine::path_get();
        get_instance()->load->helper($path->product_stock_opname_data_support);
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array('response'=>array());
        $response = array();
        switch ($method) {
            case 'product_stock_opname':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select pso.*
                                    ,tf.num_o_products
                                from product_stock_opname pso
                                    inner join (
                                    select psop.product_stock_opname_id, count(1) num_o_products
                                    from pso_product psop
                                    group by psop.product_stock_opname_id
                                    
                                )tf on pso.id = tf.product_stock_opname_id
                                where pso.status>0
                            
                        ',
                        'where' => '
                            and (
                                pso.product_stock_opname_status LIKE ' . $lookup_str . '  
                                or pso.code LIKE ' . $lookup_str . '       
                                or pso.product_stock_opname_date LIKE ' . $lookup_str . '   
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
                    $row->product_stock_opname_status = SI::get_status_attr(
                                    SI::type_get('Product_Stock_Opname_Engine', $row->product_stock_opname_status, '$status_list')['text']
                    );
                    $row->product_stock_opname_date = Tools::_date($row->product_stock_opname_date,'F d, Y H:i:s');
                    $row->num_o_products = Tools::thousand_separator($row->num_o_products,0);
                }
                $temp_result = json_decode(json_encode($temp_result), true);
                $result = $temp_result;
                //</editor-fold>
                break;
            case 'input_select_product_search':
                //<editor-fold defaultstate="collapsed">
                $response = Product_Stock_Opname_Data_Support::input_select_product_search($lookup_data);                
                //</editor-fold>
                break;
            case 'input_select_product_batch_search':
                //<editor-fold defaultstate="collapsed">
                $param = array(
                    'lookup_data'=>$lookup_data,
                    'product_id'=>Tools::_str(
                        isset($data['extra_param']['product_id'])?$data['extra_param']['product_id']:''
                    ),
                    'unit_id'=>Tools::_str(
                        isset($data['extra_param']['unit_id'])?$data['extra_param']['unit_id']:''
                    ),
                    'warehouse_id'=>Tools::_str(
                        isset($data['extra_param']['warehouse_id'])?$data['extra_param']['warehouse_id']:''
                    ),
                    'product_batch_status'=>'active',
                );
                
                $response = Product_Stock_Opname_Data_Support::input_select_product_batch_search($param);                
                //</editor-fold>
                break;
        }
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Stock_Opname_Engine::path_get();
        get_instance()->load->helper($path->product_stock_opname_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'product_stock_opname_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $product_stock_opname_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Product_Stock_Opname_Data_Support::product_stock_opname_get($product_stock_opname_id);
                if (count($temp) > 0) {
                    $product_stock_opname = $temp['product_stock_opname'];
                    $pso_product = $temp['pso_product'];
                    $product_stock_opname['product_stock_opname_status'] = array(
                        'id' => $product_stock_opname['product_stock_opname_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('product_stock_opname_engine', $product_stock_opname['product_stock_opname_status'], '$status_list'
                                )['text']
                        )
                    );
                                                            
                    $pso_product = json_decode(json_encode($pso_product));
                    foreach($pso_product as $idx=>$row){
                        $row->product_text = Tools::html_tag('strong',$row->product_code)
                            .' '.$row->product_name;
                        $row->unit_text = Tools::html_tag('strong',$row->unit_code)
                            .' '.$row->unit_name;
                        $row->product_batch_text = Tools::html_tag('strong',$row->batch_number)
                            .' '.Tools::_date($row->expired_date,'F d, Y H:i:s');
                    }
                    $pso_product = json_decode(json_encode($pso_product),true);
                    
                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('product_stock_opname_engine', $product_stock_opname['product_stock_opname_status']['id']
                    );
                    
                    $response['product_stock_opname'] = $product_stock_opname;
                    $response['pso_product'] = $pso_product;                    
                    $response['product_stock_opname_status_list'] = $next_allowed_status_list;
                }
                //</editor-fold>
                break;
            case 'supplier_dependency_get':
                //<editor-fold defaultstate="collapsed">
                $supplier_id = Tools::_str(isset($data['supplier_id'])?$data['supplier_id']:'');
                $param = array('supplier_id'=>$supplier_id);
                $response = Product_Stock_Opname_Data_Support::supplier_dependency_get($param);
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
