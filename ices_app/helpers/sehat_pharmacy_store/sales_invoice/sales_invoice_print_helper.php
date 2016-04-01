<?php

class fpdf_sales_invoice_print extends extended_fpdf{
    public function footer(){

    }
}

class Sales_Invoice_Print {
    
    private static function invoice_header_print($p_engine,$opt){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'customer','class_name'=>'customer_data_support'));
        
        $sales_invoice = $opt['sales_invoice'];
        $customer = Customer_Data_Support::customer_get($sales_invoice['customer_id'])['customer'];
        $curr_date = Tools::_date('','F d, Y H:i:s',null,array('LC_TIME'=>'ID'));
                
        $p_engine->fpdf->SetXY(5,5);
        $p_engine->bold();
        $p_engine->Cell(null,null,Lang::get('Sales Invoice').' '.$sales_invoice['code']);
        $p_engine->normal();
        $p_engine->Ln();
        
        $p_engine->Cell(null,null,Lang::get(array('Sales Invoice','Date')).': '.Tools::_date($sales_invoice['sales_invoice_date'],'F d, Y H:i:s'),0,0,'L');
        $p_engine->Ln();
        $p_engine->Cell(null,null,'Customer: '.$customer['name'],0,0,'L');
        $p_engine->Ln();
        
        
        //</editor-fold>
    }
    
    private static function invoice_footer_print($p_engine,$opt){
        //<editor-fold defaultstate="collapsed">
        
        
        $p_engine->set_xy($p_engine->fpdf->GetX(),$opt['footer_start']);
        $p_engine->Cell(0,null,''.Tools::_date('','F d, Y H:i:s'), 0, 0);
        $p_engine->Cell(0,null,'Page '.$opt['page_number'], 0, 0,'R');
        $p_engine->Ln();
        //</editor-fold>
    }
    
    public static function invoice_print($opt = array()){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
        SI::module()->load_class(array('module'=>'sales_receipt','class_name'=>'sales_receipt_data_support'));
        $success = 1;
        $db = new DB();
        
        $p_engine = $opt['p_engine'];
        $p_output = $opt['p_output'];
        $sales_invoice_id = $opt['sales_invoice_id'];
        
        $t_sales_invoice = Sales_Invoice_Data_Support::sales_invoice_get($sales_invoice_id);
        
        if(!count($t_sales_invoice)>0) $success = 0;
        
        if($success === 1){
            if($p_engine === null){
                $p_engine = new Printer('fpdf_sales_invoice_print');
                $p_engine->paper_set('1/6A4');
                $p_engine->start();
            }
            
            $sales_invoice = $t_sales_invoice['sales_invoice'];
            $si_product = $t_sales_invoice['si_product'];
            
            $q ='
                select coalesce(sum(sr.amount - sr.change_amount),0) total_amount
                from sales_receipt sr
                where sr.status > 0
                and sr.sales_receipt_status = "invoiced"
                and sr.ref_type = "sales_invoice"
                and sr.ref_id = '.$db->escape($sales_invoice_id).'
            ';
            
            $sr_total_amount = $db->query_array($q)[0]['total_amount'];
            
            $footer_start = $p_engine->fpdf->page_height_get()-5;
            $product_calculation_start = $p_engine->fpdf->page_height_get()-27;
            
            $header_data = array('sales_invoice'=>$sales_invoice);
            $footer_data = array('page_number'=>1,'footer_start'=>$footer_start);
            $tbl_col_width = array(
                'row_num'=>5,
                'product'=>39,
                'qty'=>10,
                'amount'=>15,
                'subtotal'=>20
            );

            $font_content_size = 7;
            $p_engine->font_set('Times',$font_content_size,'');
            
            Sales_Invoice_Print::invoice_header_print($p_engine,$header_data);           

            

            foreach($si_product as $sip_idx=>$sip_row){
                //<editor-fold defaultstate="collapsed">
                $new_page = false;                    
                $print_table_header = false;
                $print_footer = false;

                $curr_line_height = $p_engine->fpdf->LineHeight;
                $row_num = $sip_idx === 0?1:$row_num+1;
                
                $curr_y = $p_engine->fpdf->GetY();

                if($sip_idx === 0){
                    $row_num = 1;
                    $print_table_header = true;
                }
                else{                    

                    if((Tools::_float($curr_y)+Tools::_float($curr_line_height)) > Tools::_float($product_calculation_start)){
                        $new_page = true;
                    }

                    if($new_page) $print_table_header = true;
                    
                }
                
                

                if($new_page){
                    $p_engine->fpdf->AddPage();
                    Sales_Invoice_Print::invoice_header_print($p_engine,$header_data);
                    $footer_data['page_number']+=1;
                }


                if($print_table_header){
                    $p_engine->bold();
                    $p_engine->Ln();

                    $p_engine->Cell($tbl_col_width['row_num'],null,'No',1,0,'L');
                    $p_engine->Cell($tbl_col_width['product'],null,'Product',1,0,'L');
                    $p_engine->Cell($tbl_col_width['qty'],null,'Qty',1,0,'R');
                    $p_engine->Cell($tbl_col_width['amount'],null,'Amount',1,0,'R');
                    $p_engine->Cell($tbl_col_width['subtotal'],null,'Subtotal',1,0,'R');
                    

                    $p_engine->Ln();
                    $p_engine->normal();
                }

                $constant_sales = $sip_row['constant_sales'];
                
                $p_engine->Cell($tbl_col_width['row_num'],$curr_line_height,$row_num,1,0,'L');
                $product_text = $sip_row['product_name'].' - '.$sip_row['unit_code_sales'];
                $p_engine->Cell($tbl_col_width['product'],$curr_line_height,$product_text,1,0,'L');
                $qty = Tools::_float($sip_row['qty']) * Tools::_float($constant_sales);
                $p_engine->Cell($tbl_col_width['qty'],$curr_line_height,Tools::thousand_separator($qty,0),1,0,'R');
                $amount = Tools::_float($sip_row['amount']) / Tools::_float($constant_sales);
                $p_engine->Cell($tbl_col_width['amount'],$curr_line_height,Tools::thousand_separator($amount,0),1,0,'R');
                $p_engine->Cell($tbl_col_width['subtotal'],$curr_line_height,Tools::thousand_separator($sip_row['subtotal_amount'],0),1,0,'R');

                $p_engine->Ln();

                if (($p_engine->fpdf->GetY() + ($curr_line_height *2))> $product_calculation_start){
                    $print_footer = true;
                }
                
                if($print_footer){
                    Sales_Invoice_Print::invoice_footer_print($p_engine,$footer_data);
                    $p_engine->font_set('Times',$font_content_size,'');
                }

                //</editor-fold>
            }

            if($p_engine->fpdf->GetY() > $product_calculation_start){                
                $p_engine->fpdf->AddPage();
                Sales_Invoice_Print::invoice_header_print($p_engine,$header_data);
                $footer_data['page_number']+=1;
            }
            
            
            $p_engine->bold();
            $p_engine->Cell($p_engine->fpdf->page_width_get() - $tbl_col_width['subtotal'],null,'Total',0,0,'R');
            $p_engine->normal();
            $p_engine->Cell($tbl_col_width['subtotal'],null,Tools::thousand_separator($sales_invoice['total_amount'],0),0,0,'R');
            $p_engine->Ln();
            
            $p_engine->bold();
            $p_engine->Cell($p_engine->fpdf->page_width_get() - $tbl_col_width['subtotal'],null,'Discount',0,0,'R');
            $p_engine->normal();
            $p_engine->Cell($tbl_col_width['subtotal'],null,Tools::thousand_separator($sales_invoice['total_discount_amount'],0),0,0,'R');
            $p_engine->Ln();
            
            $p_engine->Cell(null,1,'---------------------------------------------',0,0,'R');
            $p_engine->Ln();
            
            $p_engine->bold();
            $p_engine->Cell($p_engine->fpdf->page_width_get() - $tbl_col_width['subtotal'],null,'Grand Total',0,0,'R');
            $p_engine->normal();
            $p_engine->Cell($tbl_col_width['subtotal'],null,Tools::thousand_separator($sales_invoice['grand_total_amount'],0),0,0,'R');
            $p_engine->Ln();
            
            $p_engine->bold();
            $p_engine->Cell($p_engine->fpdf->page_width_get() - $tbl_col_width['subtotal'],null,Lang::get('Payment'),0,0,'R');
            $p_engine->normal();
            $p_engine->Cell($tbl_col_width['subtotal'],null,Tools::thousand_separator($sr_total_amount,0),0,0,'R');
            $p_engine->Ln();
            $p_engine->Cell(null,1,'---------------------------------------------',0,0,'R');
            $p_engine->Ln();
            $p_engine->bold();
            $p_engine->Cell($p_engine->fpdf->page_width_get() - $tbl_col_width['subtotal'],null,Lang::prt_get('Outstanding'),0,0,'R');
            $p_engine->normal();
            $p_engine->Cell($tbl_col_width['subtotal'],null,Tools::thousand_separator((Tools::_float($sales_invoice['grand_total_amount']) - Tools::_float($sr_total_amount)),0),0,0,'R');
            $p_engine->Ln();
            
            Sales_Invoice_Print::invoice_footer_print($p_engine,$footer_data);

            $filename = 'Sales Invoice'.Tools::_date('','Ymd His').'.pdf';
            if($p_output){
                $p_engine->output($filename,'I');
            }
            else{
                $p_engine->output($filename,'F');
            }
            
        }
        
        //</editor-fold>
    }

}

?>