<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Address_Engine {

    public static $prefix_id = 'address';
    public static $prefix_method;
    public static $status_list;

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        
        //</editor-fold>
    }

    public static function path_get() {
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'address/'
            , 'address_engine' => ICES_Engine::$app['app_base_dir'] . 'address/address_engine'
            , 'address_data_support' => ICES_Engine::$app['app_base_dir'] . 'address/address_data_support'
            , 'address_renderer' => ICES_Engine::$app['app_base_dir'] . 'address/address_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'address/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'address/data_support/'
        );

        return json_decode(json_encode($path));
    }

}

?>