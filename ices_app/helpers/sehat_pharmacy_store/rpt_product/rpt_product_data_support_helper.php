<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Product_Data_Support{
    public static function product_stock_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        
        $q_product_status = '';
        $q_product_batch_expired = '';
        $warehouse_id = Tools::_str($param['warehouse_id']);
        $keyword = '%'.Tools::_str($param['keyword']).'%';
        
        $product_status = Tools::_str(isset($param['product_status'])?$param['product_status']:'');
        if(in_array($product_status,array('active','inactive'))){
            $q_product_status = ' and p.product_status = '.$db->escape($product_status);
        }
        
        $product_batch_expired = Tools::_str(isset($param['product_batch_expired'])?$param['product_batch_expired']:'');
        if($product_batch_expired === '1'){
            $q_product_batch_expired = 'and pb.expired_date < now()';
        }
        else if ($product_batch_expired === '0'){
            $q_product_batch_expired = 'and pb.expired_date > now()';
        }
        
        $q = '
            select distinct 
                p.id
                ,p.id product_id
                ,pc.code product_category_code
                ,pc.name product_category_name
                ,p.code product_code
                ,p.name product_name
                ,u.id unit_id
                ,u.code unit_code
                ,u.name unit_name
                ,sum(ps.qty) qty
                ,p.product_status
                ,pu.purchase_amount 
                ,replace(pu.sales_formula,"c",pu.purchase_amount ) sales_amount
                
            from product p
            inner join product_batch pb 
                on p.id = pb.product_id and pb.product_type = "registered_product"
                and pb.product_batch_status = "active"
            inner join product_stock_good ps on pb.id  = ps.product_batch_id
            inner join unit u on pb.unit_id = u.id
            inner join product_category pc on p.product_category_id = pc.id
            inner join p_u pu on p.id = pu.product_id and u.id = pu.unit_id
            where p.status > 0
                and pb.status > 0
                '.$q_product_status.'
                '.$q_product_batch_expired.'
                and ps.warehouse_id = '.$db->escape($warehouse_id).'
                and (
                    p.code like '.$db->escape($keyword).'
                    or p.name like '.$db->escape($keyword).'
                    or pc.code like '.$db->escape($keyword).'
                    or pc.name like '.$db->escape($keyword).'
                )
            group by p.id, u.id    
            order by p.name asc
            
        ';
        
        $rs = $db->query_array($q,1000000);
        
        if(count($rs)>0) $result = $rs;
        
        return $result;
        //</editor-fold>
    }
    
}
?>