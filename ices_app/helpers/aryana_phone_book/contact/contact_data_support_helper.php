<?php

class Contact_Data_Support {

    public static function contact_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select *
            from contact
            where id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $contact = $rs[0];
            $c_address = array();
            $c_cc = array();
            $c_mail_address = array();
            $c_phone_number = array();

            $q = '
                select ca.*
                from c_address ca
                where ca.contact_id = ' . $db->escape($id) . '
            ';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $c_address = $rs;

            $q = '
                select cc.*
                from c_cc ccc
                    inner join contact_category cc on ccc.contact_category_id = cc.id
                where ccc.contact_id = ' . $db->escape($id) . '
                    and cc.status > 0
            ';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $c_cc = $rs;


            $q = 'select cma.* from c_mail_address cma 
                inner join contact c on cma.contact_id = c.id where cma.contact_id=' . $db->escape($id) . '
                    and c.status > 0';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $c_mail_address = $rs;


            $q = 'select cpn.*,pnt.name phone_number_type_name,pnt.code phone_number_type_code
                    from c_phone_number cpn 
                    inner join contact c on cpn.contact_id = c.id 
                    inner join phone_number_type pnt on pnt.id = cpn.phone_number_type_id 
                    where cpn.contact_id=' . $db->escape($id) . '
                    and c.status > 0';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $c_phone_number = $rs;


            $result['contact'] = $contact;
            $result['c_address'] = $c_address;
            $result['c_mail_address'] = $c_mail_address;
            $result['c_phone_number'] = $c_phone_number;
            $result['c_cc'] = $c_cc;
        }
        return $result;
        //</editor-fold>
    }

    public static function contact_category_get() {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'contact_category/contact_category_engine');
        $path = Contact_Category_Engine::path_get();

        get_instance()->load->helper($path->contact_category_data_support);
        $t_contact_category = Contact_Category_Data_Support::contact_category_list_get(array('contact_category_status'=>'active'));
        $result = $t_contact_category;
        return $result;
        //</editor-fold>
    }

    public static function phone_number_type_get() {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'phone_number_type/phone_number_type_engine');
        $path = Phone_Number_Type_Engine::path_get();

        get_instance()->load->helper($path->phone_number_type_data_support);
        $t_phone_number_type = Phone_Number_Type_Data_Support::phone_number_type_list_get(array('phone_number_type_status'=>'active'));
        $result = $t_phone_number_type;
        return $result;
        //</editor-fold>
    }

}

?>