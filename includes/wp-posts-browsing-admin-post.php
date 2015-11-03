<?php
/**
 * Posts Browsing History Admin Setting
 *
 * @author Kazuya Takami
 * @since  1.0.0
 */
class Posts_Browsing_History_Admin_Post {

	/**
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $text_domain;

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 * @param String $text_domain
	 */
	public function __construct( $text_domain ) {
		$this->text_domain = $text_domain;

		/**
		 * Update Status
		 *
		 * "ok"     : Successful update
		 * "output" : Output No Check
		 */
		$status = "";

		/** DB Connect */
		$db = new Posts_Browsing_History_Admin_Db();

		/** Set Default Parameter for Array */
		$options = array(
			"id"            => "",
			"template_name" => "",
			"template"      => "",
			"storage_life"  => 7
		);

		/** Key Set */
		if ( isset( $_GET['posts_browsing_id'] ) && is_numeric( $_GET['posts_browsing_id'] ) ) {
			$options['id'] = esc_html( $_GET['posts_browsing_id'] );
		}

		/** DataBase Update & Insert Mode */
		if ( isset( $_POST['id'] ) && is_numeric( $_POST['id'] ) ) {
			$db->update_options( $_POST );
			$options['id'] = $_POST['id'];
			$status = "ok";
		} else {
			if ( isset( $_POST['id'] ) && $_POST['id'] === '' ) {
				$options['id'] = $db->insert_options( $_POST );
				$status = "ok";
			}
		}

		/** Mode Judgment */
		if ( isset( $options['id'] ) && is_numeric( $options['id'] ) ) {
			$options = $db->get_options( $options['id'] );
		}

		$this->page_render( $options, $status );
	}

	/**
	 * Setting Page of the Admin Screen.
	 *
	 * @since 1.0.0
	 * @param array $options
	 * @param string $status
	 */
	private function page_render( array $options, $status ) {
		$html  = '';
		$html .= '<div class="wrap">';
		$html .= '<h1>' . esc_html__( 'Posts Browsing History Settings', $this->text_domain ) . '</h1>';
		echo $html;

		switch ( $status ) {
			case "ok":
				$this->information_render();
				break;
			default:
				break;
		}

		$html  = '<hr>';
		$html .= '<form method="post" action="">';
		$html .= '<input type="hidden" name="id" value="' . esc_attr( $options['id'] ) . '">';
		$html .= '<table class="wp-post-browsing-history-admin-table">';
		$html .= '<tr><th><label for="template_name">' . esc_html__( 'Template Name', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="template_name" id="template_name" class="regular-text" required value="' . esc_attr( $options['template_name'] ) . '">';
		$html .= '</td></tr>';
		$html .= '<tr><th>' . esc_html__( 'Template', $this->text_domain ) . ':</th><td>';
		$html .= '<textarea name="template" rows="10" cols="50" class="large-text code">' . $options['template'] . '</textarea>';
		$html .= '</td></tr>';
		$html .= '<tr><th><label for="storage_life">' . esc_html__( 'Storage Life', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="number" name="storage_life" id="storage_life" required value="' . esc_attr( $options['storage_life'] ) . '">';
		$html .= '</td></tr>';
		$html .= '</table>';
		echo $html;

		submit_button();

		$html  = '</form></div>';
		echo $html;
	}

	/**
	 * Information Message Render
	 *
	 * @since 1.0.0
	 */
	private function information_render() {
		$html  = '<div id="message" class="updated notice notice-success is-dismissible below-h2">';
		$html .= '<p>Posts Browsing History Information Update.</p>';
		$html .= '<button type="button" class="notice-dismiss">';
		$html .= '<span class="screen-reader-text">Dismiss this notice.</span>';
		$html .= '</button>';
		$html .= '</div>';

		echo $html;
	}
}