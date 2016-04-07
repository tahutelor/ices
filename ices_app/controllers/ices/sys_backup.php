<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sys_Backup extends MY_ICES_Controller {
    
    private $title='';
    private $title_icon = '';
    private $path = array();
    
    function __construct(){
        parent::__construct();
        $this->title = Lang::get('System Backup');
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'sys_backup/sys_backup_engine');
        $this->path = Sys_Backup_Engine::path_get();
        $this->title_icon = App_Icon::sys_backup();
        
    }
    
    public function index($module_name='',$module_condition=''){
        
        $this->load->helper($this->path->sys_backup_engine);
        $this->load->helper($this->path->sys_backup_renderer);
        $this->load->helper($this->path->sys_backup_data_support);
        
        $app = new App();    
        $app->set_title($this->title);
        $app->set_breadcrumb($this->title,'sys_backup');
        $app->set_content_header($this->title,$this->title_icon,'');
        $row = $app->engine->div_add()->div_set('class','row')->div_set('id','sys_backup');            
        $form = $row->form_add()->form_set('title',Lang::get('System Backup'))->form_set('span','12');
        Sys_Backup_Renderer::sys_backup_render($app,$form,array("id"=>''),$this->path,'view');
        
        $app->render();
        
    }
    
    public function ajax_search($method){
        //<editor-fold defaultstate="collapsed">
        
        //</editor-fold>
    }
    
    public function data_support($method="",$submethod=""){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper('sys_backup/sys_backup_data_support');
        get_instance()->load->helper('sys_backup/sys_backup_renderer');
        $data = json_decode($this->input->post(), true);
        $result =array('success'=>1,'msg'=>[]);
        $msg = [];
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
    
    public function backup_db($data = ''){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sys_backup','class_name'=>'sys_backup_engine'));
        $result = array();
        $msg = [];
        $success = 1;
        $response = array();
        
        $data = json_decode(Tools::urldecode($data), true);
        $sys_backup = isset($data['sys_backup'])?Tools::_arr($data['sys_backup']):array();
        $method = isset($sys_backup['method'])?Tools::_str($sys_backup['method']):'';
        $phase = isset($sys_backup['phase'])?Tools::_str($sys_backup['phase']):'';
        $filename = isset($sys_backup['filename'])?Tools::_str($sys_backup['filename']):'';
        
        if(SI::type_get('Sys_Backup_Engine', $method,'$module_list') === null){
            $success = 0;
            $msg[] = 'Unknown Module';
        }
        
        if($success){
            switch($phase){
                case 'initialize':
                    $t_param = array(
                        'method'=>$method,
                        'sys_backup'=>$sys_backup
                    );
                    
                    $t_result = Sys_Backup_Engine::backup_create($t_param);
                    if($t_result['success'] !== 1 ){
                        $success = 0;
                        $msg = array_merge($msg,$t_result['msg']);
                    }

                    if($success === 1){
                        $filename = $t_result['response']['filename'];            
                        $response['filename'] = $filename;
                        $msg[] = 'Create File Success';
                    }
                    break;
                case 'send_file':                
                    Sys_Backup_Engine::file_send($filename);
                    exit();                
                    break;
                case 'finalize':
                    $dir = Sys_Backup_Engine::$dir_list['tmp_sys_backup_path'];
                    $filepath = $dir.$filename;
                    if(file_exists($filepath)){
                        unlink($filepath);
                        $msg[] = 'Delete file on server success';
                    }
                    break;
            }
        }
        
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }
    
    public function backup_php($data = ''){
        //<editor-fold defaultstate="collapsed">
        $this->backup_db($data);
        //</editor-fold>
    }
        
}

?>