<?php
/**
 *
 * Adds the form keeper export page
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

if( ! class_exists('FKEM_FORM_KEEPER_EXPORT') ) {
    class FKEM_FORM_KEEPER_EXPORT {
        public function __construct() {
            add_action( 'admin_init', [$this, 'add_export'] );
            add_action( 'admin_init', [$this, 'csv_export'] );
        }

        public function add_export() {
            function fkem_export_page() {
                $args = array(
                    'post_type' => 'form_keeper',
                    'posts_per_page' => -1
                );
                $posts = get_posts($args);
                $entries_by_page = [];
                $entries_by_name = [];

                foreach ( $posts as $post ) {
                    if ( $post_meta = get_post_meta( $post->ID, 'fkem_form_entry', true ) ) {
                        $entries_by_page[$post_meta['extra']['submitted_on']] = $post_meta['extra']['submitted_on'];
                    }
                    if ( $post_meta_form_name = get_post_meta( $post->ID, 'fkem_form_entry_form_id', true ) ) {
                        $entries_by_name[$post_meta_form_name] = $post_meta_form_name;
                    }
                }
                $html = '<div class="wrap"><h2>Form Keeper for Elementor Export</h2><div id="poststuff"><div class="post-body"><div class="postbox"><div  style="padding: 1.5rem;">';
                $html .= '<h3 style="margin: 0 0 1rem 0;">Export by Page Name</h3>';
                $html .= '<span style="margin-bottom: .5rem; display: block;">Exports all form entries that were submitted from the page selected.</span>';
                $html .= '<form method="POST" action="">';
                $html .= '<select  style="margin-right: 1rem; width: 200px;" name="page_name">';
                
                ksort( $entries_by_page );
                foreach ( $entries_by_page as $page_name ) {
                    $html .= '<option value="' . $page_name . '">' . $page_name . '</option>';
                }
                
                $html .= '</select>';
                $html .= '<input type="submit" name="download_csv" class="button-primary" value="Export Form Entries" />';
                $html .= wp_nonce_field( 'fkem_verify_action', 'fkem_verify_field' );
                $html .= '</form><hr style="margin: 2rem 0; display: block;">';
                $html .= '<h3 style="margin: 0 0 1rem 0;">Export by Form Name</h3>';
                $html .= '<span style="margin-bottom: .5rem; display: block;">Exports all form entries that were submitted from the form selected.</span>';
                $html .= '<form method="POST" action="">';
                $html .= '<select  style="margin-right: 1rem; width: 200px;" name="form_name">';
             
                ksort( $entries_by_name );
                foreach ( $entries_by_name as $form_name ) {
                   $html .= '<option value="' . $form_name . '">' . $form_name . '</option>';
                }
        
                $html .=  '</select>';
                $html .=  '<input type="submit" name="download_csv" class="button-primary" value="Export Form Entries" />';
                $html .= wp_nonce_field( 'fkem_verify_action', 'fkem_verify_field' );
                $html .=  '</form>';
                $html .= ' </div></div></div></div></div>';
                echo $html;
            }
        }

        public function csv_export() {
            if ( ! isset( $_POST['fkem_verify_field'] ) || ! wp_verify_nonce( $_POST['fkem_verify_field'], 'fkem_verify_action' ) ) {
               return;
            }
            if ( isset( $_POST['download_csv'] ) ) {
                if ( isset( $_POST['page_name'] ) ) {
                    $page_name = $_POST['page_name'];
                    if ( $rows = $this->build_page_name_array($page_name) ) {
                        $trimmed = strtolower(str_replace(' ', '-', $page_name));
                        header( 'Content-Type: application/csv' );
                        header( 'Content-Disposition: attachment; filename='. $trimmed .'-'. time() . '.csv' );
                        header( 'Pragma: no-cache' );
                        echo implode( "\n", $rows );
                        die;
                    }
                }
                if ( isset( $_POST['form_name'] ) ) {
                    $form_name = $_POST['form_name'];
                    if ( $rows = $this->build_form_name_array($form_name) ) {
                        $trimmed = strtolower(str_replace(' ', '-', $form_name));
                        header( 'Content-Type: application/csv' );
                        header( 'Content-Disposition: attachment; filename='. $trimmed .'-'. time() . '.csv' );
                        header( 'Pragma: no-cache' );
                        echo implode( "\n", $rows );
                        die;
                    }
                }
            }
        }

        public function build_page_name_array($page_name) {
            $row = '';
            $row .= '"Date","Submitted On","Form ID","Submitted By",';
           //make heading
            if( $entries = get_posts(['post_type' => 'form_keeper', 'posts_per_page' => -1] ) ) {
                foreach( $entries as $entry ) {
                    if( $pages = get_post_meta( $entry->ID, 'fkem_form_entry', true ) ) {
                        if( $pages['extra']['submitted_on'] == $page_name  ) {
                            foreach( $pages['data'] as $label ) {
                                $row .= '"' . $label['label']. '",';
                            }
                            break;
                        }
                    }
                }
                $rows[] = rtrim( $row, ',' );
                //make rows
                foreach( $entries as $entry ) {
                    if( $pages = get_post_meta( $entry->ID, 'fkem_form_entry', true ) ) {
                        if( $pages['extra']['submitted_on'] == $page_name  ) {
                            $row = '';
                            $form_id = get_post_meta( $entry->ID, 'fkem_form_entry_form_id', true );
                            $row .= '"' . $entry->post_date . '","';
                            $row .= $pages['extra']['submitted_on'] . '","';
                            $row .= $form_id . '","';
                            $row .= $pages['extra']['submitted_by'] . '",';

                            foreach ( $pages['data'] as $field ) {
                                $row .= '"' . addslashes( $field['value'] ) . '",';
                            }

                            $rows[] = rtrim( $row, ',' );
                        }    
                    }    
                }
            }
            return $rows;
        }

        public function build_form_name_array($form_name) {
            $row = '';
            $row .= '"Date","Submitted On","Form ID","Submitted By",';
           //make heading
            if( $entries = get_posts(['post_type' => 'form_keeper', 'posts_per_page' => -1] ) ) {
                foreach( $entries as $entry ) {
                    if( $forms = get_post_meta( $entry->ID, 'fkem_form_entry', true ) ) {
                        if( $entry->fkem_form_entry_form_id == $form_name  ) {
                            foreach( $forms['data'] as $label ) {
                                $row .= '"' . $label['label']. '",';
                            }
                            break;
                        }
                    }
                }
                $rows[] = rtrim( $row, ',' );
                //make data rows
                foreach( $entries as $entry ) {
                    if( $forms = get_post_meta( $entry->ID, 'fkem_form_entry', true ) ) {
                        if( $entry->fkem_form_entry_form_id == $form_name  ) {
                            $row = '';
                            $form_id = get_post_meta( $entry->ID, 'fkem_form_entry_form_id', true );
                            $row .= '"' . $entry->post_date . '","';
                            $row .= $forms['extra']['submitted_on'] . '","';
                            $row .= $form_id . '","';
                            $row .= $forms['extra']['submitted_by'] . '",';

                            foreach ( $forms['data'] as $field ) {
                                $row .= '"' . addslashes( $field['value'] ) . '",';
                            }

                            $rows[] = rtrim( $row, ',' );
                        }    
                    }    
                }
            }
            return $rows;
        }
    }
}    

