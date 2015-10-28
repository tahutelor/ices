<?php
class Product_Category_Data_Support {
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'product_category/product_category_engine');
        //</editor-fold>
    }
    
    public static function product_category_get($id){
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select distinct pc.*
                ,pc2.code prnt_product_category_code
                ,pc2.name prnt_product_category_name
            from product_category pc
            left outer join  product_category pc2 on pc.prnt_product_category_id = pc2.id
            where pc.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $product_category = $rs[0];
            $result['product_category'] = $product_category;
            
        }
        return $result;
        //</editor-fold>
    }
    
    public static function prnt_product_category_search($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $product_category_id = $param['product_category_id'];
        $lookup_data = $param['lookup_data'];
        $curr_route = '';
        $q = 'select pc.* from product_category pc where id = '.$db->escape($product_category_id);
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $curr_route = $rs[0]['route'];
        }
        $q = '
            select pc.*
            from product_category pc
            where pc.status > 0
                and pc.id <> '.$db->escape($product_category_id).'
                and (
                    pc.route not like '.$db->escape('%'.$curr_route.'%').'
                        
                    or '.$db->escape($curr_route).' = ""                   
                    
                )
                and pc.product_category_status = "active"
                and (
                    pc.code like '.$db->escape('%'.$lookup_data.'%').'
                    or pc.name like '.$db->escape('%'.$lookup_data.'%').'
                )
                
            order by pc.code asc
            limit 100
            '
        ;
        
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $result = $rs;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_prnt_product_category_search($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        
        $t_product_category = Product_Category_Data_Support::prnt_product_category_search($param);
        
        foreach ($t_product_category as $idx => $row) {
            $result[] = array(
                'id' => $row['id'],
                'text' => Tools::html_tag('strong', $row['code']) . ' ' . $row['name']
            );
        }
        
        return $result;
        //</editor-fold>
    }
    
    public static function product_category_search($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db= new DB();
        $q_condition = $param['q_condition'];
        $lookup_data = $param['lookup_data'];
        $q = '
            select pc.* 
            from product_category pc
            where pc.status>0 
                and( 
                    pc.name like '.$db->escape('%'.$lookup_data.'%').'
                    or pc.code like '.$db->escape('%'.$lookup_data.'%').'
                )
                '.$q_condition.'
            order by pc.code
            limit 100;
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0) $result = $rs;
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_product_category_search($param = array()){
        //<editor-fold defaultstate="collapeds">
        $result = array();
        $t_result = self::product_category_search($param);
        foreach($t_result as $idx=>$row){
            $result[] = array(
                'id'=>$row['id'],
                'text'=>  Tools::html_tag('strong',$row['code'])
                    .' '.$row['name'],
            );
        }
        return $result;
        //</editor-fold>
    }
    
    
    
        
}
?>
