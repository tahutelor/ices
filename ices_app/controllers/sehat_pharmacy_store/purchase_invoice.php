<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Purchase_Invoice extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Purchase Invoice'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'purchase_invoice/purchase_invoice_engine');
        $this->path = Purchase_Invoice_Engine::path_get();
        $this->title_icon = APP_ICON::purchase_invoice();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title, strtolower('purchase_invoice'));
        $app->set_content_header($this->title, $this->title_icon, $action);

        $row = $app->engine->div_add()->div_set('class', 'row');
        $form = $row->form_add()->form_set('title', Lang::get('Purchase Invoice', 'List'))->form_set('span', '12');
        $form->form_group_add()->button_add()->button_set('class', 'primary')->button_set('value', Lang::get(array('New', 'Purchase Invoice')))
                ->button_set('icon', 'fa fa-plus')->button_set('href', ICES_Engine::$app['app_base_url'] . 'purchase_invoice/add');

        $cols = array(
            array('name' => 'code', 'label' => Lang::get('Code'), 'data_type' => 'text', 'is_key' => true),
            array('name' => 'purchase_invoice_date', 'label' => Lang::get(array('Purchase Invoice','Date')), 'data_type' => 'text'),
            array('name' => 'grand_total_amount', 'label' => Lang::get(array('Grand Total Amount')), 'data_type' => 'text','attribute'=>array('style'=>'text-align:right'),'row_attrib'=>array('style'=>'text-align:right')),
            array('name' => 'outstanding_grand_total_amount', 'label' => Lang::get(array('Outstanding Amount')), 'data_type' => 'text','attribute'=>array('style'=>'text-align:right'),'row_attrib'=>array('style'=>'text-align:right')),
            array('name' => 'purchase_invoice_status', 'label' => Lang::get('Status'), 'data_type' => 'text'),
        );

        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id', 'ajax_table')
                ->table_ajax_set('base_href', $this->path->index . 'view')
                ->table_ajax_set('lookup_url', $this->path->index . 'ajax_search/purchase_invoice')
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
        $this->load->helper($this->path->purchase_invoice_data_support);
        $this->load->helper($this->path->purchase_invoice_renderer);

        $action = $method;
        $cont = true;

        if (!in_array($method, array('add', 'view'))) {
            Message::set('error', array("Method error"));
            $cont = false;
        }

        if ($cont) {
            if (in_array($method, array('view'))) {
                if (!count(Purchase_Invoice_Data_Support::purchase_invoice_get($id)) > 0) {
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
            $app->set_breadcrumb($this->title, strtolower('purchase_invoice'));
            $app->set_content_header($this->title, $this->title_icon, $action);
            $row = $app->engine->div_add()->div_set('class', 'row');

            $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
            Purchase_Invoice_Renderer::purchase_invoice_render($app, $detail_pane, array("id" => $id), $this->path, $method);
            if ($method === 'view') {
                $history_tab = $nav_tab->nav_tab_set('items_add'
                        , array("id" => '#status_log_tab', "value" => "Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id', 'status_log_tab')->div_set('class', 'tab-pane');
                Purchase_Invoice_Renderer::purchase_invoice_status_log_render($app, $history_pane, array("id" => $id), $this->path);
                
                $purchase_receipt_tab = $nav_tab->nav_tab_set('items_add'
                    ,array("id"=>'#pr_tab',"value"=>"Purchase Receipt"));
                $pr_pane = $purchase_receipt_tab->div_add()->div_set('id','pr_tab')->div_set('class','tab-pane');
                Purchase_Invoice_Renderer::purchase_receipt_render($app,$pr_pane,array("id"=>$id),$this->path);
                
                $purchase_return_tab = $nav_tab->nav_tab_set('items_add'
                    ,array("id"=>'#pret_tab',"value"=>"Purchase Return"));
                $pr_pane = $purchase_return_tab->div_add()->div_set('id','pret_tab')->div_set('class','tab-pane');
                Purchase_Invoice_Renderer::purchase_return_render($app,$pr_pane,array("id"=>$id),$this->path);
                
            }


            $app->render();
        } else {
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function purchase_invoice_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'purchase_invoice_add', 'primary_data_key' => 'purchase_invoice', 'data_post' => $post);
            SI::data_submit()->submit('purchase_invoice_engine', $param);
        }
    }

    public function purchase_invoice_invoiced($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'purchase_invoice_invoiced', 'primary_data_key' => 'purchase_invoice', 'data_post' => $post);
            SI::data_submit()->submit('purchase_invoice_engine', $param);
        }
    }

    public function purchase_invoice_canceled($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'purchase_invoice_canceled', 'primary_data_key' => 'purchase_invoice', 'data_post' => $post);
            $result = SI::data_submit()->submit('purchase_invoice_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Invoice_Engine::path_get();
        get_instance()->load->helper($path->purchase_invoice_data_support);
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array('response'=>array());
        $response = array();
        switch ($method) {
            case 'purchase_invoice':
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
                                from purchase_invoice pi
                                where pi.status>0
                            
                        ',
                        'where' => '
                            and (
                                purchase_invoice_status LIKE ' . $lookup_str . '  
                                or code LIKE ' . $lookup_str . '       
                                or purchase_invoice_date LIKE ' . $lookup_str . '   
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
                    $row->purchase_invoice_status = SI::get_status_attr(
                                    SI::type_get('Purchase_Invoice_Engine', $row->purchase_invoice_status, '$status_list')['text']
                    );
                    $row->purchase_invoice_date = Tools::_date($row->purchase_invoice_date,'F d, Y H:i:s');
                    $row->grand_total_amount = Tools::thousand_separator($row->grand_total_amount);
                    $row->outstanding_grand_total_amount = Tools::thousand_separator($row->outstanding_grand_total_amount);
                }
                $temp_result = json_decode(json_encode($temp_result), true);
                $result = $temp_result;
                //</editor-fold>
                break;
            case 'input_select_supplier_search':
                //<editor-fold defaultstate="collapsed">
                $response = Purchase_Invoice_Data_Support::input_select_supplier_search($lookup_data);
                //</editor-fold>
                break;
            case 'input_select_product_search':
                //<editor-fold defaultstate="collapsed">
                $response = Purchase_Invoice_Data_Support::input_select_product_search($lookup_data);
                
                //</editor-fold>
                break;
        }
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Invoice_Engine::path_get();
        get_instance()->load->helper($path->purchase_invoice_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'purchase_invoice_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $purchase_invoice_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($purchase_invoice_id);
                if (count($temp) > 0) {
                    $purchase_invoice = $temp['purchase_invoice'];
                    $pi_product = $temp['pi_product'];
                    $purchase_invoice['purchase_invoice_status'] = array(
                        'id' => $purchase_invoice['purchase_invoice_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('purchase_invoice_engine', $purchase_invoice['purchase_invoice_status'], '$status_list'
                                )['text']
                        )
                    );
                    
                    $purchase_invoice['store'] = array(
                        'id'=>$purchase_invoice['store_id'],
                        'text'=>Tools::html_tag('strong',$purchase_invoice['store_code'])
                            .' '.$purchase_invoice['store_name']
                    );
                    
                    $purchase_invoice['supplier'] = array(
                        'id'=>$purchase_invoice['supplier_id'],
                        'text'=>Tools::html_tag('strong',$purchase_invoice['supplier_code'])
                            .' '.$purchase_invoice['supplier_name']
                    );
                    
                    $pi_product = json_decode(json_encode($pi_product));
                    foreach($pi_product as $idx=>$row){
                        $row->product_text = Tools::html_tag('strong',$row->product_code)
                            .' '.$row->product_name;
                        $row->unit_text = Tools::html_tag('strong',$row->unit_code)
                            .' '.$row->unit_name;
                    }
                    $pi_product = json_decode(json_encode($pi_product),true);
                    
                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('purchase_invoice_engine', $purchase_invoice['purchase_invoice_status']['id']
                    );
                    
                    $response['purchase_invoice'] = $purchase_invoice;
                    $response['pi_product'] = $pi_product;                    
                    $response['purchase_invoice_status_list'] = $next_allowed_status_list;
                }
                //</editor-fold>
                break;
            case 'supplier_dependency_get':
                //<editor-fold defaultstate="collapsed">
                $supplier_id = Tools::_str(isset($data['supplier_id'])?$data['supplier_id']:'');
                $param = array('supplier_id'=>$supplier_id);
                $response = Purchase_Invoice_Data_Support::supplier_dependency_get($param);
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
