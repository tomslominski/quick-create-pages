<?php
/**
 * Plugin Name: Quick Create Pages
 * Plugin URI: https://github.com/tomslominski/quick-create-pages
 * Description: Quickly add a hierarchy of posts or pages when creating a new site.
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.4
 * Author: Tom Slominski
 * Author URI: https://slomin.ski/
 * License: GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 */

use QuickCreatePages\Plugin;

// Start the plugin
require_once 'vendor/autoload.php';
Plugin::get_instance();

/**
 * Get the main plugin object.
 *
 * @return Plugin
 */
function QCP(): Plugin {
	return Plugin::get_instance();
}
