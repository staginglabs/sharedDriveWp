<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sharedDriveWp' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'kaAno=7r&KXt_dKiY#.c?-[M#H3u{UR%GG A^f<)[5}}<}OnU6:F6o,ZR<M0LiJ^' );
define( 'SECURE_AUTH_KEY',  '_<Z$r`w~x0b#ImTU mH]_!p-zm/X?Ul.*eHdpu&*d!s_/Rc<$VMPs0}qh-dy1L0Y' );
define( 'LOGGED_IN_KEY',    '{*bo>(xldn6BY!Nf!,#VgLV42f~T!|*Ld[G4N1`/xBj%Sw$n^(]5zta(e,p62<V{' );
define( 'NONCE_KEY',        'm=HUFJ1dE>!u6Qz}Ew )PnVw#2rgtxuseOYptZ=KMn;!pi/SGKV QH4xq9^O(3-/' );
define( 'AUTH_SALT',        '`RlXdILQ|M{pf%lpWp%mR*bmd9-F5jvp u.u$g@N)xY7Q^-)!EiL([?I]_sC+vs6' );
define( 'SECURE_AUTH_SALT', 'Mg-l=vAPMmweri!o*fHy)fQ|n1AO*-{p{;=|$BNKG8E<gnN -NUm|!ncR.vUzl}4' );
define( 'LOGGED_IN_SALT',   '`2|~!U3[OWt9n)sZ8]nnyH_1NjWltc)s4$*$s6]XtDm=-?ml,w`PL$!>sBV|u<>]' );
define( 'NONCE_SALT',       '~CHpLBvk,u[_@TFKeHYG!B~}~)]2Y8j!eo}[N{M}!|ae@~B8&`&a}0(8+GBUL(-8' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
