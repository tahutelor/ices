<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ICES_Engine {
    
    public static $company_list = array();
    public static $company;
    public static $app_list;
    public static $app;
    
    public static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        self::$company_list = array(
            array(
                'val'=>'aryana',
                'app'=>array(
                    //<editor-fold defaultstate="collapsed">
                    array(
                        'val'=>'aryana_phone_book',
                        'text'=>'Phone Book',
                        'dev_text'=>'Aryana - Phone Book',
                        'short_name'=>'Phone Book',
                        'app_base_url'=>get_instance()->config->base_url().'aryana_phone_book/',
                        'app_base_dir'=>'aryana_phone_book/',
                        'app_db_conn_name'=>'aryana_phone_book',
                        'app_translate'=>false,
                        'app_default_url'=>get_instance()->config->base_url().'aryana_phone_book/dashboard',
                        'app_icon_img'=>get_instance()->config->base_url().'libraries/img/ices/aryana_phone_book.png',
                        'app_theme'=>'AdminLTE',
                        'app_db_lock_name'=>'aryana_phone_book',
                        'app_db_lock_limit'=>10,
                        'non_permission_controller'=>array(),
                        'app_info'=>'Support: +6289677006638<br/><i class="fa fa-skype text-blue"></i> purpl3girl'
                    ),
                    array(
                        'val'=>'civil_project',
                        'text'=>'Civil Project',
                        'dev_text'=>'Aryana - Civil Project',
                        'short_name'=>'Civil Project',
                        'app_base_url'=>get_instance()->config->base_url().'civil_project/',
                        'app_base_dir'=>'civil_project/',
                        'app_db_conn_name'=>'civil_project',
                        'app_translate'=>false,
                        'app_default_url'=>get_instance()->config->base_url().'civil_project/dashboard',
                        'app_icon_img'=>get_instance()->config->base_url().'libraries/img/ices/civil_project.png',
                        'app_theme'=>'AdminLTE',
                        'app_db_lock_name'=>'phone_book',
                        'app_db_lock_limit'=>10,
                        'non_permission_controller'=>array(),
                        'app_info'=>'Support: +6289677006638<br/><i class="fa fa-skype text-blue"></i> purpl3girl'
                    ),
                    array(
                        'val'=>'accounting',
                        'text'=>'Accounting',
                        'dev_text'=>'Aryana - Accounting',
                        'short_name'=>'Accounting',
                        'app_base_url'=>get_instance()->config->base_url().'accounting/',
                        'app_base_dir'=>'accounting/',
                        'app_db_conn_name'=>'accounting',
                        'app_translate'=>false,
                        'app_default_url'=>get_instance()->config->base_url().'accounting/dashboard',
                        'app_icon_img'=>get_instance()->config->base_url().'libraries/img/ices/accounting.png',
                        'app_theme'=>'AdminLTE',
                        'app_db_lock_name'=>'phone_book',
                        'app_db_lock_limit'=>10,
                        'non_permission_controller'=>array(),
                        'app_info'=>'Support: +6289677006638<br/><i class="fa fa-skype text-blue"></i> purpl3girl'
                    ),
                    array(
                        'val'=>'analysis',
                        'text'=>'Analysis',
                        'dev_text'=>'Aryana - Analysis',
                        'short_name'=>'Analysis',
                        'app_base_url'=>get_instance()->config->base_url().'analysis/',
                        'app_base_dir'=>'analysis/',
                        'app_db_conn_name'=>'analysis',
                        'app_translate'=>false,
                        'app_default_url'=>get_instance()->config->base_url().'analysis/dashboard',
                        'app_icon_img'=>get_instance()->config->base_url().'libraries/img/ices/analysis.png',
                        'app_theme'=>'AdminLTE',
                        'app_db_lock_name'=>'phone_book',
                        'app_db_lock_limit'=>10,
                        'non_permission_controller'=>array(),
                        'app_info'=>'Support: +6289677006638<br/><i class="fa fa-skype text-blue"></i> purpl3girl'
                    ),
                    
                    //</editor-fold>
                ),
                'active'=>true
            ),
            
        );
        
        self::$app_list = array(
            //<editor-fold defaultstate="collapsed">
            array(
                'val'=>'ices',
                'text'=>'Integrated Civil Engineering System',
                'dev_text'=>'Integrated Civil Engineering System',
                'short_name'=>'ICES System',
                'app_base_url'=>get_instance()->config->base_url().'ices/',
                'app_base_dir'=>'ices/',
                'app_db_conn_name'=>'ices',
                'app_translate'=>false,
                'app_default_url'=>get_instance()->config->base_url().'ices/dashboard',
                'app_icon_img'=>get_instance()->config->base_url().'libraries/img/ices/ices.png',
                'app_theme'=>'AdminLTE',
                'app_db_lock_name'=>'ices',
                'app_db_lock_limit'=>10,
                'non_permission_controller'=>array(),
                'app_info'=>'Support: +628113308009<br/><i class="fa fa-skype text-blue"></i> edwin_prayoga'
            ),
            //</editor-fold>
        );
        
        foreach(self::$company_list as $idx=>$row){
            if($row['active']){
                self::$company =  $row;
                self::$app_list = array_merge(self::$app_list,self::$company['app']);
                unset(self::$company['app']);
                break;
            }
        }
        
        self::helper_load();
        $app_name = get_instance()->uri->segment(1);
        self::app_set($app_name);
        //</editor-fold>
    }
    
    public static function helper_load(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper('ices/user_info/user_info');
        get_instance()->load->helper('ices/handy/printer_helper');
        get_instance()->load->helper('ices/handy/excel_helper');
        get_instance()->load->helper('ices/handy/tools');
        get_instance()->load->helper('ices/handy/si/si');
        get_instance()->load->helper('ices/handy/email/email_engine');
        get_instance()->load->helper('ices/handy/email/email_message');
        get_instance()->load->helper('ices/handy/minifier');
        get_instance()->load->helper('ices/security/security_engine');
        get_instance()->load->helper('ices/handy/db');
        get_instance()->load->helper('ices/app/app');
        get_instance()->load->helper('ices/app/app_icon');
        get_instance()->load->helper('ices/app_message/app_message_engine');
        get_instance()->load->helper('ices/app/app_message');
        
        //</editor-fold>
    }
    
    public static function app_set($app_name=''){
        //<editor-fold defaultstate="collapsed">
        $t_app = SI::type_get('ICES_Engine',$app_name,'$app_list');
        if($t_app !== null){
            self::$app = $t_app;
            get_instance()->load->helper(self::$app['app_base_dir'].'lang/lang_helper');
        }
        //<editor-fold>
    }
    
}
?>
