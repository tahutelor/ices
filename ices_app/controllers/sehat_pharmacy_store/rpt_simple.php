<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'controllers/ices/rpt_simple.php',
    'src_class' => 'Rpt_Simple',
    'src_extends_class' => '',
    'dst_class' => 'Rpt_Simple_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class Rpt_Simple extends Rpt_Simple_Parent {
    function __construct() {
        parent::__construct();

    }
}