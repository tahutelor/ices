<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Phone_Number_Data_Support {
    public static function phone_number_validate($phone_number_arr = array()){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        $db = new DB();
        
        //validate duplicate phone number
        $temp = array();
        foreach ($phone_number_arr as $idx => $val_phone) {
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
                }, $phone_number_arr));

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
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

}
?>