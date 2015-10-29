<script>
var rpt_purchase_purchase_invoice_param_get = function(){
    var lparent_pane = "<?php echo $detail_tab; ?>";
    var lprefix_id = "#<?php echo $component_prefix_id; ?>";
    var lresult = {};
    lresult.start_date = new Date($(lparent_pane).find(lprefix_id+'_start_date').val()).format('Y-m-d H:i:s');
    lresult.end_date = new Date($(lparent_pane).find(lprefix_id+'_end_date').val()).format('Y-m-d H:i:s');
    lresult.purchase_invoice_status = $(lparent_pane).find(lprefix_id+'_purchase_invoice_status').select2('val');
    lresult.supplier_id = $(lparent_pane).find(lprefix_id+'_supplier').select2('val');
    return lresult;
};


</script>