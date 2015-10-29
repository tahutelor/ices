<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$my_param = array(
    'file_path' => APPPATH . 'helpers/ices/dashboard/dashboard_data_support_helper.php',
    'src_class' => 'Dashboard_Data_Support',
    'src_extends_class' => '',
    'dst_class' => 'Dashboard_Data_Support_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class Dashboard_Data_Support extends Dashboard_Data_Support_Parent{

    public static function weekly_sales_invoice_get(){
        $result = array(
            'data'=>array('html'=>'','script'=>''),
            'target_data'=>'#weekly_sales_invoice_content'
        );
        
        $html = '';
        $script = '';
        
        $param = array();
        
        $db = new DB();
        
        $sales_invoice_data = array();
        for($i = 6;$i>=0;$i--){
            $sales_invoice_data[] = array(
                'date'=>Tools::_date('','Y-m-d','-P'.$i.'D'),
                'sales_invoice_amount'=>Tools::_float('0')
            );
        }
        
        $q = '
            select date_format(si.sales_invoice_date,"%Y-%m-%d") date
                ,sum(si.grand_total_amount) sales_invoice_amount
            from sales_invoice si
            where si.status > 0
                and si.sales_invoice_status = "invoiced"
                and date_format(si.sales_invoice_date,"%Y-%m-%d")
                    >= date_format(date_add(now(), interval -6 day),"%Y-%m-%d")
            group by date_format(si.sales_invoice_date,"%Y-%m-%d")
        ';
        
        $rs = $db->query_array($q);
        foreach($rs as $idx=>$row){
            foreach($sales_invoice_data as $idx2=>$row2){
                if($row['date'] === $row2['date']){
                    $sales_invoice_data[$idx2]['sales_invoice_amount'] = Tools::_float($row['sales_invoice_amount']);
                }
            }
        }
        
        $param = array(
            'sales_invoice_data'=>$sales_invoice_data
        );
        
        $script = str_replace(array('<script>','</script>'),'',get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'dashboard/weekly_sales_invoice_js',$param,true));
        //$app->js_set($js);
        $result['data']['html'] = $html;
        $result['data']['script'] = $script;
        return $result;
    }

}

?>
