<?php
/**
 * Admin Widget Register
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 */
class Posts_Browsing_History_Widget extends WP_Widget {

	/**
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $text_domain = 'wp-posts-browsing-history';

	/**
	 * Constructor Define.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {
		$widget_options = array( 'description' => 'Posts Browsing History Widget' );
		parent::__construct( false, 'Posts Browsing History', $widget_options );
	}

	/**
	 * Widget Form Display.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $instance
	 * @return string Parent::Default return is 'noform'
	 */
	public function form( $instance ) {
		/** DB Connect */
		$db = new Posts_Browsing_History_Admin_Db();

		$results = $db->get_list_options();

		if ( $results ) {
			if ( !isset( $instance['title'] ) ) {
				$instance['title'] = "";
			}
			if ( !isset( $instance['template'] ) ) {
				$instance['template'] = "";
			}
			if ( !isset( $instance['posts'] ) ) {
				$instance['posts'] = 0;
			}

			$id = $this->get_field_id( 'title' );
			$name = $this->get_field_name( 'title' );
			echo '<p><label for="' . $id . '">' . esc_html__( 'Title', $this->text_domain ) . ':</label><br>';
			printf( '<input type="text" id="%s" name="%s" value="%s" class="widefat">', $id, $name, esc_attr( $instance['title'] ) );
			echo '</p>';

			$id = $this->get_field_id( 'template' );
			$name = $this->get_field_name( 'template' );
			echo '<p><label for="' . $id . '">' . esc_html__( 'Template', $this->text_domain ) . ':</label><br>';
			printf( '<select id="%s" name="%s" class="widefat">', $id, $name );
			foreach ( $results as $row ) {
				if ( $row->id === $instance['template'] ) {
					printf( '<option value="%d" selected="selected">%s</option>', $row->id, $row->template_name );
				} else {
					printf( '<option value="%d">%s</option>', $row->id, $row->template_name );
				}
			}
			echo '</select></p>';

			$id = $this->get_field_id( 'posts' );
			$name = $this->get_field_name( 'posts' );
			echo '<p><label for="' . $id . '">' . esc_html__( 'Number of posts to show', $this->text_domain ) . ':</label>';
			printf( '<input type="text" id="%s" name="%s" value="%s" size="3">', $id, $name, esc_attr( $instance['posts'] ) );
			echo '</p>';
		} else {
			$post_url = admin_url() . 'admin.php?page=' . $this->text_domain . '/includes/wp-posts-browsing-admin-post.php';
			echo '<p><a href="' . $post_url . '">' . esc_html__( 'Please register of template.', $this->text_domain ) . '</a></p>';
		}
	}

	/**
	 * Widget Form Update.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array Parent::Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		if ( isset( $new_instance['posts'] ) ) {
			if ( !is_numeric( $new_instance['posts'] ) ) {
				$new_instance['posts'] = 0;
			}
		} else {
			$new_instance['posts'] = 0;
		}

		return $new_instance;
	}

	/**
	 * Widget Display.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @param  array $instance
	 */
	public function widget( $args, $instance ) {
		if ( isset( $_COOKIE[$this->text_domain] ) && isset( $instance['posts'] ) ) {
			if ( $instance['posts'] > 0 ) {
				$query_args = array(
					"post__in"       => explode( ',', esc_html( $_COOKIE[$this->text_domain] ) ),
					"posts_per_page" => esc_html( $instance['posts'] ),
					"post_status"    => "publish"
				);

				$query = new WP_Query($query_args);

				if ( $query->have_posts() ) {
					/** Display widget header. */
					echo $args['before_widget'];
					echo $args['before_title'];
					echo esc_html( $instance['title'] );
					echo $args['after_title'];

					/** Display widget body. */
					echo '<ul>';

					while ( $query->have_posts() ) {
						echo '<li>';
						echo '<span>' . esc_html( the_time( get_option( 'date_format' ) ) ) . '</span>';
						echo '<span>' . esc_html( the_title() ) . '</span>';
						echo '</li>';

						$query->the_post();
					}

					echo '</ul>';
					echo $args['after_widget'];

					wp_reset_postdata();
				}
			}
		}
	}
}