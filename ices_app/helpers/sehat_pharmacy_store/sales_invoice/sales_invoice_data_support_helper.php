<?php

class Sales_Invoice_Data_Support {

    public static function sales_invoice_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select pi.*
                ,s.id customer_id
                ,s.code customer_code
                ,s.name customer_name
                ,st.id store_id
                ,st.code store_code
                ,st.name store_name
            from sales_invoice pi
            inner join customer s on pi.customer_id = s.id
            inner join store st on pi.store_id = st.id
            where pi.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $sales_invoice = $rs[0];
            $si_product = array();
            $si_product_mov_qty = array();
            $q = '
                select sip.*
                    ,p.code product_code
                    ,p.name product_name
                    ,u.code unit_code
                    ,u.name unit_name
                    ,u_sales.code unit_code_sales
                    ,u_sales.name unit_name_sales
                    
                from si_product sip
                inner join product p on sip.product_id = p.id
                inner join unit u on sip.unit_id = u.id
                inner join unit u_sales on sip.unit_id_sales = u_sales.id
                
                where sip.sales_invoice_id = ' . $db->escape($sales_invoice['id']) . '
            ';

            $rs = $db->query_array($q);
            if (count($rs) > 0) {
                $si_product = $rs;
            }
            
            $q = '
                select distinct sipmq.*
                    ,w.id warehouse_id
                    ,w.code warehouse_code
                    ,w.name warehouse_name
                    ,p.code product_code
                    ,p.name product_name
                    ,u.code unit_code
                    ,u.name unit_name
                    ,sip.product_id
                    ,sip.unit_id
                    ,pb.id product_batch_id
                    ,pb.expired_date
                    ,pb.batch_number
                from si_product_mov_qty sipmq
                inner join si_product sip on sipmq.si_product_id = sip.id
                inner join product p on p.id = sip.product_id and sip.product_type = "registered_product"
                inner join unit u on u.id = sip.unit_id
                left outer join product_stock_good psg 
                    on sipmq.ref_type = "product_stock_good"  and psg.id = sipmq.ref_id
                left outer join product_batch pb on pb.id = psg.product_batch_id
                left outer join warehouse w on psg.warehouse_id = w.id
                where sip.sales_invoice_id = '.$db->escape($sales_invoice['id']).'
                order by sip.product_id asc, w.id asc
            ';
            
            $rs = $db->query_array($q);
            if(count($rs)>0){
                $si_product_mov_qty = $rs;
            }
            
            $result['si_product'] = $si_product;
            $result['sales_invoice'] = $sales_invoice;
            $result['si_product_mov_qty'] = $si_product_mov_qty;
        }
        return $result;
        //</editor-fold>
    }

    
    public static function input_select_customer_search($lookup_data) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module' => 'customer', 'class_name' => 'customer_data_support'));
        $result = array();
        $t_customer_list = Customer_Data_Support::input_select_customer_search(array('lookup_data' => $lookup_data, 'customer_status' => 'active'));
        return $result = $t_customer_list;
        //</editor-fold>
    }

    public static function customer_dependency_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $customer_id = $param['customer_id'];
        $customer_detail = array();

        SI::module()->load_class(array('module' => 'customer', 'class_name' => 'customer_data_support'));
        $customer_detail = Customer_Data_Support::input_select_customer_detail_get(array('customer_id' => $customer_id));

        $result['customer_detail'] = $customer_detail;
        return $result;
        //</editor-fold>
    }

    public static function input_select_product_search($lookup_data) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module' => 'product', 'class_name' => 'product_data_support'));
        $result = array();
        $t_product_list = Product_Data_Support::input_select_product_search(
            array(
                'lookup_data' => $lookup_data, 
                'product_status' => 'active',
                'q_condition'=>''
            )
        );
        $result = $t_product_list;
        return $result;
        //</editor-fold>
    }

    public static function input_select_sales_invoice_search($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $lookup_data = $param['lookup_data'];
        $q_condition = $param['q_condition'];

        $db = new DB();


        $q = '
            select distinct si.*
            from sales_invoice si
            where si.status > 0
            and (
                si.code like ' . $db->escape('%' . $lookup_data . '%') . '
                or si.grand_total_amount like ' . $db->escape('%' . $lookup_data . '%') . '
            )
                ' . $q_condition . '
            order by si.id desc
            limit 100
        ';

        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            foreach ($rs as $idx => $row) {
                $result[] = array(
                    'id' => $row['id'],
                    'text' => Tools::html_tag('strong', $row['code'])
                    . ' ' . 'Grand Total Amount: ' . Tools::thousand_separator($row['grand_total_amount'])
                    ,
                    'ref_type' => 'sales_invoice',
                    'ref_outstanding_amount' => $row['outstanding_grand_total_amount'],
                );
            }
        }

        return $result;
        //</editor-fold>
    }

    public static function input_select_sales_invoice_detail_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $sales_invoice_id = $param['sales_invoice_id'];
        $t_sales_invoice = self::sales_invoice_get($sales_invoice_id);
        if (count($t_sales_invoice) > 0) {
            $sales_invoice = $t_sales_invoice['sales_invoice'];
            $pi_view_path = ICES_Engine::$app['app_base_url'] . 'sales_invoice/view/';
            $result = array(
                array('id' => 'code', 'label' => Lang::get('Code') . ': ', 'val' => '<a href="' . $pi_view_path . $sales_invoice_id . '" target="_blank">' . $sales_invoice['code'] . '</a>'),
                array('id' => 'type', 'label' => Lang::get('Type') . ': ', 'val' => 'Sales Invoice'),
                array('id' => 'sales_invoice_date', 'label' => Lang::get('Sales Invoice Date') . ': ', 'val' => Tools::_date($sales_invoice['sales_invoice_date'], 'F d, Y H:i')),
                array('id' => 'grand_total_amount', 'label' => 'Grand Total Amount: ', 'val' => Tools::thousand_separator($sales_invoice['grand_total_amount'])),
                array('id' => 'outstanding_grand_total_amount', 'label' => 'Outstanding Amount: ', 'val' => Tools::thousand_separator($sales_invoice['outstanding_grand_total_amount'])),
            );
        }

        return $result;
        //</editor-fold>
    }

    function notification_outstanding_grand_total_amount_get() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'rpt_simple/rpt_simple_data_support');
        $result = array('response' => null);
        $response = null;
        $temp_result = Rpt_Simple_Data_Support::report_table_sales_invoice_outstanding_grand_total_amount();
        if ($temp_result['info']['data_count'] > 0) {
            $response = array(
                'icon' => App_Icon::html_get(APP_Icon::sales_invoice())
                , 'href' => ICES_Engine::$app['app_base_url'] . 'rpt_simple/index/sales_invoice/outstanding_grand_total_amount'
                , 'msg' => ' ' . ($temp_result['info']['data_count']) . ' ' . 'sales invoice' . ' - ' . Lang::get('outstanding amount', true, false, false, true)
            );
        }
        $result['response'] = $response;
        return $result;
        //</editor-fold>
    }

}

?>