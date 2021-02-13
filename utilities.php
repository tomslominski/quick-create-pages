<?php
/**
 * Utility functions.
 */

namespace QuickCreatePages;

/**
 * Include a template with variables.
 *
 * @param string $template Template path.
 * @param array $args Arguments to pass to the template.
 */
function get_template( string $template, array $args = [] ) {
	$path = trailingslashit( Plugin::get_instance()->plugin_path ) . 'templates' . DIRECTORY_SEPARATOR . $template;

	if( file_exists( $path ) ) {
		extract( $args );
		include( $path );
	}
}
