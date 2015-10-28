<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'helpers/ices/rpt_simple/rpt_simple_data_support_helper.php',
    'src_class' => 'Rpt_Simple_Data_Support',
    'src_extends_class' => '',
    'dst_class' => 'Rpt_Simple_Data_Support_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class Rpt_Simple_Data_Support extends Rpt_Simple_Data_Support_Parent{
    
    public function report_table_purchase_invoice_outstanding_grand_total_amount($cfg=array()){
        //<editor-fold defaultstate="collapsed">        
        $result = array('column'=>array(),'data'=>array(),'info'=>array());
        $db = new DB();
        //read config
        $thousand_separator = isset($cfg['thousand_separator'])?$cfg['thousand_separator']:true;
        //end of read config
        $column = array(
            array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')),
            array("name"=>"code","label"=>"Code",'col_attrib'=>array('style'=>'text-align:left'),'is_key'=>true),
            array("name"=>"purchase_invoice_date","label"=>Lang::get("Purchase Invoice Date"),'col_attrib'=>array('style'=>'text-align:left')),
            array("name"=>"grand_total_amount","label"=>"Grand Total Amount",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'),
            array("name"=>"outstanding_grand_total_amount","label"=>"Outstanding Grand Total Amount",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'),
        );
        
        $data = array();
        
        $q = '
            select null row_num, t1.*
            from purchase_invoice t1
            where t1.outstanding_grand_total_amount>0
                and t1.status>0
                and t1.purchase_invoice_status != "X"
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            for($i = 0;$i<count($rs);$i++){
                $rs[$i]['row_num'] = $i+1;
                if($thousand_separator){
                    $rs[$i]['grand_total_amount'] = Tools::thousand_separator($rs[$i]['grand_total_amount']);
                    $rs[$i]['outstanding_grand_total_amount'] = Tools::thousand_separator($rs[$i]['outstanding_grand_total_amount']);
                }
            }
            $data = $rs;
        }
        $info = array(
            'data_count'=>count($data),
            'base_href'=>(ICES_Engine::$app['app_base_url'].'purchase_invoice/view/')
            
            // end of info
        );
        
        
        $result['column'] = $column;
        $result['data'] = $data;
        $result['info'] = $info;
        return $result;
        //</editor-fold>
    }
    
    public function report_table_sales_invoice_outstanding_grand_total_amount($cfg=array()){
        //<editor-fold defaultstate="collapsed">        
        $result = array('column'=>array(),'data'=>array(),'info'=>array());
        $db = new DB();
        //read config
        $thousand_separator = isset($cfg['thousand_separator'])?$cfg['thousand_separator']:true;
        //end of read config
        $column = array(
            array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')),
            array("name"=>"code","label"=>"Code",'col_attrib'=>array('style'=>'text-align:left'),'is_key'=>true),
            array("name"=>"sales_invoice_date","label"=>Lang::get("Sales Invoice Date"),'col_attrib'=>array('style'=>'text-align:left')),
            array("name"=>"grand_total_amount","label"=>"Grand Total Amount",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'),
            array("name"=>"outstanding_grand_total_amount","label"=>"Outstanding Grand Total Amount",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'),
        );
        
        $data = array();
        
        $q = '
            select distinct null row_num, t1.*
            from sales_invoice t1
                inner join customer c on t1.customer_id = c.id
                inner join customer_type ct on c.customer_type_id = ct.id
            where t1.outstanding_grand_total_amount>0
                and t1.status>0
                and t1.sales_invoice_status = "invoiced"
                and ct.notif_si_outstanding = "1"
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            for($i = 0;$i<count($rs);$i++){
                $rs[$i]['row_num'] = $i+1;
                if($thousand_separator){
                    $rs[$i]['grand_total_amount'] = Tools::thousand_separator($rs[$i]['grand_total_amount']);
                    $rs[$i]['outstanding_grand_total_amount'] = Tools::thousand_separator($rs[$i]['outstanding_grand_total_amount']);
                }
            }
            $data = $rs;
        }
        $info = array(
            'data_count'=>count($data),
            'base_href'=>(ICES_Engine::$app['app_base_url'].'sales_invoice/view/')
            
            // end of info
        );
        
        
        $result['column'] = $column;
        $result['data'] = $data;
        $result['info'] = $info;
        return $result;
        //</editor-fold>
    }
    
    public function report_table_product_batch_nearly_expired($cfg=array()){
        //<editor-fold defaultstate="collapsed">        
        $result = array('column'=>array(),'data'=>array(),'info'=>array());
        $db = new DB();
        //read config
        $thousand_separator = isset($cfg['thousand_separator'])?$cfg['thousand_separator']:true;
        //end of read config
        $column = array(
            array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')),
            array("name"=>"product_text","label"=>"Product",'col_attrib'=>array('style'=>'text-align:left')),
            array("name"=>"batch_number","label"=>"Batch Number",'col_attrib'=>array('style'=>'text-align:left'),'is_key'=>true),
            array("name"=>"expired_date","label"=>Lang::get("Expired Date"),'col_attrib'=>array('style'=>'text-align:left')),
            array("name"=>"qty","label"=>"Qty",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'),
            array("name"=>"unit_text","label"=>"Unit",'col_attrib'=>array('style'=>'text-align:left')),
        );
        
        $data = array();
        
        $q = '
            select pb.*
                ,p.code product_code
                ,p.name product_name
                ,concat(p.code, p.name) product_text
                ,u.code unit_code
                ,u.name unit_name
                ,concat(u.code, u.name) unit_text
                
            from product_batch pb
            left outer join product p on pb.product_id = p.id and pb.product_type = "registered_product"
            left outer join unit u on pb.unit_id = u.id
            where pb.status > 0
                and pb.product_batch_status = "active"
                and pb.expired_date < date_add(now(),interval +90 day)
                and pb.expired_date > now()
                and pb.qty > 0
            order by product_text, expired_date asc
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            for($i = 0;$i<count($rs);$i++){
                $rs[$i]['row_num'] = $i+1;
                if($thousand_separator){
                    $rs[$i]['qty'] = Tools::thousand_separator($rs[$i]['qty']);
                    $rs[$i]['product_text'] = '<a target="_blank" href="'.ICES_Engine::$app['app_base_url'].'product/view/'.$rs[$i]['product_id'].'">'
                        .Tools::html_tag('strong',$rs[$i]['product_code'])
                        .' '.$rs[$i]['product_name'].'</a>';
                    $rs[$i]['unit_text'] = Tools::html_tag('strong',$rs[$i]['unit_code'])
                        .' '.$rs[$i]['unit_name'];
                    $rs[$i]['expired_date'] = Tools::_date($rs[$i]['expired_date'],'F d, Y H:i:s');
                }
            }
            $data = $rs;
        }
        $info = array(
            'data_count'=>count($data),
            'base_href'=>(ICES_Engine::$app['app_base_url'].'product_batch/view/')
            
            // end of info
        );
        
        
        $result['column'] = $column;
        $result['data'] = $data;
        $result['info'] = $info;
        return $result;
        //</editor-fold>
    }
    
}
?>