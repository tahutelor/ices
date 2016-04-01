<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_Invoice extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Sales Invoice'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'sales_invoice/sales_invoice_engine');
        $this->path = Sales_Invoice_Engine::path_get();
        $this->title_icon = APP_ICON::sales_invoice();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('sales_invoice'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Sales Invoice', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'Sales Invoice')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'sales_invoice/add');

        $cols = array(
            array('name' => 'code', 'label' => Lang::get('Code'), 'data_type' => 'text', 'is_key' => true),
            array('name' => 'sales_invoice_date', 'label' => Lang::get(array('Sales Invoice', 'Date')), 'data_type' => 'text'),
            array('name' => 'grand_total_amount', 'label' => Lang::get(array('Grand Total Amount')), 'data_type' => 'text', 'attribute' => array('style' => 'text-align:right'), 'row_attrib' => array('style' => 'text-align:right')),
            array('name' => 'outstanding_grand_total_amount', 'label' => Lang::get(array('Outstanding Amount')), 'data_type' => 'text', 'attribute' => array('style' => 'text-align:right'), 'row_attrib' => array('style' => 'text-align:right')),
            array('name' => 'sales_invoice_status', 'label' => Lang::get('Status'), 'data_type' => 'text'),
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/sales_invoice')
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
        $this->load->helper($this->path->sales_invoice_data_support);
        $this->load->helper($this->path->sales_invoice_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Sales_Invoice_Data_Support::sales_invoice_get($id)) > 0) {
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
            $app->set_breadcrumb($this->title, strtolower('sales_invoice'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Sales_Invoice_Renderer::sales_invoice_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Sales_Invoice_Renderer::sales_invoice_status_log_render($app, $history_pane, array("id" => $id), $this->path);
                
                $product_mov_qty_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#mov_qty_log', "value" => "Product Mov. Qty"));
                $pr_pane = $product_mov_qty_tab->div_add()->div_set('id', 'mov_qty_log')->div_set('class', 'tab-pane');
                Sales_Invoice_Renderer::product_mov_qty_render($app, $pr_pane, array("id" => $id), $this->path);
                
                
                $sales_receipt_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#pr_tab', "value" => "Sales Receipt"));
                $pr_pane = $sales_receipt_tab->div_add()->div_set('id', 'pr_tab')->div_set('class', 'tab-pane');
                Sales_Invoice_Renderer::sales_receipt_render($app, $pr_pane, array("id" => $id), $this->path);

            }


            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function sales_invoice_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'sales_invoice_add', 'primary_data_key' => 'sales_invoice', 'data_post' => $post);
            SI::data_submit()->submit('sales_invoice_engine', $param);
        }
    }

    public function sales_invoice_invoiced($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'sales_invoice_invoiced', 'primary_data_key' => 'sales_invoice', 'data_post' => $post);
            SI::data_submit()->submit('sales_invoice_engine', $param);
        }
    }

    public function sales_invoice_canceled($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'sales_invoice_canceled', 'primary_data_key' => 'sales_invoice', 'data_post' => $post);
            $result = SI::data_submit()->submit('sales_invoice_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Invoice_Engine::path_get();
        get_instance()->load->helper($path->sales_invoice_data_support);
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array('response' => array());
        $response = array();
        switch ($method) {
            case 'sales_invoice':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select pi.*
                                from sales_invoice pi
                                where pi.status>0
                            
                        ',
                        'where' => '
                            and (
                                sales_invoice_status LIKE ' . $lookup_str . '  
                                or code LIKE ' . $lookup_str . '       
                                or sales_invoice_date LIKE ' . $lookup_str . '   
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
                    $row->sales_invoice_status = SI::get_status_attr(
                                    SI::type_get('Sales_Invoice_Engine', $row->sales_invoice_status, '$status_list')['text']
                    );
                    $row->sales_invoice_date = Tools::_date($row->sales_invoice_date, 'F d, Y H:i:s');
                    $row->grand_total_amount = Tools::thousand_separator($row->grand_total_amount);
                    $row->outstanding_grand_total_amount = Tools::thousand_separator($row->outstanding_grand_total_amount);
                }
                $temp_result = json_decode(json_encode($temp_result), true);
                $result = $temp_result;
                //</editor-fold>
                break;
            case 'input_select_customer_search':
                //<editor-fold defaultstate="collapsed">
                $response = Sales_Invoice_Data_Support::input_select_customer_search($lookup_data);
                //</editor-fold>
                break;
            case 'input_select_product_search':
                //<editor-fold defaultstate="collapsed">
                $response = Sales_Invoice_Data_Support::input_select_product_search($lookup_data);

                //</editor-fold>
                break;
        }
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Invoice_Engine::path_get();
        get_instance()->load->helper($path->sales_invoice_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'sales_invoice_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $sales_invoice_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Sales_Invoice_Data_Support::sales_invoice_get($sales_invoice_id);
                if (count($temp) > 0) {
                    $sales_invoice = $temp['sales_invoice'];
                    $si_product = $temp['si_product'];
                    $sales_invoice['sales_invoice_status'] = array(
                        'id' => $sales_invoice['sales_invoice_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('sales_invoice_engine', $sales_invoice['sales_invoice_status'], '$status_list'
                                )['text']
                        )
                    );

                    $sales_invoice['store'] = array(
                        'id' => $sales_invoice['store_id'],
                        'text' => Tools::html_tag('strong', $sales_invoice['store_code'])
                        . ' ' . $sales_invoice['store_name']
                    );

                    $sales_invoice['customer'] = array(
                        'id' => $sales_invoice['customer_id'],
                        'text' => Tools::html_tag('strong', $sales_invoice['customer_code'])
                        . ' ' . $sales_invoice['customer_name']
                    );

                    $si_product = json_decode(json_encode($si_product));
                    foreach ($si_product as $idx => $row) {
                        $row->product_text = Tools::html_tag('strong', $row->product_code)
                                . ' ' . $row->product_name;
                        $row->unit_text = Tools::html_tag('strong', $row->unit_code)
                        ;
                        $row->unit_text_sales = Tools::html_tag('strong', $row->unit_code_sales)
                        ;
                    }
                    $si_product = json_decode(json_encode($si_product), true);

                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('sales_invoice_engine', $sales_invoice['sales_invoice_status']['id']
                    );

                    $response['sales_invoice'] = $sales_invoice;
                    $response['si_product'] = $si_product;
                    $response['sales_invoice_status_list'] = $next_allowed_status_list;
                }
                //</editor-fold>
                break;
            case 'customer_dependency_get':
                //<editor-fold defaultstate="collapsed">
                $customer_id = Tools::_str(isset($data['customer_id']) ? $data['customer_id'] : '');
                $param = array('customer_id' => $customer_id);
                $response = Sales_Invoice_Data_Support::customer_dependency_get($param);
                //</editor-fold>
                break;

        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }

    public function sales_invoice_print($data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_print'));
        $post = $this->input->post();
        $data = json_decode(urldecode($data),true);
        $sales_invoice_id = Tools::_str(isset($data['sales_invoice_id'])?$data['sales_invoice_id']:'');
        $param = array(
            'p_engine'=>null,
            'p_output'=>true,
            'sales_invoice_id'=>$sales_invoice_id,
        );
        Sales_Invoice_Print::invoice_print($param);
        
        //</editor-fold>
    }
    
}
