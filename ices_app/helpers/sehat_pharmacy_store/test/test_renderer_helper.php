<?php
class Test_Renderer{
    
    static function supplier_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'add');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
                
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','[AUTO GENERATE]')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Name'))
                ->input_set('icon', APP_ICON::user())
                ->input_set('hide_all', true)
                ->input_set('disable_all', false)
                ->input_set('value','Apotek Mitra Farma')
                ->input_set('attrib', array('style' => ''))
        ;
        
        $data = array(
            array('id'=>1,'row_num'=>1,'phone_number'=>'+628113308009','action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>'),
            array('id'=>2,'row_num'=>2,'phone_number'=>'<input class="form-control" value="+62315020393">','action'=>'<button class="fa fa-plus text-blue background-transparent no-border" style="cursor:pointer"></button>'),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','_phone_number_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"phone_number","label"=>"Phone Number",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('class'=>'table-action')));
        $table->table_set('data',$data);
                
        
        $detail_pane->input_add()->input_set('label', Lang::get('BB Pin'))
            ->input_set('icon', APP_ICON::phone())
            ->input_set('hide_all', true)
            ->input_set('disable_all', false)
            ->input_set('value','112C23S')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $data = array(
            array('id'=>1,'row_num'=>1,'address'=>'<div>Ngagel Jaya Utara 3 / 23<br/>Surabaya</br>Indonesia</div>','action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>'),
            array('id'=>2,'row_num'=>2,'address'=>'<textarea class="form-control" ></textarea>','action'=>'<button class="fa fa-plus text-blue background-transparent no-border" style="cursor:pointer"></button>'),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','_address_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"address","label"=>"Address",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('class'=>'table-action')));
        $table->table_set('data',$data);
        
        
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array())
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        $js = ''
            . ' var lstatus = [{id:1,text:"<strong class=\"text-green\">ACTIVE</strong>"}]; '
            . '$("#_contact_status").select2({data:lstatus}); '
            . '$("#_contact_status").select2("data",lstatus[0]); '
        ;
        $app->js_set($js);
                
        $detail_pane->input_add()->input_set('label', Lang::get('Supplier Debit (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','0.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Supplier Credit (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','0.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        
                
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">INVOICED</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        
        
        //</editor-fold>
    }
    
    static function customer_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'view');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
                
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','CUST/1')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Name'))
                ->input_set('icon', APP_ICON::user())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','Johanes Edwin Prayoga')
                ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Customer Type')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_customer_type')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'General'))
            ->input_select_set('hide_all', true)
        ;
        
        $data = array(
            array('id'=>1,'row_num'=>1,'phone_number'=>'+628113308009','action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>'),
            array('id'=>2,'row_num'=>2,'phone_number'=>'<input class="form-control" value="+62315020393">','action'=>'<button class="fa fa-plus text-blue background-transparent no-border" style="cursor:pointer"></button>'),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','_phone_number_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"phone_number","label"=>"Phone Number",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('class'=>'table-action')));
        $table->table_set('data',$data);
                
        
        $detail_pane->input_add()->input_set('label', Lang::get('BB Pin'))
            ->input_set('icon', APP_ICON::phone())
            ->input_set('hide_all', true)
            ->input_set('disable_all', false)
            ->input_set('value','112C23S')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $data = array(
            array('id'=>1,'row_num'=>1,'address'=>'<div>Ngagel Jaya Utara 3 / 23<br/>Surabaya</br>Indonesia</div>','action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>'),
            array('id'=>2,'row_num'=>2,'address'=>'<textarea class="form-control" ></textarea>','action'=>'<button class="fa fa-plus text-blue background-transparent no-border" style="cursor:pointer"></button>'),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','_address_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"address","label"=>"Address",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('class'=>'table-action')));
        $table->table_set('data',$data);
        
        
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">INVOICED</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
                
        $detail_pane->input_add()->input_set('label', Lang::get('Customer Debit (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','0.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Customer Credit (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','12,500,000.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        
                
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">INVOICED</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        
        
        //</editor-fold>
    }
    
    static function customer_type_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'add');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
                
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','[AUTO GENERATE]')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Name'))
                ->input_set('icon', APP_ICON::user())
                ->input_set('hide_all', true)
                ->input_set('disable_all', false)
                ->input_set('value','General')
                ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong class=\"text-green\">INVOICED</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Notification - Outstanding Sales Invoice')
            ->input_select_set('icon', APP_ICON::notification())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_notif_osi')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'true','text'=>'<strong class=\"text-green\">TRUE</strong>'))
            ->input_select_set('allow_empty', false)
            ->input_select_set('hide_all', true)
        ;
             
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Customer\'s Default')
            ->input_select_set('icon', APP_ICON::customer())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_cd')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'true','text'=>'<strong class=\"text-green\">TRUE</strong>'))
            ->input_select_set('allow_empty', false)
            ->input_select_set('hide_all', true)
        ;
        
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">INVOICED</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        //</editor-fold>
    }
    
    static function product_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'view');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
        
        
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','P/1')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Name'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','MIXAGRIP 4 KAPLET')
                ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Barcode'))
                ->input_set('icon', APP_ICON::barcode())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','8239123')
                ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_select_add()
                ->input_select_set('label', 'Product Subcategory')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', '_xxx')
                ->input_select_set('allow_empty', false)
                ->input_select_set('data_add', array(array('id'=>'MIXAGRIP','text'=>SI::get_status_attr('MIXAGRIP'))))
                ->input_select_set('value', array('id'=>'MIXAGRIP','text'=>'<strong>MIXAGRIP</strong> MIXAGRIP'))
                ->input_select_set('hide_all', true)
            ;
        
        $detail_pane->input_select_add()
                ->input_select_set('label', Lang::get('Unit'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', 'unit')
                ->input_select_set('allow_empty', false)
                ->input_select_set('data_add', array(array('id'=>'PCS','text'=>SI::get_status_attr('PCS'))))
                ->input_select_set('value', array('id'=>'PCS','text'=>'<strong>PCS</strong> PIECES'))
                ->input_select_set('hide_all', true)
            ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Purchase Amount (Rp.)'))
                ->input_set('icon', APP_ICON::money())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','25,000.00')
                ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Sales Formula'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','PA * 1.1')
                ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Sales Amount (Rp.)'))
                ->input_set('icon', APP_ICON::money())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','27,000.00')
                ->input_set('attrib', array('disabled' => ''))
        ;
        
                        
        $detail_pane->input_select_add()
                ->input_select_set('label', 'Status')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', '_contact_status')
                ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
                ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">ACTIVE</strong>'))
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true);
        
       $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-29 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>2,'row_num'=>2,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-23 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>3,'row_num'=>3,'product_status'=>'<strong style="color:red">INACTIVE</strong>','moddate'=>'2015-09-20 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>4,'row_num'=>4,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-15 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>5,'row_num'=>5,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_batch_tab', "value" => "Product Batch", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_batch_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'code'=>'<a href="#">2015120901</a>','warehouse'=>'Pucang Anom','stock_qty'=>'5,025.00','purchase_date'=>'2015-09-09','expired_date'=>'2015-12-09','purchase_amount'=>'25,000.00','purchase_code'=>'<a href="">PI/STO1/5</a>'),
            array('id'=>2,'row_num'=>2,'code'=>'<a href="#">2015112902</a>','warehouse'=>'Pucang Anom','stock_qty'=>'10.00','purchase_date'=>'2015-08-29','expired_date'=>'2015-11-29','purchase_amount'=>'25,000.00','purchase_code'=>'<a href="">PI/STO1/5</a>'),
            array('id'=>3,'row_num'=>3,'code'=>'<a href="#">2015112901</a>','warehouse'=>'Pucang Anom','stock_qty'=>'20.00','purchase_date'=>'2015-08-19','expired_date'=>'2015-11-29','purchase_amount'=>'25,000.00','purchase_code'=>'<a href="">PI/STO1/5</a>'),
            array('id'=>4,'row_num'=>4,'code'=>'<a href="#">2015110901</a>','warehouse'=>'Pucang Anom','stock_qty'=>'3.00','purchase_date'=>'2015-08-15','expired_date'=>'2015-11-09','purchase_amount'=>'23,000.00','purchase_code'=>'<a href="">PI/STO1/3</a>'),
            array('id'=>5,'row_num'=>5,'code'=>'<a href="#">2015110301</a>','warehouse'=>'Pucang Anom','stock_qty'=>'5.00','purchase_date'=>'2015-08-03','expired_date'=>'2015-11-03','purchase_amount'=>'23,000.00','purchase_code'=>'<a href="">PI/STO11</a>'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','employee_u_group_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"code","label"=>"Product Batch Code",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"expired_date","label"=>"Expired Date",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"warehouse","label"=>"Warehouse",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"stock_qty","label"=>"Stock",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"purchase_code","label"=>"Purchase Code",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"purchase_date","label"=>"Purchase Date",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"purchase_amount","label"=>"Purchase Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right;width:200px'),'attribute'=>'style="text-align:right"'));
        
        $table->table_set('data',$data);
        
        //</editor-fold>
    }
    
    static function product_batch_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'view');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
        
        
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','2015120901')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Expired Date'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','December 09, 2015')
                ->input_set('attrib', array('style' => ''))
        ;
        
        
                        
        $detail_pane->input_select_add()
                ->input_select_set('label', 'Status')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', '_contact_status')
                ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
                ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">ACTIVE</strong>'))
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true);
       
        $data = array(
            array('id'=>1,'row_num'=>1,'warehouse'=>'Pucang Anom','stock_qty'=>'5,025.00','unit'=>'PCS'),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_stock_table');
        $table->table_set('class','table fixed-table');
        $table->div_set('label','Product Stock');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"warehouse","label"=>"Warehouse",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"stock_qty","label"=>"Stock",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"unit","label"=>"Unit",'col_attrib'=>array('style'=>'width:100px')));
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('style'=>'text-align:right','class'=>'table-action'),'attribute'=>'style="text-align:right"'));
        $table->table_set('data',$data);
        
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-29 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>2,'row_num'=>2,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-23 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>3,'row_num'=>3,'product_status'=>'<strong style="color:red">INACTIVE</strong>','moddate'=>'2015-09-20 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>4,'row_num'=>4,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-15 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>5,'row_num'=>5,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_batch_tab', "value" => "Product Stock History", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_batch_tab')->div_set('class', 'tab-pane');
        
        $detail_pane->input_select_add()
                ->input_select_set('label', 'Warehouse')
                ->input_select_set('icon', APP_ICON::warehouse())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', '_warehouse')
                ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
                ->input_select_set('value', array('id'=>'active','text'=>'ALL'))
                ->input_select_set('hide_all', true)
            ;
        
        $data = array(
            array('id'=>1,'row_num'=>1,'warehouse'=>'Pucang Anom','moddate'=>'2015-08-03 00:00:00',
                'old_stock_qty'=>'465.00','stock_qty'=>'25.00','new_stock_qty'=>'485.00',
                'unit'=>'PCS','employee'=>'Johanes Edwin Prayoga','description'=>'Purchase Invoice: PI/SO1/2'

            ),
            array('id'=>1,'row_num'=>2,'warehouse'=>'Pucang Anom','moddate'=>'2015-08-03 00:00:00',
                'old_stock_qty'=>'500.00','stock_qty'=>'- 35.00','new_stock_qty'=>'465.00',
                'unit'=>'PCS','employee'=>'Johanes Edwin Prayoga','description'=>'Sales Invoice: SI/SO1/1'

            ),
            array('id'=>1,'row_num'=>3,'warehouse'=>'Pucang Anom','moddate'=>'2015-08-03 00:00:00',
                'old_stock_qty'=>'0.00','stock_qty'=>'500.00','new_stock_qty'=>'500.00',
                'unit'=>'PCS','employee'=>'Johanes Edwin Prayoga','description'=>'Product INITIALIZE'

            ),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','employee_u_group_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"warehouse","label"=>"Warehouse",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Modified Date",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"old_stock_qty","label"=>"Old Stock",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"stock_qty","label"=>"Δ Stock",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"new_stock_qty","label"=>"New Stock",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"unit","label"=>"Unit",'col_attrib'=>array('style'=>'width:100px')));
        $table->table_set('columns',array("name"=>"employee","label"=>"Employee",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"description","label"=>"Description",'col_attrib'=>array()));
        
        $table->table_set('data',$data);
        
        //</editor-fold>
    }
    
    static function product_stock_opname_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'view');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
        
        
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','[AUTO GENERATE]')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Product Stock Opname Date'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','October 02, 2015')
                ->input_set('attrib', array('style' => ''))
        ;
        
         $detail_pane->input_add()->input_set('label', Lang::get('Checker'))
                ->input_set('icon', APP_ICON::user())
                ->input_set('hide_all', true)
                ->input_set('disable_all', false)
                ->input_set('value','Budi')
                ->input_set('attrib', array('style' => ''))
        ;
        
                        
        $detail_pane->input_select_add()
                ->input_select_set('label', 'Warehouse')
                ->input_select_set('icon', APP_ICON::warehouse())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', '_warehouse')
                ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
                ->input_select_set('value', array('id'=>'active','text'=>'<strong>W1</strong> Pucang Anom'))
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true); 
         
        $detail_pane->input_select_add()
                ->input_select_set('label', 'Status')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', '_contact_status')
                ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
                ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">ACTIVE</strong>'))
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true);
       
        $data = array(
            array('id'=>1,'row_num'=>1,
                'product'=>'<strong>P/1</strong> MIXAGRIP 4 KAPLET',
                'expired_date'=>'<strong>2015120201</strong> December 02, 2015',
                'old_stock_qty'=>'5,200.00','delta_stock_qty'=>'-160.00','new_stock_qty'=>'5,040.00','unit'=>'PCS',
                'action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>',
            ),
            array('id'=>1,'row_num'=>2,
                'product'=>'<strong>P/25</strong> DECOLIN SYRUP 50ml',
                'expired_date'=>'<strong>2015122301</strong> December 23, 2015',
                'old_stock_qty'=>'25.00','delta_stock_qty'=>'-3.00','new_stock_qty'=>'22.00','unit'=>'PCS',
                'action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>',
            ),
            array('id'=>1,'row_num'=>3,
                'product'=>'<strong>P/21</strong> BODREX 4 KAPLET',
                'expired_date'=>'<strong>2015122501</strong> December 25, 2015',
                'old_stock_qty'=>'10.00','delta_stock_qty'=>'160.00','new_stock_qty'=>'170.00','unit'=>'PCS',
                'action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>',
            ),
            array('id'=>1,'row_num'=>4,
                'product'=>'<strong>P/1000</strong> OBH SYRUP 50ml',
                'expired_date'=>'<strong>2015110201</strong> November 02, 2015',
                'old_stock_qty'=>'98.00','delta_stock_qty'=>'-98.00','new_stock_qty'=>'0.00','unit'=>'PCS',
                'action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>',
            ),
            array('id'=>1,'row_num'=>5,
                'product'=>'<strong>P/2092</strong> DECOLIN SYRUP 30ml',
                'expired_date'=>'<strong>2016020201</strong> February 02, 2016',
                'old_stock_qty'=>'200.00','delta_stock_qty'=>'-25.00','new_stock_qty'=>'175.00','unit'=>'PCS',
                'action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>',
            ),
            array('id'=>2,'row_num'=>6,
                'product'=>'<input original>',
                'expired_date'=>'<input original>',
                'old_stock_qty'=>'25.00','delta_stock_qty'=>'12.00',
                'new_stock_qty'=>'<input class="form-control"style="text-align:right"value="37.00">',
                'unit'=>'PCS',
                'action'=>'<button class="fa fa-plus text-blue background-transparent no-border" style="cursor:pointer"></button>',
            ),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_stock_table');
        $table->table_set('class','table fixed-table sm-text');
        $table->div_set('label','Product Stock');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"product","label"=>"Product",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"expired_date","label"=>"Expired Date",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"old_stock_qty","label"=>"Stock",'col_attrib'=>array('style'=>'text-align:right;width:150px'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"delta_stock_qty","label"=>"Δ Stock",'col_attrib'=>array('style'=>'text-align:right;width:150px'),'attribute'=>'style="text-align:right;" class="text-red"'));
        $table->table_set('columns',array("name"=>"new_stock_qty","label"=>"New Stock",'col_attrib'=>array('style'=>'text-align:right;width:150px'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"unit","label"=>"Unit",'col_attrib'=>array('style'=>'width:50px')));
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('style'=>'text-align:right','class'=>'table-action'),'attribute'=>'style="text-align:right"'));
        $table->table_set('data',$data);
        
        $js = ''
            .'$("#product_stock_table").find("[col_name=\"product\"] input[original]").select2({data:[]});'
                .'$("#product_stock_table").find("[col_name=\"product\"] input[original]").select2("data",{id:1,text:"<strong>P/3</strong> MIXAGRIP 3 KAPLET"});'
            .'$("#product_stock_table").find("[col_name=\"expired_date\"] input[original]").select2({data:[]});'
                .'$("#product_stock_table").find("[col_name=\"expired_date\"] input[original]").select2("data",{id:1,text:"<strong>2016070201</strong> July 02, 2016"});'
            .''
        ;
        $app->js_set($js);
        
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-29 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>2,'row_num'=>2,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-23 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>3,'row_num'=>3,'product_status'=>'<strong style="color:red">INACTIVE</strong>','moddate'=>'2015-09-20 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>4,'row_num'=>4,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-15 11:00:00','name'=>'Johanes Edwin Prayoga'),
            array('id'=>5,'row_num'=>5,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        
        //</editor-fold>
    }
    
    static function purchase_invoice_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'add');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Store')
            ->input_select_set('icon', APP_ICON::store())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_store')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'<strong>STO1</strong> Pucang Anom'))
            ->input_select_set('hide_all', true)
            ->input_select_set('allow_empty',false)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','[AUTO GENERATE]')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('Purchase Invoice Date')))
                ->datetimepicker_set('id', '_purchase_invoice')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
        ;
        
        $detail_pane->input_select_detail_add()
            ->input_select_set('label',Lang::get('Supplier'))
            ->input_select_set('icon',App_Icon::supplier())
            ->input_select_set('min_length','0')
            ->input_select_set('id','_customer')
            ->input_select_set('data_add',array())
            ->input_select_set('ajax_url','')
            ->input_select_set('allow_empty',false)
            ->input_select_set('value',array('id'=>'customer','text'=>'<strong>SUP/1</strong> Apotek Mitra Fatma'))
            ->input_select_set('disable_all',false)
            ->detail_set('id','_customer_detail')
            ->detail_set('button_new',true)
            ->detail_set('button_new_id','_btn_customer_new')
            ->detail_set('button_new_class','btn btn-primary btn-sm')
        ;
        
        $js = ''
            .'$("#_customer_detail ul li").prepend("'
                .'<div><strong>Supplier:</strong> <a href=\"#\">Apotek Mitra Fatma</a> </div>'
                .'<div><strong>Address:</strong> Ngagel Jaya Utara </div>'
                .'<div><strong>Phone Number:</strong> 628113308009, 62315020393</div>'
                
            .'") ;'
        .'';
        $app->js_set($js);
        
        
        
        
        $detail_pane->input_add()->input_set('label', Lang::get('Supplier Sales Invoice Code'))
            ->input_set('icon', APP_ICON::info())
            ->input_set('hide_all', true)
            ->input_set('disable_all', false)
            ->input_set('value','8239123')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">INVOICED</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product'=>'<strong>P/1</strong> MIXAGRIP 4 KAPLET','expired_date'=>'January 24, 2015 11:32','qty'=>'5,200.00','unit'=>'PCS','amount'=>'23,000.00','action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>','subtotal_amount'=>'11,960,000.00'),
            array('id'=>2,'row_num'=>2,'product'=>'<strong>P/2</strong> MIXAGRIP 4 KAPLET','expired_date'=>'December 09, 2015 11:32','qty'=>'5,200.00','unit'=>'PCS','amount'=>'5,300.00','action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>','subtotal_amount'=>'27,560,000.00'),
            array('id'=>3,'row_num'=>3,'product'=>'<input original>','expired_date'=>'<input class="form-control" value="February 24, 2016 11:32">','qty'=>'<input style="text-align:right" class="form-control" value="52.00">','unit'=>'PCS','amount'=>'<input class="form-control" style="text-align:right" value="17,500.00">','subtotal_amount'=>'910,000.00','action'=>'<button class="fa fa-plus text-blue background-transparent no-border" style="cursor:pointer"></button>'),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_table');
        $table->table_set('class','table fixed-table sm-text');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"product","label"=>"Product",'col_attrib'=>array('style'=>'width:400px')));
        $table->table_set('columns',array("name"=>"expired_date","label"=>"Expired Date",'col_attrib'=>array('style'=>'width:175px')));
        $table->table_set('columns',array("name"=>"qty","label"=>"Qty",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"unit","label"=>"Unit",'col_attrib'=>array('style'=>'width:100px'),'attribute'=>'style=""'));
        $table->table_set('columns',array("name"=>"amount","label"=>"Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"subtotal_amount","label"=>"Subtotal Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('class'=>'table-action')));
        $table->table_set('data',$data);
        
        $js = ''
            .'$("#product_table").find("tr input[original]").select2({data:[{id:1,text:"<strong>P/25</strong> Decolsin Syrup 60ml"}]});'
                .'$("#product_table").find("tr input[original]").select2("data",{id:1,text:"<strong>P/25</strong> Decolsin Syrup 60ml"});'
            .''
        ;

        $app->js_set($js);
        
        $detail_pane->input_add()->input_set('label', Lang::get('Grand Total Amount (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','40,430,000.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Outstanding Amount (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','40,430,000.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">INVOICED</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#purchase_receipt_tab', "value" => "Purchase Receipt", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'purchase_receipt_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'payment_type'=>'CASH','purchase_receipt_date'=>'2015-09-09','purchase_receipt_code'=>'<a href="">PI/1</a>','amount'=>'23,000,000.00','status'=>'<strong class="text-green">INVOICED</strong>'),
            array('id'=>1,'row_num'=>1,'payment_type'=>'DEBIT CARD','purchase_receipt_date'=>'2015-09-09','purchase_receipt_code'=>'<a href="">PI/2</a>','amount'=>'17,430,000.00','status'=>'<strong class="text-green">INVOICED</strong>'),
            
        );
        
        $detail_pane->form_group_add();
        $detail_pane->button_add()->button_set('class','primary')
                        ->button_set('value',Lang::get(array('New','Purchase Receipt')))
                        ->button_set('icon','fa fa-plus')
                        ->button_set('attrib',array(
                            'data-toggle'=>"modal" 
                            ,'data-target'=>"#modal_sales_receipt_allocation"
                        ))
                        ->button_set('disable_after_click',false)
                        ->button_set('id','sales_receipt_allocation_new')
                    ;
        $detail_pane->form_group_add()->attrib_set(array('style'=>'margin-bottom:20px'));
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','_sales_receipt_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"purchase_receipt_code","label"=>"Purchase Receipt Code",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"purchase_receipt_date","label"=>"Purchase Receipt Date",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"payment_type","label"=>"Payment Type",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"amount","label"=>"Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"status","label"=>"Status",'col_attrib'=>array('style'=>''),'attribute'=>'style=""'));
        $table->table_set('data',$data);
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#purchase_return_tab', "value" => "Return Merchandise Authorization", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'purchase_return_tab')->div_set('class', 'tab-pane');
        
        $data = array(
           
        );
        
        $detail_pane->form_group_add();
        $detail_pane->button_add()->button_set('class','primary')
                        ->button_set('value',Lang::get(array('New','RMA')))
                        ->button_set('icon','fa fa-plus')
                        ->button_set('attrib',array(
                            'data-toggle'=>"modal" 
                            ,'data-target'=>"#modal_sales_receipt_allocation"
                        ))
                        ->button_set('disable_after_click',false)
                        ->button_set('id','sales_receipt_allocation_new')
                    ;
        $detail_pane->form_group_add()->attrib_set(array('style'=>'margin-bottom:20px'));
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','_sales_receipt_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"purchase_receipt_code","label"=>"RMA Code",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"purchase_receipt_date","label"=>"RMA Date",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"status","label"=>"Status",'col_attrib'=>array('style'=>''),'attribute'=>'style=""'));
        $table->table_set('data',$data);
        
        //</editor-fold>
    }
    
    static function purchase_receipt_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'add');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Store')
            ->input_select_set('icon', APP_ICON::store())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_store')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'<strong>STO1</strong> Pucang Anom'))
            ->input_select_set('hide_all', true)
            ->input_select_set('allow_empty',false)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','[AUTO GENERATE]')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_select_detail_add()
            ->input_select_set('label',Lang::get('Reference'))
            ->input_select_set('icon',App_Icon::info())
            ->input_select_set('min_length','0')
            ->input_select_set('id','_reference')
            ->input_select_set('data_add',array())
            ->input_select_set('ajax_url','')
            ->input_select_set('allow_empty',false)
            ->input_select_set('value',array('id'=>'customer','text'=>'<strong>PI/STO1/1</strong>'))
            ->input_select_set('disable_all',false)
            ->detail_set('id','_reference_detail')
        ;
        
        $js = ''
            .'$("#_reference_detail ul li").prepend("'
                .'<div><strong>Type:</strong> Purchase Invoice </div>'
                .'<div><strong> Purchase Invoice Date:</strong> September 30, 2015 18:35</div>'
                .'<div><strong> Grand Total Amount (Rp.):</strong> 40,403,000.00</div>'
                .'<div><strong> Outstanding Amount (Rp.):</strong> 40,403,000.00</div>'
            .'") ;'
        .'';
        $app->js_set($js);
        
        $detail_pane->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('Purchase Receipt Date')))
                ->datetimepicker_set('id', '_purchase_receipt_date')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Payment Type')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_payment_type')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'TRANSFER'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Supplier Bank Account'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('value','BCA 123-325-325 Yessi')
                ->input_set('attrib', array())
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'BOS Bank Account')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_bos_bank_account')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'BCA 53213'))
            ->input_select_set('hide_all', true)
            ->input_select_set('allow_empty',false)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Amount (Rp.)'))
                ->input_set('icon', APP_ICON::money())
                ->input_set('hide_all', true)
                ->input_set('value','23,000,000.00')
                ->input_set('attrib', array())
        ;
        
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">INVOICED</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        
        
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">INVOICED</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        //</editor-fold>
    }
    
    static function rma_render_add($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'add');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Store')
            ->input_select_set('icon', APP_ICON::store())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_store')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'<strong>STO1</strong> Pucang Anom'))
            ->input_select_set('hide_all', true)
            ->input_select_set('allow_empty',false)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','[AUTO GENERATE]')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_select_detail_add()
            ->input_select_set('label',Lang::get('Reference'))
            ->input_select_set('icon',App_Icon::info())
            ->input_select_set('min_length','0')
            ->input_select_set('id','_reference')
            ->input_select_set('data_add',array())
            ->input_select_set('ajax_url','')
            ->input_select_set('allow_empty',false)
            ->input_select_set('value',array('id'=>'customer','text'=>'<strong>PI/STO1/1</strong>'))
            ->input_select_set('disable_all',false)
            ->detail_set('id','_reference_detail')
        ;
        
        $js = ''
            .'$("#_reference_detail ul li").prepend("'
                .'<div><strong>Type:</strong> Purchase Invoice </div>'
                .'<div><strong> Purchase Invoice Date:</strong> September 30, 2015 18:35</div>'
                .'<div><strong> Supplier:</strong> Apotek Mitra Fatma</div>'
                .'<div><strong> Grand Total Amount (Rp.):</strong> 40,403,000.00</div>'
            .'") ;'
        .'';
        $app->js_set($js);
        
        $detail_pane->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('RMA Date')))
                ->datetimepicker_set('id', '_purchase_invoice')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
        ;
        
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">RETURNED</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product'=>'<strong>P/1</strong> MIXAGRIP 4 KAPLET','expired_date'=>'January 24, 2015 11:32',
                'available_qty'=>'2.00','returned_qty'=>'<input class="form-control"value="1.00" style="text-align:right">',
                'unit'=>'PCS','amount'=>'23,000.00','subtotal_amount'=>'23,000.00','action'=>''
            ),
            array('id'=>2,'row_num'=>2,'product'=>'<strong>P/1</strong> MIXAGRIP 4 KAPLET','expired_date'=>'December 09, 2015 11:32',
                'available_qty'=>'5.00','returned_qty'=>'<input class="form-control"value="3.00" style="text-align:right">',
                'unit'=>'PCS','amount'=>'5,300.00','subtotal_amount'=>'15,900.00','action'=>''
            ),
            array('id'=>3,'row_num'=>3,'product'=>'<strong>P/25</strong> Decolsin Syrup 65ml','expired_date'=>'February 24, 2016 11:32',
                'available_qty'=>'0.00','returned_qty'=>'<input class="form-control"value="0.00" style="text-align:right">',
                'unit'=>'PCS','amount'=>'17,500.00','subtotal_amount'=>'0.00','action'=>''
            ),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_table');
        $table->table_set('class','table fixed-table sm-text');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"product","label"=>"Product",'col_attrib'=>array('style'=>'width:400px')));
        $table->table_set('columns',array("name"=>"expired_date","label"=>"Expired Date",'col_attrib'=>array('style'=>'width:175px')));
        $table->table_set('columns',array("name"=>"available_qty","label"=>"Available Qty",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"returned_qty","label"=>"Returned Qty",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"unit","label"=>"Unit",'col_attrib'=>array('style'=>'width:100px'),'attribute'=>'style=""'));
        //$table->table_set('columns',array("name"=>"amount","label"=>"Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        //$table->table_set('columns',array("name"=>"subtotal_amount","label"=>"Subtotal Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('class'=>'table-action')));
        $table->table_set('data',$data);
        
        $js = ''
            .'$("#product_table").find("tr input[original]").select2({data:[{id:1,text:"<strong>P/25</strong> Decolsin Syrup 60ml"}]});'
                .'$("#product_table").find("tr input[original]").select2("data",{id:1,text:"<strong>P/25</strong> Decolsin Syrup 60ml"});'
            .''
        ;

        $app->js_set($js);
        /*
        $detail_pane->input_add()->input_set('label', Lang::get('Grand Total Amount (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','38,900.00')
            ->input_set('attrib', array('style' => ''))
        ;
        */
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
                
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">RETURNED</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        
        
        //</editor-fold>
    }
    
    static function rma_render_view($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'view');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Store')
            ->input_select_set('icon', APP_ICON::store())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_store')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'<strong>STO1</strong> Pucang Anom'))
            ->input_select_set('hide_all', true)
            ->input_select_set('allow_empty',false)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','[AUTO GENERATE]')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_select_detail_add()
            ->input_select_set('label',Lang::get('Reference'))
            ->input_select_set('icon',App_Icon::info())
            ->input_select_set('min_length','0')
            ->input_select_set('id','_reference')
            ->input_select_set('data_add',array())
            ->input_select_set('ajax_url','')
            ->input_select_set('allow_empty',false)
            ->input_select_set('value',array('id'=>'customer','text'=>'<strong>PI/STO1/1</strong>'))
            ->input_select_set('disable_all',false)
            ->detail_set('id','_reference_detail')
        ;
        
        $js = ''
            .'$("#_reference_detail ul li").prepend("'
                .'<div><strong>Type:</strong> Purchase Invoice </div>'
                .'<div><strong> Purchase Invoice Date:</strong> September 30, 2015 18:35</div>'
                .'<div><strong> Supplier:</strong> Apotek Mitra Fatma</div>'
                .'<div><strong> Grand Total Amount (Rp.):</strong> 40,403,000.00</div>'
            .'") ;'
        .'';
        $app->js_set($js);
        
        $detail_pane->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('RMA Date')))
                ->datetimepicker_set('id', '_purchase_invoice')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
        ;
        
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">RECEIVED</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product'=>'<strong>P/1</strong> MIXAGRIP 4 KAPLET',
                'expired_date'=>'January 24, 2015 11:32',
                'new_expired_date'=>'<input class="form-control" value="March 24, 2016 11:32"/>',
                'available_qty'=>'2.00','returned_qty'=>'1.00',
                'unit'=>'PCS','amount'=>'23,000.00','subtotal_amount'=>'23,000.00','action'=>''
                
            ),
            array('id'=>2,'row_num'=>2,'product'=>'<strong>P/1</strong> MIXAGRIP 4 KAPLET',
                'expired_date'=>'December 09, 2015 11:32',
                'new_expired_date'=>'<input class="form-control" value="March 09, 2016 11:32"/>',
                'available_qty'=>'5.00','returned_qty'=>'3.00',
                'unit'=>'PCS','amount'=>'5,300.00','subtotal_amount'=>'15,900.00','action'=>''
            ),
            array('id'=>3,'row_num'=>3,'product'=>'<strong>P/25</strong> Decolsin Syrup 65ml',
                'expired_date'=>'February 24, 2016 11:32',
                'available_qty'=>'0.00','returned_qty'=>'0.00',
                'unit'=>'PCS','amount'=>'17,500.00','subtotal_amount'=>'0.00','action'=>''
            ),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_table');
        $table->table_set('class','table fixed-table sm-text');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"product","label"=>"Product",'col_attrib'=>array('style'=>'width:400px')));
        $table->table_set('columns',array("name"=>"expired_date","label"=>"Expired Date",'col_attrib'=>array('style'=>'width:175px')));
        $table->table_set('columns',array("name"=>"new_expired_date","label"=>"New Expired Date",'col_attrib'=>array('style'=>'width:175px')));
        $table->table_set('columns',array("name"=>"available_qty","label"=>"Available Qty",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"returned_qty","label"=>"Returned Qty",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"unit","label"=>"Unit",'col_attrib'=>array('style'=>'width:100px'),'attribute'=>'style=""'));
        //$table->table_set('columns',array("name"=>"amount","label"=>"Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        //$table->table_set('columns',array("name"=>"subtotal_amount","label"=>"Subtotal Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('class'=>'table-action')));
        $table->table_set('data',$data);
        
        $js = ''
            .'$("#product_table").find("tr input[original]").select2({data:[{id:1,text:"<strong>P/25</strong> Decolsin Syrup 60ml"}]});'
                .'$("#product_table").find("tr input[original]").select2("data",{id:1,text:"<strong>P/25</strong> Decolsin Syrup 60ml"});'
            .''
        ;

        $app->js_set($js);
        /*
        $detail_pane->input_add()->input_set('label', Lang::get('Grand Total Amount (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','38,900.00')
            ->input_set('attrib', array('style' => ''))
        ;
        */
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
                
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">RETURNED</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        
        
        //</editor-fold>
    }
    
    static function sales_invoice_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'add');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Store')
            ->input_select_set('icon', APP_ICON::store())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_store')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'<strong>STO1</strong> Pucang Anom'))
            ->input_select_set('hide_all', true)
            ->input_select_set('allow_empty',false)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','[AUTO GENERATE]')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('Sales Invoice Date')))
                ->datetimepicker_set('id', '_si_date')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
        ;
        
        $detail_pane->input_select_detail_add()
            ->input_select_set('label',Lang::get('Customer'))
            ->input_select_set('icon',App_Icon::supplier())
            ->input_select_set('min_length','0')
            ->input_select_set('id','_customer')
            ->input_select_set('data_add',array())
            ->input_select_set('ajax_url','')
            ->input_select_set('allow_empty',false)
            ->input_select_set('value',array('id'=>'customer','text'=>'<strong>CUST/1</strong> Johanes Edwin Prayoga'))
            ->input_select_set('disable_all',false)
            ->detail_set('id','_customer_detail')
            ->detail_set('button_new',true)
            ->detail_set('button_new_id','_btn_customer_new')
            ->detail_set('button_new_class','btn btn-primary btn-sm')
        ;
        
        $js = ''
            .'$("#_customer_detail ul li").prepend("'
                .'<div><strong>Customer:</strong> <a href=\"#\">Johanes Edwin Prayoga</a> </div>'
                .'<div><strong>Customer Type:</strong> General </div>'
                .'<div><strong>Address:</strong> Ngagel Jaya Utara </div>'
                .'<div><strong>Phone Number:</strong> 628113308009, 62315020393</div>'
                
            .'") ;'
        .'';
        $app->js_set($js);
        
        
        
        
        $detail_pane->input_add()->input_set('label', Lang::get('Supplier Sales Invoice Code'))
            ->input_set('icon', APP_ICON::info())
            ->input_set('hide_all', true)
            ->input_set('disable_all', false)
            ->input_set('value','8239123')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">INVOICED</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $data = array(
            array('id'=>1,'row_num'=>1,
                'product'=>'<strong>P/1</strong> MIXAGRIP 4 KAPLET',
                'qty'=>'3.00','unit'=>'PCS','amount'=>'25,000.00',
                'subtotal_amount'=>'75,000.00',
                'action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>',
            ),
            array('id'=>1,'row_num'=>1,
                'product'=>'<strong>P/23</strong> Vicks Formula 44 botol 27ml',
                'qty'=>'2.00','unit'=>'PCS','amount'=>'23,200.00',
                'subtotal_amount'=>'46,400.00',
                'action'=>'<button class="fa fa-trash-o text-red background-transparent no-border" style="cursor:pointer"></button>',
            ),
            array('id'=>3,'row_num'=>3,'product'=>'<input original>',
                'qty'=>'<input style="text-align:right" class="form-control" value="5.00">',
                'unit'=>'PCS',
                'amount'=>'17,500.00',
                'subtotal_amount'=>'87,500.00',
                'action'=>'<button class="fa fa-plus text-blue background-transparent no-border" style="cursor:pointer"></button>'
            ),
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_table');
        $table->table_set('class','table fixed-table sm-text');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"product","label"=>"Product",'col_attrib'=>array('style'=>'max-width:100%;min-width:400px')));
        $table->table_set('columns',array("name"=>"qty","label"=>"Qty",'col_attrib'=>array('style'=>'text-align:right;min-width:100px;width:100px'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"unit","label"=>"Unit",'col_attrib'=>array('style'=>'min-width:100px;width:100px'),'attribute'=>'style=""'));
        $table->table_set('columns',array("name"=>"amount","label"=>"Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right;min-width:150px;width:150px'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"subtotal_amount","label"=>"Subtotal Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right;min-width:150px;width:150px'),'attribute'=>'style="text-align:right"'));
        
        $table->table_set('columns',array("name"=>"action","label"=>"",'col_attrib'=>array('class'=>'table-action')));
        $table->table_set('data',$data);
        
        $js = ''
            .'$("#product_table").find("tr input[original]").select2({data:[{id:1,text:"<strong>P/25</strong> Decolsin Syrup 60ml"}]});'
                .'$("#product_table").find("tr input[original]").select2("data",{id:1,text:"<strong>P/25</strong> Decolsin Syrup 60ml"});'
            .''
        ;

        $app->js_set($js);
        
        $detail_pane->input_add()->input_set('label', Lang::get('Total Amount (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','208,900.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Discount Total Amount (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', false)
            ->input_set('value','8,900.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Grand Total Amount (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','200,000.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Outstanding Amount (Rp.)'))
            ->input_set('icon', APP_ICON::money())
            ->input_set('hide_all', true)
            ->input_set('disable_all', true)
            ->input_set('value','200,000.00')
            ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">INVOICED</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#purchase_receipt_tab', "value" => "Sales Receipt", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'purchase_receipt_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'payment_type'=>'CASH','purchase_receipt_date'=>'2015-09-09','purchase_receipt_code'=>'<a href="">SR/STO1/1</a>','amount'=>'23,000,000.00','status'=>'<strong class="text-green">INVOICED</strong>'),
        );
        
        $detail_pane->form_group_add();
        $detail_pane->button_add()->button_set('class','primary')
                        ->button_set('value',Lang::get(array('New','Sales Receipt')))
                        ->button_set('icon','fa fa-plus')
                        ->button_set('attrib',array(
                            'data-toggle'=>"modal" 
                            ,'data-target'=>"#modal_sales_receipt_allocation"
                        ))
                        ->button_set('disable_after_click',false)
                        ->button_set('id','sales_receipt_allocation_new')
                    ;
        $detail_pane->form_group_add()->attrib_set(array('style'=>'margin-bottom:20px'));
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','_sales_receipt_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"purchase_receipt_code","label"=>"Sales Receipt Code",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"purchase_receipt_date","label"=>"Sales Receipt Date",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"payment_type","label"=>"Payment Type",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"amount","label"=>"Amount (Rp.)",'col_attrib'=>array('style'=>'text-align:right'),'attribute'=>'style="text-align:right"'));
        $table->table_set('columns',array("name"=>"status","label"=>"Status",'col_attrib'=>array('style'=>''),'attribute'=>'style=""'));
        $table->table_set('data',$data);
        
        //</editor-fold>
    }
    
    static function sales_receipt_render($app,$nav_tab,$title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'add');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Store')
            ->input_select_set('icon', APP_ICON::store())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_store')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'<strong>STO1</strong> Pucang Anom'))
            ->input_select_set('hide_all', true)
            ->input_select_set('allow_empty',false)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('value','[AUTO GENERATE]')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_select_detail_add()
            ->input_select_set('label',Lang::get('Reference'))
            ->input_select_set('icon',App_Icon::info())
            ->input_select_set('min_length','0')
            ->input_select_set('id','_reference')
            ->input_select_set('data_add',array())
            ->input_select_set('ajax_url','')
            ->input_select_set('allow_empty',false)
            ->input_select_set('value',array('id'=>'customer','text'=>'<strong>SI/STO1/1</strong>'))
            ->input_select_set('disable_all',false)
            ->detail_set('id','_reference_detail')
        ;
        
        $js = ''
            .'$("#_reference_detail ul li").prepend("'
                .'<div><strong>Type:</strong> Sales Invoice </div>'
                .'<div><strong> Purchase Invoice Date:</strong> September 30, 2015 18:35</div>'
                .'<div><strong> Grand Total Amount (Rp.):</strong> 200,000.00</div>'
                .'<div><strong> Outstanding Amount (Rp.):</strong> 200,000.00</div>'
            .'") ;'
        .'';
        $app->js_set($js);
        
        $detail_pane->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('Sales Receipt Date')))
                ->datetimepicker_set('id', '_purchase_receipt_date')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Payment Type')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_payment_type')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'TRANSFER'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Supplier Bank Account'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('value','BCA 123-325-325 Yessi')
                ->input_set('attrib', array())
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'BOS Bank Account')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_bos_bank_account')
            ->input_select_set('data_add', array())
            ->input_select_set('value', array('id'=>'1','text'=>'BCA 53213'))
            ->input_select_set('hide_all', true)
            ->input_select_set('allow_empty',false)
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Amount (Rp.)'))
                ->input_set('icon', APP_ICON::money())
                ->input_set('hide_all', true)
                ->input_set('value','200,000.00')
                ->input_set('attrib', array())
        ;
        
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong style=\"color:green\">INVOICED</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        
        
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">INVOICED</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        
        //</editor-fold>
    }
    
    static function bos_bank_account_render($app, $nav_tab, $title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'add');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
                
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', false)
                ->input_set('value','BCA 1123')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Bank Account'))
                ->input_set('icon', APP_ICON::user())
                ->input_set('hide_all', true)
                ->input_set('disable_all', false)
                ->input_set('value','BCA 1123-52312-321')
                ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong class=\"text-green\">ACTIVE</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        //</editor-fold>
    }
    
    static function payment_type_render($app, $nav_tab, $title){
        //<editor-fold defaultstate="collapsed">
        $app->set_title($title);
        $app->set_breadcrumb($title, strtolower(str_replace(' ','_',$title)));
        $app->set_content_header($title, '', 'add');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#detail_tab', "value" => "Detail", 'class' => 'active'));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'detail_tab')->div_set('class', 'tab-pane active');
                
        $detail_pane->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', false)
                ->input_set('value','TRANSFER')
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $detail_pane->input_add()->input_set('label', Lang::get('Name'))
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', false)
                ->input_set('value','Transfer')
                ->input_set('attrib', array('style' => ''))
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Customer Bank Account')
            ->input_select_set('icon', APP_ICON::bank_account())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_customer_bank_account_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong class=\"text-green\">TRUE</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Supplier Bank Account')
            ->input_select_set('icon', APP_ICON::bank_account())
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_supplier_bank_account_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong class=\"text-green\">TRUE</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $detail_pane->input_select_add()
            ->input_select_set('label', 'Status')
            ->input_select_set('icon', 'fa fa-info')
            ->input_select_set('min_length', '0')
            ->input_select_set('id', '_contact_status')
            ->input_select_set('data_add', array(array('id'=>'active','text'=>SI::get_status_attr('ACTIVE'))))
            ->input_select_set('value', array('id'=>'active','text'=>'<strong class=\"text-green\">ACTIVE</strong>'))
            ->input_select_set('hide_all', true)
            ->input_select_set('is_module_status', true)
        ;
        
        $detail_pane->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id','_notes')
                ->textarea_set('value','')
            ;
       
        $detail_pane->hr_add();
        
        $detail_pane->button_add()->button_set('value', 'Submit')
                ->button_set('id', '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;
        
        $detail_pane->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('class', 'btn btn-default')
        ;
        
        $app->js_set('APP_COMPONENT.disable_all($("#detail_tab"));');
        
        $detail_tab = $nav_tab->nav_tab_set('items_add'
                , array("id" => '#product_status_log_tab', "value" => "Status Log", 'class' => ''));
        $detail_pane = $detail_tab->div_add()->div_set('id', 'product_status_log_tab')->div_set('class', 'tab-pane');
        
        $data = array(
            array('id'=>1,'row_num'=>1,'product_status'=>'<strong style="color:green">ACTIVE</strong>','moddate'=>'2015-09-05 11:00:00','name'=>'Johanes Edwin Prayoga'),
            
        );
        
        $table = $detail_pane->form_group_add()->table_add();
        $table->table_set('id','product_status_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Moddate",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"product_status","label"=>"Status",'col_attrib'=>array()));        
        $table->table_set('columns',array("name"=>"name","label"=>"Name",'col_attrib'=>array()));
        $table->table_set('data',$data);
        //</editor-fold>
    }
    
}