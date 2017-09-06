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
define('DB_NAME', 'johnc151_wp142');

/** MySQL database username */
define('DB_USER', 'johnc151_wp142');

/** MySQL database password */
define('DB_PASSWORD', '4[p-2k3SYn');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'fj0mc3ax8ykji4rfapglngjeqssgnu7gj29lkdput4zdqrcmpklath3qksgrqzqm');
define('SECURE_AUTH_KEY',  '7pyteqxjseospiovxqprekxrbhfmbfqpbdqceyeae4fv4pehrozu1htg8ggilmif');
define('LOGGED_IN_KEY',    'qmofzac932cab9zz0kdik2wt6cshbvuq56wxkyekjzsty6xmxsmlfrhuw9xopujb');
define('NONCE_KEY',        'r8ao4pdei0fdvvgq64tm6xkctcwd0j1to8gcpeuplslx41ge9ptl0raoksi8ygo1');
define('AUTH_SALT',        'vwmn3icbxdf91mmb8g5wfct2ml62ep3mhesl90gnbyjly6yypgm39lmr8yhyeee8');
define('SECURE_AUTH_SALT', 'fx1osxtfsb4inzz4tstqvo2bl31e2hrkar94nq6gtuzn5fkvkawenjp6nlewrdpd');
define('LOGGED_IN_SALT',   'jtnumeiyhplqmqhpptx21qnetfqriifij1cczettqnjcvz2pbzoxsruxqhb3uwom');
define('NONCE_SALT',       'fd5z1wr19caw5ltyxxgsbzelgxwci6zdujwnzva2fbo74ndtecbpzkwd6khdlxkf');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpje_';

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
define('WP_DEBUG', false);
define( 'WP_MEMORY_LIMIT', '128M' );
define( 'WP_AUTO_UPDATE_CORE', false );

/* Multisite */
define( 'WP_ALLOW_MULTISITE', true );
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'lms.being.design');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
