<?php
/*
Plugin Name: Posted display in the widget and the short code
Plugin URI: https://github.com/miiitaka/wp-posted-display
Description: Posted display in the widget and the short code
Version: 1.0.0
Author: Kazuya Takami
Author URI: http://programp.com/
License: GPLv2 or later
Text Domain: wp-posted-display
Domain Path: /languages
*/
require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posted-display-admin-db.php' );

new Posted_Display();

/**
 * Basic Class
 *
 * @author  Kazuya Takami
 * @since   1.0.0
 * @version 1.0.0
 */
class Posted_Display {

	/**
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $text_domain = 'wp-posted-display';

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 */
	public function __construct () {
		$db = new Posted_Display_Admin_Db( $this->text_domain );
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
		wp_register_style( 'wp-posted-display-admin-style', plugins_url( 'css/style.css', __FILE__ ) );
	}

	/**
	 * Widget Register.
	 *
	 * @since 1.0.0
	 */
	public function widget_init () {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posted-display-admin-widget.php' );
		register_widget( 'Posted_Display_Widget' );
	}

	/**
	 * Add Menu to the Admin Screen.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_menu_page(
			esc_html__( 'Posted Display Settings', $this->text_domain ),
			esc_html__( 'Posted Display Settings', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'list_page_render' )
		);
		add_submenu_page(
			__FILE__,
			esc_html__( 'All Settings', $this->text_domain ),
			esc_html__( 'All Settings', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'list_page_render' )
		);
		$page = add_submenu_page(
			__FILE__,
			esc_html__( 'Posted Display', $this->text_domain ),
			esc_html__( 'Add New', $this->text_domain ),
			'manage_options',
			plugin_dir_path( __FILE__ ) . 'includes/wp-posted-display-admin-post.php',
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
		wp_enqueue_style( 'wp-posted-display-admin-style' );
	}

	/**
	 * Admin List Page Template Require.
	 *
	 * @since 1.0.0
	 */
	public function list_page_render() {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posted-display-admin-list.php' );
		new Posted_Display_Admin_List( $this->text_domain );
	}

	/**
	 * Admin Post Page Template Require.
	 *
	 * @since 1.0.0
	 */
	public function post_page_render() {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posted-display-admin-post.php' );
		new Posted_Display_Admin_Post( $this->text_domain );
	}

	/**
	 * Set Cookie.
	 *
	 * @since 1.0.0
	 */
	public function get_header () {
		/** DB Connect */
		$db = new Posted_Display_Admin_Db();

		/** DB table get list */
		$results = $db->get_list_options();

		foreach ( $results as $row ) {
			$cookie_name = $this->text_domain . '-' . esc_html( $row->id );

			if ( is_single() ) {
				global $post;
				$args = array();

				if ( $post->post_status === 'publish' ) {

					/** Cookie data read and convert string from array. */
					if ( isset( $_COOKIE[$cookie_name] ) ) {
						$args = explode( ',', esc_html( $_COOKIE[$cookie_name] ) );
					}

					/** Existence check. */
					$position = array_search( $post->ID, $args );
					if ( is_numeric( $position ) ) {
						unset( $args[$position] );
					}

					/** Cookie data add and Array reverse. */
					$args[] = ( string ) $post->ID;

					if ( count( $args ) > $row->save_item ) {
						array_shift( $args );
					}

					setcookie( $cookie_name, implode( ',', $args ), time() + 60 * 60 * 24 * $row->save_term, '/', $_SERVER['SERVER_NAME'] );
				}
			}
		}
	}
}