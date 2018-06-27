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
        add_action( 'init', array( &$this, 'register_post_type' ), 100 );
        add_action( 'init', array( &$this, 'register_taxonomy' ), 200 );
        add_action( 'rest_api_init', array( &$this, 'add_api_end_point' ) );
        
        add_filter( 'upload_mimes', array( $this, 'add_myme_types' ), 1);
    }
    
    public function add_myme_types( $mime_types ){
        if( !isset( $mime_types['json'] ) ){
            $mime_types['json'] = 'application/json';
        }
        if( !isset( $mime_types['xml'] ) ){
            $mime_types['xml'] = 'application/xml';
        }
        return $mime_types;
    }
    
    public function add_api_end_point(){
        register_rest_route( 'wp/v2', '/sites', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_sites' ),
        ) );
    }
    
    public function get_sites( $request ){
        $paged = absint( $request->get_param( 'paged' ) );
        $cat = sanitize_text_field( $request->get_param( 'cat' ) );
        $tag = sanitize_text_field( $request->get_param( 'tag' ) );
        $search = sanitize_text_field( $request->get_param( 's' ) );
        $per_page = sanitize_text_field( $request->get_param( 'per_page' ) );
        
        $per_page = absint( $per_page );
        if ( $per_page <= 0  || $per_page > 100 ) {
            $per_page = 100;
        }
        
        
        $query_arg = array(
            'posts_per_page' => $per_page,
            'paged' => $paged,
            'post_parent' => 0,
            'post_type' => 'vietweb_demo',
            's' => $search
        );
        
        $tax_query = array();
        
        if( $cat != '' ){
            $tax_query[] = array(
                'taxonomy' => 'vietveb_demo_cat',
    			'field'    => 'slug',
    			'terms'    => $cat,
            );
        }
        
        if( $tag != '' ){
            $tax_query[] = array(
                'taxonomy' => 'vietveb_demo_tag',
    			'field'    => 'slug',
    			'terms'    => $tag,
            );
        }
        
        $query_arg['tax_query'] = $tax_query;
        
        $the_query = new WP_Query( apply_filters( 'vietveb_server_api_query_arg', $query_arg ) );
        $_posts = $the_query->get_posts();
        
        
        $posts =  array();
        foreach( $_posts as $p ) {
            $thumbnail_url=  get_the_post_thumbnail_url( $p );
            $demo_tags =  get_the_terms( $p->ID, 'vietveb_demo_tag' );
            
            $vietveb_demo_tag = array();
            if( !is_wp_error( $demo_tags ) ){
                if( is_array( $demo_tags ) ){
                    foreach( $demo_tags as $demo_tag ){
                        if( isset( $demo_tag->slug ) && $demo_tag->slug != '' ){
                            $vietveb_demo_tag[] = $demo_tag->slug;
                        }
                    }
                }
            }
            
            
            
            $demo_cats =  get_the_terms( $p->ID, 'vietveb_demo_cat' );
            $vietveb_demo_cat = array();
            if( !is_wp_error( $demo_cats ) ){
                if( is_array( $demo_cats ) ){
                    foreach( $demo_cats as $demo_cat ){
                        if( isset( $demo_cat->slug ) && $demo_cat->slug != '' ){
                            $vietveb_demo_cat[] = $demo_cat->slug;
                        }
                    }
                }
            }
            
            $list_theme = $list_plugin = array();
            $recommended_themes = get_field( '_vietveb_demo_recommended_themes', $p->ID );
            $recommended_plugins = get_field( '_vietveb_demo_recommened_plugins', $p->ID );
            
          
            if( is_numeric( $recommended_themes ) ){
                
                $locate = get_field( '_vietveb_resource_locate', $recommended_themes );
                $slug = get_field( '_vietveb_resource_slug', $recommended_themes );
                
                $theme_data = array(
                    'slug'   => $slug,
                    'locate' => $locate,
                    'name'   => get_the_title( $recommended_themes )
                );
                
                if( $locate == 'self-hosted' ){
                    $theme_data['version'] = get_field( '_vietveb_resource_version', $recommended_themes );
                    $theme_data['link_package'] = get_field( '_vietveb_resource_link_package', $recommended_themes );
                }
                $list_theme = $theme_data; 
                
            }
            
            if( is_array( $recommended_plugins ) && !empty( $recommended_plugins ) ){
                foreach( $recommended_plugins as $r_plugin ){
                    $locate = get_field( '_vietveb_resource_locate', $r_plugin );
                    $slug = get_field( '_vietveb_resource_slug', $r_plugin );
                    
                    $plugin_data = array(
                        'slug'   => $slug,
                        'locate' => $locate,
                        'name'   => get_the_title( $r_plugin )
                    );
                    
                    if( $locate == 'self-hosted' ){
                        $plugin_data['version'] = get_field( '_vietveb_resource_version', $r_plugin );
                        $plugin_data['link_package'] = get_field( '_vietveb_resource_link_package', $r_plugin );
                    }
                    $list_plugin[] = $plugin_data; 
                }
            }
            
            $posts[ $p->post_name ] = array(
                'id' => $p->ID,
                'title' => $p->post_title,
                'slug' => $p->post_name,
                'desc' => $p->post_content,
                'thumbnail_url' => $thumbnail_url,
                'demo_url' => get_field( '_vietveb_demo_preview_link', $p->ID ),
                'themes_plugins' => array(
                    'themes'  => $list_theme,
                    'plugins' => $list_plugin,
                ),
                'resources' => array(
                    'xml_url'        => get_field( '_vietveb_demo_dummy_data_file', $p->ID ),
                    'json_url'     => get_field( '_vietveb_demo_settings', $p->ID ),
                ),
                'tags' => $vietveb_demo_tag,
                'categories' =>  $vietveb_demo_cat,
            );
        }
        
        $categories = array();
        $tags = array();
        $terms = get_terms( 'vietveb_demo_cat', array(
            'hide_empty' => true,
            'lang' => 'en', // use language slug in the query
            //'number' => 5,
        ) );
        if ( ! is_wp_error( $terms ) ) {
            foreach ($terms as $t) {
                $categories[$t->slug] = array(
                    'slug'  => $t->slug,
                    'name'  => $t->name,
                    'id'    => $t->term_id,
                    'count' => $t->count,
                );
            }
        }
        $terms = get_terms( 'vietveb_demo_tag', array(
            'hide_empty' => true,
            //'number' => 5,
            'lang' => 'en', // use language slug in the query
        ) );
        if ( ! is_wp_error( $terms ) ) {
            foreach ($terms as $t) {
                $tags[$t->slug] = array(
                    'slug'  => $t->slug,
                    'name'  => $t->name,
                    'id'    => $t->term_id,
                    'count' => $t->count,
                );
            }
        }
        $data = array(
            'total' => $the_query->found_posts,
            'max_num_pages' => $the_query->max_num_pages,
            'paged' => $paged,
            'categories' => $categories,
            'tags' => $tags,
            'posts' => $posts
        );
        return $data;
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
			'supports'            => array( 'title', 'thumbnail', 'editor' ),
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
        flush_rewrite_rules();
    }
    
    public function register_taxonomy(){
        /** Demo Cats **/
        $labels = array(
            'name'                       => __( 'Categories', 'vietveb_server' ),
            'singular_name'              => __( 'Category', 'vietveb_server' ),
            'menu_name'                  => __( 'Categories', 'vietveb_server' ),
            'all_items'                  => __( 'All Categories', 'vietveb_server' ),
            'edit_item'                  => __( 'Edit Category', 'vietveb_server' ),
            'view_item'                  => __( 'View Category', 'vietveb_server' ),
            'update_item'                => __( 'Update Category', 'vietveb_server' ),
            'add_new_item'               => __( 'Add New Category', 'vietveb_server' ),
            'new_item_name'              => __( 'New Category Name', 'vietveb_server' ),
            'parent_item'                => __( 'Parent Category', 'vietveb_server' ),
            'parent_item_colon'          => __( 'Parent Category:', 'vietveb_server' ),
            'search_items'               => __( 'Search Categories', 'vietveb_server' ),
            'popular_items'              => __( 'Popular Categories', 'vietveb_server' ),
            'separate_items_with_commas' => __( 'Separate categories with commas', 'vietveb_server' ),
            'add_or_remove_items'        => __( 'Add or remove categories', 'vietveb_server' ),
            'choose_from_most_used'      => __( 'Choose from the most used categories', 'vietveb_server' ),
            'not_found'                  => __( 'No categories found', 'vietveb_server' )
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'query_var'         => true,
            'public'            => true,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug'          => 'vietveb_demo_cat',
                'hierarchical'  => true,
            )
        );
        register_taxonomy( 'vietveb_demo_cat', array( 'vietweb_demo' ), $args );
        
        /** Demo Tags **/
        $labels = array(
            'name'                       => __( 'Tags', 'vietveb_server' ),
            'singular_name'              => __( 'Tag', 'vietveb_server' ),
            'menu_name'                  => __( 'Tags', 'vietveb_server' ),
            'all_items'                  => __( 'All Tags', 'vietveb_server' ),
            'edit_item'                  => __( 'Edit Tag', 'vietveb_server' ),
            'view_item'                  => __( 'View Tag', 'vietveb_server' ),
            'update_item'                => __( 'Update Tag', 'vietveb_server' ),
            'add_new_item'               => __( 'Add New Tag', 'vietveb_server' ),
            'new_item_name'              => __( 'New Tag Name', 'vietveb_server' ),
            'parent_item'                => __( 'Parent Tag', 'vietveb_server' ),
            'parent_item_colon'          => __( 'Parent Tag:', 'vietveb_server' ),
            'search_items'               => __( 'Search Tags', 'vietveb_server' ),
            'popular_items'              => __( 'Popular Tags', 'vietveb_server' ),
            'separate_items_with_commas' => __( 'Separate tags with commas', 'vietveb_server' ),
            'add_or_remove_items'        => __( 'Add or remove tags', 'vietveb_server' ),
            'choose_from_most_used'      => __( 'Choose from the most used tags', 'vietveb_server' ),
            'not_found'                  => __( 'No tags found', 'vietveb_server' )
        );

        $args = array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'query_var'         => true,
            'public'            => true,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug'          => 'vietveb_demo_tag',
                'hierarchical'  => true,
            )
        );
        register_taxonomy( 'vietveb_demo_tag', array( 'vietweb_demo' ), $args );
        
        flush_rewrite_rules();
    }
}

new Vietveb_Server_Side();

