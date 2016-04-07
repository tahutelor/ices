<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sys_Backup_Engine {

    public static $module_list;
    public static $dir_list;
    
    public static function path_get(){
        //<editor-fold defaultstate="collapsed">
        $path = array(
            'index'=>ICES_Engine::$app['app_base_url'].'sys_backup/'
            ,'sys_backup_engine'=>  ICES_Engine::$app['app_base_dir'].'sys_backup/sys_backup_engine'
            ,'sys_backup_data_support'=>ICES_Engine::$app['app_base_dir'].'sys_backup/sys_backup_data_support'
            ,'sys_backup_renderer' => ICES_Engine::$app['app_base_dir'].'sys_backup/sys_backup_renderer'
            ,'ajax_search'=>ICES_Engine::$app['app_base_url'].'sys_backup/ajax_search/'
            ,'data_support'=>ICES_Engine::$app['app_base_url'].'sys_backup/data_support/'

        );

        return json_decode(json_encode($path));
        //</editor-fold>
    }

    public static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        self::$module_list= array(
            array(
                'val'=>'backup_db'
                ,'label'=>'Backup DB'
                ,'method'=>'backup_db_start'
                ,'msg'=>array(
                    'success'=>array(
                        array('val'=>'Backup DB')
                        ,array('val'=>'success',true,false)
                    ),
                    'fail'=>array(
                        array('val'=>'Backup DB')
                        ,array('val'=>'fail',true,false)
                    )
                )
            ),
            array(
                'val'=>'backup_php'
                ,'label'=>'Backup PHP'
                ,'method'=>'backup_php_start'
                ,'msg'=>array(
                    'success'=>array(
                        array('val'=>'Backup PHP')
                        ,array('val'=>'success',true,false)
                    ),
                    'fail'=>array(
                        array('val'=>'Backup PHP')
                        ,array('val'=>'fail',true,false)
                    )
                )
            ),
        );
        
        self::$dir_list = array(
            'tmp_sys_backup_path'=>'tmp/sys_backup/',
        );
        
        //</editor-fold>
    }

    public static function backup_create($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $msg = [];
        $success = 1;
        $response = array();
        
        $method = $param['method'];
        $sys_backup = $param['sys_backup'];
                        
        set_time_limit(0);
        
        $filepath_list = array();
        $zip_prefix_filename = 'sys_backup';
        $dir = self::$dir_list['tmp_sys_backup_path'];
        $db_dir = 'db/';
        $php_dir = 'php/';
        $db_path = $dir.$db_dir;
        $php_path = $dir.$php_dir;
        $time_str = Tools::_date(null,'Ymd His');
        
        foreach(array($db_path) as $idx=>$row){
            
            Tools::clean_dir($row);
            if (!file_exists($row)) {
                mkdir($row);
            }
        }
        
        //<editor-fold defaultstate="collapsed" desc="Remove All zip files before yesterday">
        $zip_files = glob($dir.$zip_prefix_filename.'*.zip');
        
        foreach($zip_files as $i=>$row){
            if(filemtime($row) < strtotime(Tools::_date('','Y-m-d H:i:s','-P1D'))){
                unlink($row);
            }
        }
        //</editor-fold>
        
        $zip = new ZipArchive();
        $zip_filename = $zip_prefix_filename.' '.$method.' '.$time_str.'.zip';
        $zip_filepath = $dir.$zip_filename;
        if(!$zip->open($zip_filepath, ZipArchive::CREATE)){
            $success = 0;
            $msg[] = 'Unable to create zip file';
        }
        
        switch($method){
            case 'backup_db':
                //<editor-fold defaultstate="collapsed" desc="Backup DB">
                
                $db = null;
                $app_db_conn = null;
                $db_name = '';
                
                $app_name = Tools::_str(isset($sys_backup['app_name'])?$sys_backup['app_name']:'');
                $app = ICES_Engine::app_get($app_name);
                if(count($app) === 0){
                    $success = 0;
                    $msg[] = 'APP invalid';
                }
                
                if($success === 1){
                    $db = new DB(array('db_name'=>'ices'));
                    $q_data = '';
                    
                    $app_db_conn = new DB(array('db_name'=>$app['app_db_conn_name']));
                    $db_name = $app_db_conn->db_get()->database;
                    
                    if($db->query('use '.$db_name.'',FALSE,FALSE) === FALSE){
                        
                        $success = 0;
                        $msg[] = $db->_error_message();
                    }
                }
                if($success === 1){
                    
                    //<editor-fold defaultstate="collapsed" desc="Functions">        
                    $functions = array_values($db->query_array('show function status',null,array('as_index'=>true)));
                    foreach($functions as $idx=>$row){
                        $function_name = $row[1];

                        if($idx === 0){
                            $q_data.='DELIMITER $$'."\n";
                        }

                        $q_data.= 'DROP FUNCTION IF EXISTS `'.$function_name.'` $$'."\n";

                        $rs = $db->query_array('SHOW CREATE function '.$function_name,null,array('as_index'=>true))[0];
                        $q_data.= 'CREATE '.substr($rs[2],strpos($rs[2],'FUNCTION')).' $$'."\n\n";


                        if($idx === count($functions)-1){
                            $q_data.='DELIMITER ;'."\n";
                        }

                    }
                    //</editor-fold>

                    //<editor-fold defaultstate="collapsed" desc="Tables">
                    $tables = array_values($db->query_array('show tables',null,array('as_index'=>true)));

                    foreach($tables as $idx=>$row){
                        $table_name = $row[0];
                        $table_data = $db->query_array('SELECT * FROM '.$table_name,10000000);

                        $q_data.= 'DROP TABLE IF EXISTS  '.$table_name.';'."\n";
                        $rs = $db->query_array('SHOW CREATE TABLE '.$table_name,null,array('as_index'=>true))[0];
                        $q_data.= $rs[1].';'."\n\n";


                        foreach($table_data as $data_idx=>$data_row){
                            if($data_idx === 0){
                                $q_data.= 'INSERT INTO `'.$table_name.'` (';

                                $i = 0;
                                foreach($data_row as $col_idx=>$col_row){
                                    $q_data.= ($i === 0?'':',').('`'.$col_idx.'`');
                                    $i++;
                                }
                                $q_data.=') values '."\n";
                            }

                            $q_data.='(';
                            $i = 0;
                            foreach($data_row as $col_idx=>$col_data){
                                if (is_null($col_data)) { 
                                    $col_data= 'NULL' ; 
                                }
                                else{
                                    $col_data = $db->escape($col_data);
                                }

                                $q_data.=($i === 0?'':',').$col_data;
                                $i++;
                            }
                            $q_data.=')'."\n";

                            if($data_idx === count($table_data)-1){
                                $q_data.= ';'."\n";
                            }
                            else{
                                $q_data.=',';
                            }

                        }

                        $q_data.="\n\n";
                    }
                    //</editor-fold>

                    //<editor-fold defaultstate="collapsed" desc="Write DB to file">

                    $db_filename = $db_name.' '.$time_str.'.sql';        
                    $db_filepath = $db_path.$db_filename;

                    $f = fopen($db_filepath,'wb');
                    if(!$f) {
                        $success = 0;
                        $msg[] = 'Unable to create sql file';
                    }
                    else{
                        fwrite($f,$q_data,strlen($q_data));
                        fclose($f);

                        $zip->addFile($db_filepath,$db_filename);

                    }
                    //</editor-fold>

                }
                
                if($success === 1){
                    $db->db_get()->close();
                    $db->db_get()->initialize();
                }
                
                
                
                
                //</editor-fold>
                break;
            case 'backup_php':
                //<editor-fold defaultstate="collapsed" desc="Backup php Files">
                if($success === 1){
                    $base_path = FCPATH;
                    $temp_files = Tools::get_dir_contents($base_path);
                    $prevented_path = realpath($dir);
                    foreach($temp_files as $idx=>$row){                        

                        if(strpos($row,$prevented_path) === FALSE){
                            if (!is_dir($row)) {                        
                                $zip->addFile($row,str_replace(FCPATH,$php_dir,$row));
                            }
                            else{
                                $zip->addEmptyDir(str_replace(FCPATH,$php_dir,$row));
                            }
                        }
                    }
                    $zip->addEmptyDir(str_replace(FCPATH,$php_dir,$prevented_path));
                }
                //</editor-fold>
                break;
            default:
                $success = 0;
                $msg[] = 'Create Backup Fail. Unknown Method';
                break;
        }
                
        if($success === 1){
            $zip->close();
            
            switch($method){
                case 'backup_db':
                    unlink($db_filepath);
                    break;
            }
                        
            $response['filename'] = $zip_filename;
            $response['filepath'] = $zip_filepath;
            
        }
        set_time_limit(get_instance()->config->config['MY_']['controller_time_limit']);
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['response'] = $response;
        return $result;
        //</editor-fold>
    }
    
    public static function file_send($filename){
        //<editor-fold defaultstate="collapsed">
        $dir = self::$dir_list['tmp_sys_backup_path'];
        $filepath = $dir.$filename;
        if(file_exists($filepath)){
            header('Set-Cookie: fileDownload=true; path=/');
            header('Content-Type: application/download');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            readfile($filepath);
        }
        //</editor-fold>
    }
    
    public static function backup_and_destroy_db(){
        //<editor-fold defaultstate="collapsed">
        $result = array();        
        $success = 1;
        $msg = array();
        $response = array();
        
        $t_result = self::backup_create(array('backup_type'=>'db'));
        if($t_result['success']!== 1){
            $success = 0;
            $msg[] = 'Unable to create backup file';
        }
        
        if($success === 1){
            $filepath = $t_result['response']['filepath'];
            
            $filename = basename($filepath);
            
            // set up basic connection
            $conn_id = ftp_connect('ftp.jepsolution.com');

            // login with username and password
            $login_result = ftp_login($conn_id, 'hanselindo@jepsolution.com', 'Hanselindo123');

            // upload a file
            set_time_limit(0);
            
            if (!ftp_put($conn_id, $filename, $filepath, FTP_BINARY)) {
                $success = 0;
                $msg[] = 'Unable to upload file';
            }
            set_time_limit(get_instance()->config->config['MY_']['controller_time_limit']);
            
            ftp_close($conn_id);
            
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['response'] = $response;
        return $result;
        //</editor-fold>
    }


}
?>
