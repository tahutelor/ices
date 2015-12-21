<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_ICES_Controller{
    
    function __construct(){
        parent::__construct();
    }
    
    function index(){       
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_data_support'));
        $c = Tools::_float(235500);
        $a = ceil($c/500)*500;
        
        echo $a;
        
    }
}