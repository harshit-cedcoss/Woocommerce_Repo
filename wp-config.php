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
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '/3/cQFgwOZF9RsFy9IG/BxhF4FjPCxHKzOPgafyd165hj1X9jGBcJh8jJsx2TOa3Kfi7tkUnpUzGgR+1/FdeWg==');
define('SECURE_AUTH_KEY',  '45EKvRPZK5vCfDqQZ+GaPAdx21MHk0YGX6M5R7fFuS5n76FazkK/iSmOnHhfDwFeg2N/vomfR96Td444P4Ghcg==');
define('LOGGED_IN_KEY',    'NK6tWrZ3TeU1utentcp8kvR92FcvFtTSrtRnQB5B6oY9zQTJ4l4iO/Jut2eTDUtkfOP4ol0rs+cxJ0ISwSDHvw==');
define('NONCE_KEY',        'ZZZT+C+BVTm6TBnIzZdpikxcvPJWky4uZk4wCoIRc/FsShpgM7Lk4Yi3DQjqI05IyqOcrw+R3hl4w+n62wkN6w==');
define('AUTH_SALT',        'JFCBK2lDMN3EgnHbxhifhe0ehh3Kzkyty+2IEH5ct/Wmn1VFwjCFs3djKBz6Nqsdw5dY4u/BgVi0Oi9nVTU/Nw==');
define('SECURE_AUTH_SALT', 'X++R7gKPMQoQfT8QWRry2p5Q7PYm7/ZAibfLhZrfhdVRmVrNHISJwZ9ZSCjsMSwTUSzrWp+fhbs3jHhMZpeDHw==');
define('LOGGED_IN_SALT',   'WhDd3IGhi+ZBR5+HSYW3fEOPFGAA/2cmPHv8ED4QS+UuMVtCmAnut2l0+ZvRwSyGC5XNLndiQkp44wzBeWdxXw==');
define('NONCE_SALT',       'rIhaCXr5wSQJsUUULpRZXSZmV0vySHT2aUYhIbLVjkh/eRTIYnG+s09pAtpxOPCfQdW4e+CCqCMKudGT3aB2nA==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
