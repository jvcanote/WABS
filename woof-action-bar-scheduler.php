<?php 
/**
 * @category     WordPress_Plugin
 * @package      WABS
 * @author       WEBDOGS
 * @license      GPL-3.0+
 * @link         HTTPS://WEBDOGS.COM
 *
 * Plugin Name:  Woof Action Bar Scheduler, Hyah?!
 * Plugin URI:   https://github.com/theWEBDOGS/WABS
 * Description:  WABS allows you to create and schedule an action bar at the top of your site.
 * Author:       WEBDOGS
 * Author URI:   HTTPS://WEBDOGS.COM
 * Contributors: WEBDOGS (@WEBDOGS / WEBDOGS.COM)
 *               Jacob Vega Canote (@jacob / WEBDOGS.COM)
 *               Damian Ruiz (@damian / WEBDOGS.COM)
 *
 * Version:      1.0.1
 *
 * Text Domain:  woof-action-bar-scheduler
 * Domain Path:  /languages/
 *
 * License:      GPL3
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! defined( 'WABS_DEV') )
    define( 'WABS_DEV', false );

if ( ! defined( 'WABS_PATH') )
    define( 'WABS_PATH', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'WABS_URL' ) )
    define( 'WABS_URL',  plugins_url( '', __FILE__ ) );

// Load plugin class files
require_once WABS_PATH . '/includes/class-wabs.php';

// Load plugin libraries
require_once WABS_PATH . '/cmb1/init.php';
require_once WABS_PATH . '/includes/lib/wabs-functions.php';
require_once WABS_PATH . '/includes/lib/wabs-options.php';
require_once WABS_PATH . '/includes/lib/wabs-fields.php';

/**
 * Returns the main instance of WABS to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WABS
 */
function WABS () {
	$instance = WABS::instance( __FILE__, '1.0.1' ); 	// if ( is_null( $instance->settings ) ) { $instance->settings = WABS_Settings::instance( $instance ); }
	return $instance;
}
WABS();
