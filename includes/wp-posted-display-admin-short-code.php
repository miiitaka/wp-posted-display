<?php
/**
 * Admin ShortCode Settings
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 */
class Posted_Display_ShortCode {

	/**
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $text_domain = 'wp-posted-display';

	/**
	 * ShortCode Display.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $args
	 * @return string $html
	 */
	public function short_code_display( $args ) {
		extract( shortcode_atts( array (
			'id'    => "",
			'posts' => "5",
			'sort'  => 0
		), $args ) );

		$instance = array(
			'id'    => $id,
			'posts' => $posts,
			'sort'  => $sort
		);

		/** DB Connect */
		$db = new Posted_Display_Admin_Db();
		$results = $db->get_options( esc_html( (int) $id ) );
		$html = '';

		if ( $results ) {
			$cookie_name = $this->text_domain . '-' . esc_html( (int) $id );
			$query_args = $this->set_query( $results, $instance, $cookie_name );

			wp_reset_query();
			$query = new WP_Query( $query_args );

			if ( $query->have_posts() ) {
				/** Display ShortCode body. */
				$html = '<ul>' . PHP_EOL;

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
					$html .= '<li>' . PHP_EOL;
					$html .= $this->set_template( $results['template'], get_the_title(), get_the_excerpt(), $images[0], get_the_time( get_option( 'date_format' ) ), get_the_permalink() );
					$html .= '</li>' . PHP_EOL;
				}

				$html .= '</ul>';
			}
			wp_reset_query();
		}
		return $html;
	}

	/**
	 * Query Settings.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  array  $results
	 * @param  array  $instance
	 * @param  string $cookie_name
	 * @return array  $args
	 */
	private function set_query( $results, $instance, $cookie_name ) {
		/** Common Items Set */
		$args = array(
				"post_status"         => "publish",
				"posts_per_page"      => esc_html( $instance['posts'] ),
				"ignore_sticky_posts" => 1
		);

		switch ( $instance['sort'] ) {
			case 0:
				$args += array(
						"orderby"  => "post__in",
						"order"    => "ASC"
				);
				break;
			case 1:
				$args += array(
						"orderby"  => "date",
						"order"    => "DESC"
				);
				break;
			case 2:
				$args += array(
						"orderby"  => "date",
						"order"    => "ASC"
				);
				break;
			case 3:
				$args += array(
						"orderby"  => "rand"
				);
				break;
		}

		switch ( $results['type'] ) {
			case "Cookie":
				if ( isset( $_COOKIE[$cookie_name] ) ) {
					$args += array( "post__in" => array_reverse( explode( ',', esc_html( $_COOKIE[$cookie_name] ) ) ) );
				} else {
					$args = array();
				}
				break;
			case "Posts":
				$args += array( "post__in" => explode( ',', esc_html( $results['output_data']) ) );
				break;
			case "Categories":
				$args += array( "category__in" => explode( ',', esc_html( $results['output_data']) ) );
				break;
			case "Tags":
				$args += array( "tag__in" => explode( ',', esc_html( $results['output_data']) ) );
				break;
			case "Users":
				$args += array( "author__in" => explode( ',', esc_html( $results['output_data']) ) );
				break;
		}

		return (array) $args;
	}

	/**
	 * Template replace.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $template
	 * @param  string $title
	 * @param  string $excerpt
	 * @param  string $image
	 * @param  string $date
	 * @param  string $link
	 * @return string $template
	 */
	private function set_template( $template, $title, $excerpt, $image, $date, $link ) {
		$template = str_replace( '##title##',   esc_html( $title ),   $template );
		$template = str_replace( '##summary##', esc_html( $excerpt ), $template );
		$template = str_replace( '##image##',   esc_html( $image ),   $template );
		$template = str_replace( '##date##',    esc_html( $date ),    $template );
		$template = str_replace( '##link##',    esc_url( $link ),     $template );
		$template = str_replace( '\\', '', $template );

		/** Escape */
		$template = preg_replace('!<script.*?>.*?</script.*?>!is', '', $template );
		$template = preg_replace('!onerror=".*?"!is', '', $template );
		return (string) $template;
	}
}