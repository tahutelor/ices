<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'controllers/ices/smart_search.php',
    'src_class' => 'Smart_Search',
    'src_extends_class' => '',
    'dst_class' => 'Smart_Search_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class Smart_Search extends Smart_Search_Parent {
    
    function __construct(){
        parent::__construct();
    }
    
    public function ajax_search($method=''){
        //<editor-fold defaultstate="collapsed">
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data'])?Tools::_str($data['data']):'';
        $result =array();
        switch($method){
            case 'smart_search':
                $db = new DB();
                $lookup_str = $db->escape('%'.$lookup_data.'%'); 
                //<editor-fold defaultstate="collapsed" desc="Query Customer">
                $q_contact = Security_Engine::get_controller_permission(
                    ICES_Engine::$app['val'], User_Info::get()['user_id'], 'customer', 'index')?
                    ' union all
                    select * from (
                        select distinct 
                        id
                        ,"Contact" module_text
                        ,concat(c.code)
                        ,concat(c.code," ",c.name)
                        , "customer" module

                        from customer c
                        where c.status>0
                        and (
                            c.code like '.$lookup_str.'
                            or c.name like '.$lookup_str.'
                        )
                        and c.customer_status = "active"
                        order by c.id desc
                    limit 100
                    ) t_customer
                    ':
                    '';
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Query Supplier">
                $q_supplier = Security_Engine::get_controller_permission(
                    ICES_Engine::$app['val'], User_Info::get()['user_id'], 'supplier', 'index')?
                    ' union all
                    select * from (
                        select distinct 
                        id
                        ,"Supplier" module_text
                        ,concat(s.code)
                        ,concat(s.code," ",s.name)
                        , "supplier" module

                        from supplier s
                        where s.status>0
                        and (
                            s.code like '.$lookup_str.'
                            or s.name like '.$lookup_str.'
                        )
                        and s.supplier_status = "active"
                        order by s.id desc
                        limit 100
                    ) t_supplier
                    ':
                    '';
                //</editor-fold>
                                
                //<editor-fold defaultstate="collapsed" desc="Query Product">
                $q_product = Security_Engine::get_controller_permission(
                    ICES_Engine::$app['val'], User_Info::get()['user_id'], 'product', 'index')?
                    ' union all
                    select * from (
                        select distinct 
                        p.id
                        ,"Product" module_text
                        ,concat(p.code)
                        ,concat(p.code," ",p.name)
                        , "product" module

                        from product p
                        where p.status>0
                        and (
                            p.code like '.$lookup_str.'
                            or p.name like '.$lookup_str.'
                        )
                        and p.product_status = "active"
                        order by p.id desc
                        limit 100
                    ) t_product
                    ':
                    '';
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Query Purchase Invoice">
                $q_purchase_invoice = Security_Engine::get_controller_permission(
                    ICES_Engine::$app['val'], User_Info::get()['user_id'], 'purchase_invoice', 'index')?
                    ' union all
                    select * from (
                        select distinct 
                        id
                        ,"'.Lang::get("Purchase Invoice").'"
                        ,concat(pi.code)
                        ,concat(pi.code," Grand Total Amount:",cast(format(pi.grand_total_amount,2) as char))
                        , "purchase_invoice"

                        from purchase_invoice pi
                        where pi.status>0
                        and (
                            pi.code like '.$lookup_str.'
                            or pi.grand_total_amount like '.$lookup_str.'
                        )
                        and pi.purchase_invoice_status = "invoiced"
                        order by pi.id desc
                        limit 100                        
                    ) t_pi
                    ':
                    '';
                //</editor-fold>
                
                
                $config = array(
                    'additional_filter'=>array(
                        
                    ),
                    'query'=>array(
                        'basic'=>'
                            select * from (
                                select 
                                    null id
                                    ,null module_text
                                    ,null data
                                    ,null description
                                    ,null module
                                limit 0,0'
                                .$q_contact
                                .$q_supplier
                                .$q_product
                                .$q_purchase_invoice
                        ,
                        'where'=>'
                            
                        ',
                        'group'=>'
                            )tfinal
                        ',
                        'order'=>'order by module_text, data asc'
                    ),
                );                
                $temp_result = SI::form_data()->ajax_table_search($config, $data,array('output_type'=>'object'));
                $t_data = $temp_result->data;
                foreach($t_data as $i=>$row){
                    $row->data = '<a target="_blank" href="'.ICES_Engine::$app['app_base_url'].$row->module.'/view/'.$row->id.'">'.$row->data.'</a>';
                }
                $temp_result = json_decode(json_encode($temp_result),true);
                $result = $temp_result;

                break;

        }
        
        echo json_encode($result);
        //</editor-fold>
    }

    
}