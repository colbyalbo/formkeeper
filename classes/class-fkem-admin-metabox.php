<?php
/**
 * configures the admin metaboxes
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

if( ! class_exists('FKEM_METABOX') ) {
    class FKEM_METABOX {
        public function __construct() {
            add_action( 'add_meta_boxes', [$this, 'add_meta_box'] );
        }

       public function add_meta_box() {
            add_meta_box( 'fkem_form_entry', esc_html__( 'Form Submission', FKEM_TEXT_DOMAIN ), array($this, 'meta_box_callback'), 'form_keeper', 'normal', 'high' );
            add_meta_box( 'fkem_form_entry_extra', esc_html__( 'Extra Information', FKEM_TEXT_DOMAIN), array($this, 'meta_box_callback_extra'), 'form_keeper', 'normal', 'high' );
       }

        public function meta_box_callback() {
            global $current_user;
        
            $submission = get_post( get_the_ID() );
        
            if ( ! $read = get_post_meta( get_the_ID(), 'fkem_form_entry_read', true ) ) {
                $read = array( 'by_name' => $current_user->display_name, 'by' => $current_user->ID, 'on' => time() );
                update_post_meta( get_the_ID(), 'fkem_form_entry_read', $read );
            }
        
            $class   = 'notice notice-info';
            $message = 'First read by ' . $read['by_name'] . ' at ' . date( 'Y-m-d H:i', $read['on'] );
            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
        
            if ( $data = get_post_meta( get_the_ID(), 'fkem_form_entry', true ) ) {
        
                if ( $fields = $data['data'] ) {
                    echo '<table class="widefat">
                                <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Value</th>
                                </tr>
                                </thead>
                                <tbody>';
        
                    foreach ( $fields as $field ) {
                        $value = $field['value'];
        
                        if ( is_email( $value ) ) {
                            $value = '<a href="mailto:' . $value . '" target="_blank">' . $value . '</a>';
                        }
        
                        echo '<tr>
                                    <td><strong>' . $field['label'] . '</strong></td>
                                    <td>' . wpautop( sanitize_text_field( $value ) ) . '</td>
                                </tr>';
                    }
        
                    echo '<tr>
                                    <td><strong>Date of Submission</strong></td>
                                    <td>' . $submission->post_date . '</td>
                                </tr>';
        
                    echo '</tbody>
                        </table>';
                }
            }
    
        }
    
        public function meta_box_callback_extra() {
            $other_submissions = '';
        
            if ( $data = get_post_meta( get_the_ID(), 'fkem_form_entry', true ) ) {
                if ( $extra = $data['extra'] ) {
                    echo '<table class="widefat">
                                <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Value</th>
                                </tr>
                                </thead>
                                <tbody>';
        
                    foreach ( $extra as $key => $value ) {
        
                        switch ( $key ) {
                            case 'submitted_on_id':
                            case 'submitted_by_id':
                                continue( 2 ); //we don't really care about these ones
                                break;
                            case 'submitted_on':
                                if ( $extra['submitted_on_id'] ) {
                                    $value = $value . ' (<a href="' . get_permalink( $extra['submitted_on_id'] ) . '" target="_blank">View Page</a> | <a href="' . admin_url( 'post.php?action=edit&post=' . $extra['submitted_on_id'] ) . '" target="_blank">Edit Page</a>)';
                                } else {
                                    $value = '<em>Unknown</em>';
                                }
                                break;
                            case 'submitted_by':
                                if ( $extra['submitted_by_id'] ) {
                                    $value = $value . ' (<a href="' . admin_url( 'user-edit.php?user_id=' . $extra['submitted_by_id'] ) . '" target="_blank">View User Profiile</a>';
        
                                    $args = array(
                                        'posts_per_page' => - 1,
                                        'meta_key'       => 'fkem_form_entry_submitted_by',
                                        'meta_value'     => $extra['submitted_by_id'],
                                        'post_type'      => 'elementor_cf_db',
                                        'post_status'    => 'publish',
                                    );
        
                                    if ( $other_contacts = get_posts( $args ) ) {
                                        $value             .= ' | <a style="cursor: pointer;" onclick="jQuery(\'.other_submissions\').slideToggle();">View ' . count( $other_contacts ) . ' more submissions by this user</a>';
                                        $other_submissions .= '<div style="display: none;" class="other_submissions">
                                                                    <h3>Other submissions made by the same person</h3>';
                                        $other_submissions .= '<table class="widefat">';
        
                                        foreach ( $other_contacts as $other_contact ) {
                                            $other_submissions .= '<tr><td><a href="' . admin_url( 'post.php?action=edit&post=' . $other_contact->ID ) . '">' . $other_contact->post_title . '</a></td></tr>';
                                        }
        
                                        $other_submissions .= '</table></div>';
                                    }
        
                                    $value .= ')';
                                } else {
                                    $value = '<em>Not a registered user</em>';
                                }
        
                                break;
                        }
        
                        $key_label = ucwords( str_replace( '_', ' ', $key ) );
        
                        echo '<tr>
                                    <td><strong>' . $key_label . '</strong></td>
                                    <td>' . $value . '</td>
                                </tr>';
                    }
        
                    echo '</tbody>
                        </table>';
        
                    echo $other_submissions;
                }
        
            }
        
        }
    
       

    }    
}