<?php 
    $my_file_path = APPPATH.'views/ices/dashboard/dashboard_js.php';
    $my_content = file_get_contents($my_file_path); 
    eval('?> '.$my_content.'');
?>
<script>
</script>