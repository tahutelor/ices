<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Simple_Engine {

    public static $module_list;
    
    public static function path_get(){
        //<editor-fold defaultstate="collapsed">
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'rpt_simple/'
            , 'rpt_simple_engine' => ICES_Engine::$app['app_base_dir'] . 'rpt_simple/rpt_simple_engine'
            , 'rpt_simple_data_support' => ICES_Engine::$app['app_base_dir'] . 'rpt_simple/rpt_simple_data_support'
            , 'rpt_simple_renderer' => ICES_Engine::$app['app_base_dir'] . 'rpt_simple/rpt_simple_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'rpt_simple/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'rpt_simple/data_support/'
        );

        return json_decode(json_encode($path));
        //</editor-fold>
    }

    public static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        self::$module_list = array(
                    
        );
        //</editor-fold>
    }



}
?>
