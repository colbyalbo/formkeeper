<?php
/**
 * configures the admin list columns
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

if( ! class_exists('FKEM_ADMIN_COLUMNS') ) {
    class FKEM_ADMIN_COLUMNS {
        public function __construct() {
            add_filter( 'manage_form_keeper_posts_columns', [$this, 'column_headings'], 100 );
            add_action( 'manage_form_keeper_posts_custom_column', [$this, 'column_data'], 100, 2 );
        }

        public function column_headings( $defaults ) {
            unset( $defaults['date'] );
            unset( $defaults['title'] );
        
            $defaults['fkem_title'] = 'View';
            $defaults['form_id']            = 'Form ID';
            $defaults['email']              = 'Email';
            $defaults['read']               = 'Read/Unread';
            $defaults['cloned']             = 'Cloned';
            $defaults['sub_on']             = 'Submitted From';
            $defaults['sub_date']           = 'Submission Date';
        
            return $defaults;
        }

        public function column_data( $column_name, $post_id ) {
            $contact = get_post( $post_id );
            $data    = get_post_meta( $post_id, 'fkem_form_entry', true );
        
            if ( $column_name == 'fkem_title' ) {
                echo '<a href="' . admin_url( 'post.php?action=edit&post=' . $post_id ) . '">Read Entry</a>';
            } else if ( $column_name == 'read' ) {
                if ( $read = get_post_meta( $post_id, 'fkem_form_entry_read', true ) ) {
                    echo '<span style="color: green;">' . $read['by_name'] . '<br />' . date( 'd-m-Y H:i', $read['on'] ) . '</span>';
                } else {
                    echo '<span class="dashicons dashicons-email-alt"></span>';
                }
            } else if ( $column_name == 'sub_on' ) {
                if ( $data['extra']['submitted_on'] ) {
                    echo '<a href="' . get_permalink( $data['extra']['submitted_on_id'] ) . '">' . $data['extra']['submitted_on'] . '</a>';
                }
            } else if ( $column_name == 'sub_date' ) {
                echo $contact->post_date;
            } else if ( $column_name == 'cloned' ) {
                if ( $cloned = get_post_meta( $post_id, 'fkem_form_entry_cloned', true ) ) {
                    $cloned_count = count( $cloned );
        
                    echo '<span class="dashicons dashicons-yes"></span> (' . $cloned_count . ')';
                } else {
                    echo '<span class="dashicons dashicons-no-alt"></span>';
                }
            } else if ( $column_name == 'email' ) {
                if ( $email = get_post_meta( $post_id, 'fkem_form_entry_email', true ) ) {
                    $email = '<a href="mailto:' . $email . '" target="_blank">' . $email . '</a>';
                } else {
                    $email = '-';
                }
                echo $email;
            } else if ( $column_name == 'form_id' ) {
                if ( ! $form_id = get_post_meta( $post_id, 'fkem_form_entry_form_id', true ) ) {
                    $form_id = '-';
                }
        
                echo $form_id;
            }
        }
    }    
}