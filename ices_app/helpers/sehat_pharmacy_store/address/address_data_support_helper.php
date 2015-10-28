<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Address_Data_Support {
    public static function address_validate($address_arr = array()){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        
        //validate duplicate address
        $temp_address = array_count_values($address_arr);

        foreach ($temp_address as $idx_address => $val_address) {
            if (Tools::_float($val_address) > Tools::_float(1)) {
                $success = 0;
                $msg[] = Lang::get("Address") . ' ' . Lang::get("duplicate", true, FALSE);
                break;
            }
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

}
?>