<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_Form_Data_Support {
    public static function product_stock_opname_product_list_get($opt = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $warehouse_id = $opt['warehouse_id'];
        $product_category_id = $opt['product_category_id'];
        $q = '
            select distinct p.code product_code
                ,p.name product_name
                ,u.code unit_code
                ,u.name unit_name
                ,pb.*
                ,psg.qty
                                   
            from product_batch pb
            inner join product p on pb.product_id = p.id and pb.status>0
            inner join unit u on pb.unit_id = u.id and u.status>0
            inner join product_stock_good psg on psg.product_batch_id = pb.id
            inner join product_category pc on p.product_category_id = pc.id
            inner join (
                select route from product_category pc where pc.id = '.$db->escape($product_category_id).'
            ) pcr on pc.route like concat(pcr.route,"%")
            where 1 = 1 
                and psg.warehouse_id = '.$db->escape($warehouse_id).'
                and pb.product_batch_status = "active"
                and(
                    pb.qty > 0
                    or pb.expired_date > date_add(now(), interval -90 DAY)
                )
                
            order by p.name asc, u.name asc, pb.expired_date desc
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $result = $rs;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_product_category_search($lookup_data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'product_category','class_name'=>'product_category_data_support'));
        $result = array();
        $param = array(
            'lookup_data'=>$lookup_data,
            'q_condition'=>' and pc.product_category_status = "active" '
        );
        $result = Product_Category_Data_Support::input_select_product_category_search($param);
        return $result;
        //</editor-fold>
    }
    
}
?>