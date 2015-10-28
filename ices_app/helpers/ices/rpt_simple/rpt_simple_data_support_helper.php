<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Simple_Data_Support{
    
    public static function module_list_get(){
        SI::module()->load_class(array('module'=>'rpt_simple','class_name'=>'rpt_simple_engine'));
        return Rpt_Simple_Engine::$module_list;
    }
    
    public function module_get($module_name_val){
        $result = array();
        $module_list = self::module_list_get();
        foreach($module_list as $module_idx=>$module){
            if($module['name']['val'] === $module_name_val) $result = $module;
        }
        return $result;
    }
    
    public function module_condition_get($module_name_val, $module_condition_val){
        $result = array();
        $module_list = self::module_list_get();
        foreach($module_list as $module_idx=>$module){
            if($module['name']['val'] === $module_name_val){
                foreach($module['condition'] as $condition_idx=>$condition){
                    if($condition['val'] === $module_condition_val) $result = $condition;
                }
            }
        }
        return $result;
    }
    
    public function module_condition_exists($module_name_val, $module_condition_val){
        $result = false;
        $module_list = self::module_list_get();
        foreach($module_list as $module_idx=>$module){
            if($module['name']['val'] === Tools::_str($module_name_val)){
                foreach($module['condition'] as $condition_idx=>$condition){
                    if($condition['val'] === Tools::_str($module_condition_val)){
                        $result = true;
                        
                    }
                }
            }
        }
        return $result;
    }
    
}
?>