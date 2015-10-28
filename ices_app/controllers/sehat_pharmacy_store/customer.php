<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customer extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Customer'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'customer/customer_engine');
        $this->path = Customer_Engine::path_get();
        $this->title_icon = APP_ICON::customer();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('customer'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Customer', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'Customer')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'customer/add');

        $cols = array(
            array("name" => "customer_type_text", "label" => Lang::get("Type"), "data_type" => "text"),
            array("name" => "customer_code", "label" => Lang::get("Code"), "data_type" => "text", "is_key" => true),
            array("name" => "customer_name", "label" => Lang::get("Name"), "data_type" => "text"),            
            array("name" => "customer_status", "label" => Lang::get("Status"), "data_type" => "text"),
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/customer')
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
        $this->load->helper($this->path->customer_data_support);
        $this->load->helper($this->path->customer_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Customer_Data_Support::customer_get($id)) > 0) {
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
            $app->set_breadcrumb($this->title, strtolower('customer'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Customer_Renderer::customer_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Customer_Renderer::customer_status_log_render($app, $history_pane, array("id" => $id), $this->path);
                
            }


            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function customer_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'customer_add', 'primary_data_key' => 'customer', 'data_post' => $post);
            SI::data_submit()->submit('customer_engine', $param);
        }
    }

    public function customer_active($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'customer_active', 'primary_data_key' => 'customer', 'data_post' => $post);
            SI::data_submit()->submit('customer_engine', $param);
        }
    }

    public function customer_inactive($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'customer_inactive', 'primary_data_key' => 'customer', 'data_post' => $post);
            $result = SI::data_submit()->submit('customer_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array();
        switch ($method) {
            case 'customer':
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select c.id
                                    ,c.code customer_code
                                    ,c.name customer_name
                                    ,c.customer_status
                                    ,concat(ct.code, ct.name) customer_type_text
                                    ,ct.code customer_type_code
                                    ,ct.name customer_type_name
                                from customer c
                                inner join customer_type ct 
                                    on ct.id = c.customer_type_id
                                where c.status>0 and ct.status>0
                                group by c.id
                            )tf
                            where 1=1',
                        'where' => '
                            and (
                                customer_status LIKE ' . $lookup_str . '  
                                or customer_code LIKE ' . $lookup_str . '       
                                or customer_name LIKE ' . $lookup_str . '   
                                or customer_type_code LIKE ' . $lookup_str . '       
                                or customer_type_name LIKE ' . $lookup_str . '
                            )
                        ',
                        'group' => '
                            
                        ',
                        'order' => 'order by customer_name asc'
                    ),
                );
                $temp_result = SI::form_data()->ajax_table_search($config, $data, array('output_type' => 'object'));
                $t_data = $temp_result->data;
                foreach ($t_data as $i => $row) {
                    $row->customer_status = SI::get_status_attr(
                                    SI::type_get('Customer_Engine', $row->customer_status, '$status_list')['text']
                    );
                    $row->customer_type_text = Tools::html_tag('strong',$row->customer_type_code).' '.$row->customer_type_name;
                }
                $temp_result = json_decode(json_encode($temp_result), true);
                $result = $temp_result;

                break;
        }

        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Engine::path_get();
        get_instance()->load->helper($path->customer_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'customer_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $customer_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Customer_Data_Support::customer_get($customer_id);
                if (count($temp) > 0) {
                    $customer = $temp['customer'];
                    $customer['customer_status'] = array(
                        'id' => $customer['customer_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('customer_engine', $customer['customer_status'], '$status_list'
                                )['text']
                        )
                    );
                    
                    $customer['si_customer_default'] = array(
                        'id' => $customer['si_customer_default'],
                        'text' => SI::get_status_attr(
                                Tools::_float($customer['si_customer_default']) === Tools::_float('1')?'TRUE':'FALSE'
                        )
                    );
                    
                    $customer['customer_type'] = array(
                        'id' => $customer['customer_type_id'],
                        'text' => Tools::html_tag('strong',$customer['customer_type_code'])
                            .' '.$customer['customer_type_name'],
                    );

                    $address = $temp['c_address'];
                    $mail_address = $temp['c_mail_address'];
                    $phone_number = $temp['c_phone_number'];

                    foreach ($phone_number as $idx => $row) {
                        $phone_number[$idx] = array(
                            'phone_number_type_id' => $row['phone_number_type_id'],
                            'phone_number' => $row['phone_number'],
                            'phone_number_type_name' => Tools::html_tag('strong', $row['phone_number_type_code']) . ' ' . $row['phone_number_type_name'],
                        );
                    }

                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('customer_engine', $customer['customer_status']['id']
                    );

                    $response['customer'] = $customer;
                    $response['address'] = $address;
                    $response['mail_address'] = $mail_address;
                    $response['phone_number'] = $phone_number;
                    $response['customer_status_list'] = $next_allowed_status_list;
                }
                //</editor-fold>
                break;
            case 'customer_type_list_get':
                //<editor-fold defaultstate="collapsed">
                $t_customer_type = Customer_Data_Support::customer_type_list_get();
                foreach ($t_customer_type as $idx => $row) {
                    $t_customer_type[$idx] = array(
                        'id' => $row['id'],
                        'text' => Tools::html_tag('strong', $row['code']) . ' ' . $row['name']
                    );
                }
                $response = $t_customer_type;
                //</editor-fold>
                break;
            case 'customer_type_default_get':
                //<editor-fold defaultstate="collapsed">
                $temp = Customer_Data_Support::customer_type_default_get();
                $customer_type = null;
                if(count($temp)>0){
                    $customer_type = $temp['customer_type'];
                    $customer_type['text'] = Tools::html_tag('strong', $customer_type['code']) . ' ' . $customer_type['name'];
                }
                $response = $customer_type;
                //</editor-fold>
                break;
            case 'phone_number_type_get':
                //<editor-fold defaultstate="collapsed">

                $t_phone_number_type = Customer_Data_Support::phone_number_type_get();
                foreach ($t_phone_number_type as $idx => $row) {
                    $t_phone_number_type[$idx] = array(
                        'id' => $row['id'],
                        'text' => Tools::html_tag('strong', $row['code']) . ' ' . $row['name']
                    );
                }
                $response = $t_phone_number_type;
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
