<?php

/**
 * Makes it easy to use var_dump with error_log
 * @param object 
 */
function var_error_log( $object = null ) {
    ob_start();                    
    var_dump( $object );           
    $contents = ob_get_contents(); 
    ob_end_clean();                
    error_log( $contents );        
    ini_set('html_errors', 1);
}

