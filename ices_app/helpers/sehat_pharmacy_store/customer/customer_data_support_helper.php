<?php

class Customer_Data_Support {

    public static function customer_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select c.*
                ,ct.code customer_type_code
                ,ct.name customer_type_name
            from customer c   
                inner join customer_type ct on c.customer_type_id = ct.id
            where c.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $customer = $rs[0];
            $c_address = array();
            $c_mail_address = array();
            $c_phone_number = array();

            $q = '
                select ca.*
                from c_address ca
                where ca.customer_id = ' . $db->escape($id) . '
            ';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $c_address = $rs;

            $q = '
                select cma.* 
                from c_mail_address cma 
                inner join customer c on cma.customer_id = c.id where cma.customer_id=' . $db->escape($id) . '
                    and c.status > 0';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $c_mail_address = $rs;


            $q = '
                select cpn.*
                    ,pnt.name phone_number_type_name
                    ,pnt.code phone_number_type_code
                from c_phone_number cpn 
                inner join phone_number_type pnt on pnt.id = cpn.phone_number_type_id 
                where cpn.customer_id=' . $db->escape($id) . '
            ';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $c_phone_number = $rs;


            $result['customer'] = $customer;
            $result['c_address'] = $c_address;
            $result['c_mail_address'] = $c_mail_address;
            $result['c_phone_number'] = $c_phone_number;
        }
        return $result;
        //</editor-fold>
    }

    public static function customer_type_list_get() {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        SI::module()->load_class(array('module'=>'customer_type','class_name'=>'customer_type_data_support'));
        $t_customer_type = Customer_Type_Data_Support::customer_type_list_get(array('customer_type_status'=>'active'));
        $result = $t_customer_type;
        return $result;
        //</editor-fold>
    }

    public static function customer_type_default_get() {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        SI::module()->load_class(array('module'=>'customer_type','class_name'=>'customer_type_data_support'));
        $t_customer_type = Customer_Type_Data_Support::customer_type_default_get(array('customer_type_status'=>'active'));
        $result = $t_customer_type;
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
    
    public static function si_customer_default_get(){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q = '
            select c.id
            from customer c
            where c.status > 0
                and c.customer_status = "active"
                and c.si_customer_default = 1
                
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $customer_id = $rs[0]['id'];
            $result = self::customer_get($customer_id);
        }
        
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_customer_search($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $lookup_data = '%'.$param['lookup_data'].'%';
        $q_customer_status = isset($param['customer_status'])?
            ' and s.customer_status = '.$db->escape($param['customer_status']):
            ''
        ;
        
        $q = '
            select distinct s.*
            from customer s
                left outer join c_address sa on s.id = sa.customer_id
                left outer join c_phone_number spn on s.id = spn.customer_id
            where s.status > 0
                and(
                    
                    s.code like '.$db->escape($lookup_data).'
                    or s.name like '.$db->escape($lookup_data).'
                    or sa.address like '.$db->escape($lookup_data).'
                    or spn.phone_number like '.$db->escape($lookup_data).'
                )
                '.$q_customer_status.'
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

    public static function input_select_customer_detail_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'customer/customer_engine');
        $c_path = Customer_Engine::path_get();
        $result = array();
        $customer_id = $param['customer_id'];
        $t_customer = self::customer_get($customer_id);
        if(count($t_customer)>0){
            $customer = $t_customer['customer'];
            $c_address = $t_customer['c_address'];
            $c_phone_number = $t_customer['c_phone_number'];
            $address = count($c_address)>0 ?$c_address[0]['address']:'';
            $phone_number = count($c_phone_number)>0?$c_phone_number[0]['phone_number']:'';
            $result = array(
                array('id'=>'code','label'=>'Code: ','val'=>'<a href="'.$c_path->index.'view/'.$customer_id.'" target="_blank">'.$customer['code'].'</a>'),
                array('id'=>'type','label'=>'Name: ','val'=>$customer['name']),
                array('id'=>'phone_number','label'=>'Phone Number: ','val'=>$phone_number),
                array('id'=>'address','label'=>'Address: ','val'=>$address),
            );
        }
        
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_si_customer_default_get(){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $temp = self::si_customer_default_get();
        if(count($temp)>0){
            $customer = $temp['customer'];
            $result = array(
                'id'=>$customer['id'],
                'text'=>Tools::html_tag('strong',$customer['code'])
                    .' '.$customer['name'],
                
            );
        }
        return $result;
        //</editor-fold>
    }
    
    public static function customer_validate($customer_id){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        $db = new DB();
        
        $t_customer = self::customer_get($customer_id);
        if(!count($t_customer)>0){
            $success = 0;
        }
        else if ($t_customer['customer']['customer_status'] !== 'active'){
            $success = 0;
            
        }
        
        if($success !== 1){
            $msg[] = Lang::get('Customer')
                .' '.Lang::get('invalid');
        }
        
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

}

?>