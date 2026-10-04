<?php
/**
 * WordPress configuration for the homehrdecor site.
 *
 * Local WAMP development install.
 *   Site URL : http://localhost/homehrdecor/
 *   Database : homehrdecor
 *
 * Independent of the `tablighat` install: separate database, separate
 * authentication salts, so logins and cookies never cross between the two.
 *
 * @package WordPress
 */

// ** Database settings ** //
define('DB_NAME', 'homehrdecor');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// define('DB_NAME', 'homehet_homehrdecor');
// define('DB_USER', 'homehet_homehrdecor');
// define('DB_PASSWORD', 'IW42vMQBUBWnhfXDLH');
// define('DB_HOST', 'localhost');
// define('DB_CHARSET', 'utf8mb4');
// define('DB_COLLATE', '');

/**#@+
 * Authentication unique keys and salts.
 * Generated fresh for this install. Changing these invalidates all cookies.
 */
define('AUTH_KEY', 'TyIZTMXGXXYdKLa/Di/9^ajw&l0fh@*VD/4,T5lSd*Xb#FraE%/vx>tX!*Frwy[Z');
define('SECURE_AUTH_KEY', '/Fi56s0]Hq|%ta>?lN;aN{Yyt*T-C[a9Zs0@X8)|flf|mn?~plH90OT%iKYnt9~C');
define('LOGGED_IN_KEY', ';}!iKpRNLWD>pwt[f.s~S^@t!Gs3J]2nhB.Gp=3#T[h7D0X3B!ahZBP(+E*6xSAw');
define('NONCE_KEY', '(T4yWJm2s,m}?(lqeN^60rs^-g/#a6egx#O}^eHytR/QUjK?3R6h!S49n?N3Ih-[');
define('AUTH_SALT', 'U8^#sBkmJrgRUGjjE4v&~7_aL^k96)p[(7@=Za{YnQQ}^:/l1#;qnRQ+a.Ma1cPz');
define('SECURE_AUTH_SALT', 'd*nV)wd5i(9)IZ%NgyyD9QW{:aqdpYaPRsJkKKv.Me%T8TwR&yEaUHUW?,JfVpLZ');
define('LOGGED_IN_SALT', 'xdyQHH&rc(8*(/Pd^u1#eE_VtQD|?lPya|7?v!UNuPLnS{B{BWU#xbeLJpdvuZ~0');
define('NONCE_SALT', 'I?DsKedF8L.[4O5l0m8<[Yl1k.RdBo&2KzU).)CeUxq~V#neSv/0mt*r)s*ial=)');

/**#@-*/

/**
 * WordPress database table prefix.
 */
$table_prefix = 'wp_';

/**
 * Debugging.
 * Kept off: xdebug prints PHP notices inline in this WAMP environment.
 */
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */

/** Local development environment. */
define('WP_ENVIRONMENT_TYPE', 'local');

/** Write files directly instead of prompting for FTP credentials. */
define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';