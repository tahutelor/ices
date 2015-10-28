<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mail_Address_Data_Support {
    public static function mail_address_validate($mail_address_arr = array()){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        
        //validate duplicate mail_address
        $temp_mail_address = array_count_values($mail_address_arr);

        foreach ($temp_mail_address as $idx_mail_address => $val_mail_address) {
            if (Tools::_float($val_mail_address) > Tools::_float(1)) {
                $success = 0;
                $msg[] = Lang::get("Mail Address") . ' ' . Lang::get("duplicate", true, FALSE);
                break;
            }
        }
        
        //validate mail address format
        $local_success = 1;
        foreach($mail_address_arr as $idx=>$row){
            if(!filter_var($row,FILTER_VALIDATE_EMAIL)){
                $local_success = 0;
                $msg[] = Lang::get('Mail Address').' '.Lang::get('invalid',true,false);
            }
            if($local_success !== 1){
                $success = 0;
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