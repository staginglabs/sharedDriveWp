<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s Customer username */ ?>
<!-- <p><?php //printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_login ) ); ?></p> -->
<?php /* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */ ?>

<!-- <p><?php //printf( esc_html__( 'Thanks for creating an account on %1$s. Your username is %2$s. You can access your account area to view orders, change your password, and more at: %3$s', 'woocommerce' ), esc_html( $blogname ), '<strong>' . esc_html( $user_login ) . '</strong>', make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p> -->


<?php if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && $password_generated ) : ?>
	<?php /* translators: %s Auto generated password */ ?>
	<p><?php printf( esc_html__( 'Your password has been automatically generated: %s', 'woocommerce' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p>
<?php endif; ?>

<?php     $current_user = wp_get_current_user();
$user = get_user_by('login', $user_login );
?>

<p> Bienvenido! </p>
<p>Hemos creado un usuario para ti dentro del Área de Clientes en <?php echo get_bloginfo( 'name' ); ?> para que puedas cargar/descargar cualquier documento relacionado con los servicios que realizamos para ti y consultar el estado actual de los mismos.  Para acceder al sistema tu nombre de usuario es: <?php echo esc_html( $user->user_email ); ?>
</p>



<?php
/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
if ( $additional_content ) {
	//echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}
?>

<p>Por favor, haz click en el enlace siguiente y establece tu contraseña para acceder a tu área privada.</p>
<p> <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account',''); ?>"><?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?></a>
 </p>

<p>Gracias!!</p>
<p><?php echo get_bloginfo( 'name' ); ?></p>
<?php 
do_action( 'woocommerce_email_footer', $email );
