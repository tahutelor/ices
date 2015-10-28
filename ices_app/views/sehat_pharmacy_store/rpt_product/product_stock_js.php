<script>
var rpt_product_product_stock_param_get = function(){
    var lparent_pane = "<?php echo $detail_tab; ?>";
    var lprefix_id = "#<?php echo $component_prefix_id; ?>";
    var lresult = {};
    lresult.keyword = $(lparent_pane).find(lprefix_id+'_keyword').val();
    lresult.warehouse_id = $(lparent_pane).find(lprefix_id+'_warehouse').select2('val');
    lresult.product_status = $(lparent_pane).find(lprefix_id+'_product_status').select2('val');
    lresult.product_batch_expired = $(lparent_pane).find(lprefix_id+'_product_batch_expired').select2('val');
    return lresult;
};


</script>