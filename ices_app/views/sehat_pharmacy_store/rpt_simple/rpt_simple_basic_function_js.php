<?php 
    $my_file_path = APPPATH.'views/ices/rpt_simple/rpt_simple_basic_function_js.php';
    $my_content = file_get_contents($my_file_path); 
    eval('?> '.$my_content.'');
?>
<script>
</script>