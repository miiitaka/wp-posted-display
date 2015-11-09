<?php
/**
 * Admin ShortCode Register
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 */
class Posted_Display_ShortCode extends WP_Widget {

	/**
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $text_domain = 'wp-posted-display';

	/**
	 * Constructor Define.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

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
		$cookie_name = $this->text_domain;
		if ( isset( $instance['template'] ) ) {
			$cookie_name = '-' . esc_html( $instance['template'] );
		}

		/** DB Connect */
		$db = new Posted_Display_Admin_Db();
		$results = $db->get_options( esc_html( $instance['template'] ) );

		if ( $results ) {
			$query_args = $this->set_query( $results, esc_html( $instance['posts'] ), $cookie_name );

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

	/**
	 * Widget Display.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  array  $results
	 * @param  int    $posts
	 * @param  string $cookie_name
	 * @return array  $args
	 */
	private function set_query( $results, $posts, $cookie_name ) {
		/** Common Items Set */
		$args = array(
			"post_status"         => "publish",
			"posts_per_page"      => $posts,
			"ignore_sticky_posts" => 1
		);

		switch ( $results['type'] ) {
			case "Cookie":
				$args += array(
					"post__in" => array_reverse( explode( ',', esc_html( $_COOKIE[$cookie_name] ) ) ),
					"orderby"  => "post__in",
					"order"    => "DESC"
				);
				break;
			case "Any posts":
				$args += array(
					"post__in" => explode( ',', esc_html( $results['output_data']) ),
					"orderby"  => "post__in",
					"order"    => "ASC"
				);
				break;
			case "Categories":
				$args += array(
					"category__in" => explode( ',', esc_html( $results['output_data']) ),
					"orderby"      => "date",
					"order"        => "DESC"
				);
				break;
			case "Tags":
				$args += array(
					"tag__in" => explode( ',', esc_html( $results['output_data']) ),
					"orderby" => "date",
					"order"   => "DESC"
				);
				break;
		}

		return $args;
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