<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Product'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'product/product_engine');
        $this->path = Product_Engine::path_get();
        $this->title_icon = APP_ICON::product();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_menu('collapsed', false);
        $app->set_breadcrumb($this->title, strtolower('product'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Product', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'Product')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'product/add');

        $cols = array(
            array("name" => "product_category_text", "label" => Lang::get("Product Category"), "data_type" => "text"),
            array("name" => "code", "label" => Lang::get("Code"), "data_type" => "text", 'is_key' => true),
            array("name" => "name", "label" => Lang::get("Name"), "data_type" => "text"),
            array("name" => "unit_text", "label" => Lang::get("Unit"), "data_type" => "text"),
            array("name" => "purchase_amount", "label" => Lang::get("Purchase Amount"), "data_type" => "text", 'attribute' => array('style' => 'text-align:right'), 'row_attrib' => array('style' => 'text-align:right')),
            array("name" => "sales_amount", "label" => Lang::get("Sales Amount"), "data_type" => "text", 'attribute' => array('style' => 'text-align:right'), 'row_attrib' => array('style' => 'text-align:right')),
            array("name" => "product_status", "label" => Lang::get("Status"), "data_type" => "text")
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/product')
                ->table_ajax_set('columns', $cols)
        ;
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
        $this->load->helper($this->path->product_data_support);
        $this->load->helper($this->path->product_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Product_Data_Support::product_get($id)) > 0) {
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
            $app->set_breadcrumb($this->title, strtolower('product'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Product_Renderer::product_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Product_Renderer::product_status_log_render($app, $history_pane, array("id" => $id), $this->path);

                $product_batch_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#product_batch_tab', "value" => "Product Batch"));
                $product_batch_pane = $history_tab->div_add()->div_set('id', 'product_batch_tab')->div_set('class', 'tab-pane');
                Product_Renderer::product_batch_render($app, $product_batch_pane, array("id" => $id), $this->path);
                
                $product_unit_conversion_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#product_unit_conversion_tab', "value" => "Product Unit Conversion"));
                $product_unit_conversion_pane = $history_tab->div_add()->div_set('id', 'product_unit_conversion_tab')->div_set('class', 'tab-pane');
                Product_Renderer::product_unit_conversion_render($app, $product_unit_conversion_pane, array("id" => $id), $this->path);
            
            }

            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function product_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'product_add', 'primary_data_key' => 'product', 'data_post' => $post);
            SI::data_submit()->submit('product_engine', $param);
        }
    }

    public function product_active($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'product_active', 'primary_data_key' => 'product', 'data_post' => $post);
            SI::data_submit()->submit('product_engine', $param);
        }
    }

    public function product_inactive($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'product_inactive', 'primary_data_key' => 'product', 'data_post' => $post);
            $result = SI::data_submit()->submit('product_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Engine::path_get();
        get_instance()->load->helper($path->product_data_support);

        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array();
        switch ($method) {
            case 'product':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select distinct p.*
                                    ,pc.name product_category_name
                                    ,pc.code product_category_code
                                    ,pu.purchase_amount
                                    ,pu.sales_formula
                                    ,replace(pu.sales_formula,"c",pu.purchase_amount ) sales_amount
                                    ,replace(GROUP_CONCAT(DISTINCT concat(u.code," ",u.name)),",",", ") unit_text
                                from product p
                                    inner join product_category pc on p.product_category_id = pc.id
                                    inner join p_u pu on p.id = pu.product_id
                                    inner join unit u on u.id = pu.unit_id
                                
                        ',
                        'where' => '
                            where p.status>0
                            and (
                                p.code like ' . $lookup_str . ' 
                                or p.name like ' . $lookup_str . ' 
                                or p.product_status like ' . $lookup_str . '
                                or pc.code like ' . $lookup_str . '
                                or pc.name like ' . $lookup_str . '
                                or u.code like ' . $lookup_str . '
                                or u.name like ' . $lookup_str . '
                            )
                        ',
                        'group' => '
                           group by p.id 
                           ) tf
                        ',
                        'order' => 'order by id desc'
                    ),
                );
                $temp_result = SI::form_data()->ajax_table_search($config, $data, array('output_type' => 'object'));
                $t_data = $temp_result->data;
                foreach ($t_data as $idx => $row) {
                    $row->product_status = SI::get_status_attr(SI::type_get('Product_Engine', $row->product_status, '$status_list')['text']);
                    $row->product_category_text = Tools::html_tag('strong', $row->product_category_code). ' ' . $row->product_category_name;
                    $row->purchase_amount = Tools::thousand_separator($row->purchase_amount);
                    
                    $row->sales_amount = Tools::thousand_separator(Product_Data_Support::sales_amount_get($row->sales_amount));
                }
                $temp_result = json_decode(json_encode($temp_result), true);
                $result = $temp_result;
                //</editor-fold>
                break;
            case 'product_category_search':
                //<editor-fold defaultstate="collapsed">
                $product_id = Tools::_str(isset($data['product_id']) ? $data['product_id'] : '');

                $t_product_category = Product_Data_Support::input_select_product_category_search($lookup_data);
                /*
                  foreach ($t_product_category as $idx => $row) {
                  $t_product[$idx] = array(
                  'id' => $row['id'],
                  'text' => Tools::html_tag('strong', $row['code']) . ' ' . $row['name']
                  );
                  } */
                $result = $t_product_category;
                //</editor-fold>
                break;
        }

        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Engine::path_get();
        get_instance()->load->helper($path->product_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'product_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $product_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Product_Data_Support::product_get($product_id);
                if (count($temp) > 0) {
                    $product = $temp['product'];
                    $product['product_status'] = array(
                        'id' => $product['product_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('product_engine', $product['product_status'], '$status_list'
                                )['text']
                        )
                    );

                    $product['product_category'] = array(
                        'id' => $product['product_category_id'],
                        'text' => Tools::html_tag('strong', $product['product_category_code'])
                        . ' ' . $product['product_category_name']
                    );

                    $product['unit'] = array(
                        'id' => $product['unit_id'],
                        'text' => Tools::html_tag('strong', $product['unit_code'])
                        . ' ' . $product['unit_name']
                    );
                    
                    $product['unit_sales'] = array(
                        'id' => $product['unit_sales_id'],
                        'text' => Tools::html_tag('strong', $product['unit_sales_code'])
                        . ' ' . $product['unit_sales_name']
                    );

                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('product_engine', $product['product_status']['id']
                    );

                    $response['product'] = $product;
                    $response['product_status_list'] = $next_allowed_status_list;
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
