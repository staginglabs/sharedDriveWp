<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php
	elegant_description();
	elegant_keywords();
	elegant_canonical();

	/**
	 * Fires in the head, before {@see wp_head()} is called. This action can be used to
	 * insert elements into the beginning of the head before any styles or scripts.
	 *
	 * @since 1.0
	 */
	do_action( 'et_head_meta' );

	$template_directory_uri = get_template_directory_uri();
?>

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<script type="text/javascript">
		document.documentElement.className = 'js';
	</script>

	<?php wp_head(); ?>
</head>
<body <?php body_class();  global $post;
    $post_slug = $post->post_name; ?> id="consult_page_<?php echo $post_slug;?>">
<?php
	$product_tour_enabled = et_builder_is_product_tour_enabled();
	$page_container_style = $product_tour_enabled ? ' style="padding-top: 0px;"' : ''; ?>
	<div id="page-container"<?php echo et_core_intentionally_unescaped( $page_container_style, 'fixed_string' ); ?>>
<?php
	if ( $product_tour_enabled || is_page_template( 'page-template-blank.php' ) ) {
		return;
	}

	$et_secondary_nav_items = et_divi_get_top_nav_items();

	$et_phone_number = $et_secondary_nav_items->phone_number;

	$et_email = $et_secondary_nav_items->email;

	$et_contact_info_defined = $et_secondary_nav_items->contact_info_defined;

	$show_header_social_icons = $et_secondary_nav_items->show_header_social_icons;

	$et_secondary_nav = $et_secondary_nav_items->secondary_nav;

	$et_top_info_defined = $et_secondary_nav_items->top_info_defined;

	$et_slide_header = 'slide' === et_get_option( 'header_style', 'left' ) || 'fullscreen' === et_get_option( 'header_style', 'left' ) ? true : false;
?>

	<?php if ( $et_top_info_defined && ! $et_slide_header || is_customize_preview() ) : ?>
		<?php ob_start(); ?>
		<div id="top-header"<?php echo $et_top_info_defined ? '' : 'style="display: none;"'; ?>>
			<div class="container clearfix">

			<?php if ( $et_contact_info_defined ) : ?>

				<div id="et-info">
				<?php if ( '' !== ( $et_phone_number = et_get_option( 'phone_number' ) ) ) : ?>
					<span id="et-info-phone"><?php echo et_core_esc_previously( et_sanitize_html_input_text( $et_phone_number ) ); ?></span>
				<?php endif; ?>

				<?php if ( '' !== ( $et_email = et_get_option( 'header_email' ) ) ) : ?>
					<a href="<?php echo esc_attr( 'mailto:' . $et_email ); ?>"><span id="et-info-email"><?php echo esc_html( $et_email ); ?></span></a>
				<?php endif; ?>

				<?php
				if ( true === $show_header_social_icons ) {
					get_template_part( 'includes/social_icons', 'header' );
				} ?>
				</div> <!-- #et-info -->


			<?php endif; // true === $et_contact_info_defined ?>

				<div id="et-secondary-menu">
					<?php
/**
 * WPGlobus language switcher.
 */
if ( class_exists( 'WPGlobus' ) ): ?>
<div class="wpglobus-selector-box">  <?php

/**
* Filter that prevent using language that has `draft` status.
* That works with module `Publish` from WPGlobus Plus add-on.
*/
$enabled_languages = apply_filters( 'wpglobus_extra_languages', WPGlobus::Config()->enabled_languages, WPGlobus::Config()->language );

foreach ( $enabled_languages as $language ):
$url = null;
$is_current = true;
if ( $language != WPGlobus::Config()->language ) {
$url = WPGlobus_Utils::localize_current_url( $language );
$is_current = false;
}

$flag = '<img src="'.WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $language ].'" />';
$link = $flag . '&nbsp;';

printf( '<a %s %s>%s</a>', ( empty( $url ) ? '' : 'href="' . esc_url( $url ) . '"' ), ( $is_current ? 'class="wpglobus-current-language"' : '' ), $link );
 
endforeach; ?>
 
   </div>  <?php
endif; ?>
				<?php
					if ( ! $et_contact_info_defined && true === $show_header_social_icons ) {
						get_template_part( 'includes/social_icons', 'header' );
					} else if ( $et_contact_info_defined && true === $show_header_social_icons ) {
						ob_start();

						get_template_part( 'includes/social_icons', 'header' );

						$duplicate_social_icons = ob_get_contents();

						ob_end_clean();

						printf(
							'<div class="et_duplicate_social_icons">
								%1$s
							</div>',
							et_core_esc_previously( $duplicate_social_icons )
						);
					}

					if ( '' !== $et_secondary_nav ) {
						echo et_core_esc_wp( $et_secondary_nav );
					}

					et_show_cart_total();
				?>
				</div> <!-- #et-secondary-menu -->

			</div> <!-- .container -->
		</div> <!-- #top-header -->
	<?php
		$top_header = ob_get_clean();

		/**
		 * Filters the HTML output for the top header.
		 *
		 * @since 3.10
		 *
		 * @param string $top_header
		 */
		echo et_core_intentionally_unescaped( apply_filters( 'et_html_top_header', $top_header ), 'html' );
	?>
	<?php endif; // true ==== $et_top_info_defined ?>

	<?php if ( $et_slide_header || is_customize_preview() ) : ?>
		<?php ob_start(); ?>
		<div class="et_slide_in_menu_container">
			<?php if ( 'fullscreen' === et_get_option( 'header_style', 'left' ) || is_customize_preview() ) { ?>
				<span class="mobile_menu_bar et_toggle_fullscreen_menu"></span>
			<?php } ?>

			<?php
				if ( $et_contact_info_defined || true === $show_header_social_icons || false !== et_get_option( 'show_search_icon', true ) || class_exists( 'woocommerce' ) || is_customize_preview() ) { ?>
					<div class="et_slide_menu_top">

					<?php if ( 'fullscreen' === et_get_option( 'header_style', 'left' ) ) { ?>
						<div class="et_pb_top_menu_inner">
					<?php } ?>
			<?php }

				if ( true === $show_header_social_icons ) {
					get_template_part( 'includes/social_icons', 'header' );
				}

				et_show_cart_total();
			?>
			<?php if ( false !== et_get_option( 'show_search_icon', true ) || is_customize_preview() ) : ?>
				<?php if ( 'fullscreen' !== et_get_option( 'header_style', 'left' ) ) { ?>
					<div class="clear"></div>
				<?php } ?>
				<form role="search" method="get" class="et-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php
						printf( '<input type="search" class="et-search-field" placeholder="%1$s" value="%2$s" name="s" title="%3$s" />',
							esc_attr__( 'Search &hellip;', 'Divi' ),
							get_search_query(),
							esc_attr__( 'Search for:', 'Divi' )
						);
					?>
					<button type="submit" id="searchsubmit_header"></button>
				</form>
			<?php endif; // true === et_get_option( 'show_search_icon', false ) ?>

			<?php if ( $et_contact_info_defined ) : ?>

				<div id="et-info">
				<?php if ( '' !== ( $et_phone_number = et_get_option( 'phone_number' ) ) ) : ?>
					<span id="et-info-phone"><?php echo et_core_esc_previously( et_sanitize_html_input_text( $et_phone_number ) ); ?></span>
				<?php endif; ?>

				<?php if ( '' !== ( $et_email = et_get_option( 'header_email' ) ) ) : ?>
					<a href="<?php echo esc_attr( 'mailto:' . $et_email ); ?>"><span id="et-info-email"><?php echo esc_html( $et_email ); ?></span></a>
				<?php endif; ?>
				</div> <!-- #et-info -->

			<?php endif; // true === $et_contact_info_defined ?>
			<?php if ( $et_contact_info_defined || true === $show_header_social_icons || false !== et_get_option( 'show_search_icon', true ) || class_exists( 'woocommerce' ) || is_customize_preview() ) { ?>
				<?php if ( 'fullscreen' === et_get_option( 'header_style', 'left' ) ) { ?>
					</div> <!-- .et_pb_top_menu_inner -->
				<?php } ?>

				</div> <!-- .et_slide_menu_top -->
			<?php } ?>

			<div class="et_pb_fullscreen_nav_container">
				<?php
					$slide_nav = '';
					$slide_menu_class = 'et_mobile_menu';

					$slide_nav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'echo' => false, 'items_wrap' => '%3$s' ) );
					$slide_nav .= wp_nav_menu( array( 'theme_location' => 'secondary-menu', 'container' => '', 'fallback_cb' => '', 'echo' => false, 'items_wrap' => '%3$s' ) );
				?>

				<ul id="mobile_menu_slide" class="<?php echo esc_attr( $slide_menu_class ); ?>">

				<?php
					if ( '' === $slide_nav ) :
				?>
						<?php if ( 'on' === et_get_option( 'divi_home_link' ) ) { ?>
							<li <?php if ( is_home() ) echo( 'class="current_page_item"' ); ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'Divi' ); ?></a></li>
						<?php }; ?>

						<?php show_page_menu( $slide_menu_class, false, false ); ?>
						<?php show_categories_menu( $slide_menu_class, false ); ?>
				<?php
					else :
						echo et_core_esc_wp( $slide_nav ) ;
					endif;
				?>

				</ul>
			</div>
		</div>
	<?php
		$slide_header = ob_get_clean();

		/**
		 * Filters the HTML output for the slide header.
		 *
		 * @since 3.10
		 *
		 * @param string $top_header
		 */
		echo et_core_intentionally_unescaped( apply_filters( 'et_html_slide_header', $slide_header ), 'html' );
	?>
	<?php endif; // true ==== $et_slide_header ?>

	<?php ob_start(); ?>
		<header id="main-header" data-height-onload="<?php echo esc_attr( et_get_option( 'menu_height', '66' ) ); ?>">
			<div class="container clearfix et_menu_container">
			<?php
				$logo = ( $user_logo = et_get_option( 'divi_logo' ) ) && ! empty( $user_logo )
					? $user_logo
					: $template_directory_uri . '/images/logo.png';

				ob_start();
			?>
				<div class="logo_container">
					<span class="logo_helper"></span>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" id="logo" data-height-percentage="<?php echo esc_attr( et_get_option( 'logo_height', '54' ) ); ?>" />
					</a>
				</div>
			<?php
				$logo_container = ob_get_clean();

				/**
				 * Filters the HTML output for the logo container.
				 *
				 * @since 3.10
				 *
				 * @param string $logo_container
				 */
				echo et_core_intentionally_unescaped( apply_filters( 'et_html_logo_container', $logo_container ), 'html' );
			?>
				<div id="et-top-navigation" data-height="<?php echo esc_attr( et_get_option( 'menu_height', '66' ) ); ?>" data-fixed-height="<?php echo esc_attr( et_get_option( 'minimized_menu_height', '40' ) ); ?>">
					<?php if ( ! $et_slide_header || is_customize_preview() ) : ?>
						<nav id="top-menu-nav">
						<?php
							$menuClass = 'nav';
							if ( 'on' === et_get_option( 'divi_disable_toptier' ) ) $menuClass .= ' et_disable_top_tier';
							$primaryNav = '';

							$primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'menu_id' => 'top-menu', 'echo' => false ) );
							if ( empty( $primaryNav ) ) :
						?>
							<ul id="top-menu" class="<?php echo esc_attr( $menuClass ); ?>">
								<?php if ( 'on' === et_get_option( 'divi_home_link' ) ) { ?>
									<li <?php if ( is_home() ) echo( 'class="current_page_item"' ); ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'Divi' ); ?></a></li>
								<?php }; ?>

								<?php show_page_menu( $menuClass, false, false ); ?>
								<?php show_categories_menu( $menuClass, false ); ?>
							</ul>
						<?php
							else :
								echo et_core_esc_wp( $primaryNav );
							endif;
						?>
						</nav>
					<?php endif; ?>

					<?php
					if ( ! $et_top_info_defined && ( ! $et_slide_header || is_customize_preview() ) ) {
						et_show_cart_total( array(
							'no_text' => true,
						) );
					}
					?>

					<?php if ( $et_slide_header || is_customize_preview() ) : ?>
						<span class="mobile_menu_bar et_pb_header_toggle et_toggle_<?php echo esc_attr( et_get_option( 'header_style', 'left' ) ); ?>_menu"></span>
					<?php endif; ?>

					<?php if ( ( false !== et_get_option( 'show_search_icon', true ) && ! $et_slide_header ) || is_customize_preview() ) : ?>
					<div id="et_top_search">
						<span id="et_search_icon"></span>
					</div>
					<?php endif; // true === et_get_option( 'show_search_icon', false ) ?>

					<?php

					/**
					 * Fires at the end of the 'et-top-navigation' element, just before its closing tag.
					 *
					 * @since 1.0
					 */
					do_action( 'et_header_top' );

					?>
				</div> <!-- #et-top-navigation -->
			</div> <!-- .container -->
			<div class="et_search_outer">
				<div class="container et_search_form_container">
					<form role="search" method="get" class="et-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php
						printf( '<input type="search" class="et-search-field" placeholder="%1$s" value="%2$s" name="s" title="%3$s" />',
							esc_attr__( 'Search &hellip;', 'Divi' ),
							get_search_query(),
							esc_attr__( 'Search for:', 'Divi' )
						);
					?>
					</form>
					<span class="et_close_search_field"></span>
				</div>
			</div>
		</header> <!-- #main-header -->
	<?php
		$main_header = ob_get_clean();

		/**
		 * Filters the HTML output for the main header.
		 *
		 * @since 3.10
		 *
		 * @param string $main_header
		 */
		echo et_core_intentionally_unescaped( apply_filters( 'et_html_main_header', $main_header ), 'html' );
	?>
		<div id="et-main-area">

	<?php
	 // if(is_category('product-category')){
		// 		woocommerce_breadcrumb(); 
		// }
		/**
		 * Fires after the header, before the main content is output.
		 *
		 * @since 3.10
		 */
		do_action( 'et_before_main_content' );

?>
<style type="text/css">
	.woocommerce-MyAccount-content table thead tr th:last-child{
    border-top-right-radius: 5px !important;
    border-bottom-right-radius: 5px !important;
}
.woocommerce-MyAccount-content table thead tr th:first-child {
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
}
tr.woocommerce-orders-table__row td a{
	    color: #195073;
}
a.woocommerce-button.button.view {
    background-color: transparent;
    padding: 1px !important;
    color: #195073;
}
a.woocommerce-button.button.view:hover{
	background-color: transparent !important;
	padding: 1px !important;
}
td.woocommerce-orders-table__cell.woocommerce-orders-table__cell-file-actions a.woocommerce-button.button.view {
    background-color: #195072;
    color: #fff !important;
    font-size: 12px;
    padding: 4px 23px !important;
}
th.woocommerce-orders-table__header.woocommerce-orders-table__header-order-date {
    text-align: center;
}
.woocommerce-button--next.woocommerce-Button.woocommerce-Button--next.button {
    padding: 10px 48px !important;
    color: #fff;
}
.woocommerce-MyAccount-navigation-link.is-active a {
    color: #195073;
}
.woocommerce-orders-table__cell-order-date {
    /* text-align: center; */
    padding: 10px 30px !important;
}
.woocommerce-Address address{
	    padding: 25px 30px 30px 30px;
	    box-shadow: 0 0 3px 1px #cacaca78;
    font-style: normal;
    line-height: 29px;
}
header.woocommerce-Address-title.title{
	    background-color: #195073;
}
header.woocommerce-Address-title.title h3 {
    color: #fff;
    font-size: 16px;
    padding: 22px 30px;
}
header.woocommerce-Address-title.title a.edit {
    background-color: #fff;
    padding: 3px 9px;
    color: #195073;
        margin: 15px 15px 0 0;
}
.breadcrumb ol {
    display: flex;
}
.breadcrumb ol li{
	list-style-type: none;
}
i.fa.fa-angle-right {
    padding: 0 8px;
}
div#left-area h1.entry-title.main_title {
    margin-bottom: 0;
    padding-top: 25px;
}
.woocommerce-account #main-content .container {
    padding-top: 0;
}
.woocommerce-account #main-content:before ,
body.product-template-default.single.single-product div#main-content:before{
    position: absolute;
    width: 100%;
    height: 116px;
    background-color: #F6F6F6;
    content: '';
}
div#left-area .breadcrumb {
    margin-bottom: 50px;
}
.breadcrumb ol li a {
    color: #195073;
}
.woocommerce .woocommerce-breadcrumb{
	display: block !important;
}
body.product-template-default.single.single-product #main-content .container {
    padding-top: 30px !important;
}
.woocommerce-breadcrumb{
    margin: 0 0 8em !important;
    font-size: 14px !important;
}
nav.woocommerce-breadcrumb a {
    color: #195073 !important;
}
.woocommerce-MyAccount-content a {
    color: #195073;
}

.top_footer {
    background-color: #195073;
}
.top_footer h4.title {
    font-family: 'Source Sans Pro',Helvetica,Arial,Lucida,sans-serif;
    font-weight: 700;
    font-size: 18px;
    line-height: 1.4em;
        color: #fff !important;
        padding-left: 13px;
}
div#media_image-2 img{
	width: 102px;
}
.footer-widget {
    font-size: 16px;
}

a.icon.et_pb_with_border {
    background-color: #3b5998!important;
        border-radius: 50% !important;
}
.et-social-instagram a.icon.et_pb_with_border {
    background-color: #ea2c59!important;
}
div#footer-bottom{
	display: none;
}

#woocommerce_product_search-4 {
    margin-bottom: 0;
    padding: 30px 20px 0 20px!important;
 background-color: #f6f6f6 !important;
    border-radius: 10px 10px 0 0 !important;
	  
}

#woocommerce-product-search-field-0{
	    width: 100% !important;
	    padding:17px 30px 17px 45px !important;
	    border-radius: 8px;
	    font-size: 15px;
    border-radius: 8px;
	color:#777;
}
#woocommerce-product-search-field-0::placeholder{
	color:#C4C4C4;
}
#woocommerce-product-search-field-0:-ms-input-placeholder{
	color:#C4C4C4;
}
#woocommerce-product-search-field-0::-ms-input-placeholder{
	color:#C4C4C4;
}
form.woocommerce-product-search {
    position: relative;
    border: 1px solid #efefef;
    border-radius: 6px;
}
.woocommerce-product-search button {
    position: absolute;
    top:20px;
    background-color: transparent;
    border: none;
    font-size: 0;
    left: 20px;
    cursor: pointer;
}
.woocommerce-product-search button::before{
	    content: "\f002";
    position: absolute;
    top: -2px;
    left: -8px;
    font-family: FontAwesome;
    font-size: 18px;
    color: #C4C4C4;
}

#execphp-2 .tabs.sidebar-shop {
    border-radius: 0;
}
#woocommerce_product_search-4 h4.widgettitle {
    font-size: 28px;
    color: #a7a7a7;
    font-weight: 700;
    font-family: 'Playfair Display';
    display: inline-block;
    padding-bottom: 25px;
}

body #sortable #English a {
    width: 27px!important;
}
.woocommerce-cart-form tbody td.product-name a {
    color: #76767b;
}


.cart_totals h2 {
    font-size: 28px;
    color: #767676;
}
.cart-subtotal th {
    font-weight: 400 !important;
}


.woocommerce .woocommerce-message{
	color: #0b5073 !important;
}
#left-area ul.woocommerce-error li {
    line-height: 49px;
	color: #0b5073 !important;
}
input#billing_company:placeholder{
color:transparent !important;
}
#customer_details input#billing_company {
    border: 1px solid #dbdbdb;
}
#customer_details label {
    color: #9f9e9f;
	    margin-bottom: 10px;
	    margin-top: 10px;
}
#customer_details input, #customer_details select,.select2-container--default .select2-selection--single ,textarea#order_comments{
    border: 1px solid #dbdbdb !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #100f0f transparent transparent transparent;
    border-style: solid;
    border-width: 7px 5px 0 5px;
    margin-left: -13px;
}
#customer_details .woocommerce-additional-fields h3 {
    padding-bottom: 10px;
}
textarea#order_comments {
    height: 100px;
}
#upload_CNIC_image input#file_upload {
    border: none !important;
}
#upload_CNIC_image h3 {
    margin-top: 8px !important;
    padding-bottom: 10px !important;
}
.upload_img {
    padding: 5px 0 10px;
        color: #195073;
}
a.woocommerce-product-gallery__trigger {
    display: none;
}
#product-436 .related.products {
    border-top: 1px solid #e2dbdb !important;
    padding-top: 43px;
}
section.related.products h2 {
    /*font-size: 20px !important;*/
}
#product-436 .related.products h2.woocommerce-loop-product__title {
    font-size: 15px !important;
}
#product-436 ul.tabs.wc-tabs li {
    margin-left: 5px !important;
}
.related.products {
    margin: 58px 0 !important;
    border-top: 1px solid #ccc;
    padding-top: 40px;
}
.product-template-default .et_pb_image img {
    position: relative;
    width: 65%;
}

.product-template-default .footer-widget{
	margin-bottom:0;
}
body.product-template-default.single.single-product .et_pb_section {
    background-color: #0b5073;
    padding: 0;
}
body li.product-type-simple h2.woocommerce-loop-product__title {
    font-size: 15px !important;
}
body .woocommerce-orders-table__cell-order-date{
	padding: 10px 10px !important;
}
.upload-files #upload_CNIC_image {
    border: none !important;
    padding: 20px 20px !important;
    border-radius: 5px;
    border-bottom-width: 2px;
    border-right-width: 2px;
    margin-bottom: 15px;
}
.woocommerce table.shop_table{
	border: none !important;
}
.wpglobus-selector-box {
    width: auto;
    float: left;
}
.breadcrumbs ul li span {
    color: #7b7b7B !important;
}.camp_back >.et_pb_row_0.et_pb_row {
    padding-top: 0px !important;
}
.wpglobus-selector-box a {
    padding-right: 10px;
}
#execphp-2 h4.widgettitle {
    background-color: #f6f6f6;
    padding: 37px 0 0 34px;
        color: #C4C4C4;
    font-size: 28px;
    text-transform: capitalize;
}
.page-id-8 .xoo-el-form-container.xoo-el-form-inline {
    margin-top: 70px;
}
.woocommerce-lost-password .woocommerce-message.add-message {
    background-color: #f6f6f6 !important;
    margin-top: 93px;
}
.woocommerce-lost-password p.woocommerce-form-row.woocommerce-form-row--last.form-row.form-row-last {
    width: 100%;
}
.woocommerce .woocommerce-message p{
	    color: #fff;
}
body #main-content .woocommerce-notices-wrapper .woocommerce-error li strong{
	font-weight: 500 !important;
}
.woocommerce-edit-address .woocommerce-message.add-message,
.woocommerce-edit-account .woocommerce-error{
	margin-top:0px !important;
}
table.woocommerce-table.woocommerce-table--order-details.shop_table.order_details tbody tr td a .fa{
    padding-right: 5px;
}
a.downloaded_link.btn-download.button.woocommerce-button.button.view{
    background-color: #195073;
    color: #fff !important;
    margin-right: 15px;
}
body a.downloaded_link.btn-download.button.woocommerce-button.button.view:hover 
{
    background-color: #195073 !important;
}
a.downloaded_link.woocommerce-button.button.view:hover{
	    background-color: #195073 !important;
}
a.btn-delete.button:hover {
    background-color: #e80001 !important;
}
a.downloaded_link.woocommerce-button.button.view {
    padding: 5px 10px !important;
        background-color: #195073;
    color: #fff !important;
}
div#upload_CNIC_image_checkout {
    margin-bottom: 15px;
}
</style>