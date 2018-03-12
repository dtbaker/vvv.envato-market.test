<?php

// ** MySQL settings ** //

if ( file_exists( __DIR__ . '/wp-config-local.php' ) ) {
	require_once __DIR__ . '/wp-config-local.php';
}

call_user_func( function() {

	$constants = array(
		'DB_NAME' => 'envato',
		'DB_USER' => 'envato',
		'DB_PASSWORD' => 'envato',
		'DB_HOST' => '127.0.0.1',
		'DB_CHARSET' => 'utf8mb4',
		'DB_COLLATE' => '',

		'WP_CACHE' => false,
		'WP_CACHE_KEY_SALT' => 'envato',

		'AWS_USE_EC2_IAM_ROLE' => true,
		'WP_DEBUG' => false,
		'JETPACK_DEV_DEBUG' => false,
		'SAVEQUERIES' => false,

		'SCRIPT_DEBUG' => true, // @todo Change this to false; this cannot be false until Grunt is run as part of Packer, so JS and CSS can be minified.
		'CONCATENATE_SCRIPTS' => true,
		'COMPRESS_SCRIPTS' => true,
		'COMPRESS_CSS' => true,

		'FORCE_SSL_LOGIN' => false,
		'FORCE_SSL_ADMIN' => false,

		'ABSPATH' => __DIR__ . '/docroot/',
	);

	// Define home/site URL to match HTTP Host if supplied.
	if ( isset( $_SERVER['HTTP_HOST'] ) ) {
		$is_ssl = ( isset( $_SERVER['X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['X_FORWARDED_PROTO'] );
		$protocol = ( $is_ssl ? 'https://' : 'http://' );
		$constants['WP_HOME'] = $protocol . $_SERVER['HTTP_HOST'];
		$constants['WP_SITEURL'] = $protocol . $_SERVER['HTTP_HOST'];
	}

	foreach ( $constants as $key => $value ) {
		if ( ! defined( $key ) ) {
			define( $key, $value );
		}
	}
} );

$table_prefix = 'wp_';

/* That's all, stop editing! Happy blogging. */

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
