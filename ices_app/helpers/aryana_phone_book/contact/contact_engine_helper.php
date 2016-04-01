<?php

class Contact_Engine {

    public static $prefix_id = 'contact';
    public static $prefix_method;
    public static $status_list;

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        self::$prefix_method = self::$prefix_id;

        self::$status_list = array(
            //<editor-fold defaultstate="collapsed">
            array(
                'val' => ''
                , 'text' => ''
                , 'method' => 'contact_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Contact'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'contact_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Contact'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'contact_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Contact'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
                //</editor-fold>
        );

        //</editor-fold>
    }

    public static function path_get() {
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'contact/'
            , 'contact_engine' => ICES_Engine::$app['app_base_dir'] . 'contact/contact_engine'
            , 'contact_data_support' => ICES_Engine::$app['app_base_dir'] . 'contact/contact_data_support'
            , 'contact_renderer' => ICES_Engine::$app['app_base_dir'] . 'contact/contact_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'contact/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'contact/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Contact_Engine::path_get();
        get_instance()->load->helper($path->contact_data_support);

        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $contact = isset($data['contact']) ? Tools::_arr($data['contact']) : array();
        $contact_category_id = isset($data['contact_category_id']) ? Tools::_arr($data['contact_category_id']) : array();
        $company_id = isset($data['company_id']) ? Tools::_arr($data['company_id']) : array();
        $address = isset($data['address']) ? Tools::_arr($data['address']) : array();
        $mail_address = isset($data['mail_address']) ? Tools::_arr($data['mail_address']) : array();
        $keyword = isset($data['keyword']) ? Tools::_arr($data['keyword']) : array();
        $phone_number = isset($data['phone_number']) ? Tools::_arr($data['phone_number']) : array();
        $contact_id = $contact['id'];

        $temp = Contact_Data_Support::contact_get($contact_id);
        $contact_db = count($temp) > 0 ? $temp['contact'] : array();
        
//        var_dump($company_id);
//        die();

        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                //validate Mail Address, Address, and Phone Number duplicate
                if (!(isset($contact['code']) && isset($contact['name']) && isset($contact['notes']) 
                        && isset($contact['contact_status']) && isset($data['mail_address']) 
                        && isset($data['address']) && isset($data['phone_number'])&& isset($data['keyword']))) {
                    $success = 0;
                    $msg[] = Lang::get('Contact')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Mail Address')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Address')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Phone Number')
                            . ' ' . Lang::get('data') . ' ' . Lang::get('invalid');
                }
                 if ($success === 1) {
                    
                    //validate if empty field
                    $name = Tools::empty_to_null(Tools::_str($contact['name']));
                    $code = Tools::empty_to_null(Tools::_str($contact['code']));
                    $notes = Tools::empty_to_null(Tools::_str($contact['notes']));
                    $birthdate = Tools::empty_to_null(Tools::_date($contact['birthdate']));

                    foreach ($address as $idx => $row) {
                        $address[$idx] = Tools::empty_to_null(Tools::_str($row));
                    }

                    foreach ($phone_number as $idx => $row) {
                        $phone_number[$idx]['phone_number'] = Tools::empty_to_null(preg_replace('/[^0-9]/', '', Tools::_str($row['phone_number'])));
                    }

                    foreach ($mail_address as $idx => $row) {
                        $mail_address[$idx] = Tools::empty_to_null(Tools::_str($row));
                    }
                    
                    foreach ($keyword as $idx => $row) {
                        $keyword[$idx] = Tools::empty_to_null(Tools::_str($row));
                    }

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($name) || is_null($code) || is_null($birthdate)) {
                        $success = 0;
                        $msg[] = 'Name' . ' ' . Lang::get('or') . ' ' . 'Code ' . ' ' . ' ' . 'Birthday' . ' ' . Lang::get('empty', true, false);
                    }
                    if ($success !== 1) {
                        break;
                    }

                    //</editor-fold>

                    //validate name or code exist
                    $q = '
                        select 1
                        from contact c
                        where c.status > 0
                            and (c.name = ' . $db->escape($name) . ' or c.code = ' . $db->escape($code) . ')
                            and c.id <> ' . $db->escape($contact_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = 'Name or Code exists';
                    }


                    if ($success === 1) {
                        // <editor-fold defaultstate="collapsed" desc="Contact Category">
                        //validate contact category duplicate
                        $temp_cc = array_count_values($contact_category_id);
                        foreach ($temp_cc as $idx => $val) {
                            if (Tools::_float($val) > Tools::_float(1)) {
                                $success = 0;
                                $msg[] = Lang::get("Contact category") . ' ' . Lang::get("duplicate", true, FALSE);
                                break;
                            }
                        }
                        
                        //validate invalid contact category
                        $q_cc_list = '';
                        foreach ($contact_category_id as $idx => $row) {
                            $q_cc_list .= ($q_cc_list === '' ? '' : ',') . $db->escape($row);
                        }

                        $q = '
                            select distinct id
                            from contact_category cc
                            where cc.status > 0
                                and cc.id in (' . $q_cc_list . ')
                                and cc.contact_category_status = "active"
                        ';
                        $t_contact_category_db = $db->query_array($q);
                        if (count($t_contact_category_db) !== count($contact_category_id)) {
                            $success = 0;
                            $msg[] = Lang::get('Contact Category') . ' ' . Lang::get('invalid', true, false);
                        }
                        
                        // </editor-fold>                        
                        // <editor-fold defaultstate="collapsed" desc="Company">
                        //validate company duplicate
                        $temp_company = array_count_values($company_id);
                        foreach ($temp_company as $idx => $val) {
                            if (Tools::_float($val) > Tools::_float(1)) {
                                $success = 0;
                                $msg[] = Lang::get("Company") . ' ' . Lang::get("duplicate", true, FALSE);
                                break;
                            }
                        }
                        
                        //validate invalid company
                        $q_company_list = '';
                        foreach ($company_id as $idx => $row) {
                            $q_company_list .= ($q_company_list === '' ? '' : ',') . $db->escape($row);
                        }

                        $q = '
                            select distinct id
                            from company cpn
                            where cpn.status > 0
                                and cpn.id in (' . $q_company_list . ')
                                and cpn.company_status = "active"
                        ';
                        $t_company_db = $db->query_array($q);
                        if (count($t_company_db) !== count($company_id)) {
                            $success = 0;
                            $msg[] = Lang::get('Company') . ' ' . Lang::get('invalid', true, false);
                        }
                        // </editor-fold>
                        //<editor-fold defaultstate="collapsed" desc="Mail Address">
                        
                        //validate duplicate mail address
                        $temp_mail = array_count_values($mail_address);

                        foreach ($temp_mail as $idx_mail => $val_mail) {
                            if (Tools::_float($val_mail) > Tools::_float(1)) {
                                $success = 0;
                                $msg[] = Lang::get("Mail Address") . ' ' . Lang::get("duplicate", true, FALSE);
                                break;
                            }
                        }
                        
                        //validate mail address format
                        $local_success = 1;
                        foreach($mail_address as $idx=>$row){
                            if(!filter_var($row,FILTER_VALIDATE_EMAIL)){
                                $local_success = 0;
                                $msg[] = Lang::get('Mail Address').' '.Lang::get('invalid',true,false);
                            }
                            if($local_success !== 1){
                                $success = 0;
                                break;
                            }
                        }
                        
                        
                        //</editor-fold>
                        //<editor-fold defaultstate="collapsed" desc="Keyword">
                        
                        //validate duplicate keyword
                        $temp_keyword = array_count_values($keyword);

                        foreach ($temp_keyword as $idx_keyword => $val_keyword) {
                            if (Tools::_float($val_keyword) > Tools::_float(1)) {
                                $success = 0;
                                $msg[] = Lang::get("Keyword") . ' ' . Lang::get("duplicate", true, FALSE);
                                break;
                            }
                        }
                        //</editor-fold>
                        //<editor-fold defaultstate="collapsed" desc="Address">
                        
                        //validate duplicate address
                        $temp_address = array_count_values($address);

                        foreach ($temp_address as $idx_address => $val_address) {
                            if (Tools::_float($val_address) > Tools::_float(1)) {
                                $success = 0;
                                $msg[] = Lang::get("Address") . ' ' . Lang::get("duplicate", true, FALSE);
                                break;
                            }
                        }
                        //</editor-fold>
                        //<editor-fold defaultstate="collapsed" desc="Phone Number">
                        
                        //validate duplicate phone number
                        $temp = array();
                        foreach ($phone_number as $idx => $val_phone) {
                            $temp[] = $val_phone['phone_number'] . '_' . $val_phone['phone_number_type_id'];
                        }

                        $temp_phone_number = array_count_values($temp);
                        foreach ($temp_phone_number as $idx_phone_number => $val_phone_number) {
                            if (Tools::_float($val_phone_number) > Tools::_float(1)) {
                                $success = 0;
                                $msg[] = Lang::get("Phone Number") . ' ' . Lang::get("duplicate", true, FALSE);
                                break;
                            }
                        }

                        //validate phone number type if invalid
                        $unique_phone_number_type_id = array_unique(array_map(function ($i) {
                                    return $i['phone_number_type_id'];
                                }, $phone_number));

                        $q_pnt_list = '';
                        foreach ($unique_phone_number_type_id as $idx => $row) {
                            $q_pnt_list .= ($q_pnt_list === '' ? '' : ',') . $db->escape($row);
                        }

                        $q = '
                            select distinct id
                            from phone_number_type pnt
                            where pnt.status > 0
                                and pnt.id in (' . $q_pnt_list . ')
                                and pnt.phone_number_type_status = "active"
                        ';

                        $t_phone_number_type_db = $db->query_array($q);
                        if (count($t_phone_number_type_db) !== count($unique_phone_number_type_id)) {
                            $success = 0;
                            $msg[] = Lang::get('Phone Number Type') . ' ' . Lang::get('invalid', true, false);
                        }

                        //</editor-fold>     
                    }
                    
                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        
                        
                        //validate invalid contact
                        if (!count($contact_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Contact';
                        }

                        if ($success === 1) {
                            //<editor-fold defaultstate="collapsed" desc="validate on update">
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array('module' => 'contact',
                                        'module_name' => Lang::get('Contact'),
                                        'module_engine' => 'contact_engine',
                                            ), $contact
                            );
                            $success = $temp_result['success'];
                            $msg = array_merge($msg, $temp_result['msg']);
                            //</editor-fold>
                        }
                        //</editor-fold>
                    }
                }


                //</editor-fold>
                break;

            default:
                $success = 0;
                $msg[] = 'Invalid Method';
                break;
        }
        $result['success'] = $success;
        $result['msg'] = $msg;

        return $result;
        //</editor-fold>
    }

    public static function adjust($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();

        $contact_data = isset($data['contact']) ? $data['contact'] : array();
//        $notes = Tools::_arr(isset($data['notes']) ? $data['notes'] : array());
        $address_data = Tools::_arr(isset($data['address']) ? $data['address'] : array());
        $phone_number_data = Tools::_arr(isset($data['phone_number']) ? $data['phone_number'] : array());
        $mail_address_data = Tools::_arr(isset($data['mail_address']) ? $data['mail_address'] : array());
        $keyword_data = Tools::_arr(isset($data['keyword']) ? $data['keyword'] : array());
        $contact_category_id_data = Tools::_arr(isset($data['contact_category_id']) ? $data['contact_category_id'] : array());
        $company_id_data = Tools::_arr(isset($data['company_id']) ? $data['company_id'] : array());
      
        $temp = Contact_Data_Support::contact_get($contact_data['id']);
        $contact_db = Tools::_arr(isset($temp['contact']) ? $temp['contact'] : array());

        $contact_id = $contact_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $contact = array(
                    'name' => Tools::_str($contact_data['name']),
                    'code' => Tools::_str($contact_data['code']),
                    'birthdate' => Tools::_date($contact_data['birthdate']),
                    'notes' => Tools::_str($contact_data['notes']),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );

                $c_address = array();
                foreach ($address_data as $idx => $row) {
                    $c_address[] = array(
                        'contact_id' => $contact_id,
                        'address' => Tools::_str($row)
                    );
                }

                $c_mail_address = array();
                foreach ($mail_address_data as $idx => $row) {
                    $c_mail_address[] = array(
                        'contact_id' => $contact_id,
                        'mail_address' => Tools::_str($row)
                    );
                }
                
                $c_keyword = array();
                foreach ($keyword_data as $idx => $row) {
                    $c_keyword[] = array(
                        'contact_id' => $contact_id,
                        'keyword' => Tools::_str($row)
                    );
                }

                $c_cc = array();
                foreach ($contact_category_id_data as $idx => $row) {
                    $c_cc[] = array(
                        'contact_id' => $contact_id,
                        'contact_category_id' => Tools::_str($row)
                    );
                }
                
                $c_company = array();
                foreach ($company_id_data as $idx => $row) {
                    $c_company[] = array(
                        'contact_id' => $contact_id,
                        'company_id' => Tools::_str($row)
                    );
                }

                
                $c_phone_number = array();
                foreach ($phone_number_data as $idx => $row) {
                    $c_phone_number[] = array(
                        'contact_id' => $contact_id,
                        'phone_number_type_id' => $row['phone_number_type_id'],
                        'phone_number' => Tools::_str(preg_replace('/[^0-9]/', '', $row['phone_number']))
                    );
                }

                switch ($method) {
                    case self::$prefix_method . '_add':
                        $contact['contact_status'] = SI::type_default_type_get('Contact_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $contact['contact_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $contact['contact_status'] = 'inactive';
                        break;
                }

                $result['contact'] = $contact;
                $result['c_address'] = $c_address;
                $result['c_cc'] = $c_cc;
                $result['c_company'] = $c_company;
                $result['c_phone_number'] = $c_phone_number;
                $result['c_mail_address'] = $c_mail_address;
                $result['c_keyword'] = $c_keyword;
                //</editor-fold>
                break;
        }
        
        return $result;
        //</editor-fold>
    }

    public function contact_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Contact_Engine::path_get();
        get_instance()->load->helper($path->contact_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcontact = $final_data['contact'];
        $fc_address = $final_data['c_address'];
        $fc_cc = $final_data['c_cc'];
        $fc_company = $final_data['c_company'];
        $fc_phone_number = $final_data['c_phone_number'];
        $fc_mail_address = $final_data['c_mail_address'];
        $fc_keyword = $final_data['c_keyword'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        $fcontact['code'] = SI::code_counter_get($db, 'contact');

        if (!$db->insert('contact', $fcontact)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $contact_id = $db->last_insert_id();
            $result['trans_id'] = $contact_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'contact', $contact_id, $fcontact['contact_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if ($success === 1) {
            foreach ($fc_address as $idx => $row) {
                $row['contact_id'] = $contact_id;
                if (!$db->insert('c_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if ($success === 1) {
            foreach ($fc_cc as $idx => $row) {
                $row['contact_id'] = $contact_id;
                if (!$db->insert('c_cc', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }
        
        if ($success === 1) {
            foreach ($fc_company as $idx => $row) {
                $row['contact_id'] = $contact_id;
                if (!$db->insert('c_company', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if ($success === 1) {
            foreach ($fc_phone_number as $idx => $row) {
                $row['contact_id'] = $contact_id;
                if (!$db->insert('c_phone_number', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if ($success === 1) {
            foreach ($fc_mail_address as $idx => $row) {
                $row['contact_id'] = $contact_id;
                if (!$db->insert('c_mail_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }
        
        if ($success === 1) {
            foreach ($fc_keyword as $idx => $row) {
                $row['contact_id'] = $contact_id;
                if (!$db->insert('c_keyword', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function contact_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Contact_Engine::path_get();
        get_instance()->load->helper($path->contact_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcontact = $final_data['contact'];
        $fc_address = $final_data['c_address'];
        $fc_cc = $final_data['c_cc'];
        $fc_company = $final_data['c_company'];
        $fc_phone = $final_data['c_phone_number'];
        $fc_mail_address = $final_data['c_mail_address'];
        $fc_keyword = $final_data['c_keyword'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $contact_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('contact', $fcontact, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'contact', $contact_id, $fcontact['contact_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if (!$db->query('delete from c_address where contact_id = ' . $db->escape($contact_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_address as $idx => $row) {
                if (!$db->insert('c_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from c_cc where contact_id = ' . $db->escape($contact_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_cc as $idx => $row) {
                if (!$db->insert('c_cc', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from c_company where contact_id = ' . $db->escape($contact_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_company as $idx => $row) {
                if (!$db->insert('c_company', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from c_mail_address where contact_id = ' . $db->escape($contact_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_mail_address as $idx => $row) {
                if (!$db->insert('c_mail_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }
        
        if (!$db->query('delete from c_keyword where contact_id = ' . $db->escape($contact_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_keyword as $idx => $row) {
                if (!$db->insert('c_keyword', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from c_phone_number where contact_id = ' . $db->escape($contact_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_phone as $idx => $row) {
                if (!$db->insert('c_phone_number', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function contact_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::contact_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>