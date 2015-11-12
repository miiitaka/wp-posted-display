<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
new Posts_Browsing_History_Uninstall();

/**
 * Plugin Uninstall
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 */
class Posts_Browsing_History_Uninstall {

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$this->drop_table();
	}

	/**
	 * Drop Table.
	 *
	 * @since 1.0.0
	 */
	private function drop_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . "posted_display";
		$wpdb->query( "DROP TABLE IF EXISTS " . $table_name );
	}
}