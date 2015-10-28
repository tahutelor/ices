<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class BOS_Bank_Account extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('BOS Bank Account'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'bos_bank_account/bos_bank_account_engine');
        $this->path = BOS_Bank_Account_Engine::path_get();
        $this->title_icon = APP_ICON::bank_account();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('bos_bank_account'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('BOS Bank Account', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'BOS Bank Account')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'bos_bank_account/add');

        $cols = array(
            array("name" => "bos_bank_account_code", "label" => Lang::get("Code"), "data_type" => "text", "is_key" => true),
            array("name" => "bank_account", "label" => Lang::get("Bank Account"), "data_type" => "text"),
            array("name" => "bos_bank_account_status", "label" => Lang::get("Status"), "data_type" => "text"),
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/bos_bank_account')
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
        $this->load->helper($this->path->bos_bank_account_data_support);
        $this->load->helper($this->path->bos_bank_account_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(BOS_Bank_Account_Data_Support::bos_bank_account_get($id)) > 0) {
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
            $app->set_breadcrumb($this->title, strtolower('bos_bank_account'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            BOS_Bank_Account_Renderer::bos_bank_account_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                BOS_Bank_Account_Renderer::bos_bank_account_status_log_render($app, $history_pane, array("id" => $id), $this->path);
            }


            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function bos_bank_account_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'bos_bank_account_add', 'primary_data_key' => 'bos_bank_account', 'data_post' => $post);
            SI::data_submit()->submit('bos_bank_account_engine', $param);
        }
    }

    public function bos_bank_account_active($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'bos_bank_account_active', 'primary_data_key' => 'bos_bank_account', 'data_post' => $post);
            SI::data_submit()->submit('bos_bank_account_engine', $param);
        }
    }

    public function bos_bank_account_inactive($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'bos_bank_account_inactive', 'primary_data_key' => 'bos_bank_account', 'data_post' => $post);
            $result = SI::data_submit()->submit('bos_bank_account_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array();
        switch ($method) {
            case 'bos_bank_account':
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select bba.id
                                    ,bba.code bos_bank_account_code
                                    ,bba.bank_account bank_account
                                    ,bba.bos_bank_account_status
                                from bos_bank_account bba
                                where bba.status>0
                                group by bba.id
                            )tf
                            where 1=1',
                        'where' => '
                            and (
                                bos_bank_account_status LIKE ' . $lookup_str . '  
                                or bos_bank_account_code LIKE ' . $lookup_str . '       
                                or bank_account LIKE ' . $lookup_str . '   
                            )
                        ',
                        'group' => '
                            
                        ',
                        'order' => 'order by bos_bank_account_code asc'
                    ),
                );
                $temp_result = SI::form_data()->ajax_table_search($config, $data, array('output_type' => 'object'));
                $t_data = $temp_result->data;
                foreach ($t_data as $i => $row) {
                    $row->bos_bank_account_status = SI::get_status_attr(
                                    SI::type_get('BOS_Bank_Account_Engine', $row->bos_bank_account_status, '$status_list')['text']
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
        $path = BOS_Bank_Account_Engine::path_get();
        get_instance()->load->helper($path->bos_bank_account_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'bos_bank_account_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $bos_bank_account_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = BOS_Bank_Account_Data_Support::bos_bank_account_get($bos_bank_account_id);
                if (count($temp) > 0) {
                    $bos_bank_account = $temp['bos_bank_account'];
                    $bos_bank_account['bos_bank_account_status'] = array(
                        'id' => $bos_bank_account['bos_bank_account_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('bos_bank_account_engine', $bos_bank_account['bos_bank_account_status'], '$status_list'
                                )['text']
                        )
                    );

                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('bos_bank_account_engine', $bos_bank_account['bos_bank_account_status']['id']
                    );

                    $response['bos_bank_account'] = $bos_bank_account;
                    $response['bos_bank_account_status_list'] = $next_allowed_status_list;
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
