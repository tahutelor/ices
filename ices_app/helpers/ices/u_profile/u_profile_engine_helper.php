<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class U_Profile_Engine {
    public static $prefix_id = 'u_profile';
    public static $prefix_method;
    static $status_list;
    
    public static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        self::$prefix_method = self::$prefix_id;
        self::$status_list = array(
        //<editor-fold defaultstate="collapsed">
            array(
                'val'=>''
                ,'label'=>''
                ,'method'=>self::$prefix_method.'_update'
                ,'next_allowed_status'=>array()
                ,'msg'=>array(
                    'success'=>array(
                        array('val'=>'Update'),array('val'=>'User Profile'),array('val'=>'success')
                    )
                )
            ),
            
        //</editor-fold>
        );
        //</editor-fold>
    }
    
    public static function path_get(){
        $path = array(
            'index'=>ICES_Engine::$app['app_base_url'].'u_profile/'
            ,'u_profile_engine'=>  ICES_Engine::$app['app_base_dir'].'u_profile/u_profile_engine'
            ,'u_profile_data_support'=>ICES_Engine::$app['app_base_dir'].'u_profile/u_profile_data_support'
            ,'u_profile_renderer' => ICES_Engine::$app['app_base_dir'].'u_profile/u_profile_renderer'
            ,'ajax_search'=>ICES_Engine::$app['app_base_url'].'u_profile/ajax_search/'
            ,'data_support'=>ICES_Engine::$app['app_base_url'].'u_profile/data_support/'

        );

        return json_decode(json_encode($path));
    }

    
    public static function validate($method,$data=array()){  
        //<editor-fold defaultstate="collapsed">
        
        $result = array(
            "success"=>1
            ,"msg"=>array()

        );
        $success = 1;
        $msg = array();
        $u_profile = isset($data['u_profile'])?Tools::_arr($data['u_profile']):array();
        
        
        switch($method){
            
            case self::$prefix_method.'_update':
                $firstname = isset($u_profile['firstname'])?Tools::_str($u_profile['firstname']):'';
                $lastname = isset($u_profile['lastname'])?Tools::_str($u_profile['lastname']):'';
                $password = isset($u_profile['password'])?Tools::_str($u_profile['password']):'';
                
                if(strlen(str_replace(' ','',$firstname)) === 0){
                    $success = 0;
                    $msg[] = 'First Name empty';
                }
                
                
                if(strlen(str_replace(' ','',$password)) === 0){
                    $success = 0;
                    $msg[] = 'Password empty';
                }
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

    public static function adjust($action,$data=array()){
        //<editor-fold defaultstate="collpased">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'employee/employee_engine');
        $employee_path = Employee_Engine::path_get();
        get_instance()->load->helper($employee_path->employee_data_support);
                
        $db = new DB();
        $result = array();
        $u_profile_data = isset($data['u_profile'])?
            Tools::_arr($data['u_profile']):array();
        $employee_db = Employee_Data_Support::employee_get($u_profile_data['id']);
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');
        switch($action){
            case self::$prefix_method.'_update':                
                //<editor-fold defaultstate="collapsed">
                $employee = array();
                
                $pwd = $u_profile_data['password'];
                
                if (count($db->query_array_obj('select password from employee where id = ' . $db->escape($u_profile_data['id']) . ' and password=' . $db->escape($pwd) . '')) === 0) {
                    $pwd = md5($pwd);
                }
                                
                $employee = array(
                    'firstname'=>Tools::_str($u_profile_data['firstname'])
                    ,'lastname'=>Tools::_str($u_profile_data['lastname'])
                    ,'modid'=>$modid
                    ,'moddate'=>$datetime_curr
                    ,'password'=>$pwd

                );
                
                
                $result['employee'] = $employee;
                
                
                
                //</editor-fold>  
                break;
        }

        return $result;
        //</editor-fold>
    }    
    
    function u_profile_update($db, $final_data,$id){
        //<editor-fold defaultstate="collapsed">
        $result=array('success'=>1,'msg'=>array(),'trans_id'=>$id);
        $success = 1;
        $msg = array();

        $femployee = $final_data['employee'];
                
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if(!$db->update('employee',$femployee,array("id"=>$id))){
            $msg[] = $db->_error_message();
            $db->trans_rollback();                                
            $success = 0;
        }
        
        if($success === 1){
            User_Info::set($id);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;

        return $result;
        //</editor-fold>
    }
    



}
?>
