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
					printf( '<option value="%d" selected="selected">%s</option>', $row->id, esc_html( $row->template_name ) );
				} else {
					printf( '<option value="%d">%s</option>', $row->id, esc_html( $row->template_name ) );
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
		$cookie_name = $this->text_domain . '-' . esc_html( $instance['template'] );

		if ( isset( $_COOKIE[$cookie_name] ) && isset( $instance['posts'] ) ) {
			/** DB Connect */
			$db = new Posts_Browsing_History_Admin_Db();
			$results = $db->get_options( esc_html( $instance['template'] ) );

			if ( $results ) {
				$query_args = array(
					"posts_per_page"      => esc_html( $instance['posts'] ),
					"post_status"         => "publish",
					"ignore_sticky_posts" => 1,
					"post__in"            => array_reverse( explode( ',', esc_html( $_COOKIE[$cookie_name] ) ) ),
					"orderby"             => 'post__in',
					"order"               => 'DESC'
				);
				wp_reset_query();
				$query = new WP_Query( $query_args );

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
							if ( isset( $results['template_no_image'] ) ) {
								$images[0] = $results['template_no_image'];
							} else {
								$images[0] = '';
							}
						}
						echo '<li>' . PHP_EOL;
						$this->set_template(
							$results['template'],
							esc_html( get_the_title() ),
							esc_html( get_the_excerpt() ),
							$images[0],
							esc_html( get_the_time( get_option( 'date_format' ) ) ),
							esc_url( get_the_permalink() )
						);
						echo '</li>' . PHP_EOL;
					}

					echo '</ul>';
					echo $args['after_widget'];
				}
				wp_reset_query();
			}
		}
	}

	/**
	 * Widget Display.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $template
	 * @param  string $title
	 * @param  string $excerpt
	 * @param  string $image
	 * @param  string $date
	 * @param  string $link
	 */
	private function set_template( $template, $title, $excerpt, $image, $date, $link ) {
		$template = str_replace( '##title##',   $title,   $template );
		$template = str_replace( '##summary##', $excerpt, $template );
		$template = str_replace( '##image##',   $image,   $template );
		$template = str_replace( '##date##',    $date,    $template );
		$template = str_replace( '##link##',    $link,    $template );
		$template = str_replace( '\\',          '',       $template );

		/** Escape */
		$template = preg_replace('!<script.*?>.*?</script.*?>!is', '', $template );
		$template = preg_replace('!onerror=".*?"!is', '', $template );
		echo $template;
	}
}