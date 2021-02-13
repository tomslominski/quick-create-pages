<?php
namespace QuickCreatePages;

/**
 * Main plugin class.
 */
class Plugin
{
	/**
	 * @var Plugin|null Plugin instance.
	 */
	private static ?Plugin $instance = null;

	/**
	 * @var string Plugin root directory.
	 */
	public string $plugin_path;

	/**
	 * Register hooks.
	 */
	public function __construct() {
		$this->plugin_path = dirname(__DIR__);
		add_action( 'admin_menu', [$this, 'register_page'] );
	}

	/**
	 * Register plugin page in the Tools menu.
	 */
	public function register_page() {
		add_submenu_page( 'tools.php',
			__('Quick Create Pages', 'quick-create-pages'),
			__('Quick Create Pages', 'quick-create-pages'),
			'publish_pages',
			'quick-create-pages',
			fn() => get_template('plugin-page.php'),
		);
	}

	/**
	 * Return plugin instance, instantiating the plugin first if needed.
	 *
	 * @return Plugin
	 */
	public static function get_instance(): Plugin {
		if( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
