<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'helpers/ices/rpt_simple/rpt_simple_renderer_helper.php',
    'src_class' => 'Rpt_Simple_Renderer',
    'src_extends_class' => '',
    'dst_class' => 'Rpt_Simple_Renderer_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class Rpt_Simple_Renderer extends Rpt_Simple_Renderer_Parent {

}