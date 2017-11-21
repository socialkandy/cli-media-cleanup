<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Implements Media Cleanup Command.
 */
class Media_Cleanup_Command {
	/**
	 * Cleanup unused medias.
	 *
	 * [--missing-attachments]
	 * : Clean files with no attachment.
	 *
	 * [--missing-files]
	 * : Clean attachments with no file.
	 *
	 * [--dry-run]
	 * : Checks how many entries will be deleted
	 *
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		if ( empty( $assoc_args ) ) {
			WP_CLI::line( 'usage: wp media cleanup [--dry-run] [--missing-files] [--missing-attachments]' );
		}

		if ( isset( $assoc_args['dry-run'] ) ) {
			$attachments_count = count( $this->get_attachments_missing_files() );
			$files_count       = count( $this->get_files_missing_attachments() );

			WP_CLI::line( sprintf( 'There are %d empty attachments to clean.', $attachments_count ) );
			WP_CLI::line( sprintf( 'There are %d files with no attachment to clean.', $files_count ) );
		}
	}

	/**
	 * Get all attachments IDs that are missing files.
	 */
	private function get_attachments_missing_files() {
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

		return $missing_files;
	}

	/**
	 * Get all files that have no attachments.
	 */
	private function get_files_missing_attachments() {
		return array(
			'/path/to/wp-content/file-1.jpg',
			'/path/to/wp-content/file-2.jpg',
			'/path/to/wp-content/file-3.jpg',
		);
	}
}
WP_CLI::add_command( 'media cleanup', 'Media_Cleanup_Command' );
