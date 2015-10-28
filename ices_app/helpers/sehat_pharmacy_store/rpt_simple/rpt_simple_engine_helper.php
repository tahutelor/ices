<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'helpers/ices/rpt_simple/rpt_simple_engine_helper.php',
    'src_class' => 'Rpt_Simple_Engine',
    'src_extends_class' => '',
    'dst_class' => 'Rpt_Simple_Engine_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class Rpt_Simple_Engine extends Rpt_Simple_Engine_Parent {
    
    public static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        self::$module_list = array(
            
            array(
                'name'=>array('val'=>'purchase_invoice','label'=>'Purchase Invoice'),
                'condition'=>array(
                    array('val'=>'outstanding_grand_total_amount','label'=>'Outstanding Amount'),
                ),
            ),            
            array(
                'name'=>array('val'=>'sales_invoice','label'=>'Sales Invoice'),
                'condition'=>array(
                    array('val'=>'outstanding_grand_total_amount','label'=>'Outstanding Amount'),
                ),
            ),
            array(
                'name'=>array('val'=>'product_batch','label'=>'Product Batch'),
                'condition'=>array(
                    array('val'=>'nearly_expired','label'=>Lang::get('Nearly Expired')),
                ),
            ),
            
        
        );
        //</editor-fold>
    }
    
}