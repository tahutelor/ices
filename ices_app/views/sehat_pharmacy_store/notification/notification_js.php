<?php 
    $my_file_path = APPPATH.'views/ices/notification/notification_js.php';
    $my_content = file_get_contents($my_file_path); 
    eval('?> '.$my_content.'');
?>
<script>
    notification.notification_list= [
        {controller:"purchase_invoice",method:"notification_outstanding_grand_total_amount_get"},
        {controller:"sales_invoice",method:"notification_outstanding_grand_total_amount_get"},
        {controller:"product_batch",method:"notification_nearly_expired_get"}
    ]
</script>