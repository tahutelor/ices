<?php $lib_root = $this->config->base_url()."libraries/"; 
    $img_link_style = 'float:left;width:25px;height:25px';
    $ices_base_url = $this->config->base_url().'ices/';
    $company = ICES_Engine::$company['val'];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex,nofollow">
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport' >
<?php if($company === 'aryana')echo '<title>Integrated Civil Engineering System</title>';
    else echo '<title>Integrated & Computerized Enterprise System</title>'; ?>
<link rel="icon" href="<?php echo $lib_root ?>img/ices/system.png">
<link href="<?php echo $lib_root ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $lib_root ?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $lib_root; ?>css/adminLTE/adminLTE.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $lib_root; ?>css/adminLTE/adminLTE_ext.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $lib_root; ?>css/ices/style.css" rel="stylesheet" type="text/css" />


</head>
<body >
    <section>
    <div class="container_12" id="content">
        <div style="position:absolute;top:0px;right:0px;height:50px;width:285px">
            <div class="navbar-right" >
                <ul style="margin-top:25px;margin-right:25px;">
                    <li class="dropdown user user-menu" style="display:none;text-align:right">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#ffffff;font-size:16px">
                            <i class="glyphicon glyphicon-user"></i>
                            <span fullname></span><i class="caret"></i>
                        </a>
                        <ul class="dropdown-menu" style="width:280px;padding: 1px 0 0 0;;
                        border-top-width: 0;">
                            <li class="" style="max-height:150px;padding: 10px;
                            background: #6aa3c0;
                            text-align: center;">
                                <img style="border-radius: 50%;width: 90px;
                                border: 8px solid;border-color: rgba(255, 255, 255, 0.2);"src="<?php echo get_instance()->config->base_url();?>/libraries/img/avatar.png" class="" alt="User Image">
                                    <p style="color: rgba(255, 255, 255, 1);
                                    font-size: 17px;
                                    text-shadow: 2px 2px 3px #333333;
                                    margin-top: 10px;" fullname>                                                              
                                    </p>
                            </li>
                            <li class="" style="background-color: #f9f9f9;
                            padding: 10px;height:50px">                            
                                <div class="pull-left">
                                    <a href="<?php echo $ices_base_url.'u_profile/index/';?>" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo $ices_base_url.'sign_in/sign_out'; ?>" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <h1 style="font-family: Adobe Caslon Pro;font-weight:normal;">
            <?php if($company === 'aryana'){echo 'Integrated Civil Engineering System  (ICES)';}
                else {echo 'Integrated & Computerized Enterprise System  (ICES)';} 
            ?>
        </h1>
        <h3>fast, precise, reliable </h3>
        <div class="main" style="margin-top:80px">
                <div class="mcontent">
                    <div style="float:left;width:30px;height:30px;margin-top:50px;display:none">
                    <a id = 'left_control' href="#" style=''><div style='position:relative;top:-12px;'>‹</div></a>
                    </div>
                    <div class='carousel slide' style='float:left;width:690px'>
                        <div class="carousel-inner" role="listbox" style="white-space:nowrap">
                        </div>
                        
                    </div>
                    <div style="float:left;width:30px;height:30px;margin-top:50px;display:none">
                    <a id = 'right_control' href="#" style=''><div style='position:relative;top:-12px;'>›</div></a>
                    </div>
                </div>
        </div>
    </div>

    <div class="modal fade" id="modal_sign_in" tabindex="" role="dialog" aria-hidden="false" style="display: none;overflow-y:auto">
        <div class="modal-dialog">
        <div class="modal-content" style="">


        <div class="modal-body" style="background-color:#6aa3c0;padding:1px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            border-bottom-left-radius: 4px;"
        >
            <div class="" id="login-box" style='margin:0 auto 0 auto'>
                <div class="" style="border-top-left-radius: 4px;
                        border-top-right-radius: 4px;
                        border-bottom-right-radius: 0;
                        border-bottom-left-radius: 0;

                        background: #3d9970;
                        box-shadow: inset 0px -3px 0px rgba(0, 0, 0, 0.2);
                        padding: 20px 10px;
                        text-align: center;
                        font-size: 26px;
                        font-weight: 300;
                        color: #fff;
                        background-color:#3c8dbc">
                    SIGN IN
                </div>
                <form action="" method="post">
                    <div class="" 
                         style="padding: 10px 20px;
                            background: #fff;
                            color: #444;background-color: #eaeaec !important;"
                    >
                        <div class="form-group">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user fa-lg "></i>
                            </span>
                            <input type="text" name="username" class="form-control" placeholder="Username"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-key fa-lg"></i>
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="Password"/>
                            </div>
                        </div>          
                        <div class="form-group">
                            <strong id="login_msg" style="color:#f56954 " 
                                    class=""></strong>
                        </div>
                    </div>
                    <div class="" style="border-top-left-radius: 0;
                    border-top-right-radius: 0;
                    border-bottom-right-radius: 4px;
                    border-bottom-left-radius: 4px;padding: 10px 20px;
                    background: #fff;
                    color: #444;">                                                               
                        <button type="submit" class="btn btn-primary btn-block" style="
                                margin-bottom: 10px;background-color: #3c8dbc;
        border-color: #6aa3c0;">Let me in</button>  
                    </div>
                </form>            
            </div>
        </div>

        </div>
        </div>
    </div>
        </section>
    
    <div style="">
    <p style="text-align:center;color:#2a6888">
        <?php if($company === 'aryana')echo 'PT. Aryana Cakasana - 2015'; 
            else echo 'ICES - 2015';
        ?>
    </p>
    </div>
    </body>


<script type="text/javascript" src="<?php echo $lib_root; ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $lib_root; ?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $lib_root; ?>js/jquery.actual.min.js"></script>
<script src="<?php echo $lib_root ?>js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $lib_root ?>js/ices/ices.js" type="text/javascript"></script>
<script src="<?php echo $lib_root ?>js/AdminLTE/pace.js" type="text/javascript"></script>

</html>
