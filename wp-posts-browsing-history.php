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
Domain Path: /languages
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
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $domain_name = 'wp-posts-browsing-history';

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 */
	public function __construct () {
		add_action( 'widgets_init', array( $this, 'widget_init' ) );
		add_action( 'get_header',   array( $this, 'get_header' ) );
	}

	/**
	 * Widget Register.
	 *
	 * @since 1.0.0
	 */
	public function widget_init () {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posts-browsing-history-widget.php' );
		register_widget( 'Posts_Browsing_History_Widget' );
	}

	/**
	 * Set Cookie.
	 *
	 * @since 1.0.0
	 */
	public function get_header () {
		if ( is_single() ) {
			global $post;
			$array = array();

			if ( $post->post_status === 'publish' ) {

				/** Cookie data read and convert string from array. */
				if ( isset( $_COOKIE[$this->domain_name] ) ) {
					$array = explode( ',', esc_html( $_COOKIE[$this->domain_name] ) );
				}

				/** Existence check. */
				$position = array_search( $post->ID, $array );
				if ( is_numeric( $position ) ) {
					unset( $array[$position] );
				}

				/** Cookie data add and Array reverse. */
				$array[] = ( string ) $post->ID;
				$array = array_reverse( $array );

				if ( count( $array ) > 10 ) {
					array_pop( $array );
				}

				setcookie( $this->domain_name, implode( ',', $array ), time() + 60 * 60 * 24 * 7, '/', $_SERVER['SERVER_NAME'] );
			}
		}
	}
}