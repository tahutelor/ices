<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Product_Engine {
    public static $prefix_id = 'rpt_product';
    public static $module_type_list;
    
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        self::$module_type_list = array(
            array(
                'val'=>'product_stock'
                ,'label'=>Lang::get(array('Product Stock'),true,true,false,false,true)
                ,'method'=>'product_stock'
            ),
            
        );
        //</editor-fold>
    }
    
    public static function path_get(){
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'rpt_product/'
            , 'rpt_product_engine' => ICES_Engine::$app['app_base_dir'] . 'rpt_product/rpt_product_engine'
            , 'rpt_product_data_support' => ICES_Engine::$app['app_base_dir'] . 'rpt_product/rpt_product_data_support'
            , 'rpt_product_renderer' => ICES_Engine::$app['app_base_dir'] . 'rpt_product/rpt_product_renderer'
            , 'rpt_product_download_excel' => ICES_Engine::$app['app_base_dir'] . 'rpt_product/rpt_product_download_excel'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'rpt_product/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'rpt_product/data_support/'
        );

        return json_decode(json_encode($path));
    }

     
    



}
?>
