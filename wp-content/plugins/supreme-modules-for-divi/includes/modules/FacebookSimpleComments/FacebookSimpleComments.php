<?php

class DSM_FacebookSimpleComments extends ET_Builder_Module {

	public $slug       = 'dsm_facebook_comments';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://suprememodules.com/',
		'author'     => 'Supreme Modules',
		'author_uri' => 'https://suprememodules.com/',
	);

	public function init() {
		$this->name = esc_html__( 'Supreme Facebook Comments', 'dsm-supreme-modules-for-divi' );
		$this->icon             = 'z';
		// Toggle settings
		$this->settings_modal_toggles  = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Facebook Comments Settings', 'dsm-supreme-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
				),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'text' => false,
			'fonts' => false,
			'background' => array(
				'css'     => array(
					'main' => '%%order_class%%',
				),
				'options' => array(
					'parallax_method' => array(
						'default' => 'off',
					),
				),
			),
			'max_width'  => array(
				'css'     => array(
					'main' => '%%order_class%%',
				),
			),
			'borders' => false,
			'box_shadow' => false,
			'filters' => false,
		);
	}

	public function get_fields() {
		return array(
			'fb_app_id' => array(
				'label'           => esc_html__( 'App ID', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'description'     => sprintf( esc_html__( 'Enter the Facebook App ID. You can go to <a href="%1$s">Facebook Developer</a> and click on Create New App to get one.'), 'https://developers.facebook.com/', 'dsm-supreme-modules-for-divi' ),
			),
			'page_url' => array(
				'label'           => esc_html__( 'URL', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the website URL.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'main_content',
				'default_on_front' => 'https://www.facebook.com/divisupreme/',
			),
			'num_posts' => array(
				'label'           => esc_html__( 'Number of Posts', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'description'     => esc_html__( 'The number of comments to show by default. The minimum value is 1.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => '10',
			),
			'color_scheme' => array(
				'label'            => esc_html__( 'Color Scheme', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'light' => esc_html__( 'Light', 'dsm-supreme-modules-for-divi' ),
					'dark'  => esc_html__( 'Dark', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'main_content',
				'description'      => esc_html__( 'The color scheme used by the comments plugin. Can be "light" or "dark".', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'light',
			),
			'order_by' => array(
				'label'            => esc_html__( 'Order By', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'social' => esc_html__( 'Social', 'dsm-supreme-modules-for-divi' ),
					'reverse_time'  => esc_html__( 'Reverse Time', 'dsm-supreme-modules-for-divi' ),
					'time'  => esc_html__( 'Time', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'main_content',
				'description'     => sprintf( esc_html__( 'The order to use when displaying comments. Can be "social", "reverse_time", or "time". The different order types are explained <a href="%1$s">in the FAQ</a>'), 'https://developers.facebook.com/docs/plugins/comments/#faqorder', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'social',
			),
		);
	}

	public function render( $attrs, $content = null, $render_slug ) {
		$fb_app_id = $this->props['fb_app_id'];
		$page_url = $this->props['page_url'];
		$num_posts = $this->props['num_posts'];
		$color_scheme = $this->props['color_scheme'];
		$order_by = $this->props['order_by'];

		$dsmFBCommentData = array( 'appID' => $fb_app_id  );
		wp_enqueue_script('dsm-facebook');
		wp_localize_script( 'dsm-facebook', 'dsm_facebook_data_var', $dsmFBCommentData );

		// Render module content
		$output = sprintf(
			'<div class="dsm-facebook-comments">
				<div id="fb-root"></div>
				<div class="fb-comments" data-href="%1$s" data-colorscheme="%3$s" data-numposts="%2$s" data-order-by="%4$s" width="100%%"></div>
			</div>',
			esc_url( $page_url ),
			esc_attr( $num_posts ),
			esc_attr( $color_scheme ),
			esc_attr( $order_by )
		);

		return $output;
		//return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DSM_FacebookSimpleComments;