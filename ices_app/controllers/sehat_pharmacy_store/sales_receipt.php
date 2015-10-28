<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_Receipt extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Sales Receipt'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'sales_receipt/sales_receipt_engine');
        $this->path = Sales_Receipt_Engine::path_get();
        $this->title_icon = APP_ICON::sales_receipt();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('sales_receipt'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Sales Receipt', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'Sales Receipt')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'sales_receipt/add');

        $cols = array(
            array('name' => 'code', 'label' => Lang::get('Code'), 'data_type' => 'text', 'is_key' => true),
            array('name' => 'sales_receipt_date', 'label' => Lang::get(array('Sales Receipt','Date')), 'data_type' => 'text'),
            array('name' => 'payment_type_text', 'label' => Lang::get(array('Payment Type')), 'data_type' => 'text'),
            array('name' => 'amount', 'label' => Lang::get(array('Amount')), 'data_type' => 'text','attribute'=>array('style'=>'text-align:right'),'row_attrib'=>array('style'=>'text-align:right')),
            array('name' => 'sales_receipt_status', 'label' => Lang::get('Status'), 'data_type' => 'text'),
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/sales_receipt')
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
        $this->load->helper($this->path->sales_receipt_data_support);
        $this->load->helper($this->path->sales_receipt_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Sales_Receipt_Data_Support::sales_receipt_get($id)) > 0) {
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
            $app->set_breadcrumb($this->title, strtolower('sales_receipt'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Sales_Receipt_Renderer::sales_receipt_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Sales_Receipt_Renderer::sales_receipt_status_log_render($app, $history_pane, array("id" => $id), $this->path);
            }


            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function sales_receipt_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'sales_receipt_add', 'primary_data_key' => 'sales_receipt', 'data_post' => $post);
            SI::data_submit()->submit('sales_receipt_engine', $param);
        }
    }

    public function sales_receipt_invoiced($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'sales_receipt_invoiced', 'primary_data_key' => 'sales_receipt', 'data_post' => $post);
            SI::data_submit()->submit('sales_receipt_engine', $param);
        }
    }

    public function sales_receipt_canceled($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'sales_receipt_canceled', 'primary_data_key' => 'sales_receipt', 'data_post' => $post);
            $result = SI::data_submit()->submit('sales_receipt_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Receipt_Engine::path_get();
        get_instance()->load->helper($path->sales_receipt_data_support);
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array('response'=>array());
        $response = array();
        switch ($method) {
            case 'sales_receipt':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select distinct pr.*
                                    ,pt.code payment_type_code
                                    ,pt.name payment_type_name
                                    ,concat(pt.code, pt.name) payment_type_text
                                from sales_receipt pr
                                inner join payment_type = pt on pr.payment_type_id = pt.id
                                where pr.status>0
                            
                        ',
                        'where' => '
                            and (
                                pr.sales_receipt_status LIKE ' . $lookup_str . '  
                                or pr.code LIKE ' . $lookup_str . '       
                                or pr.sales_receipt_date LIKE ' . $lookup_str . '
                                or pr.amount LIKE ' . $lookup_str . '
                                or pt.code LIKE ' . $lookup_str . '
                                or pt.code LIKE ' . $lookup_str . '
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
                    $row->sales_receipt_status = SI::get_status_attr(
                                    SI::type_get('Sales_Receipt_Engine', $row->sales_receipt_status, '$status_list')['text']
                    );
                    $row->sales_receipt_date = Tools::_date($row->sales_receipt_date,'F d, Y H:i:s');
                    $row->amount = Tools::thousand_separator($row->amount);
                    $row->payment_type_text = Tools::html_tag('strong',$row->payment_type_code)
                        .' '.$row->payment_type_name;
                }
                $temp_result = json_decode(json_encode($temp_result), true);
                $result = $temp_result;
                //</editor-fold>
                break;
            case 'input_select_reference_search':
                //<editor-fold defaultstate="collapsed">
                $response = Sales_Receipt_Data_Support::input_select_reference_search($lookup_data);
                //</editor-fold>
                break;
            
        }
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Receipt_Engine::path_get();
        get_instance()->load->helper($path->sales_receipt_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'sales_receipt_get':
                //<editor-fold defaultstate="collapsed">
                SI::module()->load_class(array('module'=>'payment_type','class_name'=>'payment_type_data_support'));
                SI::module()->load_class(array('module'=>'bos_bank_account','class_name'=>'bos_bank_account_data_support'));
                $db = new DB();
                $response = array();
                $sales_receipt_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Sales_Receipt_Data_Support::sales_receipt_get($sales_receipt_id);
                if (count($temp) > 0) {
                    $sales_receipt = $temp['sales_receipt'];
                    $sales_receipt['sales_receipt_status'] = array(
                        'id' => $sales_receipt['sales_receipt_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('sales_receipt_engine', $sales_receipt['sales_receipt_status'], '$status_list'
                                )['text']
                        )
                    );
                                        
                    $reference = array();
                    switch($sales_receipt['ref_type']){
                        case 'sales_invoice':
                            //<editor-fold defaultstate="collapsed">
                            SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
                            $temp = Sales_Invoice_Data_Support::sales_invoice_get($sales_receipt['ref_id']);
                            $sales_invoice = $temp['sales_invoice'];
                            $reference = array(
                                'id'=>$sales_invoice['id'],
                                'text'=>Tools::html_tag('strong',$sales_invoice['customer_code']),
                                'ref_type'=>$sales_receipt['ref_type'],
                            );
                            //</editor-fold>
                            break;
                    }
                    $sales_receipt['reference'] = $reference;
                    
                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('sales_receipt_engine', $sales_receipt['sales_receipt_status']['id']
                    );
                    
                    
                    $response['sales_receipt'] = $sales_receipt;
                    $response['sales_receipt_status_list'] = $next_allowed_status_list;
                }
                //</editor-fold>
                break;
            case 'reference_dependency_get':
                //<editor-fold defaultstate="collapsed">
                $ref_type = Tools::_str(isset($data['ref_type'])?$data['ref_type']:'');
                $ref_id = Tools::_str(isset($data['ref_id'])?$data['ref_id']:'');
                $param = array('ref_type'=>$ref_type,'ref_id'=>$ref_id);
                $response = Sales_Receipt_Data_Support::reference_dependency_get($param);
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
