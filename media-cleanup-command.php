<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Cleanup invalid files and attachments.
 *
 * ## EXAMPLES
 *
 *     # Cleanup all invalid files and attachments.
 *     $ wp media cleanup
 *     Success: All invalid files and attachments clean.
 *
 *     # Cleanup all invalid attachments.
 *     $ wp media cleanup --attachments-only
 *     Success: All invalid attachments clean.
 *
 * @package wp-cli
 */
class Media_Cleanup_Command {
	/**
	 * All attachments in current installation.
	 *
	 * @var array.
	 */
	private $attachments;

	/**
	 * Delete files with no attachments and attachments with no file.
	 *
	 * [--dry-run]
	 * : Checks how many entries will be deleted
	 *
	 * [--attachments-only]
	 * : Clean files with no existing attachment.
	 *
	 * [--files-only]
	 * : Clean attachments with no existing file.
	 *
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		if ( empty( $assoc_args ) ) {
			WP_CLI::log( 'usage: wp media cleanup [--dry-run] [--files-only] [--attachments-only]' );
		}

		if ( empty( $this->attachments ) ) {
			$this->attachments = get_posts( array(
				'post_type'      => 'attachment',
				'posts_per_page' => -1,
			) );
		}

		if ( isset( $assoc_args['dry-run'] ) ) {
			$attachments_count = count( $this->get_attachments_missing_files() );
			WP_CLI::log( sprintf( 'There are %d empty attachments to clean.', $attachments_count ) );

			$files_count       = count( $this->get_files_missing_attachments() );
			WP_CLI::log( sprintf( 'There are %d files with no attachment to clean.', $files_count ) );
		}
	}

	/**
	 * Get all attachments IDs that are missing files.
	 */
	private function get_attachments_missing_files() {
		$missing_files = array();

		WP_CLI::log( 'Scanning attachments...' );

		foreach ( $this->attachments as $attachment ) {
			$attached_file = get_attached_file( $attachment->ID );

			if ( ! file_exists( $attached_file ) ) {
				$missing_files[] = $attachment->ID;
			}
		}

		return $missing_files;
	}

	/**
	 * Get all files that have no attachments.
	 */
	private function get_files_missing_attachments() {
		global $wpdb;

		$missing_attachments = array();
		$uploads             = wp_upload_dir();
		$path                = trailingslashit( $uploads['basedir'] );

		WP_CLI::log( 'Scanning uploads folder: ' . $path );

		$iterator = new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS );
		$files    = iterator_to_array( new RecursiveIteratorIterator( $iterator ) );
		$count    = 0;

		WP_CLI::log( sprintf( 'There are %d files in total.', count( $files ) ) );

		foreach ( $files as $filepath => $file ) {
			// Relative path to uploads folder.
			$filename      = basename( $filepath );
			$relative_path = _wp_relative_upload_path( $filepath );

			// If it's not a hidden file.
			if ( strpos( $filename, '.' ) !== 0 ) {
				/**
				 * @todo Make this query look for meta_key = '_wp_attachment_metadata' AND meta_value LIKE '$relative_path'
				 */
				$sql = $wpdb->get_results( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value = '$relative_path'" );

				if ( $sql ) {
					$count++;
					unset( $files[ $filepath ] );
				}
			}
		}

		WP_CLI::log( sprintf( 'There are %d files with valid attachments.', $count ) );

		return $files;
	}
}
WP_CLI::add_command( 'media cleanup', 'Media_Cleanup_Command' );
