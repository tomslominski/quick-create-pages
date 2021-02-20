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
		$post_type = $_POST['qcp']['post_type'] ?? 'page';
		$pages = $_POST['qcp']['pages'] ?? [];
		$default = [
			[
				'name' => '',
				'slug' => '',
			],
		];

		if( !isset( $_POST['qcp']['nonce'] ) || !wp_verify_nonce( $_POST['qcp']['nonce'], 'qcp_create_pages' ) ) {
			$this->errors[] = __( 'An error occurred. Please try creating the pages again.', 'quick-create-pages' );
		}

		if( $pages && $pages !== $default ) {
			$this->save_pages( $pages, $post_type );

			if( !$this->errors ) {
				$this->messages[] = __( 'Pages created successfully.', 'quick-create-pages' );
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
