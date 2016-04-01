<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Unit_Conversion extends MY_ICES_Controller {

    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Product Unit Conversion'), true, true, false, false, true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'product_unit_conversion/product_unit_conversion_engine');
        $this->path = Product_Unit_Conversion_Engine::path_get();
        $this->title_icon = APP_ICON::info();
    }

    public function index() {
        //<editor-fold defaultstate="collapsed">
        //</editor-fold>
    }

    public function add() {
        //<editor-fold defaultstate="collapsed">
        
        //</editor-fold>
    }

    public function view($id = "", $method = "view") {
        //<editor-fold defaultstate="collapsed">
        
        //</editor-fold>
    }

    public function product_unit_conversion_add() {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => '', 'method' => 'product_unit_conversion_add', 'primary_data_key' => 'product_unit_conversion', 'data_post' => $post);
            SI::data_submit()->submit('product_unit_conversion_engine', $param);
        }
    }

    public function product_unit_conversion_active($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'product_unit_conversion_active', 'primary_data_key' => 'product_unit_conversion', 'data_post' => $post);
            SI::data_submit()->submit('product_unit_conversion_engine', $param);
        }
    }
    
    public function product_unit_conversion_delete($id = '') {
        $post = $this->input->post();
        if ($post != null) {
            $param = array('id' => $id, 'method' => 'product_unit_conversion_delete', 'primary_data_key' => 'product_unit_conversion', 'data_post' => $post);
            SI::data_submit()->submit('product_unit_conversion_engine', $param);
        }
    }

    public function ajax_search($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Unit_Conversion_Engine::path_get();
        get_instance()->load->helper($path->product_unit_conversion_data_support);

        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data']) ? Tools::_str($data['data']) : '';
        $result = array();
        switch ($method) {
            
        }

        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Unit_Conversion_Engine::path_get();
        get_instance()->load->helper($path->product_unit_conversion_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg = [];
        $success = 1;
        $response = array();
        switch ($method) {
            case 'product_unit_conversion_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $product_unit_conversion_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Product_Unit_Conversion_Data_Support::product_unit_conversion_get($product_unit_conversion_id);
                
                if (count($temp) > 0) {
                    $product_unit_conversion = $temp['product_unit_conversion'];
                    $default_status = SI::type_default_type_get('Product_Unit_Conversion_Engine','$status_list');
                    $product_unit_conversion['product_unit_conversion_status'] = array(
                        'id' => $default_status['val'],
                        'text' => SI::get_status_attr(
                                $default_status['text']
                        ),
                        'method'=>$default_status['method'],
                    );
                    
                    $product_unit_conversion['unit'] = array(
                        'id'=>$product_unit_conversion['unit_id'],
                        'text'=>Tools::html_tag('strong',$product_unit_conversion['unit_code'])
                            .' '.$product_unit_conversion['unit_name'],
                    );
                    
                    $product_unit_conversion['unit2'] = array(
                        'id'=>$product_unit_conversion['unit_id2'],
                        'text'=>Tools::html_tag('strong',$product_unit_conversion['unit_code2'])
                            .' '.$product_unit_conversion['unit_name2'],
                    );
                    
                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('product_unit_conversion_engine', $product_unit_conversion['product_unit_conversion_status']['id']
                    );

                    $response['product_unit_conversion'] = $product_unit_conversion;
                    $response['product_unit_conversion_status_list'] = $next_allowed_status_list;
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
