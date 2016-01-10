<?php
/**
 * Posted Display Admin Setting
 *
 * @author Kazuya Takami
 * @since  1.0.0
 */
class Posted_Display_Admin_Post {

	/**
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $text_domain;
	private $type_args = array( 'Cookie', 'Posts', 'Categories', 'Tags', 'Users' );

	/**
	 * Constructor Define.
	 *
	 * @since 1.0.0
	 * @param String $text_domain
	 */
	public function __construct ( $text_domain ) {
		$this->text_domain = $text_domain;

		/**
		 * Update Status
		 *
		 * "ok" : Successful update
		 */
		$status = "";

		/** DB Connect */
		$db = new Posted_Display_Admin_Db();

		/** Set Default Parameter for Array */
		$options = array(
			"id"                => "",
			"type"              => "",
			"template_name"     => "",
			"template"          => '<img src="##image##">' . PHP_EOL . '<span>##date##</span>' . PHP_EOL . '<span><a href="##link##">##title##</a></span>',
			"template_no_image" => "",
			"save_term"         => 7,
			"save_item"         => 10,
			"output_data"       => ""
		);

		/** Key Set */
		if ( isset( $_GET['posted_display_id'] ) && is_numeric( $_GET['posted_display_id'] ) ) {
			$options['id'] = esc_html( $_GET['posted_display_id'] );
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
	 * @since   1.0.0
	 * @version 1.0.6
	 * @param   array  $options
	 * @param   string $status
	 */
	private function page_render ( array $options, $status ) {
		$html  = '';
		$html .= '<div class="wrap">';
		$html .= '<h1>' . esc_html__( 'Posted Display Settings', $this->text_domain ) . '</h1>';
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
		echo $html;

		/** Common settings */
		$html  = '<table class="wp-posted-display-admin-table">';
		$html .= '<caption>' . esc_html__( 'Common settings', $this->text_domain ) . '</caption>';
		$html .= '<tr><th><label for="template_name">' . esc_html__( 'Template Name', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="template_name" id="template_name" class="regular-text" required autofocus value="';
		$html .= esc_attr( $options['template_name'] ) . '">';
		$html .= '</td></tr>';
		$html .= '<tr><th><label for="type">' . esc_html__( 'Type', $this->text_domain ) . ':</label></th><td>';
		$html .= '<select name="type" id="type">';
		foreach ( $this->type_args as $value ) {
			$html .= '<option value="' . $value . '"';
			$html .= ( $options['type'] === $value ) ? ' selected=selected' : '';
			$html .= '>' . $value;
		}
		$html .= '</select>';
		$html .= '</td></tr>';
		$html .= '<tr><th><label for="template">' . esc_html__( 'Template', $this->text_domain ) . ':</label></th><td>';
		$html .= '<p>';
		$html .= esc_html__( 'Child elements of the li element is markup.', $this->text_domain ) . '<br>';
		$html .= esc_html__( 'Date, post title, post summary, tags, categories, author name, you can view the featured image.', $this->text_domain ) . '<br>';
		$html .= esc_html__( 'Please set as "##item##" the items to be displayed.', $this->text_domain );
		$html .= '</p>';
		$html .= '<p>';
		$html .= '<span>##date##</span>';
		$html .= '<span>##title##</span>';
		$html .= '<span>##summary##</span>';
		$html .= '<span>##image##</span>';
		$html .= '<span>##link##</span>';
		$html .= '<span>##tag##</span>';
		$html .= '<span>##category##</span>';
		$html .= '<span>##author_name##</span>';
		$html .= '</p>';
		$html .= '<textarea name="template" id="template" rows="10" cols="50" class="large-text code">' . $template = str_replace( '\\', '', $options['template'] ) . '</textarea>';
		$html .= '</td></tr>';
		$html .= '<tr><th><label for="template_no_image">' . esc_html__( 'No Image Path', $this->text_domain ) . ':</label></th><td>';
		$html .= '<p>' . esc_html__( 'It specifies the posts of Alternative Image path that does not set the featured image.', $this->text_domain ) . '</p>';
		$html .= '<input type="text" name="template_no_image" id="template_no_image" class="regular-text" value="';
		$html .= esc_attr( $options['template_no_image'] ) . '">';
		$html .= '</td></tr>';
		$html .= '</table>';
		echo $html;

		/** Type: Cookie settings */
		$html  = '<table class="wp-posted-display-admin-table">';
		$html .= '<caption>' . esc_html__( 'Type: Cookie settings', $this->text_domain ) . '</caption>';
		$html .= '<tr><th><label for="save_term">' . esc_html__( 'Save Term', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="number" name="save_term" id="save_term" required class="small-text" min="1" max="30" value="';
		$html .= esc_attr( $options['save_term'] ) . '">' . esc_html__( 'day', $this->text_domain );
		$html .= '</td></tr>';
		$html .= '<tr><th><label for="save_item">' . esc_html__( 'Save Item', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="number" name="save_item" id="save_item" required class="small-text" min="1" max="30" value="';
		$html .= esc_attr( $options['save_item'] ) . '">' . esc_html__( 'item', $this->text_domain );
		$html .= '</td></tr>';
		$html .= '</table>';
		echo $html;

		/** Type: Posts, Categories, Tags, Users settings */
		$html  = '<table class="wp-posted-display-admin-table">';
		$html .= '<caption>' . esc_html__( 'Type: Posts, Categories, Tags, Users settings', $this->text_domain ) . '</caption>';
		$html .= '<tr><th><label for="output_data">' . esc_html__( 'Output Data', $this->text_domain ) . ':</label></th><td>';
		$html .= '<p>';
		$html .= esc_html__( 'The specified in a comma-separated posts ID, category ID, tag ID, user ID respectively.', $this->text_domain );
		$html .= '</p>';
		$html .= '<input type="text" name="output_data" id="output_data" class="regular-text" placeholder="e.g. 1,2,3" value="';
		$html .= esc_attr( $options['output_data'] ) . '">';
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
	private function information_render () {
		$html  = '<div id="message" class="updated notice notice-success is-dismissible below-h2">';
		$html .= '<p>Posted Display Information Update.</p>';
		$html .= '<button type="button" class="notice-dismiss">';
		$html .= '<span class="screen-reader-text">Dismiss this notice.</span>';
		$html .= '</button>';
		$html .= '</div>';

		echo $html;
	}
}