<?php

class BOS_Bank_Account_Data_Support {

    public static function bos_bank_account_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select s.*
            from bos_bank_account s   
            where s.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $bos_bank_account = $rs[0];
            

            $result['bos_bank_account'] = $bos_bank_account;
        }
        return $result;
        //</editor-fold>
    }

    public static function bos_bank_account_list_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q_bos_bank_account_status = isset($param['bos_bank_account_status'])?
            ' and u.bos_bank_account_status = '.$db->escape($param['bos_bank_account_status']):'';
        $q = '
            select u.*
            from bos_bank_account u
            where u.status>0
            
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            foreach($rs as $idx=>$row){
                $result[] = self::bos_bank_account_get($row['id']);
            }
        }
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_bos_bank_account_list_get(){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $t_payment_list = BOS_Bank_Account_Data_Support::bos_bank_account_list_get(array('bos_bank_account_status'=>'active'));
        foreach($t_payment_list as $idx=>$row){

            $result[] = array(
                'id'=>$row['bos_bank_account']['id'],
                'text'=>Tools::html_tag('strong',$row['bos_bank_account']['code'])
                    .' '.$row['bos_bank_account']['bank_account'],
            );
        }
        return $result;
        //</editor-fold>
    }

    public static function bos_bank_account_validate($bos_bank_account_id){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        $db = new DB();
        
        $temp = BOS_Bank_Account_Data_Support::bos_bank_account_get($bos_bank_account_id);
        if(!count($temp)>0){
            $success = 0;            
        }
        else{
            $bos_bank_account = $temp['bos_bank_account'];
            if($bos_bank_account['bos_bank_account_status'] !== 'active'){
                $success = 0;
            }
        }
        
        if($success !== 1){
            $msg[] = Lang::get('BOS Bank Account')
                .' '.Lang::get('invalid',true,false);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
}

?>