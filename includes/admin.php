<?php
/**
 * Schema.org Admin Setting
 *
 * @author Kazuya Takami
 * @since  1.0.0
 */
class Posts_Browsing_History_Admin {

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
		$db = new Posts_Browsing_History_Admin_Db();
		$options = $db->get_options(1);
		$this->page_render( $options );
	}

	/**
	 * Setting Page of the Admin Screen.
	 *
	 * @since 1.0.0
	 * @param array $options
	 */
	private function page_render( $options ) {
		$html  = '';
		$html .= '<div class="wrap">';
		$html .= '<h1>' . esc_html__( 'Posts Browsing History Settings', $this->text_domain ) . '</h1>';
		$html .= '<form method="post" action="">';
		$html .= '<input type="hidden" name="id" value="' . esc_attr( $options['id'] ) . '">';
		$html .= '<table class="wp-post-browsing-history-admin-table">';
		$html .= '<tr><th><label for="template_name">' . esc_html__( 'Template Name', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="template_name" id="template_name" class="regular-text" required value="' . esc_attr( $options['template_name'] ) . '">';
		$html .= '</td></tr>';
		$html .= '<tr><th>' . esc_html__( 'Output Template', $this->text_domain ) . ':</th><td>';
		$html .= '<textarea>' . $options['template'] . '</textarea>';
		$html .= '</td></tr>';
		$html .= '</table>';
		echo $html;

		submit_button();

		$html  = '</form></div>';
		echo $html;
	}
}