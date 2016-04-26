<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Warehouse_Category extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Warehouse Category'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'warehouse_category/warehouse_category_engine');
        $this->path = Warehouse_Category_Engine::path_get();
        $this->title_icon = '';
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('warehouse_category'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Warehouse Category', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'Warehouse Category')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'warehouse_category/add');

        $cols = array(
            array("name" => "code", "label" => "Code", "data_type" => "text",'is_key'=>true),
            array("name" => "name", "label" => "Name", "data_type" => "text"),
            array("name" => "warehouse_category_status", "label" => "Status", "data_type" => "text")
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
            ->table_ajax_set('base_href', $this->path->index . 'view')
            ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/warehouse_category')
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
        $this->load->helper($this->path->warehouse_category_data_support);
        $this->load->helper($this->path->warehouse_category_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Warehouse_Category_Data_Support::warehouse_category_get($id)) > 0) {
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
            $app->set_breadcrumb($this->title, strtolower('warehouse_category'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Warehouse_Category_Renderer::warehouse_category_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Warehouse_Category_Renderer::warehouse_category_status_log_render($app, $history_pane, array("id" => $id), $this->path);
            }


            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function warehouse_category_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'warehouse_category_add', 'primary_data_key' => 'warehouse_category', 'data_post' => $post);
            SI::data_submit()->submit('warehouse_category_engine', $param);
        }
    }

    public function warehouse_category_active($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'warehouse_category_active', 'primary_data_key' => 'warehouse_category', 'data_post' => $post);
            SI::data_submit()->submit('warehouse_category_engine', $param);
        }
    }

    public function warehouse_category_inactive($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'warehouse_category_inactive', 'primary_data_key' => 'warehouse_category', 'data_post' => $post);
            $result = SI::data_submit()->submit('warehouse_category_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array();
        switch ($method) {
            case 'warehouse_category':
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select distinct *
                                from warehouse_category t1
                                where t1.status>0',
                        'where' => '
                            and (
                                t1.warehouse_category_status like ' . $lookup_str . '
                                or t1.name like ' . $lookup_str . '
                            )
                        ',
                        'group' => '
                            )tfinal
                        ',
                        'order' => 'order by id desc'
                    ),
                );
                $temp_result = SI::form_data()->ajax_table_search($config, $data, array('output_type' => 'object'));
                $t_data = $temp_result->data;
                foreach ($t_data as $i => $row) {
                    $row->warehouse_category_status = SI::get_status_attr(
                                    SI::type_get('Warehouse_Category_Engine', $row->warehouse_category_status, '$status_list')['text']
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
        $path = Warehouse_Category_Engine::path_get();
        get_instance()->load->helper($path->warehouse_category_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'warehouse_category_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $warehouse_category_id = Tools::_str(isset($data['data'])? $data['data']:'');
                $temp = Warehouse_Category_Data_Support::warehouse_category_get($warehouse_category_id);
                if (count($temp) > 0) {
                    $warehouse_category = $temp['warehouse_category'];
                    $warehouse_category['warehouse_category_status'] = array(
                        'id'=>$warehouse_category['warehouse_category_status'],
                        'text'=>SI::get_status_attr(
                                    SI::type_get('warehouse_category_engine', $warehouse_category['warehouse_category_status'], '$status_list'
                                    )['text']
                        )
                    );

                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('warehouse_category_engine', $warehouse_category['warehouse_category_status']['id']
                    );

                    $response['warehouse_category'] = $warehouse_category;
                    $response['warehouse_category_status_list'] = $next_allowed_status_list;
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
