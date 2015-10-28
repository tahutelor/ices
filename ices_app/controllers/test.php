<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Test extends MY_Extended_Controller{
    
    function index(){
        $str = 'return true; aadsd asd asd';
        die(var_dump(@eval($str)));
    }
}