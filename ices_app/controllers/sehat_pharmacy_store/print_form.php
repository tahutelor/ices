<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_Form extends MY_ICES_Controller {
    
    private $title='';
    private $title_icon = '';
    private $path = array();
    
    function __construct(){
        parent::__construct();
        $this->title = Lang::get(array('Print Form'),true,true,false,false,true);
        SI::module()->load_class(array('module'=>'print_form','class_name'=>'print_form_engine'));
        $this->path = Print_Form_Engine::path_get();
        $this->title_icon = App_Icon::print_form();
        
    }
    
    public function index()
    {           
        SI::module()->load_class(array('module'=>'print_form','class_name'=>'print_form_renderer'));
        $app = new App();    
        $app->set_title($this->title);
        $app->set_menu('collapsed',true);
        $app->set_breadcrumb($this->title,'print_form');
        $app->set_content_header($this->title,$this->title_icon,'');
        $row = $app->engine->div_add()->div_set('class','row')->div_set('id','print_form');            
        $form = $row->form_add()->form_set('title',$this->title)->form_set('span','12');
        Print_Form_Renderer::print_form_render($app,$form,array("id"=>''),$this->path,'view');
        
        $app->render();
    }
        
    public function ajax_search($method="",$submethod=""){
        //<editor-fold defaultstate="collapsed">
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data'])?Tools::_str($data['data']):'';
        $result =array('success'=>1,'msg'=>[],'response'=>array());
        $success = 1;
        $msg = [];
        $response = array();
        $limit = 10;
        switch($method){            
            case 'product_category_search':
                //<editor-fold defaultstate="collapsed">
                SI::module()->load_class(array('module'=>'print_form','class_name'=>'print_form_data_support'));
                $response = Print_Form_Data_Support::input_select_product_category_search($lookup_data);
                //</editor-fold>
                break;
            
        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['response'] = $response;
        echo json_encode($result);
        
        //</editor-fold>
    }
    
    public function data_support($method="",$submethod=""){
        //<editor-fold defaultstate="collapsed">
        
        $data = json_decode($this->input->post(), true);
        $result =array('success'=>1,'msg'=>[]);
        $msg=[];
        $success = 1;
        $response = array();
        switch($method){
                            
        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }
    
    public function print_form_print($data=''){
        SI::module()->load_class(array('module'=>'print_form','class_name'=>'print_form_print'));
        $post = $this->input->post();
        $data = json_decode(urldecode($data),true);
        $module = Tools::_str(isset($data['module'])?$data['module']:'');
        switch($module){
            case 'product_stock_opname':
                $param = array(
                    'p_engine'=>null,
                    'p_output'=>true,
                    'warehouse_id'=>Tools::_str(isset($data['warehouse_id'])?$data['warehouse_id']:''),
                    'product_category_id'=>Tools::_str(isset($data['product_category_id'])?
                        $data['product_category_id']:''
                    )
                );
                Print_Form_Print::product_stock_opname_print($param);
                break;
        }
    }
    
}

?>