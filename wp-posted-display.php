<?php
/*
Plugin Name: WordPress Posted Display
Plugin URI: https://github.com/miiitaka/wp-posted-display
Description: Plug-in Posted Display Widget & ShortCode Add. You can also save and display your browsing history to Cookie.
Version: 1.1.0
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
 * @version 1.1.0
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
	 * @since   1.0.0
	 * @version 1.1.0
	 */
	public function __construct () {
		register_activation_hook( __FILE__, array( $this, 'create_table' ) );
		add_shortcode( $this->text_domain, array( $this, 'short_code_init' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'widgets_init',   array( $this, 'widget_init' ) );

		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		} else {
			add_action( 'get_header', array( $this, 'get_header' ) );
		}
	}

	/**
	 * Create table.
	 *
	 * @since 1.0.2
	 */
	public function create_table () {
		$db = new Posted_Display_Admin_Db( $this->text_domain );
		$db->create_table();
	}

	/**
	 * i18n.
	 *
	 * @since   1.0.0
	 */
	public function plugins_loaded () {
		load_plugin_textdomain( $this->text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
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
	 * ShortCode Register.
	 *
	 * @since  1.0.0
	 * @param  string $args short code params
	 * @return string
	 */
	public function short_code_init ( $args ) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posted-display-admin-short-code.php' );
		$obj = new Posted_Display_ShortCode( $args );
		return $obj->short_code_display( $args );
	}

	/**
	 * admin init.
	 *
	 * @since   1.0.0
	 */
	public function admin_init () {
		wp_register_style( 'wp-posted-display-admin-style', plugins_url( 'css/style.css', __FILE__ ) );
	}

	/**
	 * Add Menu to the Admin Screen.
	 *
	 * @since   1.0.0
	 * @version 1.1.0
	 */
	public function admin_menu () {
		add_menu_page(
			esc_html__( 'Posted Display Settings', $this->text_domain ),
			esc_html__( 'Posted Display Settings', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'list_page_render' )
		);
		$list_page = add_submenu_page(
			__FILE__,
			esc_html__( 'All Settings', $this->text_domain ),
			esc_html__( 'All Settings', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'list_page_render' )
		);
		$post_page = add_submenu_page(
			__FILE__,
			esc_html__( 'Posted Display', $this->text_domain ),
			esc_html__( 'Add New',        $this->text_domain ),
			'manage_options',
			plugin_dir_path( __FILE__ ) . 'includes/wp-posted-display-admin-post.php',
			array( $this, 'post_page_render' )
		);

		add_action( 'admin_head-'          . $post_page, array( $this, 'post_delete_cookie' ) );
		add_action( 'admin_head-'          . $list_page, array( $this, 'list_delete_cookie' ) );
		add_action( 'admin_print_styles-'  . $post_page, array( $this, 'add_style' ) );
		add_action( 'admin_print_styles-'  . $list_page, array( $this, 'add_style' ) );
		add_action( 'admin_print_scripts-' . $post_page, array( $this, 'admin_scripts') );
	}

	/**
	 * admin_scripts
	 *
	 * @since   1.1.0
	 * @version 1.1.0
	 */
	public function admin_scripts () {
		wp_enqueue_script( 'wp-posted-display-admin-main-js', plugins_url( 'js/main.js', __FILE__ ), array('jquery'), '1.0' );
	}

	/**
	 * CSS admin add.
	 *
	 * @since 1.0.0
	 */
	public function add_style () {
		wp_enqueue_style( 'wp-posted-display-admin-style' );
	}

	/**
	 * Admin List Page Template Require.
	 *
	 * @since 1.0.0
	 */
	public function list_page_render () {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-posted-display-admin-list.php' );
		new Posted_Display_Admin_List( $this->text_domain );
	}

	/**
	 * Admin Post Page Template Require.
	 *
	 * @since 1.0.0
	 */
	public function post_page_render () {
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
		$results = $db->get_list_options( 'Cookie' );

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

					setcookie( $cookie_name, implode( ',', $args ), time() + 86400 * $row->save_term, '/', $_SERVER['SERVER_NAME'] );
				}
			}
		}
	}

	/**
	 * Delete Cookie information If you change the type from the Edit.
	 *
	 * @since 1.0.0
	 */
	public function post_delete_cookie () {
		if ( isset( $_POST['id'] ) && is_numeric( $_POST['id'] ) ) {
			$db = new Posted_Display_Admin_Db();

			$options = $db->get_options( $_POST['id'] );
			if ( $options['type'] === 'Cookie' && $_POST['type'] !== 'Cookie' ) {
				$cookie_name = $this->text_domain . '-' . esc_html( $_POST['id'] );
				setcookie( $cookie_name, '', time() - 3600, '/', $_SERVER['SERVER_NAME'] );
			}
		}
	}

	/**
	 * Delete Cookie information If you have deleted from the list.
	 *
	 * @since 1.0.0
	 */
	public function list_delete_cookie () {
		if ( isset( $_GET['posted_display_id'] ) && is_numeric( $_GET['posted_display_id'] ) ) {
			if ( isset( $_GET['mode'] ) && $_GET['mode'] === 'delete' ) {
				$db = new Posted_Display_Admin_Db();

				$options = $db->get_options( $_GET['posted_display_id'] );
				if ( $options['type'] === 'Cookie' ) {
					$cookie_name = $this->text_domain . '-' . esc_html( $_GET['posted_display_id'] );
					setcookie( $cookie_name, '', time() - 3600, '/', $_SERVER['SERVER_NAME'] );
				}
			}
		}
	}

	/**
	 * Remove all the cookie information when uninstalling
	 *
	 * @since 1.0.0
	 */
	public function uninstall_delete_cookie () {
		/** DB Connect */
		$db = new Posted_Display_Admin_Db();

		/** DB table get list */
		$results = $db->get_list_options( 'Cookie' );

		foreach ( $results as $row ) {
			$cookie_name = $this->text_domain . '-' . esc_html( $row->id );
			setcookie( $cookie_name, '', time() - 3600, '/', $_SERVER['SERVER_NAME'] );
		}
	}
}