<?php
/**
 *
 * Adds the form keeper submenus
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

if( ! class_exists('FKEM_FORM_KEEPER_SUBMENU') ) {
    class FKEM_FORM_KEEPER_SUBMENU {
        public function __construct() {
            add_action( 'admin_menu', [$this, 'add_submenu'] );
            add_action( 'admin_menu', [$this, 'unread_notice'] );
        }

        public function add_submenu() {
            add_submenu_page(
                'edit.php?post_type=form_keeper',
                'Export',
                'Export',
                'manage_options',
                'form-keeper-export',
                'fkem_export_page'
            );
            
            add_submenu_page(
                'edit.php?post_type=form_keeper',
                'Settings',
                'Settings',
                'manage_options',
                'form_keeper_settings',
                'fkem_settings_page'
            );
        }

        public function unread_notice() {
            $notice_setting = get_option('fkem_option_settings');
            if( $notice_setting['fkem_unread_notice'] === 0 ){
                global $menu;
                $pending_count = '';
                $args = array(
                    'posts_per_page' => - 1,
                    'meta_key'       => 'fkem_form_entry_read',
                    'meta_value'     => 0,
                    'post_type'      => 'form_keeper',
                    'post_status'    => 'publish',
                );
                if ( $unread_entries = get_posts( $args ) ) {
                    $pending_count = count($unread_entries);
                }
                $listings_menu_item = wp_list_filter( $menu, [ 2 => 'edit.php?post_type=form_keeper' ] );
                if ( ! empty( $listings_menu_item ) && is_array( $listings_menu_item ) && $pending_count ) {
                    $menu[ key( $listings_menu_item ) ][0] .= " <span class='awaiting-mod update-plugins count-" . esc_attr( $pending_count ) . "'><span class='pending-count'>" . absint( number_format_i18n( $pending_count ) ) . '</span></span>';
                }
            }
        }
    }
}
