<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rpt_Purchase_Data_Support {

    public static function purchase_invoice_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();

        $q_purchase_invoice_status = '';
        $q_purchase_invoice_date = '';
        $q_supplier_id = '';

        $purchase_invoice_status = Tools::_str(isset($param['purchase_invoice_status']) ? $param['purchase_invoice_status'] : '');
        if (in_array($purchase_invoice_status, array('invoiced', 'X'))) {
            $q_purchase_invoice_status = ' and pi.purchase_invoice_status = ' . $db->escape($purchase_invoice_status);
        }
        
        $supplier_id = Tools::_str(isset($param['supplier_id']) ? $param['supplier_id'] : '');
        if (!in_array($supplier_id, array('all_supplier'))) {
            $q_supplier_id = ' and pi.supplier_id = ' . $db->escape($supplier_id);
        }

        $start_date = Tools::empty_to_null(isset($param['start_date']) ? $param['start_date'] : '');
        $end_date = Tools::empty_to_null(isset($param['end_date']) ? $param['end_date'] : '');
        if (!is_null($start_date) && !is_null($end_date)) {
            $q_purchase_invoice_date = ' and pi.purchase_invoice_date between '
                    . $db->escape($start_date) . ' and ' . $db->escape($end_date);
        }

        $q = '
            select distinct pi.*
                ,s.code supplier_code
                ,s.name supplier_name
            from purchase_invoice pi
            inner join supplier s on pi.supplier_id = s.id
            where pi.status > 0
                ' . $q_purchase_invoice_status . '
                ' . $q_purchase_invoice_date . '
                ' . $q_supplier_id . '
            order by pi.purchase_invoice_date desc
            
        ';

        $rs = $db->query_array($q, 1000000);

        if (count($rs) > 0)
            $result = $rs;

        return $result;
        //</editor-fold>
    }

    public static function purchase_receipt_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();

        $q_purchase_receipt_status = '';
        $q_purchase_receipt_date = '';

        $purchase_receipt_status = Tools::_str(isset($param['purchase_receipt_status']) ? $param['purchase_receipt_status'] : '');
        if (in_array($purchase_receipt_status, array('invoiced', 'X'))) {
            $q_purchase_receipt_status = ' and pr.purchase_receipt_status = ' . $db->escape($purchase_receipt_status);
        }

        $start_date = Tools::empty_to_null(isset($param['start_date']) ? $param['start_date'] : '');
        $end_date = Tools::empty_to_null(isset($param['end_date']) ? $param['end_date'] : '');
        if (!is_null($start_date) && !is_null($end_date)) {
            $q_purchase_receipt_date = ' and pr.purchase_receipt_date between '
                    . $db->escape($start_date) . ' and ' . $db->escape($end_date);
        }

        $q = '
            select distinct pr.*
                ,pi.code purchase_invoice_code
                ,s.code supplier_code
                ,s.name supplier_name
            from purchase_receipt pr
            inner join purchase_invoice pi 
                on pr.ref_id = pi.id and pr.ref_type = "purchase_invoice"
            inner join supplier s on pi.supplier_id = s.id
            where pr.status > 0
                ' . $q_purchase_receipt_status . '
                ' . $q_purchase_receipt_date . '
            order by pr.purchase_receipt_date desc
            
        ';

        $rs = $db->query_array($q, 1000000);

        if (count($rs) > 0)
            $result = $rs;

        return $result;
        //</editor-fold>
    }

    public static function purchase_return_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();

        $q_purchase_return_status = '';
        $q_purchase_return_date = '';

        $purchase_return_status = Tools::_str(isset($param['purchase_return_status']) ? $param['purchase_return_status'] : '');
        if (in_array($purchase_return_status, array('returned', 'X'))) {
            $q_purchase_return_status = ' and pr.purchase_return_status = ' . $db->escape($purchase_return_status);
        }

        $start_date = Tools::empty_to_null(isset($param['start_date']) ? $param['start_date'] : '');
        $end_date = Tools::empty_to_null(isset($param['end_date']) ? $param['end_date'] : '');
        if (!is_null($start_date) && !is_null($end_date)) {
            $q_purchase_return_date = ' and pr.purchase_return_date between '
                    . $db->escape($start_date) . ' and ' . $db->escape($end_date);
        }

        $q = '
            select distinct pr.*
                ,pi.code purchase_invoice_code
                ,s.code supplier_code
                ,s.name supplier_name
            from purchase_return pr
            inner join purchase_invoice pi 
                on pr.ref_id = pi.id and pr.ref_type = "purchase_invoice"
            inner join supplier s on pi.supplier_id = s.id
            where pr.status > 0
                ' . $q_purchase_return_status . '
                ' . $q_purchase_return_date . '
            order by pr.purchase_return_date desc
            
        ';

        $rs = $db->query_array($q, 1000000);

        if (count($rs) > 0)
            $result = $rs;

        return $result;
        //</editor-fold>
    }

    public static function input_select_supplier_search($lookup_data) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module' => 'supplier', 'class_name' => 'supplier_data_support'));
        $result = array();
        
        $t_supplier_list = Supplier_Data_Support::input_select_supplier_search(array('lookup_data' => $lookup_data, 'supplier_status' => 'active'));
        $result[] = array(
            'id'=>'all_supplier',
            'text'=>Tools::html_tag('strong','All Supplier'),
        );
        return $result = array_merge($result,$t_supplier_list);
        //</editor-fold>
    }

}

?>