<?php

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if(! function_exists('ebxwcts_Remove_All_Options') ) {
    function ebxwcts_Remove_All_Options() {
        $all_timeslot_options = 'ebxwcts_option_settings';
        $remove_check = get_option('ebxwcts_option_settings')['ebxwcts_remove_data'];
        if( 1 == $remove_check ) {
            delete_option( $all_timeslot_options );
        }
        flush_rewrite_rules();
    }
}
ebxwcts_Remove_All_Options();