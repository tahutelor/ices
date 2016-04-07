<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Engine{
    
    function __construct(){
        
    }
    
    
    public static function download_excel(){
        get_instance()->load->helper('handy/rpt/rpt_download_excel');
        return new Rpt_Download_Excel();
    }
    
    
}




?>