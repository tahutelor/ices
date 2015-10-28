<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'controllers/ices/dashboard.php',
    'src_class' => 'Dashboard',
    'src_extends_class' => '',
    'dst_class' => 'Dashboard_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class Dashboard extends Dashboard_Parent {

    function __construct() {
        parent::__construct();
        
    }


}

?>