<?php $lib_root = get_instance()->config->base_url()."libraries/"; 

?>
<head>
    <meta content-type="text/html;" charset="UTF-8">
    <title><?php echo ICES_Engine::$app['short_name'].' - '.$title; ?></title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport' >
    <link rel="icon" href="<?php echo ICES_Engine::$app['app_icon_img']; ?>">
    <link href="<?php echo $lib_root ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $lib_root ?>css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $lib_root ?>css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $lib_root ?>css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $lib_root ?>css/iCheck/all.css" rel="stylesheet" type="text/css" />
    
    <?php foreach($library as $idx=>$row){
        if($row['type'] === 'css'){
            $path = $lib_root.'css/'.$row['val'];
            echo '<link href = "'.$path.'" rel="stylesheet" type="text/css"/>';
        }
    }?>
    <link href="<?php echo $lib_root ?>css/select2/select2.css" rel="stylesheet" type="text/css" />
    <!-- <link href="<?php echo $lib_root ?>css/datepicker/datepicker3.css" rel="stylesheet" type="text/css" /> -->
    <!-- <link href="<?php echo $lib_root ?>css/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" /> -->
    <link href="<?php echo $lib_root ?>css/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $lib_root ?>css/AdminLTE/AdminLTE.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $lib_root ?>css/AdminLTE/AdminLTE_ext.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="<?php echo ICES_Engine::$app['app_icon_img']; ?>">

</head>
    