<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contact extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Contact'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'contact/contact_engine');
        $this->path = Contact_Engine::path_get();
        $this->title_icon = '';
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('contact'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Contact', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'Contact')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'contact/add');

				
				
        $cols = array(
            array("name" => "contact_name", "label" => "Name", "data_type" => "text", "is_key" => true),
            array("name" => "contact_category_text", "label" => "Contact Category", "data_type" => "text"),
            array("name" => "contact_phone_number", "label" => "Phone", "data_type" => "text"),
            array("name" => "contact_status", "label" => "Status", "data_type" => "text"),
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/contact')
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
        $this->load->helper($this->path->contact_data_support);
        $this->load->helper($this->path->contact_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Contact_Data_Support::contact_get($id)) > 0) {
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
            $app->set_breadcrumb($this->title, strtolower('contact'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Contact_Renderer::contact_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Contact_Renderer::contact_status_log_render($app, $history_pane, array("id" => $id), $this->path);
            }


            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function contact_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'contact_add', 'primary_data_key' => 'contact', 'data_post' => $post);
            SI::data_submit()->submit('contact_engine', $param);
        }
    }

    public function contact_active($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'contact_active', 'primary_data_key' => 'contact', 'data_post' => $post);
            SI::data_submit()->submit('contact_engine', $param);
        }
    }

    public function contact_inactive($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'contact_inactive', 'primary_data_key' => 'contact', 'data_post' => $post);
            $result = SI::data_submit()->submit('contact_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array();
        switch ($method) {
            case 'contact':
                $db = new DB();
                $lookup_str = $db->escape('%' . $lookup_data . '%');
                $config = array(
                    'additional_filter' => array(
                    ),
                    'query' => array(
                        'basic' => '
                            select * from (
                                select c.id, c.name contact_name,c.contact_status
                                  ,replace(GROUP_CONCAT(DISTINCT cc.name),",",", ")contact_category_text
                                  ,replace(GROUP_CONCAT(DISTINCT cpn.phone_number),",",", ")contact_phone_number
                                  from contact c
                                  left outer join c_cc ccc on c.id = ccc.contact_id
                                  left outer join contact_category cc on cc.id = ccc.contact_category_id
                                  left outer join c_phone_number cpn on cpn.contact_id = c.id
                                  where c.status>0 and cc.status>0
                                  group by c.id
                            )tf
                            where 1=1',
                        'where' => '
                            and (
                                contact_category_text LIKE ' . $lookup_str . ' 
                                or contact_phone_number LIKE ' . $lookup_str . '
                                or contact_status LIKE ' . $lookup_str . '  
                                or contact_name LIKE ' . $lookup_str . '       
                            )
                        ',
                        'group' => '
                            
                        ',
                        'order' => 'order by contact_name asc'
                    ),
                );
                $temp_result = SI::form_data()->ajax_table_search($config, $data, array('output_type' => 'object'));
                $t_data = $temp_result->data;
                foreach ($t_data as $i => $row) {
                    $row->contact_status = SI::get_status_attr(
                                    SI::type_get('Contact_Engine', $row->contact_status, '$status_list')['text']
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
        $path = Contact_Engine::path_get();
        get_instance()->load->helper($path->contact_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'contact_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $contact_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Contact_Data_Support::contact_get($contact_id);
                if (count($temp) > 0) {
                    $contact = $temp['contact'];
                    $contact['contact_status'] = array(
                        'id' => $contact['contact_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('contact_engine', $contact['contact_status'], '$status_list'
                                )['text']
                        )
                    );

                    $address = $temp['c_address'];
                    $mail_address = $temp['c_mail_address'];
                    $phone_number = $temp['c_phone_number'];
                    $contact_category = $temp['c_cc'];

                    foreach ($contact_category as $idx => $row) {
                        $contact_category[$idx] = array(
                            'id' => $row['id'],
                            'text' => Tools::html_tag('strong', $row['code']) . ' ' . $row['name']
                        );
                    }

                    foreach ($phone_number as $idx => $row) {
                        $phone_number[$idx] = array(
                            'phone_number_type_id' => $row['phone_number_type_id'],
                            'phone_number' => $row['phone_number'],
                            'phone_number_type_name' => Tools::html_tag('strong', $row['phone_number_type_code']) . ' ' . $row['phone_number_type_name'],
                        );
                    }

                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('contact_engine', $contact['contact_status']['id']
                    );

                    $response['contact'] = $contact;
                    $response['address'] = $address;
                    $response['mail_address'] = $mail_address;
                    $response['phone_number'] = $phone_number;
                    $response['contact_category'] = $contact_category;
                    $response['contact_status_list'] = $next_allowed_status_list;
                }
                //</editor-fold>
                break;
            case 'contact_category_get':
                //<editor-fold defaultstate="collapsed">
                $t_contact_category = Contact_Data_Support::contact_category_get();
                foreach ($t_contact_category as $idx => $row) {
                    $t_contact_category[$idx] = array(
                        'id' => $row['id'],
                        'text' => Tools::html_tag('strong', $row['code']) . ' ' . $row['name']
                    );
                }
                $response = $t_contact_category;
                //</editor-fold>
                break;
            case 'phone_number_type_get':
                //<editor-fold defaultstate="collapsed">
                $t_phone_number_type = Contact_Data_Support::phone_number_type_get();
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
