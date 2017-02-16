<?php
/**
 * Admin Widget Register
 *
 * @author  Kazuya Takami
 * @version 1.1.2
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
	private $sort_array  = array( 'Input order', 'Date descending order', 'Date ascending order', 'Random' );

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
	 * @version 1.2.3
	 * @since   1.0.0
	 * @access  public
	 * @param   array $instance
	 * @return  string Parent::Default return is 'noform'
	 */
	public function form ( $instance ) {
		/** DB Connect */
		$db = new Posted_Display_Admin_Db();

		$results = $db->get_list_options();

		if ( $results ) {
			if ( !isset( $instance['title'] ) ) {
				$instance['title'] = "";
			}
			if ( !isset( $instance['template'] ) ) {
				$instance['template'] = "";
			}
			if ( !isset( $instance['sort'] ) ) {
				$instance['sort'] = 0;
			}
			if ( !isset( $instance['posts'] ) ) {
				$instance['posts'] = 5;
			}

			$id   = $this->get_field_id( 'title' );
			$name = $this->get_field_name( 'title' );
			echo '<p><label for="' . $id . '">' . esc_html__( 'Title', $this->text_domain ) . ':</label><br>';
			printf( '<input type="text" id="%s" name="%s" value="%s" class="widefat">', $id, $name, esc_attr( $instance['title'] ) );
			echo '</p>';

			$id   = $this->get_field_id( 'template' );
			$name = $this->get_field_name( 'template' );
			echo '<p><label for="' . $id . '">' . esc_html__( 'Template', $this->text_domain ) . ':</label><br>';
			printf( '<select id="%s" name="%s" class="widefat">', $id, $name );
			foreach ( $results as $row ) {
				if ( $row->id === $instance['template'] ) {
					printf( '<option value="%d" selected="selected">%s</option>', $row->id, esc_html( $row->template_name ) );
				} else {
					printf( '<option value="%d">%s</option>', $row->id, esc_html( $row->template_name ) );
				}
			}
			echo '</select></p>';

			$id   = $this->get_field_id( 'sort' );
			$name = $this->get_field_name( 'sort' );
			echo '<p><label for="' . $id . '">' . esc_html__( 'Sorted by', $this->text_domain ) . ':</label><br>';
			printf( '<select id="%s" name="%s" class="widefat">', $id, $name );
			foreach ( $this->sort_array as $key => $row ) {
				if ( $key == $instance['sort'] ) {
					printf( '<option value="%d" selected="selected">%s</option>', $key, esc_html( $row ) );
				} else {
					printf( '<option value="%d">%s</option>', $key, esc_html( $row ) );
				}
			}
			echo '</select></p>';

			$id   = $this->get_field_id( 'posts' );
			$name = $this->get_field_name( 'posts' );
			echo '<p><label for="' . $id . '">' . esc_html__( 'Number of posts to show', $this->text_domain ) . ':&nbsp;</label>';
			printf( '<input type="number" id="%s" name="%s" value="%s" class="small-text">', $id, $name, esc_attr( $instance['posts'] ) );
			echo '</p>';
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

		return (array) $new_instance;
	}

	/**
	 * Widget Display.
	 *
	 * @version 1.1.1
	 * @since   1.0.0
	 * @access  public
	 * @param   array $args
	 * @param   array $instance
	 */
	public function widget ( $args, $instance ) {
		/** DB Connect */
		$db      = new Posted_Display_Admin_Db();
		$results = $db->get_options( esc_html( $instance['template'] ) );

		if ( $results ) {
			$cookie_name = $this->text_domain . '-' . esc_html( $instance['template'] );
			$query_args  = $db->set_query( $results, $instance, $cookie_name );
			$query       = new WP_Query( $query_args );

			if ( $query->have_posts() ) {
				/** Display widget header. */
				echo $args['before_widget'] . PHP_EOL;
				echo $args['before_title'] . PHP_EOL;
				echo esc_html( $instance['title'] ) . PHP_EOL;
				echo $args['after_title'] . PHP_EOL;

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
}