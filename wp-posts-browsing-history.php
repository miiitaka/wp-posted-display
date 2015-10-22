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
 * @since   1.0.0
 * @version 1.0.0
 */
class Posts_Browsing_History {

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 */
	public function __construct () {
		$this->widget_register();

		if ( !is_admin() ) {
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
				global $post;

				/** Cookie data read and convert string from array. */
				$array = array();
				if ( isset( $_COOKIE['wp-posts-browsing-history'] ) ) {
					$array = explode( ',', esc_html( $_COOKIE['wp-posts-browsing-history'] ) );
				}

				/** Existence check. */
				$position = array_search( $post->ID, $array, true );
				if ( is_numeric( $position ) ) {
					unset( $array[$position] );
				}

				/** Cookie data add and Array reverse. */
				$array[] = $post->ID;
				$array = array_reverse( $array );

				if ( count( $array) > 10 ) {
					array_pop( $array );
				}

				setcookie( 'wp-posts-browsing-history', implode( ',', $array ), time() + 60 * 60 * 24 * 7, '/', $_SERVER['SERVER_NAME'] );
			}
		} );
	}
}