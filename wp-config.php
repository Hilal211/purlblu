<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'swimsuit' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '[}l/*dnZtcz|3L5s#>&nx1lH<r`fpT|OUvC$Y51% k9M;?.tqa{.hD(6E=a+,6$*' );
define( 'SECURE_AUTH_KEY',  'O]McsU[+!`Uez|i~co+&YTD%%ekf]ni#JZLLaA27@=G,^NF*jWxMW;_3coa0x|`6' );
define( 'LOGGED_IN_KEY',    's6XBK$X,!gB!8ARMcu K}|lbxi=sf_YdwEbSwLI?^i#A4ouw,?.&VUB1Le&O&*/o' );
define( 'NONCE_KEY',        '7?u1<JW6Xl<gGTTeNRwx+fHWi_/p}#()@=3J>a;38YG6n$TT|rO]rP}DCb>dBlg7' );
define( 'AUTH_SALT',        '<+7;bnzEaa.!C@PdnV74R1H#/xY`#q@=RUks=bba7+5hJs`2Ezh8Q, ZUTELP}$x' );
define( 'SECURE_AUTH_SALT', '/)@IRZGV<3>6tJhOmKz),Y|EgS,k6w_dQ2yFh9w/bxM7QtW<[|XDWHa0;Fu$jN[Q' );
define( 'LOGGED_IN_SALT',   'O$W&V$0O4TWM8@{(z6zuc,ue-0089svL6 a1x|Q!5#1!r2{ZfH*>t?i#A&Sb,gev' );
define( 'NONCE_SALT',       'KleocTf7{,{=Q~KjoYV#/n$y&q=?x%e36y!nx6%kUpF!~?.RN?#P?!EMrW7+u^h}' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
