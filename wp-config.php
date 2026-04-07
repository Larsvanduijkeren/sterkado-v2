<?php
// Begin AIOWPSEC Firewall
if (file_exists('/var/www/app/aios-bootstrap.php')) {
	include_once('/var/www/app/aios-bootstrap.php');
}
// End AIOWPSEC Firewall
define( 'WP_CACHE', false ); // Added by WP Rocket

require_once(__DIR__ . '/vendor/autoload.php');

// vlucas/phpdotenv v5+: constructor expects StoreInterface; use createImmutable (works in v4 and v5)
if (!method_exists(Dotenv\Dotenv::class, 'createImmutable')) {
	$dotenv = new Dotenv\Dotenv(__DIR__);
	$dotenv->load();
} else {
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->safeLoad();
}

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
define('DB_NAME', $_ENV['DB_NAME']);

/** MySQL database username */
define('DB_USER', $_ENV['DB_USER']);

/** MySQL database password */
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

/** MySQL hostname */
define('DB_HOST', $_ENV['DB_HOST']);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_HOME', $_ENV['WP_HOME']);

define('WP_SITEURL', $_ENV['WP_SITEURL']);


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'fSO4qHfAez^9U|~{Px&P&}]<+./>|s|Jkv8bQnCWW#K*gs[g_CwxtueDR=}3-5v?');
define('SECURE_AUTH_KEY',  '7|lyjmjUDs-!0IA|;-fp{MvHj_) sH,I*[3}AZu/9*]d> l~b/ortgk+[^dlUwl0');
define('LOGGED_IN_KEY',    ';)-f]|svd_Xm-qWYK8RE|%{*^rT_.uTKQ#_I$AxG2te=$JpPJ9N HAYW#7]by9@i');
define('NONCE_KEY',        '% x;_Uzr$DRc1ebU[w;o3vvp|8rVkRMVc+o<prW6Tnrl{[/mLXKTW*}9BIkFi<F<');
define('AUTH_SALT',        'hw86;-ahNBAe4uLOaK-*mb,OuAaeFH*{PN_<l,22`LHyne&X+*[/tCOe03Yn?7|I');
define('SECURE_AUTH_SALT', 'o9dFLyDe 6bC0i?:fNS[=?WPzys#6mGeNtVZbf5O%::fs.|f6?e:3oK~a9^~<o%G');
define('LOGGED_IN_SALT',   'lkQ>;Iqy]w2iE+]]n->wxi?b:*5!T~dTI|H[aDaZ)In50l|l(~2_TuB7~6=i4fB-');
define('NONCE_SALT',       '&IalD;UZ5|$-|`aK @aHi?*UZB]mG3CF0RDWlo!v9QYg;V#1]e4|dFEMKT|AYAAn');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  =  $_ENV['WP_PREFIX'];

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
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_DEBUG_LOG', true );

define('WP_MEMORY_LIMIT', '1024M');
define('WP_MAX_MEMORY_LIMIT', '1024M');
/* That's all, stop editing! Happy blogging. */

define( 'WP_REDIS_CLIENT', 'pecl' );
define( 'WP_REDIS_SCHEME', 'unix' );
define( 'WP_REDIS_PATH', '/var/run/redis/redis.sock' );
define('WP_CACHE_KEY_SALT', $_ENV['WP_SITEURL']);
define('WP_REDIS_DATABASE', (int) $_ENV['REDIS_DB']);

/** -----------------------------------------------------------------
 *  Redis auto-fallback: keep WP running if Redis/socket is down
 *  -----------------------------------------------------------------
 *  When the Redis socket isn’t reachable, disable the drop-in so
 *  WordPress continues without object cache (no manual edits needed).
 */
if ( ! defined('WP_REDIS_DISABLED') ) {
    // probe the configured socket quickly (<= 200ms)
    $redis_up = false;
    $sock = defined('WP_REDIS_PATH') ? WP_REDIS_PATH : null;

    if ( $sock ) {
        if (@file_exists($sock)) {
            $fp = @stream_socket_client("unix://{$sock}", $eNo, $eStr, 0.2);
            if ($fp) { fclose($fp); $redis_up = true; }
        }
    } else {
        // TCP fallback if someone switches to host/port later
        $host = defined('WP_REDIS_HOST') ? WP_REDIS_HOST : '127.0.0.1';
        $port = defined('WP_REDIS_PORT') ? WP_REDIS_PORT : 6379;
        $fp = @fsockopen($host, $port, $eNo, $eStr, 0.2);
        if ($fp) { fclose($fp); $redis_up = true; }
    }

    if (! $redis_up) {
        define('WP_REDIS_DISABLED', true);
    }
}

// keep Redis timeouts small even when it is up (defensive)
if (!defined('WP_REDIS_TIMEOUT'))      define('WP_REDIS_TIMEOUT', 1);
if (!defined('WP_REDIS_READ_TIMEOUT')) define('WP_REDIS_READ_TIMEOUT', 1);
/** ----------------------------------------------------------------- */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
