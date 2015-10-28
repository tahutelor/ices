<?php

class Phone_Number_Engine {

    public static $prefix_id = 'phone_number';
    public static $prefix_method;
    public static $status_list;

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        
        //</editor-fold>
    }

    public static function path_get() {
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'phone_number/'
            , 'phone_number_engine' => ICES_Engine::$app['app_base_dir'] . 'phone_number/phone_number_engine'
            , 'phone_number_data_support' => ICES_Engine::$app['app_base_dir'] . 'phone_number/phone_number_data_support'
            , 'phone_number_renderer' => ICES_Engine::$app['app_base_dir'] . 'phone_number/phone_number_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'phone_number/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'phone_number/data_support/'
        );

        return json_decode(json_encode($path));
    }

}

?>