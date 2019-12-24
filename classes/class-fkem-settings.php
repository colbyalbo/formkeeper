<?php
/**
 *
 * Adds the form keeper settings page
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

if( ! class_exists('FKEM_FORM_KEEPER_SETTINGS') ) {
    class FKEM_FORM_KEEPER_SETTINGS {
        public function __construct() {
            add_action( 'admin_init', [$this, 'add_settings'] );
        }

        public function add_settings() {
            register_setting( 'fkem-options', 'fkem_option_settings', array( 'sanitize_callback' => 'fkem_sanitize_validate',) );

            add_settings_section(
                'fkem_options_section',
                __( '', FKEM_TEXT_DOMAIN ),
                array($this, 'fkem_options_section_callback'),
                'fkem-options'
            );

            add_settings_field(
                'fkem_unread_notice',
                __( 'Disable the Unread Notice:', FKEM_TEXT_DOMAIN ),
                'fkem_unread_notice_render',
                'fkem-options',
                'fkem_options_section'
            );

            add_settings_field(
                'fkem_authorize_export',
                __( 'Minimum Role to Access Export:', FKEM_TEXT_DOMAIN ),
                'fkem_authorize_export_render',
                'fkem-options',
                'fkem_options_section'
            );

            function fkem_unread_notice_render() {
                $options = get_option('fkem_option_settings');
                ?>
                    <label for="fkem-unread-notice-input" style="margin-right: .5rem;"><?php _e( 'Check the box to disable the unread notices', FKEM_TEXT_DOMAIN ); ?></label>
                    <input id="fkem-unread_notice-input" type="checkbox" name="fkem_option_settings[fkem_unread_notice]" value="1" <?php !isset($options['fkem_unread_notice']) ? :  checked(1, $options['fkem_unread_notice']); ?>>
                <?php 
            }

            function fkem_authorize_export_render() {
                $options = get_option('fkem_option_settings');
                $roles = ['administrator', 'editor', 'author', 'contributor'];
                ?>
                <label for="fkem-authorize-export" style="margin-right: .5rem;"><?php _e( 'Authorized to Export', FKEM_TEXT_DOMAIN ); ?></label>
                <select id="fkem-authorize-export" name="fkem_option_settings[fkem_authorize_export]" autoComplete="off">
                <?php 
                foreach( $roles as $val ){
                    $check = isset($options['fkem_authorize_export']) ? esc_attr(selected($options['fkem_authorize_export'], $val)): '';
                    echo '<option value="'. $val .'"'. $check .'>'. ucfirst($val) .'</option>';
                }
                echo '</select>';
            }

            function fkem_sanitize_validate($input) {
                if(!isset($input['fkem_unread_notice'])){
                    $input['fkem_unread_notice'] = absint(0);
                }

                if(!isset($input['fkem_authorize_export'])){
                    $input['fkem_authorize_export'] = sanitize_text_field('administrator');
                }

                $input['fkem_unread_notice'] = absint($input['fkem_unread_notice']);
                $input['fkem_authorize_export'] = sanitize_text_field($input['fkem_authorize_export']);
                return $input;
            }
            
            function fkem_settings_page() {
                ?>
                <div class="wrap">
                    <h2>Form Keeper for Elementor Settings</h2>
                    <div id="poststuff">
                        <div class="post-body">
                            <div class="postbox">
                                <div class="inside" style="padding: 1rem 1.5rem;">
                                    <form action='options.php' method='post'>
                                        
                                        <?php
                                        settings_errors();
                                        settings_fields('fkem-options');
                                        do_settings_sections('fkem-options');
                                        submit_button();
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            
        }

        public function fkem_options_section_callback() {
            echo __( '<span>Choose Your Settings and Click the Save Changes Button</span>', FKEM_TEXT_DOMAIN );
        }
    }
}    

