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
		if ( isset( $assoc_args['attachments-only'], $assoc_args['files-only'] ) ) {
			WP_CLI::error( 'Use just \'wp media cleanup\' instead.' );
		}

		if ( ! isset( $assoc_args['files-only'] ) ) {
			$attachments = $this->get_invalid_attachments();

			if ( ! isset( $assoc_args['dry-run'] ) ) {
				WP_CLI::confirm( sprintf( 'Are you sure you want to delete %d invalid attachments?', count( $attachments ) ), $assoc_args );
				// $this->delete_attachments( $attachments );
			}
		}

		if ( ! isset( $assoc_args['attachments-only'] ) ) {
			$files = $this->get_invalid_files();

			if ( ! isset( $assoc_args['dry-run'] ) ) {
				WP_CLI::confirm( sprintf( 'Are you sure you want to delete %d invalid files?', count( $files ) ), $assoc_args );
				// $this->delete_files( $files );
			}
		}
	}

	/**
	 * Get all attachments IDs that are missing files.
	 */
	private function get_invalid_attachments() {
		WP_CLI::log( 'Scanning attachments...' );

		$missing_files = array();
		$attachments   = get_posts( array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
		) );

		foreach ( $attachments as $attachment ) {
			$attached_file = get_attached_file( $attachment->ID );

			if ( ! file_exists( $attached_file ) ) {
				$missing_files[] = $attachment->ID;
			}
		}

		WP_CLI::log( sprintf( 'You have %d attachments with no file associated.', count( $missing_files ) ) );

		return $missing_files;
	}

	/**
	 * Get all files that have no attachments.
	 */
	private function get_invalid_files() {
		global $wpdb;

		$missing_attachments = array();
		$uploads             = wp_upload_dir();
		$path                = trailingslashit( $uploads['basedir'] );

		WP_CLI::log( 'Scanning uploads folder: ' . $path );

		$iterator    = new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS );
		$files       = iterator_to_array( new RecursiveIteratorIterator( $iterator ) );
		$valid_files = 0;

		WP_CLI::log( sprintf( 'You have %d files in total.', count( $files ) ) );

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
					$valid_files++;
					unset( $files[ $filepath ] );
				}
			}
		}

		WP_CLI::log( sprintf( 'There are %d files with valid attachments.', $valid_files ) );
		WP_CLI::log( sprintf( 'There are %d files with no attachment associated.', count( $files ) ) );

		return $files;
	}
}
WP_CLI::add_command( 'media cleanup', 'Media_Cleanup_Command' );
