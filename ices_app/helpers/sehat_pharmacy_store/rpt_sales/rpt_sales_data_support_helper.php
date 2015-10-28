<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Sales_Data_Support{
    public static function sales_invoice_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        
        $q_sales_invoice_status = '';
        $q_sales_invoice_date = '';
        
        $sales_invoice_status = Tools::_str(isset($param['sales_invoice_status'])?$param['sales_invoice_status']:'');
        if(in_array($sales_invoice_status,array('invoiced','X'))){
            $q_sales_invoice_status = ' and pi.sales_invoice_status = '.$db->escape($sales_invoice_status);
        }
        
        $start_date = Tools::empty_to_null(isset($param['start_date'])?$param['start_date']:'');
        $end_date = Tools::empty_to_null(isset($param['end_date'])?$param['end_date']:'');
        if(!is_null($start_date) && !is_null($end_date)){
            $q_sales_invoice_date = ' and pi.sales_invoice_date between '
                .$db->escape($start_date).' and '.$db->escape($end_date);
        }
        
        
        
        $q = '
            select distinct pi.*
                ,s.code customer_code
                ,s.name customer_name
            from sales_invoice pi
            inner join customer s on pi.customer_id = s.id
            where pi.status > 0
                '.$q_sales_invoice_status.'
                '.$q_sales_invoice_date.'
            order by pi.sales_invoice_date desc
            
        ';
        
        $rs = $db->query_array($q,1000000);
        
        if(count($rs)>0) $result = $rs;
        
        return $result;
        //</editor-fold>
    }
    
    public static function sales_receipt_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        
        $q_sales_receipt_status = '';
        $q_sales_receipt_date = '';
        
        $sales_receipt_status = Tools::_str(isset($param['sales_receipt_status'])?$param['sales_receipt_status']:'');
        if(in_array($sales_receipt_status,array('invoiced','X'))){
            $q_sales_receipt_status = ' and pr.sales_receipt_status = '.$db->escape($sales_receipt_status);
        }
        
        $start_date = Tools::empty_to_null(isset($param['start_date'])?$param['start_date']:'');
        $end_date = Tools::empty_to_null(isset($param['end_date'])?$param['end_date']:'');
        if(!is_null($start_date) && !is_null($end_date)){
            $q_sales_receipt_date = ' and pr.sales_receipt_date between '
                .$db->escape($start_date).' and '.$db->escape($end_date);
        }

        $q = '
            select distinct pr.*
                ,pi.code sales_invoice_code
                ,s.code customer_code
                ,s.name customer_name
            from sales_receipt pr
            inner join sales_invoice pi 
                on pr.ref_id = pi.id and pr.ref_type = "sales_invoice"
            inner join customer s on pi.customer_id = s.id
            where pr.status > 0
                '.$q_sales_receipt_status.'
                '.$q_sales_receipt_date.'
            order by pr.sales_receipt_date desc
            
        ';
        
        $rs = $db->query_array($q,1000000);
        
        if(count($rs)>0) $result = $rs;
        
        return $result;
        //</editor-fold>
    }
    
}
?>