<?php
/**
 * Fired during plugin activation, sets initial values for form keeper settings
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

if( ! class_exists('FKEM_ACTIVATOR') ) {
    class FKEM_ACTIVATOR {
        /**
         *
         * @since    1.0.0
         * 
         */
        public function __construct() {
            $this->activate();
        }

        public function activate() {
            if( null == get_option('fkem_option_settings') ) {
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                    return;
                }
                if ( ! current_user_can( 'manage_options' ) ) {
                    return;
                }
                $args = [
                    'fkem_unread_notice' => absint(0),
                    'fkem_authorize_export' => 'administrator'
                ];
                update_option( 'fkem_option_settings', $args );
            } else {
                return;
            }
        }
    }
}