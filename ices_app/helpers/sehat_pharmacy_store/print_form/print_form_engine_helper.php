<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Print_Form_Engine {
    public static $prefix_id = 'print_form';
    public static $prefix_method;
    public static $module;
    
    public function helper_init(){
        //<editor-fold desc="this function is called automatically in MY_Loader class" defaultstate="collapsed">
        
        self::$prefix_method = self::$prefix_id;
        self::$module = array(
            array('val'=>'product_stock_opname','text'=>'Product Stock Opname')
        );
        //</editor-fold>
    }
    
    public static function path_get(){
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'print_form/'
            , 'print_form_engine' => ICES_Engine::$app['app_base_dir'] . 'print_form/print_form_engine'
            , 'print_form_data_support' => ICES_Engine::$app['app_base_dir'] . 'print_form/print_form_data_support'
            , 'print_form_renderer' => ICES_Engine::$app['app_base_dir'] . 'print_form/print_form_renderer'
            , 'print_form_print' => ICES_Engine::$app['app_base_dir'] . 'print_form/print_form_print'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'print_form/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'print_form/data_support/'
        );

        return json_decode(json_encode($path));
    }

    
    
}
?>