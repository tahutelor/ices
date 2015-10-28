<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SI_Module{
    function __construct(){
        
    }
    
    public function load_class($param = array()){
        //<editor-fold defaultstate="collapsed">
        $module = $param['module'];
        $class_name = $param['class_name'];
        
        $path_engine = get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].$module.'/'.$module.'_engine');
        $path = eval('return '.Tools::class_name_get($module.'_engine').'::path_get();');
        get_instance()->load->helper(eval('return $path->'.strtolower($class_name).';'));
        //</editor-fold>
    }
    
    
}

?>