<?php
/**
 * Posted Display Admin Setting
 *
 * @author  Kazuya Takami
 * @version 2.0.5
 * @since   1.0.0
 */
class Posted_Display_Admin_Post {

	/**
	 * Variable definition.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private $text_domain;
	private $type_args = array( 'Cookie', 'Posts', 'Categories', 'Tags', 'Users' );

	/**
	 * Constructor Define.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   String $text_domain
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
			"type"              => "Cookie",
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
		if ( isset( $_POST['posted_display_id'] ) && is_numeric( $_POST['posted_display_id'] ) ) {
			$db->update_options( $_POST );
			$options['id'] = $_POST['posted_display_id'];
			$status = "ok";
		} else {
			if ( isset( $_POST['posted_display_id'] ) && $_POST['posted_display_id'] === '' ) {
				$options['id'] = $db->insert_options( $_POST );
				$status = "ok";
			}
		}

		/** Mode Judgment */
		if ( isset( $options['id'] ) && is_numeric( $options['id'] ) ) {
			$options = $db->get_options( $options['id'] );
		}

		$this->set_custom_post();
		$this->page_render( $options, $status );
	}

	/**
	 * Setting custom post array.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	private function set_custom_post () {
		$args = array(
			'public'   => true,
			'_builtin' => false
		);
		$post_types = get_post_types( $args, 'objects' );

		foreach ( $post_types as $post_type ) {
			$this->type_args[] = $post_type->name;
		}
	}

	/**
	 * Setting Page of the Admin Screen.
	 *
	 * @version 2.0.6
	 * @since   1.0.0
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
		$html .= '<input type="hidden" name="posted_display_id" value="' . esc_attr( $options['id'] ) . '">';
		echo $html;

		/** Common settings */
		$html  = '<table class="wp-posted-display-admin-table">';
		$html .= '<caption>' . esc_html__( 'Common settings', $this->text_domain ) . '</caption>';
		$html .= '<tr><th><label for="template_name">' . esc_html__( 'Template Name', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="template_name" id="template_name" class="regular-text" required autofocus value="';
		$html .= esc_attr( $options['template_name'] ) . '">';
		$html .= '</td></tr>';
		$html .= '<tr><th><label for="wp-posted-display-type">' . esc_html__( 'Type', $this->text_domain ) . ':</label></th><td>';
		$html .= '<select name="type" id="wp-posted-display-type">';
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
		$html .= '<p id="template_item">';
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
		$html .= '<input type="text" name="template_no_image" id="template_no_image" class="regular-text" value="';
		$html .= esc_attr( $options['template_no_image'] ) . '">';
		$html .= '<button id="media-upload" class="schema-admin-media-button dashicons-before dashicons-admin-media"><span>Add Media</span></button>';
		$html .= '<p>' . esc_html__( 'It specifies the posts of Alternative Image path that does not set the featured image.', $this->text_domain ) . '</p>';
		$html .= '</td></tr>';
		$html .= '</table>';
		echo $html;

		$html  = '<table class="wp-posted-display-admin-table" id="wp-posted-display-type-cookie">';
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

		$html  = '<table class="wp-posted-display-admin-table" id="wp-posted-display-type-categories">';
		$html .= '<caption>' . esc_html__( 'Type: Category settings', $this->text_domain ) . '</caption>';
		$html .= '<tr><th><label for="categories_output_data">' . esc_html__( 'Output Category ID', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="categories_output_data" id="categories_output_data" class="regular-text" placeholder="e.g. 1,2,3" value="';
		$html .= esc_attr( $options['output_data'] );
		$html .= '">';
		$html .= '</td></tr>';
		$html .= '</table>';
		echo $html;

		$html  = '<table class="wp-posted-display-admin-table" id="wp-posted-display-type-tags">';
		$html .= '<caption>' . esc_html__( 'Type: Tag settings', $this->text_domain ) . '</caption>';
		$html .= '<tr><th><label for="tags_output_data">' . esc_html__( 'Output Tag ID', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="tags_output_data" id="tags_output_data" class="regular-text" placeholder="e.g. 1,2,3" value="';
		$html .= esc_attr( $options['output_data'] );
		$html .= '">';
		$html .= '</td></tr>';
		$html .= '</table>';
		echo $html;

		$html  = '<table class="wp-posted-display-admin-table" id="wp-posted-display-type-users">';
		$html .= '<caption>' . esc_html__( 'Type: User settings', $this->text_domain ) . '</caption>';
		$html .= '<tr><th><label for="users_output_data">' . esc_html__( 'Output User ID', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="users_output_data" id="users_output_data" class="regular-text" placeholder="e.g. 1,2,3" value="';
		$html .= ( $options['type'] === 'Users') ? esc_attr( $options['output_data'] ) : '';
		$html .= '">';
		$html .= '</td></tr>';
		$html .= '</table>';
		echo $html;

		$html  = '<table class="wp-posted-display-admin-table" id="wp-posted-display-type-posts">';
		$html .= '<caption>' . esc_html__( 'Type: Posts settings', $this->text_domain ) . '</caption>';
		$html .= '<tr><th><label for="posts_output_data">' . esc_html__( 'Output Posts ID', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="posts_output_data" id="posts_output_data" class="regular-text" placeholder="e.g. 1,2,3" value="';
		$html .= esc_attr( $options['output_data'] );
		$html .= '">';
		$html .= '<p>' . esc_html__( 'Ignore post__in if you do not set it.', $this->text_domain ) . '</p>';
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
	 * @version 1.0.0
	 * @since   1.0.0
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