<?php

class Supplier_Data_Support {

    public static function supplier_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select s.*
            from supplier s   
            where s.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $supplier = $rs[0];
            $sup_address = array();
            $sup_mail_address = array();
            $sup_phone_number = array();

            $q = '
                select sa.*
                from sup_address sa
                where sa.supplier_id = ' . $db->escape($id) . '
            ';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $sup_address = $rs;

            $q = '
                select sma.* 
                from sup_mail_address sma 
                where sma.supplier_id=' . $db->escape($id) . '
            ';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $sup_mail_address = $rs;


            $q = 'select cpn.*
                    ,pnt.name phone_number_type_name
                    ,pnt.code phone_number_type_code
                from sup_phone_number cpn 
                inner join phone_number_type pnt on pnt.id = cpn.phone_number_type_id 
                where cpn.supplier_id=' . $db->escape($id) . '
            ';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $sup_phone_number = $rs;


            $result['supplier'] = $supplier;
            $result['sup_address'] = $sup_address;
            $result['sup_mail_address'] = $sup_mail_address;
            $result['sup_phone_number'] = $sup_phone_number;
        }
        return $result;
        //</editor-fold>
    }

    public static function supplier_type_list_get() {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        SI::module()->load_class(array('module'=>'supplier_type','class_name'=>'supplier_type_data_support'));
        $t_supplier_type = Supplier_Type_Data_Support::supplier_type_list_get(array('supplier_type_status'=>'active'));
        $result = $t_supplier_type;
        return $result;
        //</editor-fold>
    }

    public static function supplier_type_default_get() {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        SI::module()->load_class(array('module'=>'supplier_type','class_name'=>'supplier_type_data_support'));
        $t_supplier_type = Supplier_Type_Data_Support::supplier_type_default_get(array('supplier_type_status'=>'active'));
        $result = $t_supplier_type;
        return $result;
        //</editor-fold>
    }
    
    public static function phone_number_type_get() {
        //<editor-fold defaultstate="collapsed">
        $result = array();
       SI::module()->load_class(array('module'=>'phone_number_type','class_name'=>'phone_number_type_data_support'));
        $t_phone_number_type = Phone_Number_Type_Data_Support::phone_number_type_list_get(array('phone_number_type_status'=>'active'));
        $result = $t_phone_number_type;
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_supplier_search($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $lookup_data = '%'.$param['lookup_data'].'%';
        $q_supplier_status = isset($param['supplier_status'])?
            ' and s.supplier_status = '.$db->escape($param['supplier_status']):
            ''
        ;
        
        $q = '
            select distinct s.*
            from supplier s
                left outer join sup_address sa on s.id = sa.supplier_id
                left outer join sup_phone_number spn on s.id = spn.supplier_id
            where s.status > 0
                and(
                    s.code like '.$db->escape($lookup_data).'
                    or s.name like '.$db->escape($lookup_data).'
                    or sa.address like '.$db->escape($lookup_data).'
                    or spn.phone_number like '.$db->escape($lookup_data).'
                )
                '.$q_supplier_status.'
            order by s.code
            limit 20
            
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            foreach($rs as $idx=>$row){
                $result[] = array(
                    'id'=>$row['id'],
                    'text'=>Tools::html_tag('strong',$row['code']).' '.$row['name']
                );
            }
        }
        return $result;
        //</editor-fold>
    }

    public static function input_select_supplier_detail_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'supplier/supplier_engine');
        $sup_path = Supplier_Engine::path_get();
        $result = array();
        $supplier_id = $param['supplier_id'];
        $t_supplier = self::supplier_get($supplier_id);
        if(count($t_supplier)>0){
            $supplier = $t_supplier['supplier'];
            $sup_address = $t_supplier['sup_address'];
            $sup_phone_number = $t_supplier['sup_phone_number'];
            $address = count($sup_address)>0 ?$sup_address[0]['address']:'';
            $phone_number = count($sup_phone_number)>0?$sup_phone_number[0]['phone_number']:'';
            $result = array(
                array('id'=>'code','label'=>'Code: ','val'=>'<a href="'.$sup_path->index.'view/'.$supplier_id.'" target="_blank">'.$supplier['code'].'</a>'),
                array('id'=>'type','label'=>'Name: ','val'=>$supplier['name']),
                array('id'=>'phone_number','label'=>'Phone Number: ','val'=>$phone_number),
                array('id'=>'address','label'=>'Address: ','val'=>$address),
            );
        }
        
        return $result;
        //</editor-fold>
    }
    
    public static function supplier_validate($supplier_id){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        $db = new DB();
        
        $t_supplier = self::supplier_get($supplier_id);
        if(!count($t_supplier)>0){
            $success = 0;
            
        }
        else if ($t_supplier['supplier']['supplier_status'] !== 'active'){
            $success = 0;
            
        }
        
        if($success !== 1){
            $msg[] = Lang::get('Supplier')
                .' '.Lang::get('invalid');
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
}

?>