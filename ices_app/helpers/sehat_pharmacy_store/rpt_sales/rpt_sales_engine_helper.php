<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Sales_Engine {
    public static $prefix_id = 'rpt_sales';
    public static $module_type_list;
    
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        self::$module_type_list = array(
            array(
                'val'=>'sales_invoice'
                ,'label'=>Lang::get(array('Sales Invoice'),true,true,false,false,true)
                ,'method'=>'sales_invoice'
            ),
            array(
                'val'=>'sales_receipt'
                ,'label'=>Lang::get(array('Sales Receipt'),true,true,false,false,true)
                ,'method'=>'sales_receipt'
            ),
            
        );
        //</editor-fold>
    }
    
    public static function path_get(){
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'rpt_sales/'
            , 'rpt_sales_engine' => ICES_Engine::$app['app_base_dir'] . 'rpt_sales/rpt_sales_engine'
            , 'rpt_sales_data_support' => ICES_Engine::$app['app_base_dir'] . 'rpt_sales/rpt_sales_data_support'
            , 'rpt_sales_renderer' => ICES_Engine::$app['app_base_dir'] . 'rpt_sales/rpt_sales_renderer'
            , 'rpt_sales_download_excel' => ICES_Engine::$app['app_base_dir'] . 'rpt_sales/rpt_sales_download_excel'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'rpt_sales/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'rpt_sales/data_support/'
        );

        return json_decode(json_encode($path));
    }

     
    



}
?>
