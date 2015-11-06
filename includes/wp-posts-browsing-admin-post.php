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
			"id"                => "",
			"template_name"     => "",
			"template"          => '<figure><img src="##image##"></figure>' . PHP_EOL . '<span>##date##</span>' . PHP_EOL . '<span><a href="##link##">##title##</a></span>',
			"template_no_image" => "",
			"storage_life"      => 7
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
		$html .= '<tr><th><label for="template">' . esc_html__( 'Template', $this->text_domain ) . ':</label></th><td>';
		$html .= '<p>';
		$html .= esc_html__( 'Child elements of the li element is markup.', $this->text_domain ) . '&nbsp;';
		$html .= esc_html__( 'Date, post title, post summary, you can view the featured image.', $this->text_domain ) . '<br>';
		$html .= esc_html__( 'Please set as "##item##" the items to be displayed.', $this->text_domain );
		$html .= '</p>';
		$html .= '<ul>';
		$html .= '<li>&dash;&nbsp;' . esc_html__( 'The date', $this->text_domain )        . '<span>##date##</span></li>';
		$html .= '<li>&dash;&nbsp;' . esc_html__( 'Post title', $this->text_domain )      . '<span>##title##</span></li>';
		$html .= '<li>&dash;&nbsp;' . esc_html__( 'Posted overview', $this->text_domain ) . '<span>##summary##</span></li>';
		$html .= '<li>&dash;&nbsp;' . esc_html__( 'Featured image', $this->text_domain )  . '<span>##image##</span></li>';
		$html .= '<li>&dash;&nbsp;' . esc_html__( 'Post link', $this->text_domain )       . '<span>##link##</span></li>';
		$html .= '</ul>';
		$html .= '<textarea name="template" id="template" rows="10" cols="50" class="large-text code">' . $template = str_replace( '\\', '', $options['template'] ) . '</textarea>';
		$html .= '</td></tr>';
		$html .= '<tr><th><label for="template_no_image">' . esc_html__( 'No Image Path', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="template_no_image" id="template_no_image" class="regular-text" value="' . esc_attr( $options['template_no_image'] ) . '">';
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