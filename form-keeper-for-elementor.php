<?php
/*
* Plugin Name: Form Keeper for Elementor
* Plugin URI: 
* Description: Stores form entries for the Elementor Pro form widget
* Version: 1.0.0
* Author: Colby Albarado
* Author URI: https://colbyalbo.com
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
* Text Domain:       form-keeper-for-elementor
* Domain Path:       /languages
*/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2019 Eyebox Media LLC
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

if( ! class_exists('FKEM_FORM_KEEPER') ) {
    class FKEM_FORM_KEEPER {
        //require any class php files here
        
        public function __construct() {
            require_once('classes/class-fkem-post-type.php');
            require_once('classes/class-fkem-submenu.php');
            require_once('classes/class-fkem-settings.php');
            require_once('classes/class-fkem-export.php');
            require_once('classes/class-fkem-activator.php');
            require_once('classes/class-fkem-save-entry.php');
            require_once('classes/class-fkem-admin-list.php');
            require_once('classes/class-fkem-admin-metabox.php');

            define( 'FKEM_VERSION', '1.0.0' ); 
            define( 'FKEM_TEXT_DOMAIN', 'form-keeper-for-elementor' );
            define( 'FKEM_PATH', __FILE__ );
            define( 'FKEM_DIR_PATH', __DIR__ . '/');

            register_activation_hook( __FILE__, [ $this, 'activate_plugin' ] );
            add_action( 'plugins_loaded', [ $this, 'init' ] );
        }

        public function init() {
            new FKEM_FORM_KEEPER_CPT;
            new FKEM_FORM_KEEPER_SUBMENU;
            new FKEM_FORM_KEEPER_SETTINGS;
            new FKEM_FORM_KEEPER_EXPORT;
            new FKEM_SAVE_ENTRY;
            new FKEM_ADMIN_COLUMNS;
            new FKEM_METABOX;
        }

        public function activate_plugin() {
           new FKEM_ACTIVATOR;
        }

    }
    new FKEM_FORM_KEEPER;
}