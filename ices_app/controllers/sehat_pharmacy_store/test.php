<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_ICES_Controller{
    
    function __construct(){
        parent::__construct();
    }
    
    function index(){       
        var_dump(Tools::_date('','Y-m-d H:i:s','-P6D'));
        
    }
}