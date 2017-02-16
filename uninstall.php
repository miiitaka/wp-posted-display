<?php
/**
 * Plugin Uninstall
 *
 * @author  Kazuya Takami
 * @version 1.0.3
 * @since   1.0.0
 */

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
new Posted_Display_Uninstall();

class Posted_Display_Uninstall {

	/**
	 * Constructor Define.
	 *
	 * @since   1.0.0
	 * @version 1.0.3
	 */
	public function __construct () {
		$this->drop_table();
		delete_option( 'widget_posted_display_widget' );
	}

	/**
	 * Drop Table.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private function drop_table () {
		global $wpdb;
		$table_name = $wpdb->prefix . "posted_display";
		$wpdb->query( "DROP TABLE IF EXISTS " . $table_name );
	}
}