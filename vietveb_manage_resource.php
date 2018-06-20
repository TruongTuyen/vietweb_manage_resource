<?php
/*
Plugin Name: Vietveb Manage Resources
Plugin URI: https://vietveb.com/
Description: Vietveb Manage Resource
Author: Vietveb
Author URI: https://vietveb.com/about/
Version: 1.0.0
Text Domain: vietveb
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

define( 'VIETVEB_MANAGE_RESOURCE_URL', untrailingslashit( plugins_url(  '', __FILE__ ) ) );
define( 'VIETVEB_MANAGE_RESOURCE_PATH',dirname( __FILE__ ) );

class Vietveb_Manage_Resource{
    public function __construct(){
        add_action( 'init', array( &$this, 'register_post_type' ), 9999 );
        
    }
    
    public function register_post_type(){
        /*Post Type: Demo */
		$args = array(
			'labels'              => array(
				'name'               => __( 'Demo', 'vietveb_manage_resource' ),
				'singular_name'      => __( 'Demo item', 'vietveb_manage_resource' ),
				'add_new'            => __( 'Add new', 'vietveb_manage_resource' ),
				'add_new_item'       => __( 'Add new demo item', 'vietveb_manage_resource' ),
				'edit_item'          => __( 'Edit demo item', 'vietveb_manage_resource' ),
				'new_item'           => __( 'New demo item', 'vietveb_manage_resource' ),
				'view_item'          => __( 'View demo item', 'vietveb_manage_resource' ),
				'search_items'       => __( 'Search demo items', 'vietveb_manage_resource' ),
				'not_found'          => __( 'No demo items found', 'vietveb_manage_resource' ),
				'not_found_in_trash' => __( 'No demo items found in trash', 'vietveb_manage_resource' ),
				'parent_item_colon'  => __( 'Parent demo item:', 'vietveb_manage_resource' ),
				'menu_name'          => __( 'Vietveb Demo', 'vietveb_manage_resource' ),
			),
			'hierarchical'        => false,
			'description'         => __( 'Vietveb Demo.', 'vietveb_manage_resource' ),
			'supports'            => array( 'title' ),
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
				'name'               => __( 'Resource', 'vietveb_manage_resource' ),
				'singular_name'      => __( 'Resource item', 'vietveb_manage_resource' ),
				'add_new'            => __( 'Add new', 'vietveb_manage_resource' ),
				'add_new_item'       => __( 'Add new resource item', 'vietveb_manage_resource' ),
				'edit_item'          => __( 'Edit resource item', 'vietveb_manage_resource' ),
				'new_item'           => __( 'New resource item', 'vietveb_manage_resource' ),
				'view_item'          => __( 'View resource item', 'vietveb_manage_resource' ),
				'search_items'       => __( 'Search resource items', 'vietveb_manage_resource' ),
				'not_found'          => __( 'No resource items found', 'vietveb_manage_resource' ),
				'not_found_in_trash' => __( 'No resource items found in trash', 'vietveb_manage_resource' ),
				'parent_item_colon'  => __( 'Parent resource item:', 'vietveb_manage_resource' ),
				'menu_name'          => __( 'Vietveb resource', 'vietveb_manage_resource' ),
			),
			'hierarchical'        => false,
			'description'         => __( 'Vietveb Resource.', 'vietveb_manage_resource' ),
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

new Vietveb_Manage_Resource();