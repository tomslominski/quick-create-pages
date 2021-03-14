<?php
namespace QuickCreatePages;

/**
 * Class used to handle submission and create the pages.
 */
class SubmissionHandler
{
	/**
	 * @var string[] Array of error messages.
	 */
	public array $errors = [];

	/**
	 * @var string[] Array of success messages.
	 */
	public array $messages = [];

	/**
	 * @var int Batch ID based on current time.
	 */
	public int $batch_id;

	/**
	 * Register hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', [$this, 'check_submission'] );
	}

	/**
	 * Check if a submission has been made and process it if it has.
	 */
	public function check_submission() {
		if( 'POST' === filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) && isset( $_POST['qcp'] ) ) {
			$this->batch_id = time();

			$this->process_submission();
		}
	}

	/**
	 * Save the pages.
	 */
	public function process_submission() {
		$post_type = isset( $_POST['qcp']['post_type'] ) ? sanitize_text_field( $_POST['qcp']['post_type'] ) : null;
		$post_type_object = get_post_type_object( $post_type );
		$pages = $_POST['qcp']['pages'] ?? null;
		$default = [
			[
				'name' => '',
				'slug' => '',
			],
		];

		// Check if nonce is valid
		if( !isset( $_POST['qcp']['nonce'] ) || !wp_verify_nonce( $_POST['qcp']['nonce'], 'qcp_create_pages' ) ) {
			$this->errors[] = __( 'An error occurred. Please try creating the pages again.', 'quick-create-pages' );
			return;
		}

		// Check if post type is valid
		if( !$post_type_object || !$post_type_object->public ) {
			$this->errors[] = __( 'An incorrect post type was set. Please try creating the pages again.', 'quick-create-pages' );
			return;
		}

		// Check if the user is allowed to create posts of this type
		if( !current_user_can( $post_type_object->cap->publish_posts ) ) {
			$this->errors[] = __( 'You are not allowed to create posts of this post type.', 'quick-create-pages' );
			return;
		}

		// Check if posts were provided
		if( !isset( $pages ) || $pages === $default ) {
			$this->errors[] = __( 'No pages were added. Please try creating the pages again.', 'quick-create-pages' );
			return;
		}

		if( !$this->errors ) {
			$this->parse_pages( $pages );
			$this->save_pages( $pages, $post_type );
		}

		if( $this->errors ) {
			// Hand the data back to the Vue script in the case of an error
			if( isset( $_POST['qcp']['pages'] ) ) {
				add_filter( 'qcp/js_config', function( array $config ) use ( $pages, $post_type ) {
					$config['pages'] = $pages;
					$config['selectedPostType'] = $post_type;

					$this->parse_pages( $config['pages'] );

					return $config;
				} );
			}
		} else {
			$this->messages[] = __( 'Pages created successfully.', 'quick-create-pages' );
		}
	}

	/**
	 * Parse pages from $_POST to ensure they have all the necessary properties.
	 *
	 * @param array $pages Array of pages
	 */
	public function parse_pages( array &$pages ) {
		foreach( $pages as &$page ) {
			$page = array_merge( [
				'name' => '',
				'slug' => '',
				'children' => [],
			], $page );

			$page['name'] = sanitize_text_field( $page['name'] );
			$page['slug'] = sanitize_title( $page['slug'] );

			if( $page['children'] ) {
				$this->parse_pages( $page['children'] );
			}
		}
	}

	/**
	 * Save an array of pages.
	 *
	 * @param array $pages Array of pages to save.
	 * @param string $post_type Post type.
	 * @param int $parent_id Parent ID.
	 */
	public function save_pages( array &$pages, string $post_type, $parent_id = null ) {
		foreach( $pages as &$page ) {
			if( isset( $page['name'] ) ) {
				$inserted = wp_insert_post( apply_filters( 'qcp/insert_post_args', (array) [
					'post_title' => $page['name'],
					'post_content' => '',
					'post_type' => $post_type,
					'post_name' => $page['slug'] ?? false,
					'post_parent' => $parent_id ?? 0,
					'post_status' => 'publish',
				] ), true );

				if( is_wp_error( $inserted ) ) {
					$this->errors[] = $inserted->get_error_message();
				} else {
					add_post_meta( $inserted, 'qcp_batch_id', $this->batch_id );

					if( isset( $page['children'] ) && $page['children'] ) {
						$this->save_pages( $page['children'], $post_type, $inserted );
					}
				}
			}
		}
	}
}
