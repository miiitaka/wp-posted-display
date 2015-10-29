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
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $text_domain = 'wp-posts-browsing-history';

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$this->drop_table();
		$this->delete_cookie();
	}

	/**
	 * Delete Cookie.
	 *
	 * @since 1.0.0
	 */
	private function delete_cookie() {
		setcookie( $this->text_domain, '', time() - 60 * 60 * 24 * 7, '/', $_SERVER['SERVER_NAME'] );
	}

	/**
	 * Drop Table.
	 *
	 * @since 1.0.0
	 */
	private function drop_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . "posts_browsing_history";
		$wpdb->query( "DROP TABLE IF EXISTS " . $table_name );
	}
}