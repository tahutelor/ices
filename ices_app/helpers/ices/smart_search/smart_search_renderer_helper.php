<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Smart_Search_Renderer {

    public static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'smart_search/smart_search_engine');
        //</editor-fold>
    }
    
    
}
    
?>