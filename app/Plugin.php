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
	 * @var string Main plugin file location.
	 */
	public string $plugin_filename;

	/**
	 * @var string Plugin root URL.
	 */
	public string $plugin_url;

	/**
	 * @var SubmissionHandler Submission handler singleton class.
	 */
	public SubmissionHandler $submission_handler;

	/**
	 * Register hooks.
	 */
	public function __construct() {
		$this->plugin_path = untrailingslashit( dirname(__DIR__) );
		$this->plugin_filename = $this->plugin_path . '/quick-create-pages.php';
		$this->plugin_url = plugin_dir_url( $this->plugin_filename );

		$this->submission_handler = new SubmissionHandler();

		add_action( 'admin_menu', [$this, 'register_page'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_assets'] );
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
	 * Enqueue assets.
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'quick-create-pages', $this->plugin_url . 'assets/js/app.js', [], get_plugin_version(), true );
		wp_enqueue_style( 'quick-create-pages', $this->plugin_url . 'assets/css/style.css', [], get_plugin_version() );
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
