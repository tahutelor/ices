<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class fpdf_stock_opname_print extends extended_fpdf{
    public function footer(){

    }
}

class Print_Form_Print{
    function __construct(){}
    
    
    
    private static function stock_opname_header_print($p_engine,$opt){
        //<editor-fold defaultstate="collapsed">

        $temp = Warehouse_Data_Support::warehouse_get($opt['warehouse_id']);
        $warehouse_text = isset($temp['warehouse']['name'])?$temp['warehouse']['name']:'';
        $curr_date = Tools::_date('','F d, Y H:i:s',null,array('LC_TIME'=>'ID'));
        
        $p_engine->font_set('Times',10);
        $p_engine->bold();
        $p_engine->Cell(30,null,Lang::prt_get('STOCK OPNAME'));
        $p_engine->normal();
        
        $p_engine->font_set('Times',8);
        $p_engine->Cell(40,null,' '.Lang::prt_get($curr_date),0,0,'L');
        
        $p_engine->Cell(0,null,'Warehouse: '.$warehouse_text,0,0,'L');
        $p_engine->normal();
        $p_engine->Ln();
        
        $p_engine->Ln();
        //</editor-fold>
    }
    
    private static function stock_opname_footer_print($p_engine,$opt){
        //<editor-fold defaultstate="collapsed">
        $p_engine->font_set('Times',8);
        $p_engine->Ln();
        $p_engine->set_xy($p_engine->fpdf->GetX(),$opt['footer_start']);
        $p_engine->Cell(0,null,''.Tools::_date('','F d, Y H:i:s'), 0, 0);
        $p_engine->Cell(0,null,'Page '.$opt['page_number'], 0, 0,'R');
        $p_engine->Ln();
        //</editor-fold>
    }
    
    public static function product_stock_opname_print($opt = array()){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'print_form','class_name'=>'print_form_data_support'));
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        $success = 1;
        $db = new DB();
        
        $p_engine = $opt['p_engine'];
        $p_output = $opt['p_output'];
        $warehouse_id = $opt['warehouse_id'];
        $product_category_id = $opt['product_category_id'];
        
        $param = array(
            'warehouse_id'=>$warehouse_id,
            'product_category_id'=>$product_category_id,
        );
        
        $product_list = Print_Form_Data_Support::product_stock_opname_product_list_get($param);
        
        if($p_engine === null){
            $p_engine = new Printer('fpdf_stock_opname_print');
            $p_engine->paper_set('A4');
            $p_engine->start();
        }
        
        $footer_start = 277;
        $header_data = array('warehouse_id'=>$warehouse_id);
        $footer_data = array('page_number'=>1,'footer_start'=>$footer_start);
        $product_col_width = array(
            'row_num'=>7,
            'product'=>85,
            'product_batch'=>50,
            'unit'=>7,
            'curr_qty'=>25,
            'new_qty'=>25,
        );
        
        Print_Form_Print::stock_opname_header_print($p_engine,$header_data);
        $font_content_size = 8;
        $p_engine->font_set('Times',$font_content_size,'');

        
        
        foreach($product_list as $pl_idx=>$pl_row){
            //<editor-fold defaultstate="collapsed">
            $new_page = false;                    
            $print_table_header = false;
            $print_footer = false;
            $switch_col = false;
            
            $curr_line_height = $p_engine->fpdf->LineHeight;
            $header_line_height = $p_engine->fpdf->LineHeight * 3;
            $row_num = $pl_idx === 0?1:$row_num+1;
            
            if($pl_idx === 0){
                $row_num = 1;
                $print_table_header = true;
            }
            else{
                $curr_y = $p_engine->fpdf->GetY();
                
                if((Tools::_float($curr_y)+Tools::_float($curr_line_height)) > Tools::_float($footer_start)){
                    $new_page = true;
                }
                
                if($new_page) $print_table_header;
                                
            }

            if($new_page){
                $p_engine->fpdf->AddPage();
                Print_Form_Print::stock_opname_header_print($p_engine,$header_data);
                $p_engine->font_set('Times',$font_content_size,'');
                $footer_data['page_number']+=1;
            }
            
            
            if($print_table_header){
                $p_engine->bold();
                $p_engine->Ln();
                
                $p_engine->Cell($product_col_width['row_num'],null,'No',1,0,'L');
                $p_engine->Cell($product_col_width['product'],null,'Product',1,0,'L');
                $p_engine->Cell($product_col_width['product_batch'],null,'Batch Number',1,0,'L');
                $p_engine->Cell($product_col_width['unit'],null,'Unit',1,0,'L');
                $p_engine->Cell($product_col_width['curr_qty'],null,'Curr Qty',1,0,'R');
                $p_engine->Cell($product_col_width['new_qty'],null,'New Qty',1,0,'R');
                
                $p_engine->Ln();
                $p_engine->normal();
            }
            
            
            $p_engine->Cell($product_col_width['row_num'],$curr_line_height,$row_num,1,0,'L');
            $p_engine->Cell($product_col_width['product'],$curr_line_height,$pl_row['product_code'].' '.$pl_row['product_name'],1,0,'L');
            $p_engine->Cell($product_col_width['product_batch'],$curr_line_height,$pl_row['batch_number'].' '.$pl_row['expired_date'],1,0,'L');
            $p_engine->Cell($product_col_width['unit'],$curr_line_height,$pl_row['unit_code'],1,0,'L');
            $p_engine->Cell($product_col_width['curr_qty'],$curr_line_height,Tools::thousand_separator($pl_row['qty']),1,0,'R');
            $p_engine->Cell($product_col_width['new_qty'],$curr_line_height,'',1,0,'R');
            
            $p_engine->Ln();
            
            if($print_footer){
                Print_Form_Print::stock_opname_footer_print($p_engine,$footer_data);
                $p_engine->font_set('Times',$font_content_size,'');
            }
            
            //</editor-fold>
        }
        
        
        Print_Form_Print::stock_opname_footer_print($p_engine,$footer_data);
        
        if($p_output){
            $p_engine->output('Stock Opname Form.pdf','I');
        }
        
        //</editor-fold>
    }
    
}

?>