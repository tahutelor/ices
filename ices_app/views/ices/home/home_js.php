<script>
    <?php $company = $company = ICES_Engine::$company['val']; ?>
    $(document).ready(function(){
        var show_user_profile = function(iUser_Info){

            if(iUser_Info.user_id !== ''){
                $('[fullname]').html(iUser_Info.name);
                $('li.dropdown.user.user-menu').show();
            }
        }
        
        var ices_data_support = "<?php echo get_instance()->config->base_url().'ices/home/data_support';?>";
        var sign_in_url = "<?php echo get_instance()->config->base_url().'ices/sign_in/';?>";
        
        $('#modal_sign_in_btn_close').on('click',function(e){
            e.preventDefault();
           $('#modal_sign_in').modal('hide'); 
        });
        
        $('#modal_sign_in button').off('click');
        $('#modal_sign_in button').on('click',function(e){
            e.preventDefault();
            var lajax_url = sign_in_url+'';
            var ljson_data = {app_name:$(this).attr('app_name'),username:$('input[name="username"]').val(),password:$('input[name="password"]').val()};
            var lresult = ICES_DATA_TRANSFER.ajaxPOST(lajax_url,ljson_data);
            $('#login_msg')[0].innerHTML = lresult.msg;
            if(lresult.response.username_pwd_match === 1){
                show_user_profile(lresult.response.user_info);
                
                if(lresult.success === 1){
                    window.location = lresult.response.app_url;
                }
            }
        });
        
        var lib_root = '<?php echo $this->config->base_url()."libraries/" ?>';
        var img_link_style = 'float:left;width:25px;height:25px';
        var lbase_url = '<?php echo $this->config->base_url() ?>';
        var lapp_to_show = 5;
        
        var lapp_list = [
            <?php foreach (ICES_Engine::$app_list as $idx=>$row){?>
                {html:
                    '<div class="item" style="display:inline-block;padding:0px 5px 0px 5px">'
                        +'<div class="box" style="">'
                            +'<div><a open_app app_name="<?php echo $row['val']?>" href=""><img src="'+lib_root+'img/ices/<?php echo $row['val']; ?>.png" style="'+img_link_style+'"/></a></div>'
                            +'<h2 style="margin-bottom:15px"><a style="margin-left:5px;" open_app app_name="<?php echo $row['val']?>" href="'+lbase_url+'<?php echo $row['val']?>/"><?php echo $row['short_name']?></a></h2>' 
                            +'<p style="margin-bottom:3px"><?php echo $row['short_name']?><br/><?php echo $row['app_info']; ?>'
                            +'</p>'
                        +'</div>'
                    +'</div>'
                },
            <?php }?>
            
        ];
    
        var lapp_to_show = 5;
        var lcomp_width = 230;
    
        var home_draw_application = function(lapp_arr){
            $.each(lapp_arr, function(lidx, lrow){
                var lcomp = $(lapp_list[lrow].html);
                $(lcomp).addClass('active');
                $(lcomp).attr('idx',lrow);
                $(lcomp).find('div.box').width(lcomp_width-10);
                $('.carousel-inner').append($(lcomp));
            });
            
            $('a[open_app]').on('click',function(e){
                e.preventDefault();
            });
            $('a[open_app]').on('click',function(e){
                e.preventDefault();
                var lajax_url = ices_data_support+'/is_auth';
                var ljson_data = {app_name:$(this).attr('app_name')};
                var lresponse = ICES_DATA_TRANSFER.ajaxPOST(lajax_url,ljson_data).response;
                if(!lresponse.is_auth){
                    $('#modal_sign_in').modal('show');
                    $('#modal_sign_in button').attr('app_name',$(this).attr('app_name'));
                    var lmargin_top = 100 ;
                    $("#modal_sign_in .modal-dialog").css('margin-top',lmargin_top+'px');

                }
                else{
                    window.location = lresponse.app_url;
                }
            });
        }
        
        $('#right_control').off('click');
        $('#right_control').on('click',function(e){
            e.preventDefault();
            var llast_div = $('.carousel-inner>div').last();
            var lapp_idx = parseInt($(llast_div).attr('idx'));
            
            if(lapp_idx  === lapp_list.length-1){
                lapp_idx = 0;
            }
            else{
                lapp_idx+=1;
            }
            
            var lapp_res = [];
            for(var i = 0;i<lapp_to_show;i++){
                if(i<=lapp_list.length-1){
                    lapp_res.unshift(lapp_idx);
                    lapp_idx-=1;
                    if(lapp_idx < 0)lapp_idx = lapp_list.length-1;
                    
                }
            }
            
            $('.carousel-inner').empty();
            home_draw_application(lapp_res);
            
            var ldiv = $('.carousel-inner>div');
            $.each(ldiv,function(lidx, lrow){
                $(lrow).css('left','0');
               
            });
            
            $.each(ldiv,function(lidx, lrow){
                $(lrow).animate({
                    left:'-'+lcomp_width.toString()+'px'
                  }, 100
                );
               
            });
             
            
        });
        
        $('#left_control').off('click');
        $('#left_control').on('click',function(e){
            e.preventDefault();
            var lfirst_div = $('.carousel-inner>div').eq(0);
            var lapp_idx = parseInt($(lfirst_div).attr('idx'));
            
            if(lapp_idx === 0){
                lapp_idx = lapp_list.length-1;
            }
            else{
                lapp_idx-=1;
            }
            
            var lapp_res = [];
            for(var i = 0;i<lapp_to_show;i++){
                if(i<=lapp_list.length-1){
                    lapp_res.push(lapp_idx);
                    lapp_idx+=1;
                    if(lapp_idx>lapp_list.length-1)lapp_idx = 0;
                }
            }
            $('.carousel-inner').empty();
            home_draw_application(lapp_res);
            
            var ldiv = $('.carousel-inner>div');
            $.each(ldiv,function(lidx, lrow){
                $(lrow).css('left','-'+(lcomp_width*2).toString()+'px');
               
            });
            
            $.each(ldiv,function(lidx, lrow){
                $(lrow).animate({
                    left:'-'+lcomp_width+'px'
                  }, 100
                );
               
            });
            
        });
        
        $('.mcontent>div').show();
        $('#left_control').hide();
        $('#right_control').hide();
        var home_init = function(){
            
            if($(window).width()<767 ){
                lapp_to_show = 3;
                lcomp_width = $('.mcontent').width()-60;
                var lcarousel_slide_width = $('.mcontent').width()-60;
                $('.carousel.slide').width(lcarousel_slide_width);
            }
            else{
                lapp_to_show = 5;
                lcomp_width = 230;
                var lcarousel_slide_width = $('.mcontent').width()-60;
                $('.carousel.slide').width(lcarousel_slide_width);
            }
            
            
            
            if(lapp_list.length>=lapp_to_show){
                var ltemp = [];
                for(i = 0;i<lapp_to_show;i++){
                    if(i === 0) ltemp.push(lapp_list.length-1);
                    else ltemp.push(i-1);
                    
                }
                
                home_draw_application(ltemp);
                var ldiv = $('.carousel-inner>div');
                $.each(ldiv,function(lidx, lrow){
                    $(lrow).css('left','-'+lcomp_width.toString()+'px');
                });
                $('#left_control').show();
                $('#right_control').show();
            }else{
                var ltemp = [];
                $.each(lapp_list,function(lidx, lrow){
                    ltemp.push(lidx);
                });
                home_draw_application(ltemp);

                var ldiv = $('.carousel-inner>div');
                $.each(ldiv,function(lidx, lrow){
                    $(lrow).css('float','left');
                });

            }
        }
        home_init();
        $(window).resize(function(){
            home_init();
        });
        show_user_profile(JSON.parse('<?php echo json_encode(User_Info::get());?>'));
        
        
    });
    
</script>