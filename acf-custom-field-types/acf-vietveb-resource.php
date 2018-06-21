<?php
function vietveb_include_field_types_posttype_select($version){
    include_once( VIETVEB_SERVER_PATH . '/acf-custom-field-types/fields/acf-resource-plugin-v5.php');
    include_once( VIETVEB_SERVER_PATH . '/acf-custom-field-types/fields/acf-resource-theme-v5.php');
}

add_action('acf/include_field_types', 'vietveb_include_field_types_posttype_select');

function vietveb_register_fields_posttype_select(){
    include_once( VIETVEB_SERVER_PATH . '/acf-custom-field-types/fields/acf-resource-plugin-v4.php');
    include_once( VIETVEB_SERVER_PATH . '/acf-custom-field-types/fields/acf-resource-theme-v4.php');
}

add_action('acf/register_fields', 'vietveb_register_fields_posttype_select');

/** Change file location to other directory **/

add_filter('upload_dir', 'vietveb_resource_custom_uploaded_dir');
function vietveb_resource_custom_uploaded_dir( $uploads ) {
    if( isset( $_POST['post_id'] ) && $_POST['post_id'] != '' && isset( $_POST['action'] ) && $_POST['action'] == 'upload-attachment' ){
        $parent = get_post( $_POST['post_id'] )->post_parent;
      
        if( ("vietweb_resource" == get_post_type( $_POST['post_id'] ) || "vietweb_resource" == get_post_type( $parent )) && isset( $_FILES['async-upload']['type'] ) && 'application/x-zip-compressed' == $_FILES['async-upload']['type'] ){
            $uploads['path'] = $uploads['basedir'].'/vietveb_resource';
            $uploads['url'] = $uploads['baseurl'].'/vietveb_resource';
            $uploads['subdir'] = '/vietveb_resource';
        }
    }
  
    return $uploads;
}
