<?php
class Smart_Search_Engine {
    public static $prefix_id = 'smart_search';
    public static $prefix_method;
    public static $status_list; 

    public static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        self::$prefix_method = self::$prefix_id;
        
        self::$status_list = array(
            //<editor-fold defaultstate="collapsed">
            array(
                'val'=>''
                ,'text'=>''
                ,'method'=>'smart_search_add'
                ,'next_allowed_status'=>array()
                ,'msg'=>array(
                    'success'=>array(
                        array('val'=>'Add')
                        ,array('val'=>Lang::get(array('Security Controller'),true,true,false,false,true))
                        ,array('val'=>'success')
                    )
                )
            ),
            array(
                'val'=>'active'
                ,'text'=>'ACTIVE'
                ,'method'=>'smart_search_active'
                ,'next_allowed_status'=>array('inactive')
                ,'default'=>true
                ,'msg'=>array(
                    'success'=>array(
                        array('val'=>'Update')
                        ,array('val'=>Lang::get(array('Security Controller'),true,true,false,false,true))
                        ,array('val'=>'success')
                    )
                )
            ),
            array(
                'val'=>'inactive'
                ,'text'=>'INACTIVE'
                ,'method'=>'smart_search_inactive'
                ,'next_allowed_status'=>array('active')
                ,'msg'=>array(
                    'success'=>array(
                        array('val'=>'Update')
                        ,array('val'=>Lang::get(array('Security Controller'),true,true,false,false,true))
                        ,array('val'=>'success')
                    )
                )
            ),
            //</editor-fold>
        );
        
        //</editor-fold>
    }
    
    public static function path_get(){
        $path = array(
            'index'=>ICES_Engine::$app['app_base_url'].'smart_search/'
            ,'smart_search_engine'=>  ICES_Engine::$app['app_base_dir'].'smart_search/smart_search_engine'
            ,'smart_search_data_support'=>ICES_Engine::$app['app_base_dir'].'smart_search/smart_search_data_support'
            ,'smart_search_renderer' => ICES_Engine::$app['app_base_dir'].'smart_search/smart_search_renderer'
            ,'ajax_search'=>ICES_Engine::$app['app_base_url'].'smart_search/ajax_search/'
            ,'data_support'=>ICES_Engine::$app['app_base_url'].'smart_search/data_support/'

        );

        return json_decode(json_encode($path));
    }
    
}
?>
