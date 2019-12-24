<?php
/**
 *
 * Adds the form keeper custom post type to record the submissions
 *
 * @since      1.0.0
 * @package    form-keeper-for-elementor
 * @subpackage form-keeper-for-elementor/classes
 * @author     Colby Albarado <ca@colbyalbo.com>
 * 
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if( ! class_exists('FKEM_FORM_KEEPER_CPT') ) {
    class FKEM_FORM_KEEPER_CPT {
        public function __construct() {
            add_action( 'init', [$this, 'fkem_init_formkeeper_post_type'] );
        }

        public function fkem_init_formkeeper_post_type() {
            $labels = array(
                'name'               => _x( 'Form Keeper - Contact Form Submissions', 'post type general name', FKEM_TEXT_DOMAIN ),
                'singular_name'      => _x( 'Form Keeper', 'post type singular name', FKEM_TEXT_DOMAIN ),
                'menu_name'          => _x( 'Form Keeper', 'admin menu', FKEM_TEXT_DOMAIN ),
                'name_admin_bar'     => _x( 'Form Keeper', 'add new on admin bar', FKEM_TEXT_DOMAIN ),
                'add_new'            => _x( 'Add New', 'Form Keeper', FKEM_TEXT_DOMAIN ),
                'add_new_item'       => __( 'Add New Form Keeper', FKEM_TEXT_DOMAIN ),
                'new_item'           => __( 'New Form Keeper', FKEM_TEXT_DOMAIN ),
                'edit_item'          => __( 'Edit Form Keeper', FKEM_TEXT_DOMAIN ),
                'view_item'          => __( 'View Form Keeper', FKEM_TEXT_DOMAIN ),
                'all_items'          => __( 'All Form Keeper', FKEM_TEXT_DOMAIN ),
                'search_items'       => __( 'Search Form Keeper', FKEM_TEXT_DOMAIN ),
                'parent_item_colon'  => __( 'Parent Form Keeper:', FKEM_TEXT_DOMAIN ),
                'not_found'          => __( 'No contact form submissions found.', FKEM_TEXT_DOMAIN ),
                'not_found_in_trash' => __( 'No contact form submissions found in Trash.', FKEM_TEXT_DOMAIN )
            );

            $args = array(
                'labels'             => $labels,
                'description'        => __( 'For storing Elementor contact form submissions.', FKEM_TEXT_DOMAIN ),
                'public'             => false,
                'publicly_queryable' => false,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => false,
                'capability_type'    => 'post',
                'has_archive'        => false,
                'hierarchical'       => false,
                'menu_position'      => null,
                'menu_icon'          => 'dashicons-list-view',
                'supports'           => array( 'title' ),
                'capabilities' => array(
                    'create_posts' => false
                ),
                'map_meta_cap'        => true
            );

            register_post_type( 'form_keeper', $args );
        }
    }
}
