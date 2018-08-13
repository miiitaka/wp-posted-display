<?php
/**
 * Admin Widget Register
 *
 * @author  Kazuya Takami
 * @version 2.2.0
 * @since   1.0.0
 */
class Posted_Display_Widget extends WP_Widget {

	/**
	 * Variable definition.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private $text_domain = 'wp-posted-display';

	/**
	 * Variable definition.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private $sort_array = array(
		'Input order',
		'Date descending order',
		'Date ascending order',
		'Random'
	);

	/**
	 * Variable definition.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	private $target_array = array(
		'all'    => 'All Users',
		'login'  => 'Logged-in users',
		'logout' => 'Logged-out users',
	);

	/**
	 * Constructor Define.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @access  public
	 */
	public function __construct () {
		$widget_options = array( 'description' => esc_html__( 'Posted Display Widget', $this->text_domain ) );
		parent::__construct( false, esc_html__( 'Posted Display', $this->text_domain ), $widget_options );
	}

	/**
	 * Widget Form Display.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 * @access  public
	 * @param   array $instance
	 * @return  string Parent::Default return is 'noform'
	 */
	public function form ( $instance ) {
		/** DB Connect */
		$db = new Posted_Display_Admin_Db();

		$results      = $db->get_list_options();
		$option_array = $this->get_default_options();

		if ( $results ) {
			$option_array = array_merge( $option_array, $instance );

			/** Title form setting */
			$this->form_input_text( 'title', 'Title', $option_array['title'] );

			/** Title link form setting */
			$this->form_checkbox( 'link', 'Title link setting ( Single category / tag / author only )', $option_array['link'] );

			/** Title link form setting */
			$this->form_checkbox( 'blank', 'Show title link in a new window', $option_array['blank'] );

			/** Template form setting */
			$this->form_select_template( 'template', 'Template', $option_array['template'], $results );

			/** Sort form setting */
			$this->form_select_default( 'sort', 'Sorted by', $option_array['sort'], $this->sort_array );

			/** Posts form setting */
			$this->form_input_number( 'posts', 'Number of posts to show', $option_array['posts'] );

			/** Target form setting */
			$this->form_select_default( 'target', 'Widget display target', $option_array['target'], $this->target_array );
		} else {
			$post_url = admin_url() . 'admin.php?page=' . $this->text_domain . '/includes/wp-posted-display-admin-post.php';
			echo '<p><a href="' . $post_url . '">' . esc_html__( 'Please register of template.', $this->text_domain ) . '</a></p>';
		}
	}

	/**
	 * Widget Form Update.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @access  public
	 * @param   array $new_instance
	 * @param   array $old_instance
	 * @return  array Parent::Settings to save or bool false to cancel saving.
	 */
	public function update ( $new_instance, $old_instance ) {
		if ( isset( $new_instance['posts'] ) ) {
			if ( !is_numeric( $new_instance['posts'] ) ) {
				$new_instance['posts'] = 5;
			}
		} else {
			$new_instance['posts'] = 5;
		}
		$new_instance['link']  = isset( $new_instance['link'] )  ? 'on' : '';
		$new_instance['blank'] = isset( $new_instance['blank'] ) ? 'on' : '';

		$instance['title']    = sanitize_text_field( $new_instance['title'] );
		$instance['template'] = sanitize_text_field( $new_instance['template'] );
		$instance['sort']     = sanitize_text_field( $new_instance['sort'] );
		$instance['target']   = sanitize_text_field( $new_instance['target'] );

		return (array) $new_instance;
	}

	/**
	 * Widget Display.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 * @access  public
	 * @param   array $args
	 * @param   array $instance
	 */
	public function widget ( $args, $instance ) {
		if ( is_user_logged_in() && isset( $instance['target'] ) && $instance['target'] === 'logout' ) {
			return;
		}
		if ( !is_user_logged_in() && isset( $instance['target'] ) && $instance['target'] === 'login' ) {
			return;
		}

		/** DB Connect */
		if ( isset( $instance['template'] ) ) {
			$db      = new Posted_Display_Admin_Db();
			$results = $db->get_options( esc_html( $instance['template'] ) );
		} else {
			$results = __return_false();
		}

		if ( $results ) {
			$cookie_name = $this->text_domain . '-' . esc_html( $instance['template'] );
			list( $query_args, $permalink ) = $db->set_query( $results, $instance, $cookie_name );

			$query = new WP_Query( $query_args );

			if ( $query->have_posts() ) {
				/** Display widget header. */
				echo $args['before_widget'] . PHP_EOL;

				if ( !empty( $instance['title'] ) ) {
					echo $args['before_title'] . PHP_EOL;

					if ( $instance['link'] === 'on' && !empty( $permalink ) ) {
						if ($instance['blank'] === 'on') {
							echo '<a href="' . $permalink . '"' . ' target="_blank">';
						} else {
							echo '<a href="' . $permalink . '">';
						}
						echo esc_html( $instance['title'] ) . '</a>' . PHP_EOL;
					} else {
						echo esc_html( $instance['title'] ) . PHP_EOL;
					}
					echo $args['after_title'] . PHP_EOL;
				}

				/** Display widget body. */
				echo '<ul>' . PHP_EOL;

				while ( $query->have_posts() ) {
					$query->the_post();

					if ( has_post_thumbnail( get_the_ID() ) ) {
						$images = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
					} else {
						$images[0] = isset( $results['template_no_image'] ) ? $results['template_no_image'] : '';
					}
					echo '<li>' . PHP_EOL;

					$items = array(
						"title"       => get_the_title(),
						"excerpt"     => get_the_excerpt(),
						"image"       => $images[0],
						"date"        => get_the_time( get_option( 'date_format' ) ),
						"link"        => get_the_permalink(),
						"tag"         => get_the_tag_list( '', '', '' ),
						"category"    => get_the_category_list( '', '', get_the_ID() ),
						"author_name" => get_the_author()
					);

					echo $db->set_template( $results['template'], $items );
					echo '</li>' . PHP_EOL;
				}
				wp_reset_postdata();

				echo '</ul>';
				echo $args['after_widget'];
			}
		}
	}

	/**
	 * Return the default options array
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @return  array $args
	 */
	private function get_default_options () {
		$args['title']    = '';
		$args['link']     = '';
		$args['blank']    = '';
		$args['template'] = '';
		$args['sort']     = 0;
		$args['posts']    = 5;
		$args['target']   = 'all';

		return (array) $args;
	}

	/**
	 * Create form text
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @param   string $field
	 * @param   string $label
	 * @param   string $value
	 */
	private function form_input_text ( $field, $label, $value ) {
		$id   = $this->get_field_id( $field );
		$name = $this->get_field_name( $field );

		printf( '<p><label for="%s">%s:</label><br>', $id, esc_html__( $label, $this->text_domain ) );
		printf( '<input type="text" id="%s" name="%s" value="%s" class="widefat"></p>', $id, $name, esc_attr( $value ) );
	}

	/**
	 * Create form number
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @param   string $field
	 * @param   string $label
	 * @param   string $value
	 */
	private function form_input_number ( $field, $label, $value ) {
		$id   = $this->get_field_id( $field );
		$name = $this->get_field_name( $field );

		printf( '<p><label for="%s">%s:</label>', $id, esc_html__( $label, $this->text_domain ) );
		printf( '<input type="number" id="%s" name="%s" value="%s" class="small-text"></p>', $id, $name, esc_attr( $value ) );
	}

	/**
	 * Create form select (Template)
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @param   string $field
	 * @param   string $label
	 * @param   string $value
	 * @param   array  $results
	 */
	private function form_select_template ( $field, $label, $value, $results ) {
		$id   = $this->get_field_id( $field );
		$name = $this->get_field_name( $field );

		printf( '<p><label for="%s">%s:</label>', $id, esc_html__( $label, $this->text_domain ) );

		printf( '<select id="%s" name="%s" class="widefat">', $id, $name );
		foreach ( $results as $row ) {
			if ( $row->id === $value ) {
				printf( '<option value="%d" selected="selected">%s</option>', $row->id, esc_html( $row->template_name ) );
			} else {
				printf( '<option value="%d">%s</option>', $row->id, esc_html( $row->template_name ) );
			}
		}
		printf( '</select></p>' );
	}

	/**
	 * Create form select (Default)
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @param   string $field
	 * @param   string $label
	 * @param   string $value
	 * @param   array  $results
	 */
	private function form_select_default ( $field, $label, $value, $results ) {
		$id   = $this->get_field_id( $field );
		$name = $this->get_field_name( $field );

		printf( '<p><label for="%s">%s:</label>', $id, esc_html__( $label, $this->text_domain ) );

		printf( '<select id="%s" name="%s" class="widefat">', $id, $name );
		foreach ( $results as $key => $row ) {
			if ( $key == $value ) {
				printf( '<option value="%d" selected="selected">%s</option>', $key, esc_html( $row ) );
			} else {
				printf( '<option value="%d">%s</option>', $key, esc_html( $row ) );
			}
		}
		printf( '</select></p>' );
	}

	/**
	 * Widget Form Checkbox.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @access  private
	 * @param   string  $field
	 * @param   string  $label
	 * @param   boolean $value
	 */
	public function form_checkbox ( $field, $label, $value ) {
		$id   = $this->get_field_id( $field );
		$name = $this->get_field_name( $field );

		if ( $value === 'on' ) {
			printf( '<p><input type="checkbox" id="%s" name="%s" class="checkbox" checked="checked">', $id, $name );
		} else {
			printf( '<p><input type="checkbox" id="%s" name="%s" class="checkbox">', $id, $name );
		}
		printf( '<label for="%s">%s</label></p>', $id, $label );
	}
}