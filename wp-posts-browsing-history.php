<?php
/*
Plugin Name: Posts Browsing History
Plugin URI: https://github.com/miiitaka/wp-posts-browsing-history
Description: Posts Browsing History Widget & Short Code Add.
Version: 1.0.0
Author: Kazuya Takami
Author URI: http://programp.com/
License: GPLv2 or later
Text Domain: wp-posts-browsing-history
*/
new Posts_Browsing_History();

/**
 * Basic Class
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 */
class Posts_Browsing_History {

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 */
	public function __construct () {
		if ( is_admin() ) {
			$this->widget_register();
		} else {
			$this->set_cookie();
		}
	}

	/**
	 * Widget Register.
	 *
	 * @since 1.0.0
	 */
	private function widget_register () {
		require_once(  plugin_dir_path( __FILE__ ) . 'wp-posts-browsing-history-widget.php' );
		add_action( 'widgets_init', function () {
			register_widget( 'Posts_Browsing_History_Widget' );
		});
	}

	/**
	 * Set Cookie.
	 *
	 * @since 1.0.0
	 */
	private function set_cookie () {
		add_action( 'get_header', function () {
			if ( is_single() ) {
				setcookie( 'test', 'true', time() + 60 * 60 * 24 * 7, '/', $_SERVER["SERVER_NAME"] );
			}
		} );
	}
}