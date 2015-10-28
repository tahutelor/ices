<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Purchase_Engine {
    public static $prefix_id = 'rpt_purchase';
    public static $module_type_list;
    
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        self::$module_type_list = array(
            array(
                'val'=>'purchase_invoice'
                ,'label'=>Lang::get(array('Purchase Invoice'),true,true,false,false,true)
                ,'method'=>'purchase_invoice'
            ),
            array(
                'val'=>'purchase_receipt'
                ,'label'=>Lang::get(array('Purchase Receipt'),true,true,false,false,true)
                ,'method'=>'purchase_receipt'
            ),
            array(
                'val'=>'purchase_return'
                ,'label'=>Lang::get(array('Purchase Return'),true,true,false,false,true)
                ,'method'=>'purchase_return'
            ),
        );
        //</editor-fold>
    }
    
    public static function path_get(){
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'rpt_purchase/'
            , 'rpt_purchase_engine' => ICES_Engine::$app['app_base_dir'] . 'rpt_purchase/rpt_purchase_engine'
            , 'rpt_purchase_data_support' => ICES_Engine::$app['app_base_dir'] . 'rpt_purchase/rpt_purchase_data_support'
            , 'rpt_purchase_renderer' => ICES_Engine::$app['app_base_dir'] . 'rpt_purchase/rpt_purchase_renderer'
            , 'rpt_purchase_download_excel' => ICES_Engine::$app['app_base_dir'] . 'rpt_purchase/rpt_purchase_download_excel'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'rpt_purchase/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'rpt_purchase/data_support/'
        );

        return json_decode(json_encode($path));
    }

     
    



}
?>
