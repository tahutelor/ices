<?php 
    $data['input_selector_id']=$input_selector_id;
    $data['raw_data_id'] = 'raw_data_'.$id;
    $data['format_id'] = 'format_'.$id;
    $data['ajax_url'] = $ajax_url;  
    $data['raw_data'] = $raw_data;
    $data['min_length'] = $min_length;
    $data['value']=$value;
    $data['allow_empty'] = $allow_empty;
    $data=json_decode(json_encode($data));
?>
<script>
    
    function <?php echo $data->format_id; ?>(result,container, query, escapeMarkup) {
        var markup=[];
        window.Select2.util.markMatch(result.text, query.term, markup, escapeMarkup);
        return markup.join("");
    }
    var <?php echo $data->raw_data_id; ?> = [<?php echo $data->raw_data; ?>];
    
    var <?php echo $data->input_selector_id; ?>_extra_param_get=function(){
        return {};
    }
    
    var <?php echo $data->input_selector_id; ?>_timeout;

    
    $("#<?php echo $data->input_selector_id; ?>").select2({        
        minimumInputLength:<?php echo $data->min_length?>, 
        allowClear:<?php if($data->allow_empty) echo 'true'; else echo 'false'; ?>,
        quietMillis: 100,
        formatResult:<?php echo $data->format_id; ?>,
        <?php if(strlen($data->ajax_url)>0){?>
        query:function(query){
            window.clearTimeout(<?php echo $data->input_selector_id; ?>_timeout);
            <?php echo $data->input_selector_id; ?>_timeout = window.setTimeout(function(){    
                
                var typed_word = query.term.toLowerCase().trim();
                if(typed_word.replace(/[' ']/g,'') == '') typed_word = '';
                if(typed_word[0] == ' '){typed_word=typed_word.substr(1,typed_word.length-1);}
                var data={results:[]};
                var json_data = {data:typed_word,extra_param:<?php echo $data->input_selector_id; ?>_extra_param_get()}; 
                
                    var url = "<?php echo $data->ajax_url; ?>";            
                    var result = APP_DATA_TRANSFER.ajaxPOST(url,json_data);
                <?php ?>
                
                var <?php echo $data->raw_data_id ?> = null;
                if(typeof result.response !== 'undefined'){
                    <?php echo $data->raw_data_id ?> = result.response;
                }
                else{
                    <?php echo $data->raw_data_id ?> = result;
                }
                
                data.results = <?php echo $data->raw_data_id ?>;
                /*
                for (var i = 0; i < <?php echo $data->raw_data_id?>.length ;i++ ){
                    var item  = <?php echo $data->raw_data_id?>[i];
                    if(item.text.toLowerCase().indexOf(typed_word)!= -1){
                        data.results.push(item);

                    }
                }
                */
                $('#<?php echo $data->input_selector_id; ?>').attr('select2_data_list',btoa(JSON.stringify(data.results)));
                                
                query.callback(data);
            },250);
        },
        <?php }else if(count($data->raw_data)>0){ ?>
        data:[<?php echo $data->raw_data ?>],
        <?php } ?>
    });
    

    <?php if( count($data->value)>0){ ?>
        $("#<?php   echo $data->input_selector_id; ?>").select2("data"
            ,JSON.parse('<?php  echo json_encode($data->value); ?>')
        );
        
    <?php } ?>
    
    $('#<?php echo $data->input_selector_id; ?>').attr('select2_data_list',btoa(JSON.stringify(<?php echo $data->raw_data_id; ?>)));
</script>