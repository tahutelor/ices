<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supplier extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Supplier'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'supplier/supplier_engine');
        $this->path = Supplier_Engine::path_get();
        $this->title_icon = APP_ICON::supplier();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('supplier'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Supplier', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'Supplier')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'supplier/add');

        $cols = array(
            array("name" => "supplier_code", "label" => Lang::get("Code"), "data_type" => "text", "is_key" => true),
            array("name" => "supplier_name", "label" => Lang::get("Name"), "data_type" => "text"),            
            array("name" => "supplier_status", "label" => Lang::get("Status"), "data_type" => "text"),
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/supplier')
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
        $this->load->helper($this->path->supplier_data_support);
        $this->load->helper($this->path->supplier_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Supplier_Data_Support::supplier_get($id)) > 0) {
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
            $app->set_breadcrumb($this->title, strtolower('supplier'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Supplier_Renderer::supplier_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Supplier_Renderer::supplier_status_log_render($app, $history_pane, array("id" => $id), $this->path);
                
                $sdal = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#sda_log_tab', "value" => "Debit Amount Log"));
                $sdal = $history_tab->div_add()->div_set('id', 'sda_log_tab')->div_set('class', 'tab-pane');
                Supplier_Renderer::supplier_debit_credit_amount_log_render($app, $sdal, array("id" => $id), $this->path,'debit');
                
                $scal = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#sca_log_tab', "value" => "Credit Amount Log"));
                $scal = $history_tab->div_add()->div_set('id', 'sca_log_tab')->div_set('class', 'tab-pane');
                Supplier_Renderer::supplier_debit_credit_amount_log_render($app, $scal, array("id" => $id), $this->path,'credit');
            }


            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function supplier_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'supplier_add', 'primary_data_key' => 'supplier', 'data_post' => $post);
            SI::data_submit()->submit('supplier_engine', $param);
        }
    }

    public function supplier_active($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'supplier_active', 'primary_data_key' => 'supplier', 'data_post' => $post);
            SI::data_submit()->submit('supplier_engine', $param);
        }
    }

    public function supplier_inactive($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'supplier_inactive', 'primary_data_key' => 'supplier', 'data_post' => $post);
            $result = SI::data_submit()->submit('supplier_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array();
        switch ($method) {
            case 'supplier':
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select s.id
                                    ,s.code supplier_code
                                    ,s.name supplier_name
                                    ,s.supplier_status
                                from supplier s
                                where s.status>0
                                group by s.id
                            )tf
                            where 1=1',
                        'where' => '
                            and (
                                supplier_status LIKE ' . $lookup_str . '  
                                or supplier_code LIKE ' . $lookup_str . '       
                                or supplier_name LIKE ' . $lookup_str . '   
                            )
                        ',
                        'group' => '
                            
                        ',
                        'order' => 'order by supplier_name asc'
                    ),
                );
                $temp_result = SI::form_data()->ajax_table_search($config, $data, array('output_type' => 'object'));
                $t_data = $temp_result->data;
                foreach ($t_data as $i => $row) {
                    $row->supplier_status = SI::get_status_attr(
                                    SI::type_get('Supplier_Engine', $row->supplier_status, '$status_list')['text']
                    );
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
        $path = Supplier_Engine::path_get();
        get_instance()->load->helper($path->supplier_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'supplier_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $supplier_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Supplier_Data_Support::supplier_get($supplier_id);
                if (count($temp) > 0) {
                    $supplier = $temp['supplier'];
                    $supplier['supplier_status'] = array(
                        'id' => $supplier['supplier_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('supplier_engine', $supplier['supplier_status'], '$status_list'
                                )['text']
                        )
                    );
                    
                    $address = $temp['sup_address'];
                    $mail_address = $temp['sup_mail_address'];
                    $phone_number = $temp['sup_phone_number'];

                    foreach ($phone_number as $idx => $row) {
                        $phone_number[$idx] = array(
                            'phone_number_type_id' => $row['phone_number_type_id'],
                            'phone_number' => $row['phone_number'],
                            'phone_number_type_name' => Tools::html_tag('strong', $row['phone_number_type_code']) . ' ' . $row['phone_number_type_name'],
                        );
                    }

                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('supplier_engine', $supplier['supplier_status']['id']
                    );

                    $response['supplier'] = $supplier;
                    $response['address'] = $address;
                    $response['mail_address'] = $mail_address;
                    $response['phone_number'] = $phone_number;
                    $response['supplier_status_list'] = $next_allowed_status_list;
                }
                //</editor-fold>
                break;
            case 'phone_number_type_get':
                //<editor-fold defaultstate="collapsed">

                $t_phone_number_type = Supplier_Data_Support::phone_number_type_get();
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
