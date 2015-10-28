<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Batch_Data_Support {

    static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'product_batch/product_batch_engine');
        //</editor-fold>
    }

    public static function product_batch_get($id) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_data_support'));
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        $db = new DB();
        $result = array();
        $q = '
            select distinct pb.*
                ,u.code unit_code
                ,u.name unit_name
                ,p.code product_code
                ,p.name product_name
            from product_batch pb
            inner join unit u on pb.unit_id = u.id
            left outer join product p 
                on pb.product_id = p.id and pb.product_type = "registered_product"
            where pb.id = ' . $db->escape($id) . '
                and pb.status > 0
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $product_batch = $rs[0];
            $product_stock = array();
            $warehouse_list = Warehouse_Data_Support::warehouse_list_get(array('warehouse_status'=>'active'));
            $q = '
                select psg.id, warehouse_id
                from product_stock_good psg 
                where psg.status > 0
                and psg.product_batch_id = '.$db->escape($product_batch['id']).'
            ';
            $rs = $db->query_array($q);
            if(count($rs)>0){
                foreach($rs as $idx=>$row){
                    $param = array(
                        'module'=>'stock_good',
                        'product_batch'=>array($row['id']),
                        'warehouse'=>array($row['warehouse_id']),
                    );
                    $temp = Product_Stock_Data_Support::product_stock_mass_get($param);
                    if(count($temp)>0){
                        $t_product_stock = $temp[0];
                        $warehouse = Warehouse_Data_Support::warehouse_get($row['warehouse_id'])['warehouse'];
                        $t_product_stock['warehouse_code'] = $warehouse['code'];
                        $t_product_stock['warehouse_name'] = $warehouse['name'];
                        $product_stock[] = $t_product_stock;
                    }
                }
            }

            
            $result['product_stock'] = $product_stock;
            $result['product_batch'] = $product_batch;
//            die(var_dump($q));
        }
        return $result;
        //</editor-fold>
    }
    
    public static function product_batch_search($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $lookup_data = '%'.$param['lookup_data'].'%';
        $product_id = $param['product_id'];
        $unit_id = $param['unit_id'];
        $q_product_batch_status = isset($param['product_batch_status'])?
            ' and pb.product_batch_status = '.$db->escape($param['product_batch_status']):
            ''
        ;
        $q = '
            select distinct pb.*                
            from product_batch pb
            where pb.status > 0
            and (
                pb.batch_number like '.$db->escape($lookup_data).'
                or pb.expired_date like '.$db->escape($lookup_data).'
            )
            and pb.product_id = '.$db->escape($product_id).'
            and pb.unit_id = '.$db->escape($unit_id).'
            '.$q_product_batch_status.'
            order by pb.id desc
            limit 100
        ';
        $rs = $db->query_array($q);
        
        if(count($rs)>0){
            $result = $rs;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_product_batch_search($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $t_product_batch = self::product_batch_search($param);
        
        foreach($t_product_batch as $idx=>$row){
            $t_product_batch[$idx]['text'] = Tools::html_tag('strong',$row['batch_number'])
                .' '.$row['expired_date'];
        }
        $result = $t_product_batch;
        return $result;
        //</editor-fold>
    }
    
    public static function product_batch_list_validate($product_batch_list = array()){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        $db = new DB();
        
        $q_product_batch_list = 'select -1 product_batch_id';
        $product_batch_total = 0;
        foreach($product_batch_list as $idx=>$row){
            $product_batch_total+=1;
            $product_batch_id = Tools::_str(isset($row['product_batch_id'])?$row['product_batch_id']:'');
            $q_product_batch_list.=' union all select '.$db->escape($product_batch_id);
            if($success !== 1) break;
        }
        
        if($success === 1){
            $q = '
                select tp.*
                from ('.$q_product_batch_list.') tp
                inner join product_batch pb on tp.product_batch_id = pb.id 
                    and pb.status>0
            ';

            $rs = $db->query_array($q);

            if(count($rs) !== $product_batch_total){
                $success = 0;
                $msg[] = Lang::get('Product Batch')
                    .' '.Lang::get('invalid',true,false);
            }
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
    function notification_nearly_expired_get(){
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'rpt_simple/rpt_simple_data_support');
        $result = array('response'=>null);
        $response = null;        
        $temp_result = Rpt_Simple_Data_Support::report_table_product_batch_nearly_expired();        
        if($temp_result['info']['data_count']>0){
            $response = array(
                'icon'=>App_Icon::html_get(APP_Icon::purchase_invoice())
                ,'href'=>ICES_Engine::$app['app_base_url'].'rpt_simple/index/product_batch/nearly_expired'
                ,'msg'=>' '.($temp_result['info']['data_count']).' '.'product batch'.' - '.Lang::get('nearly expired',true,false,false,true)
            );
        }
        $result['response'] = $response;
        return  $result;
    }

}

?>