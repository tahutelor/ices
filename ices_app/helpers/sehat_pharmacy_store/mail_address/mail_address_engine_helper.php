<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mail_Address_Engine {

    public static $prefix_id = 'mail_address';
    public static $prefix_method;
    public static $status_list;

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        
        //</editor-fold>
    }

    public static function path_get() {
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'mail_address/'
            , 'mail_address_engine' => ICES_Engine::$app['app_base_dir'] . 'mail_address/mail_address_engine'
            , 'mail_address_data_support' => ICES_Engine::$app['app_base_dir'] . 'mail_address/mail_address_data_support'
            , 'mail_address_renderer' => ICES_Engine::$app['app_base_dir'] . 'mail_address/mail_address_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'mail_address/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'mail_address/data_support/'
        );

        return json_decode(json_encode($path));
    }

}

?>