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
			fn() => get_template('plugin-page'),
		);
	}

	/**
	 * Enqueue assets.
	 */
	public function enqueue_assets() {
		if( ( $screen = get_current_screen() ) && 'tools_page_quick-create-pages' === $screen->id ) {
			wp_register_script( 'quick-create-pages', $this->plugin_url . 'assets/js/app.js', [], get_plugin_version(), true );
			wp_localize_script( 'quick-create-pages', 'qcpConfig', apply_filters( 'qcp/js_config', [
				'postTypes' => $this->get_post_types(),
				'strings' => $this->get_strings(),
				'formAction' => admin_url( 'tools.php?page=quick-create-pages' ),
				'nonce' => wp_create_nonce( 'qcp_create_pages' ),
			] ) );
			wp_enqueue_script( 'quick-create-pages' );

			wp_enqueue_style( 'quick-create-pages', $this->plugin_url . 'assets/css/style.css', [], get_plugin_version() );
		}
	}

	/**
	 * Let a list of post types which can be created by the plugin.
	 *
	 * @return array
	 */
	public function get_post_types(): array {
		$post_types = get_post_types( [
			'public' => true,
		], 'objects' );
		$return = [];

		foreach( $post_types as $post_type ) {
			if( current_user_can( $post_type->cap->publish_posts ) ) {
				$return[$post_type->name] = [
					'slug' => $post_type->name,
					'name' => $post_type->labels->singular_name,
					'pluralName' => $post_type->labels->name,
					'hierarchical' => $post_type->hierarchical,
				];
			}
		}

		unset( $return['attachment'] );

		return apply_filters( 'qcp/post_types', (array) $return );
	}

	/**
	 * Get translatable strings to be used in JavaScript.
	 *
	 * @return array
	 */
	public function get_strings(): array {
		return [
			'Name' => __( 'Name', 'quick-create-pages' ),
			'Slug' => __( 'Slug', 'quick-create-pages' ),
			'Changing from a hierarchical post type to a non-hierarchical post type will erase all child pages. Continue?' => __( 'Changing from a hierarchical post type to a non-hierarchical post type will erase all child pages. Continue?', 'quick-create-pages' ),
			'{0} {1}, including {2} top level {3} and {4} child {5} will be created.' => __( '{0} {1}, including {2} top level {3} and {4} child {5} will be created.', 'quick-create-pages' ),
			'{0} {1} will be created.' => __( '{0} {1} will be created.', 'quick-create-pages' ),
			'Delete page' => __( 'Delete page', 'quick-create-pages' ),
			'Add child page' => __( 'Add child page', 'quick-create-pages' ),
			'Add page' => __( 'Add page', 'quick-create-pages' ),
			'Create {0}' => __( 'Create {0}', 'quick-create-pages' ),
			'Post type' => __( 'Post type', 'quick-create-pages' ),
		];
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
