<?php
$root_dir = dirname( dirname( dirname( dirname(__FILE__) ) ) );
require_once $root_dir .'/wp-load.php';

$license_key  = (isset( $_GET['license'] ) && $_GET['license'] != '' ) ? $_GET['license'] : '';
$license_site = (isset( $_GET['url'] ) && $_GET['url'] != '' ) ? $_GET['url'] : '';

$error = array();
if( function_exists( 'edd_software_licensing' ) && $license_key != '' && $license_site != '' ){
    $license = edd_software_licensing()->get_license( $license_key, true );
    if( isset( $license->status ) && $license->status == 'active' ){
        if( $license->is_site_active( $license_site ) ){
            $file_name = dirname(dirname(dirname(__FILE__))) . '/uploads/' .  $_GET['file'];
            
            if( is_file( $file_name ) ){
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=" . basename($file_name));
                header("Content-Length: ".filesize($file_name));
                header("Content-Type: application/force-download");
                header("Content-Transfer-Encoding: binary");
                readfile($file_name);
                exit;
            }else{
                $error[] = 'The file which you find is not exists';
            }
        }else{
            $error[] = 'License not valid on this site';
        }
    }else{
        $error[] = 'License is not active';
    }
}else{
    $error[] = 'Your license can not be check';
}

if( !empty( $error ) ){
    exit;  
}