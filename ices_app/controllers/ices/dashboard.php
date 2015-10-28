<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_ICES_Controller {
        
    private $title='Dashboard';
    private $title_icon = '';
    private $path = array();
    
    function __construct(){
        parent::__construct();
        SI::module()->load_class(array('module'=>'dashboard','class_name'=>'dashboard_engine'));
        $this->path = Dashboard_Engine::path_get();
        $this->title_icon = App_Icon::dashboard();
        
    }
    
    
    public function index()
    {       
        SI::module()->load_class(array('module'=>'dashboard','class_name'=>'dashboard_renderer'));
        $action = "";
        
        $app = new App();
        $db = new DB();
        
        $app->set_title($this->title);
        $app->set_content_header($this->title,$this->title_icon,$action);
        $pane = $app->engine->div_add()->div_set('class','row'); 
        
        Dashboard_Renderer::dashboard_render($app,$pane);
        
        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'dashboard/dashboard_js', array(), TRUE);
        $app->js_set($js);
        $js = '
            $("[dashboard_component] .box-body").slimScroll({
                height: $("[dashboard_component] .box-body").height()
            });

            dashboard.refresh(null);
            window.setInterval(function(){dashboard.refresh(null)},dashboard_refresh_every_ms);
        ';
        $app->js_set($js);
        
        $app->render();
        
    }
    
    public function data_support($method="",$submethod=""){
        //<editor-fold defaultstate="collapsed">
        //this function only used for urgently data retrieve
        SI::module()->load_class(array('module'=>'dashboard','class_name'=>'dashboard_data_support'));
        $data = json_decode($this->input->post(), true);
        $result =array('success'=>1,'msg'=>[]);
        $msg=[];
        $success = 1;
        $response = array();
        switch($method){
            case 'data_get':
                $response = array();
                $module = isset($data['module'])?Tools::_arr($data['module']):array();
                
                foreach($module as $idx=>$row){
                    if(method_exists('Dashboard_Data_Support', $row.'_get')){
                        if(Security_Engine::get_controller_permission(ICES_Engine::$app['val'],User_Info::get()['user_id']
                            ,'dashboard',$row)){
                            $response[] = eval('return Dashboard_Data_Support::'.$row.'_get();');
                        }
                    }
                }
                
                break;
            
                
        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }
}

