<?php

//Custom Post Types for IATI

//Default Labels Array for Custom Post Types
/*
'name' => _x('Books', 'post type general name', 'your_text_domain'),
    'singular_name' => _x('Book', 'post type singular name', 'your_text_domain'),
    'add_new' => _x('Add New', 'book', 'your_text_domain'),
    'add_new_item' => __('Add New Book', 'your_text_domain'),
    'edit_item' => __('Edit Book', 'your_text_domain'),
    'new_item' => __('New Book', 'your_text_domain'),
    'all_items' => __('All Books', 'your_text_domain'),
    'view_item' => __('View Book', 'your_text_domain'),
    'search_items' => __('Search Books', 'your_text_domain'),
    'not_found' =>  __('No books found', 'your_text_domain'),
    'not_found_in_trash' => __('No books found in Trash', 'your_text_domain'),
    'parent_item_colon' => '',
    'menu_name' => __('Books', 'your_text_domain')
*/


//Default Arguments Array for Custom Post Types
/*
$args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => _x( 'book', 'URL slug', 'your_text_domain' ) ),
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
  );
 */

function create_iati_organisation() {
	register_post_type( 'iati_organisation',
		array(
                'labels' => array(
                    'name' => __( 'Organisations' ),
                    'singular_name' => __( 'Organisation' ),
                    'menu_name' => __('Implementing Organisations', 'iati')
                ),
		'public' => true,
                'publicly_queryable' => false,
		'has_archive' => false,
                'rewrite' => array( 'slug' => _x( 'organisation', 'URL slug', 'iati' ) ),
                'capability_type' => 'page',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => 30,
                'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' )
            )
	);
}

function create_iati_implementation() {
	register_post_type( 'iati_implementation',
		array(
                'labels' => array(
                    'name' => __( 'Implementations' ),
                    'singular_name' => __( 'Implementation' ),
                    'menu_name' => __('Implementation Events', 'iati')
                ),
		'public' => true,
		'has_archive' => false,
                'rewrite' => array( 'slug' => _x( 'implementation', 'URL slug', 'iati' ) ),
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => 31,
                'supports' => array( 'title', 'editor', 'thumbnail', )
            )
	);
}


/**
 * Loads the custom post types on init
 */
function load_custom_post_types()
{
    create_iati_organisation();
    create_iati_implementation();
}

add_action( 'init', 'load_custom_post_types' );


function set_edit_iati_organisation_columns($columns) {
    unset($columns['date']);
    return array_merge($columns,
              array('type' => __('Type') )
            );
}
add_filter('manage_iati_organisation_posts_columns' , 'set_edit_iati_organisation_columns');

function custom_iati_organisation_type_column( $column, $post_id ) {

    global $post;

    switch ( $column ) {
      case 'type':
//        $terms = get_the_term_list( $post_id , 'iati_organisation_type' , '' , ',' , '' );
        $terms = wp_get_post_terms( $post_id , 'iati_organisation_type');

          if ( !empty( $terms ) ) {
                $out = array();
                foreach ( $terms as $term ) {
                        $out[] = sprintf( '<a href="%s">%s</a>',
                                esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'taxonomy' => $term->slug ), 'edit.php' ) ),
                                esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'category', 'display' ) )
                        );
                }
                /* translators: used between list items, there is a space after the comma */
                echo join( __( ', ' ), $out );
        } else {
                _e( 'Uncategorized' );
	}
        break;
    }
}
add_action( 'manage_iati_organisation_posts_custom_column' , 'custom_iati_organisation_type_column', 10, 2 );



function set_edit_post_columns($columns) {
    unset($columns['date']);
    return array_merge($columns,
              array('publisher' => __('Publisher'),
                    'book_author' =>__( 'Book Author')));
}
//add_filter('manage_post_posts_columns' , 'set_edit_post_columns');



// Make these Custom Post Type Columns sortable
function iati_organisation_sortable_columns($columns) {
    $fgdf = "";
    $columns['type'] = 'type';
    return $columns;
}
add_filter( "manage_edit-iati_organisation_sortable_columns", "iati_organisation_sortable_columns" );

/**
 * Add custom taxonomies
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_custom_taxonomies() {
	// Add new "Organisation Type" taxonomy to iati_organisation
	register_taxonomy('iati_organisation_type', 'iati_organisation', array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => true,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => _x( 'Organisation Types', 'taxonomy general name' ),
			'singular_name' => _x( 'Organisation Type', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Types' ),
			'all_items' => __( 'All Types' ),
			'parent_item' => __( 'Parent Type' ),
			'parent_item_colon' => __( 'Parent Type:' ),
			'edit_item' => __( 'Edit Type' ),
			'update_item' => __( 'Update Type' ),
			'add_new_item' => __( 'Add New Type' ),
			'new_item_name' => __( 'New Type Name' ),
			'menu_name' => __( 'Organisation Types' ),
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			'slug' => 'organisation_type', // This controls the base slug that will display before each term
			'with_front' => false, // Don't display the category base before "/locations/"
			'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
		),
	));
}
add_action( 'init', 'add_custom_taxonomies', 0 );
