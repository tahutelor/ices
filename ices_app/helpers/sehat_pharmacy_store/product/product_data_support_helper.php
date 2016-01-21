<?php
class Product_Data_Support {
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'product/product_engine');
        //</editor-fold>
    }
    
    public static function product_get($id){
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select distinct p.*                
                ,pc.code product_category_code
                ,pc.name product_category_name
                ,u.id unit_id
                ,u.code unit_code
                ,u.name unit_name
                ,pu.purchase_amount
                ,pu.sales_formula
                ,replace(pu.sales_formula,"c",pu.purchase_amount ) sales_amount
            from product p
                inner join product_category pc on p.product_category_id = pc.id
                inner join p_u pu on p.id = pu.product_id
                inner join unit u on pu.unit_id = u.id
            where p.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $product = $rs[0];
            $product['sales_amount'] = self::sales_amount_get($product['sales_amount']);
            $result['product'] = $product;
            //die(var_dump($product));
        }
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_product_category_search($lookup_data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'product_category','class_name'=>'product_category_data_support'));
        $result = array();
        $param = array(
            'lookup_data'=>$lookup_data,
            'q_condition'=>' and pc.product_category_status = "active" '
        );
        $result = Product_Category_Data_Support::input_select_product_category_search($param);
        return $result;
        //</editor-fold>
    }
    
    public static function unit_list_get(){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        SI::module()->load_class(array('module'=>'unit','class_name'=>'unit_data_support'));
        $t_unit = Unit_Data_Support::unit_list_get(array('unit_status'=>'active'));
        $result = $t_unit;
        return $result;
        //</editor-fold>
    }
    
    public static function product_search($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $lookup_data = '%'.$param['lookup_data'].'%';
        $q_product_status = isset($param['product_status'])?
            ' and p.product_status = '.$db->escape($param['product_status']):
            ''
        ;
        $q_condition = isset($param['q_condition'])?$param['q_condition']:'';
        $q = '
            select distinct p.*
                ,u.id unit_id
                ,u.code unit_code
                ,u.name unit_name
                ,coalesce(sum(psg.qty),0) qty
                ,replace(pu.sales_formula,"c",pu.purchase_amount ) sales_amount
            from product p
            inner join p_u pu on p.id = pu.product_id
            inner join unit u 
                on pu.unit_id = u.id 
                and u.status > 0 
                and u.unit_status = "active"
            left outer join product_batch pb 
                on p.id = pb.product_id and pb.product_type = "registered_product"
                and pb.unit_id = u.id
                and pb.status > 0
                and pb.product_batch_status = "active"
                and pb.expired_date>now()
            left outer join product_stock_good psg
                on psg.product_batch_id = pb.id and psg.status > 0
            where p.status > 0
            and (
                p.code like '.$db->escape($lookup_data).'
                or p.name like '.$db->escape($lookup_data).'
                or p.barcode like '.$db->escape($lookup_data).'
            )
            '.$q_product_status.'
            '.$q_condition.'
            group by p.id, u.id
            order by p.code
            limit 100
        ';
        $rs = $db->query_array($q);
        
        if(count($rs)>0){
            $rs;
            
            foreach($rs as $idx=>$row){
                $t_product = array(
                    'id'=>$row['id'],
                    'code'=>$row['code'],
                    'name'=>$row['name'],
                    'product_type'=>'registered_product',
                    'unit_description'=>$row['unit_description'],
                    'unit'=>array(
                        array(
                            'id'=>$row['unit_id'],
                            'code'=>$row['unit_code'],
                            'name'=>$row['unit_name'],
                            'qty'=>$row['qty'],
                            'sales_amount' => self::sales_amount_get($row['sales_amount']),
                        )
                    ),
                    'barcode'=>$row['barcode'],
                );
                                
                $product_already_exists = false;
                foreach($result as $idx2=>$row2){
                    if($t_product['id'] === $row2['id']){
                        $product_already_exists = true;
                        break;
                    }
                }
                
                if(!$product_already_exists){
                    foreach($rs as $idx2=>$row2){
                        if($row2['id'] === $t_product['id'] && $idx2 !== $idx){
                            $t_product['unit'][] = array(
                                'id'=>$row2['unit_id'],
                                'code'=>$row2['unit_code'],
                                'name'=>$row2['unit_name'],
                                'qty'=>$row2['qty'],
                                'sales_amount' => self::sales_amount_get($row2['sales_amount']),
                            );
                        }
                    }
                    $result[] = $t_product;
                }
                
            }
        }
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_product_search($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $t_product = self::product_search($param);
        
        foreach($t_product as $idx=>$row){
            $t_product[$idx]['text'] = Tools::html_tag('strong',$row['code'])
                .' '.$row['name'].' '.$row['unit_description'];
            foreach($t_product[$idx]['unit'] as $idx2=>$row2){
                $t_product[$idx]['unit'][$idx2]['text'] = Tools::html_tag('strong',$row2['code'])
                    .' '.$row2['name'];
            }
        }
        $result = $t_product;
        return $result;
        //</editor-fold>
    }
    
    public static function product_list_validate($product_list = array(),$param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        $db = new DB();
        
        $q_product_status = isset($param['product_status'])?
            'and p.product_status = '.$db->escape($param['product_status']):
            'and p.product_status = "active" ';
        
        $q_reg_product_list = 'select -1 product_id, -1 unit_id';
        $reg_product_total = 0;
        foreach($product_list as $idx=>$row){
            $product_type = isset($row['product_type'])?$row['product_type']:'';
            if($product_type !== 'registered_product'){
                $success = 0;
                $msg[] = Lang::get('Product Type')
                    .' '.Lang::get('invalid');
            }
            if($product_type === 'registered_product'){   
                $reg_product_total+=1;
                $product_id = $db->escape(isset($row['product_id'])?$row['product_id']:'');
                $unit_id = $db->escape(isset($row['unit_id'])?$row['unit_id']:'');
                $q_reg_product_list.=' union all select '.$product_id.', '.$unit_id;
            }
            if($success !== 1) break;
        }
        
        if($success === 1){
            $q = '
                select tp.*
                from ('.$q_reg_product_list.') tp
                inner join p_u pu on tp.product_id = pu.product_id and tp.unit_id = pu.unit_id
                inner join product p on pu.product_id = p.id and p.status>0
                inner join unit u on pu.unit_id = u.id and u.status>0
                where 1 = 1
                    '.$q_product_status.'
            ';

            $rs = $db->query_array($q);

            if(count($rs) !== $reg_product_total){
                $success = 0;
                $msg[] = Lang::get('Registered Product')
                    .' '.Lang::get('invalid',true,false);
            }
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
    public static function product_list_get($product_list = array(),$param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        
        $db = new DB();
        
        $q_condition = isset($param['q_condition'])?$param['q_condition']:'';
        
        
        $q_product = 'select -1 product_id, -1 unit_id ';
        foreach($product_list as $idx=>$row){
            $product_type = $row['product_type'];
            $product_id = $row['product_id'];
            $unit_id = $row['unit_id'];
            if($product_type === 'registered_product'){
                $q_product.= ' union all select '.$db->escape($product_id).', '.$db->escape($unit_id);
            }
        }
        
        $q = '
            select distinct
                "registered_product" product_type,
                pu.*
                ,p.code product_code
                ,p.name product_name
                ,u.code unit_code
                ,u.name unit_name
                ,coalesce(sum(psg.qty),0) qty
                ,replace(pu.sales_formula,"c",pu.purchase_amount ) sales_amount
            from p_u pu
            inner join ('.$q_product.')tp 
                on pu.product_id = tp.product_id and pu.unit_id = tp.unit_id
            inner join product p on pu.product_id = p.id and p.status > 0
            inner join unit u on pu.unit_id = u.id and u.status > 0
            left outer join product_batch pb 
                on pb.product_id = pu.product_id and pb.product_type = "registered_product"
                and pb.unit_id = pu.unit_id and pb.status > 0
            left outer join product_stock_good psg 
                on psg.product_batch_id = pb.id and psg.status > 0
            left outer join warehouse w on psg.warehouse_id = w.id and w.status > 0
            where 1 = 1
                '.$q_condition.'
            group by pu.product_id, pu.unit_id
            
        ';
        
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $result = $rs;
            foreach($result as $idx=>$row){
                $result[$idx]['sales_amount'] = self::sales_amount_get($row['sales_amount']);
            }
        }
        
        return $result;
        //</editor-fold>
    }
    
    public static function sales_amount_get($formula){
        //<editor-fold defaultstate="collapsed">
        $amount = eval('return '.$formula.';');;
        $result = ceil($amount/500)*500;        
        return $result;
        //</editor-fold>
    }
        
}
?>
