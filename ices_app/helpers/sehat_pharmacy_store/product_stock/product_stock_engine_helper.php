<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_Stock_Engine {
    
    public static $module_list;
    
    public static function helper_init(){
        self::$module_list = array(
            array('val'=>'stock_good','label'=>'Good Stock'),
            array('val'=>'stock_bad','label'=>'Bad Stock'),
        );
    }
    
    function __construct(){
        
    }
    
    public static function path_get() {
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'product_stock/'
            , 'product_stock_engine' => ICES_Engine::$app['app_base_dir'] . 'product_stock/product_stock_engine'
            , 'product_stock_data_support' => ICES_Engine::$app['app_base_dir'] . 'product_stock/product_stock_data_support'
            , 'product_stock_renderer' => ICES_Engine::$app['app_base_dir'] . 'product_stock/product_stock_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'product_stock/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'product_stock/data_support/'
        );

        return json_decode(json_encode($path));
    }
    
    public function make_stock_available($db,$module,$ref_type,$ref_id,$warehouse_id, $product_batch_id, $date){
        //<editor-fold defaultstate="collapsed">
        $success = 1;
        $msg = array();
        $exists = false;
        //$db = new DB();
        $result = array(
            'success' => 1
            ,'msg'=>array()
        );
        $tbl_name = '';
        if(!in_array($module,array('stock_good','stock_bad'))){
            $success = 0;
            $msg[] = 'Invalid Module';
        }
        else{
            switch($module){
                case 'stock_good':
                    $tbl_name = 'product_stock_good';
                    break;
                case 'stock_bad':
                    $tbl_name = 'product_stock_bad';
                    break;
            }
        }
        
        if($success === 1){
            
            $q = '
                select 1 
                from '.$tbl_name.'
                where 
                    status = 1
                    and product_batch_id = '.$db->escape($product_batch_id).'
                    and warehouse_id = '.$db->escape($warehouse_id).'
            ';

            $rs = $db->query_array_obj($q);

            if(count($rs)>0){
                $exists = true;
            }

            if(!$exists){            

                try{
                    $qty = 0;
                    $modid = User_Info::get()['user_id'];
                    $moddate = date('Y-m-d H:i:s');
                    //$db->trans_begin();

                    $stock_param = array(
                        'product_batch_id'=>$product_batch_id
                        ,'warehouse_id'=>$warehouse_id
                        ,'modid'=>  $modid
                        ,'moddate'=> $moddate
                        ,'status'=>'1'
                        ,'qty'=>$qty
                    );

                    if(!$db->insert($tbl_name,$stock_param)){
                        $success = 0;
                        $msg[] = $db->_error_message();
                        $db->trans_rollback();
                    }

                    if($success == 1){
                        $q = '
                            select id from '.$tbl_name.'
                            where product_batch_id = '.$db->escape($product_batch_id).'
                                and warehouse_id = '.$db->escape($warehouse_id).'
                        ';

                        $item_stock = $db->query_array_obj($q)[0];
                        $history_param = array(
                            'ref_type'=>$ref_type
                            ,'ref_id'=>$ref_id
                            ,'product_'.$module.'_id'=>$item_stock->id
                            ,'old_qty'=>'0'
                            ,'qty'=>$qty
                            ,'new_qty'=>$qty
                            ,'modid'=>$modid
                            ,'moddate'=>$moddate
                            ,'description'=>'Product Stock Initialization'
                            ,'transactional_date'=>$date
                        );
                        if(!$db->insert($tbl_name.'_qty_log',$history_param)){
                            $success = 0;
                            $msg[] = $db->_error_message();
                            $db->trans_rollback();
                        }
                    }

                }catch(Exception $e){
                    $db->trans_rollback();
                    $msg[] = $e->getMessage();
                    $success = 0;
                }
                
            }
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        
        return $result;
        //</editor-fold>
    }
    
    public function stock_add($db,$module,$ref_type,$ref_id,$warehouse_id="", $product_batch_id="", $qty="", $description="",$date = ""){
        //<editor-fold defaultstate="collapsed">
        $result = array('success'=>1,'msg'=>array(),'trans_id'=>'');
        $success = 1;
        $msg = array();
        $trans_id = '';
        if(strlen($warehouse_id) == 0 || strlen($product_batch_id) == 0 
                || strlen($qty) == 0 
                || strlen($description) == 0
                || strlen($date) == 0
        ){
            $success  = 0;
            $msg[] = 'incomplete parameter';
            return $result;
        }
        
        $tbl_name = '';
        if(!in_array($module,array('stock_good','stock_bad'))){
            $success = 0;
            $msg[] = 'Invalid Module';
        }
        else{
            switch($module){
                case 'stock_good':
                    $tbl_name = 'product_stock_good';
                    break;
                case 'stock_bad':
                    $tbl_name = 'product_stock_bad';
                    break;
            }
        }
        
        
        if($success === 1){
            $temp_result = self::make_stock_available($db,$module,$ref_type,$ref_id,$warehouse_id,$product_batch_id,$date);
            if($temp_result['success'] !== 1) $success = 0;
            $msg = $temp_result['msg'];

            if($success === 1){
                try{
                    $product_stock_old = null;
                    $product_stock = null;
                    
                    $moddate = date('Y-m-d H:i:s');
                    $modid = User_Info::get()['user_id'];
                    
                    $q = '
                        select * from '.$tbl_name.'
                        where product_batch_id = '.$db->escape($product_batch_id).'
                            and warehouse_id = '.$db->escape($warehouse_id).'
                    ';
                    $rs = $db->query_array_obj($q);
                    
                    if(count($rs)>0){
                        $product_stock_old = $rs[0];
                    }
                    else{
                        $success = 0;
                        $msg[] = 'Unable to get old stock';
                        $db->trans_rollback();
                    }
                    
                    $q = '
                        update '.$tbl_name.' 
                        set qty = qty+'.$db->escape($qty).'
                            ,modid = '.$db->escape($modid).'
                            ,moddate = '.$db->escape($moddate).'
                        where product_batch_id = '.$db->escape($product_batch_id).'
                            and warehouse_id = '.$db->escape($warehouse_id).'    
                    ';

                    if(!$db->query($q)){
                        $success = 0;
                        $msg[] = $db->_error_message();
                        $db->trans_rollback();
                    }
                                        
                    
                    if($success === 1){
                        $q = '
                            select * from '.$tbl_name.'
                            where product_batch_id = '.$db->escape($product_batch_id).'
                                and warehouse_id = '.$db->escape($warehouse_id).'
                                and qty >= 0
                        ';
                        $rs = $db->query_array_obj($q);
                        if(!count($rs)>0){
                            $success = 0;
                            $msg[] = 'Stock Qty less than 0';
                            $db->trans_rollback();
                        }
                        else{
                            $product_stock = $db->query_array_obj($q)[0];
                            $trans_id = $product_stock->id;
                        }
                        
                    }
                    
                    if($success === 1){
                        
                        $history_param = array(
                            'ref_type'=>$ref_type
                            ,'ref_id'=>$ref_id
                            ,'product_'.$module.'_id'=>$product_stock->id
                            ,'qty'=>$qty
                            ,'old_qty'=>$product_stock_old->qty
                            ,'new_qty'=>$product_stock->qty
                            ,'modid'=>$modid
                            ,'moddate'=>$moddate
                            ,'description'=>$description
                            ,'transactional_date'=>$date
                        );

                        if(!$db->insert($tbl_name.'_qty_log',$history_param)){
                            $success = 0;
                            $msg[] = $db->_error_message();
                            $db->trans_rollback();
                        }
                        

                    }

                }
                catch(Exception $e){
                    $success = 0;
                    $msg[] = $e->getMessage();
                    $db->trans_rollback();
                }

            }
            
        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['trans_id'] = $trans_id;
        return $result;
        //</editor-fold>
    }
    
    public static function stock_good_add($db,$ref_type, $ref_id,$warehouse_id="", $product_batch_id="", $qty="", $description="",$date = ""){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_engine'));
        $success = 1;
        $msg = array();
        $result = array('success'=>1,'msg'=>array(),'trans_id'=>'');
        //$db = new DB();
        //$db->trans_begin();
        
        try{
            $product_stock_id = '';
            if($success === 1){ 
                $temp_result = self::stock_add($db,'stock_good',$ref_type,$ref_id,$warehouse_id, $product_batch_id, $qty, $description,$date);
                if($temp_result['success'] !== 1){
                    $success = 0;
                    $msg = array_merge($temp_result['msg']);
                }
                else{
                    $product_stock_id = $temp_result['trans_id'];
                    $result['trans_id'] = $product_stock_id;
                }
            }
            
            if($success === 1){
                $param = array(
                    'product_batch'=>array(
                        'qty'=>$qty
                    ),
                    'product_batch_qty_log'=>array(
                        'ref_type'=>'product_stock_good',
                        'ref_id'=>$product_stock_id,
                        'qty'=>$qty,
                        'description'=>$description
                    ),
                );                
                $temp_result = Product_Batch_Engine::product_batch_qty_add($db, $param, $product_batch_id);
                if($temp_result['success'] !== 1){
                    $success = 0;
                    $msg = array_merge($temp_result['msg']);
                }
            }
        }
        catch(Exception $e){
            $success = 0;
            $msg[] = $e->getMessage();
            $db->trans_rollback();
        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        
        return $result;
        //</editor-fold>
    }
    
    public static function stock_bad_add($db,$ref_type, $ref_id,$warehouse_id="", $product_batch_id="", $qty="", $description="",$date = ""){
        //<editor-fold defaultstate="collapsed">
        $success = 1;
        $msg = array();
        $result = array('success'=>1,'msg'=>array(),'trans_id');
        //$db = new DB();
        //$db->trans_begin();
        
        try{
            $product_stock_id = '';
            if($success === 1){ 
                $temp_result = self::stock_add($db,'stock_bad',$ref_type,$ref_id,$warehouse_id, $product_batch_id, $qty, $description,$date);
                if($temp_result['success'] !== 1){
                    $success = 0;
                    $msg = array_merge($temp_result['msg']);
                }
                else{
                    $product_stock_id = $temp_result['trans_id'];
                    $result['trans_id'] = $product_stock_id;
                }
                
            }
            
            if($success === 1){
                $param = array(
                    'product_batch'=>array(
                        'qty'=>$qty
                    ),
                    'product_batch_qty_log'=>array(
                        'ref_type'=>'product_stock_bad',
                        'ref_type'=>$product_stock_id,
                        'qty'=>$qty,
                        'description'=>$description
                    ),
                );                
                $temp_result = Product_Batch_Engine::product_batch_qty_add($db, $param, $product_batch_id);
                if($temp_result['success'] !== 1){
                    $success = 0;
                    $msg = array_merge($temp_result['msg']);
                }
            }
            
        }
        catch(Exception $e){
            $success = 0;
            $msg[] = $e->getMessage();
            $db->trans_rollback();
        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        
        return $result;
        //</editor-fold>
    }
    
        
    
    
    
}
?>
