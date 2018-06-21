<?php
/*
Plugin Name: Vietveb Server
Plugin URI: https://vietveb.com/
Description: Vietveb Server Management
Author: Vietveb
Author URI: https://vietveb.com/about/
Version: 1.0.0
Text Domain: vietveb
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

define( 'VIETVEB_SERVER_URL', untrailingslashit( plugins_url(  '', __FILE__ ) ) );
define( 'VIETVEB_SERVER_PATH',dirname( __FILE__ ) );

if( class_exists( 'acf_field' ) ){
    require_once VIETVEB_SERVER_PATH . '/acf-custom-field-types/acf-vietveb-resource.php';
}

class Vietveb_Server_Side{
    public function __construct(){
        add_action( 'init', array( &$this, 'register_post_type' ), 9999 );
        
    }
    
    public function register_post_type(){
        /*Post Type: Demo */
		$args = array(
			'labels'              => array(
				'name'               => __( 'Demo', 'vietveb_server' ),
				'singular_name'      => __( 'Demo item', 'vietveb_server' ),
				'add_new'            => __( 'Add new', 'vietveb_server' ),
				'add_new_item'       => __( 'Add new demo item', 'vietveb_server' ),
				'edit_item'          => __( 'Edit demo item', 'vietveb_server' ),
				'new_item'           => __( 'New demo item', 'vietveb_server' ),
				'view_item'          => __( 'View demo item', 'vietveb_server' ),
				'search_items'       => __( 'Search demo items', 'vietveb_server' ),
				'not_found'          => __( 'No demo items found', 'vietveb_server' ),
				'not_found_in_trash' => __( 'No demo items found in trash', 'vietveb_server' ),
				'parent_item_colon'  => __( 'Parent demo item:', 'vietveb_server' ),
				'menu_name'          => __( 'Vietveb Demo', 'vietveb_server' ),
			),
			'hierarchical'        => false,
			'description'         => __( 'Vietveb Demo.', 'vietveb_server' ),
			'supports'            => array( 'title', 'thumbnail' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 7,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-welcome-widgets-menus',
		);
		register_post_type( 'vietweb_demo', $args );
        
        /*Post Type: Resource */
		$args = array(
			'labels'              => array(
				'name'               => __( 'Resource', 'vietveb_server' ),
				'singular_name'      => __( 'Resource item', 'vietveb_server' ),
				'add_new'            => __( 'Add new', 'vietveb_server' ),
				'add_new_item'       => __( 'Add new resource item', 'vietveb_server' ),
				'edit_item'          => __( 'Edit resource item', 'vietveb_server' ),
				'new_item'           => __( 'New resource item', 'vietveb_server' ),
				'view_item'          => __( 'View resource item', 'vietveb_server' ),
				'search_items'       => __( 'Search resource items', 'vietveb_server' ),
				'not_found'          => __( 'No resource items found', 'vietveb_server' ),
				'not_found_in_trash' => __( 'No resource items found in trash', 'vietveb_server' ),
				'parent_item_colon'  => __( 'Parent resource item:', 'vietveb_server' ),
				'menu_name'          => __( 'Vietveb Resource', 'vietveb_server' ),
			),
			'hierarchical'        => false,
			'description'         => __( 'Vietveb Resource.', 'vietveb_server' ),
			'supports'            => array( 'title' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 8,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-feedback',
		);
		register_post_type( 'vietweb_resource', $args );
    }
}

new Vietveb_Server_Side();

