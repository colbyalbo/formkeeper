<?php
/**
 * Saves the elementor form submissions
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

if( ! class_exists('FKEM_SAVE_ENTRY') ) {
    class FKEM_SAVE_ENTRY {
        public function __construct() {
            add_action( 'elementor_pro/forms/new_record', [$this, 'save_entry'], 10, 10 );
        }

        public function save_entry($record, $handler) {
            if ( $fields = $record->get_formatted_data() ) {
                $data  = array();
                $email = false;
        
                foreach ( $fields as $label => $value ) {
                    if ( stripos( $label, 'email' ) !== false  ) {
                        $email = $value;
                    }
        
                    $data[] = array( 'label' => $label, 'value' => sanitize_text_field( $value ) );
                }
        
                $this_page    = get_post( $_POST['post_id'] );
                $this_user    = false;
                $current_user = get_current_user_id();
        
                if ( $this_user_id = ( $current_user ? $current_user : 0 ) ) {
                    if ( $this_user = get_userdata( $this_user_id ) ) {
                        $this_user = $this_user->display_name;
                    }
                }
        
                $extra = array(
                    'submitted_on'    => $this_page->post_title,
                    'submitted_on_id' => $this_page->ID,
                    'submitted_by'    => $this_user,
                    'submitted_by_id' => $this_user_id
                );
        
                $db_ins = array(
                    'post_title'  => $record->get_form_settings( 'form_name' ) . ' - ' . date( 'Y-m-d H:i:s' ),
                    'post_status' => 'publish',
                    'post_type'   => 'form_keeper',
                );
        
                // Insert the post into the database
                if ( $post_id = wp_insert_post( $db_ins ) ) {
                    update_post_meta( $post_id, 'fkem_form_entry', array(
                        'data'            => $data,
                        'extra'           => $extra,
                        'fields_original' => array( 'form_fields' => $record->get_form_settings( 'form_fields' ) ),
                        'record_original' => $record,
                        'post'            => array_map( 'sanitize_text_field', $_POST ),
                        'server'          => $_SERVER
                    ) );
        
                    if ( $this_user_id ) {
                        update_post_meta( $post_id, 'fkem_form_entry_submitted_by', $this_user_id );
                    }
        
                    update_post_meta( $post_id, 'fkem_form_entry_read', 0 );
                    update_post_meta( $post_id, 'fkem_form_entry_email', $email );
                    update_post_meta( $post_id, 'fkem_form_entry_form_id', $record->get_form_settings( 'form_name' ) );
        
                }
            }
        }

    }
}