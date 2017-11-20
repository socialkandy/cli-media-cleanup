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
	 * : Clean files without an attachment.
	 *
	 * [--missing-files]
	 * : Clean attachments withour an file.
	 *
	 * [--dry-run]
	 * : Checks how many entries will be deleted
	 *
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		if ( $assoc_args['dry-run'] ) {
			WP_CLI::line( count( $this->get_attachments_missing_files() ) );
		} else {
			WP_CLI::success( 'Command running' );
		}
	}

	/**
	 * Gets attachments IDs that are missing files.
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
}
WP_CLI::add_command( 'media cleanup', 'Media_Cleanup_Command' );
