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
require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posts-browsing-admin-db.php' );

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
	private $text_domain = 'wp-posts-browsing-history';

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 */
	public function __construct () {
		$db = new Posts_Browsing_History_Admin_Db( $this->text_domain );
		$db->create_table();

		add_action( 'widgets_init', array( $this, 'widget_init' ) );

		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		} else {
			add_action( 'get_header', array( $this, 'get_header' ) );
		}
	}

	/**
	 * admin init.
	 *
	 * @since   1.0.0
	 */
	public function admin_init() {
		wp_register_style( 'wp-posts-browsing-history-admin-style', plugins_url( 'css/style.css', __FILE__ ) );
	}

	/**
	 * Widget Register.
	 *
	 * @since 1.0.0
	 */
	public function widget_init () {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posts-browsing-admin-widget.php' );
		register_widget( 'Posts_Browsing_History_Widget' );
	}

	/**
	 * Add Menu to the Admin Screen.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_menu_page(
			esc_html__( 'Browsing History Setting', $this->text_domain ),
			esc_html__( 'Browsing History Setting', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'list_page_render' )
		);
		add_submenu_page(
			__FILE__,
			esc_html__( 'Settings All', $this->text_domain ),
			esc_html__( 'Settings All', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'list_page_render' )
		);
		$page = add_submenu_page(
			__FILE__,
			esc_html__( 'Posts Browsing History', $this->text_domain ),
			esc_html__( 'Add New', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'post_page_render' )
		);

		/** Using registered $page handle to hook stylesheet loading */
		add_action( 'admin_print_styles-' . $page, array( $this, 'add_style' ) );
	}

	/**
	 * CSS admin add.
	 *
	 * @since 1.0.0
	 */
	public function add_style() {
		wp_enqueue_style( 'wp-posts-browsing-history-admin-style' );
	}

	/**
	 * Admin List Page Template Require.
	 *
	 * @since 1.0.0
	 */
	public function list_page_render() {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posts-browsing-admin-list.php' );
		new Posts_Browsing_History_Admin_List( $this->text_domain );
	}

	/**
	 * Admin Post Page Template Require.
	 *
	 * @since 1.0.0
	 */
	public function post_page_render() {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posts-browsing-admin-post.php' );
		new Posts_Browsing_History_Admin_Post( $this->text_domain );
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
				if ( isset( $_COOKIE[$this->text_domain] ) ) {
					$array = explode( ',', esc_html( $_COOKIE[$this->text_domain] ) );
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

				setcookie( $this->text_domain, implode( ',', $array ), time() + 60 * 60 * 24 * 7, '/', $_SERVER['SERVER_NAME'] );
			}
		}
	}
}