<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Exceptions extends CI_Exceptions{
    
    public function __construct() {
        parent::__construct();

    }
    
    function show_php_error($severity, $message, $filepath, $line)
    {
        $severity = ( ! isset($this->levels[$severity])) ? $severity : $this->levels[$severity];

        $filepath = str_replace("\\", "/", $filepath);
        
        $post = array();
        
        if(!empty($_POST)){
            $post = $_POST;
        }
        else{
            $post = file_get_contents('php://input');
            if (strlen($post)>0){
                $post = base64_decode($post);
                if(json_decode($post)!=null){                        
                    $post = json_decode($post,TRUE);
                }
            }
        }
        
        
        $ajax_post = false;
        if(isset($post['ajax_post'])) $ajax_post = ($post['ajax_post'] === true?true:false);

        // For safety reasons we do not show the full file path
        if (FALSE !== strpos($filepath, '/'))
        {
                $x = explode('/', $filepath);
                $filepath = $x[count($x)-2].'/'.end($x);
        }

        if (ob_get_level() > $this->ob_level + 1)
        {
                ob_end_flush();
        }
        ob_start();
        include(APPPATH.'errors/error_php.php');
        $buffer = ob_get_contents();
        ob_end_clean();


        if($ajax_post){
            $result = array('success'=>0,
                'msg'=>array('Severity: '.$severity,
                    'Path: '.$filepath,
                    'Line: '.$line,$message
                ),
                'response'=>''
            );
            echo json_encode($result);
        }
        else{
            echo $buffer;
        }
    }
    
}