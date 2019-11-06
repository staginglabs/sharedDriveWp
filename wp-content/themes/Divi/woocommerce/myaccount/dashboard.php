<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="mydashboard-hello"><?php
	/* translators: 1: user display name 2: logout url */
	printf(
		__( '<h3>Welcome to your Client Area %1$s  </h3> <a href="%2$s" class="button">Log out</a>', 'woocommerce' ),
		'<strong>' . esc_html( $current_user->first_name ) . " " . esc_html( $current_user->last_name ) . '</strong>',
		esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) )
	);
?></div>

<?php
	printf(
		__( '<div class="three-box">
			<h3 class="recent-icon">Recent Orders</h3>
			<p>You can view your recent orders</p>
			<a href="%1$s" class="button">Click Here</a>
			</div>
			<div class="three-box">
			<h3 class="address-icon">Manage Addresses</h3>
			<p>Manage your shipping and billing addresses </p>
			<a href="%2$s" class="button">Click Here</a>
			</div>
			<div class="three-box last">
			<h3 class="password-icon">Your Password</h3>
			<p>Edit your password and account details.</p>
			<a href="%3$s" class="button">Click Here</a>
			</div>', 'woocommerce' ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-address' ) ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);
?>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
