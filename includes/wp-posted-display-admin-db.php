<?php
/**
 * Admin DB Connection
 *
 * @author  Kazuya Takami
 * @since   1.0.0
 */
class Posted_Display_Admin_Db {

	/**
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $table_name;

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'posted_display';
	}

	/**
	 * Create Table.
	 *
	 * @since 1.0.0
	 */
	public function create_table() {
		global $wpdb;

		$prepared     = $wpdb->prepare( "SHOW TABLES LIKE %s", $this->table_name );
		$is_db_exists = $wpdb->get_var( $prepared );

		if ( is_null( $is_db_exists ) ) {
			$charset_collate = $wpdb->get_charset_collate();

			$query  = " CREATE TABLE " . $this->table_name;
			$query .= " (id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY";
			$query .= ",type tinytext NOT NULL";
			$query .= ",template_name tinytext NOT NULL";
			$query .= ",template text NOT NULL";
			$query .= ",template_no_image text";
			$query .= ",save_term int DEFAULT 7";
			$query .= ",save_item int DEFAULT 10";
			$query .= ",output_data tinytext";
			$query .= ",register_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
			$query .= ",update_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
			$query .= ",UNIQUE KEY id (id)) " . $charset_collate;

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $query );
		}
	}

	/**
	 * Get Data.
	 *
	 * @since  1.0.0
	 * @param  integer $id
	 * @return array   $args
	 */
	public function get_options( $id ) {
		global $wpdb;

		$query    = "SELECT * FROM " . $this->table_name . " WHERE id = %d";
		$data     = array( $id );
		$prepared = $wpdb->prepare( $query, $data );

		return (array) $wpdb->get_row( $prepared );
	}

	/**
	 * Get All Data.
	 *
	 * @since  1.0.0
	 * @param  string $type
	 * @return array  $results
	 */
	public function get_list_options( $type = null ) {
		global $wpdb;

		if ( $type === 'Cookie' ) {
			$query    = "SELECT * FROM " . $this->table_name . " WHERE type = %s ORDER BY update_date DESC";
			$data     = array( $type );
			$prepared = $wpdb->prepare( $query, $data );
		} else {
			$prepared = "SELECT * FROM " . $this->table_name . " ORDER BY update_date DESC";
		}
		return (array) $wpdb->get_results( $prepared );
	}

	/**
	 * Insert Data.
	 *
	 * @since  1.0.0
	 * @param  array $post($_POST)
	 * @return integer $id
	 */
	public function insert_options( array $post ) {
		global $wpdb;

		$data = array(
			'template_name'     => strip_tags( $post['template_name'] ),
			'type'              => strip_tags( $post['type'] ),
			'template'          => preg_replace('!<script.*?>.*?</script.*?>!is', '', $post['template'] ),
			'template_no_image' => strip_tags( $post['template_no_image'] ),
			'save_term'         => $post['save_term'],
			'save_item'         => $post['save_item'],
			'output_data'       => strip_tags( $post['output_data'] ),
			'register_date'     => date( "Y-m-d H:i:s" ),
			'update_date'       => date( "Y-m-d H:i:s" )
		);
		$prepared = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s'
		);

		$wpdb->insert( $this->table_name, $data, $prepared );
		return (int) $wpdb->insert_id;
	}

	/**
	 * Update Data.
	 *
	 * @since 1.0.0
	 * @param array $post($_POST)
	 */
	public function update_options( array $post ) {
		global $wpdb;

		$data = array(
			'template_name'     => strip_tags( $post['template_name'] ),
			'type'              => strip_tags( $post['type'] ),
			'template'          => preg_replace('!<script.*?>.*?</script.*?>!is', '', $post['template'] ),
			'template_no_image' => strip_tags( $post['template_no_image'] ),
			'save_term'         => $post['save_term'],
			'save_item'         => $post['save_item'],
			'output_data'       => strip_tags( $post['output_data'] ),
			'update_date'       => date( "Y-m-d H:i:s" )
		);
		$key = array( 'id' => esc_html( $post['id'] ) );
		$prepared = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s',
			'%s'
		);
		$key_prepared = array( '%d' );

		$wpdb->update( $this->table_name, $data, $key, $prepared, $key_prepared );
	}

	/**
	 * Delete Data.
	 *
	 * @since 1.0.0
	 * @param integer $id
	 */
	public function delete_options( $id ) {
		global $wpdb;

		$key = array( 'id' => esc_html( $id ) );
		$key_prepared = array( '%d' );

		$wpdb->delete( $this->table_name, $key, $key_prepared );

		/** Widget delete. */
		$options = get_option( 'widget_posted_display_widget' );

		if ( $options ) {
			foreach ( $options as $key => $option ) {
				if ( isset( $option['template'] ) && $option['template'] == $id) {
					unset( $options[$key] );
				}
			}
			update_option( 'widget_posted_display_widget', $options );
		}
	}
}