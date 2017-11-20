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
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		WP_CLI::success( $args[0] . implode( ', ', $assoc_args ) );
	}
}
WP_CLI::add_command( 'media cleanup', 'Media_Cleanup_Command' );
