<?php
/**
 * VVV Config
 *
 * @package Envato
 */

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

		'AWS_USE_EC2_IAM_ROLE' => false,
		'WP_DEBUG' => true,
		'JETPACK_DEV_DEBUG' => true,
		'SAVEQUERIES' => true,

		'SCRIPT_DEBUG' => true,
		'CONCATENATE_SCRIPTS' => false,
		'COMPRESS_SCRIPTS' => false,
		'COMPRESS_CSS' => false,

		'FORCE_SSL_LOGIN' => false,
		'FORCE_SSL_ADMIN' => false,
	);

	if ( ! file_exists( '/vagrant' ) ) {
		$constants['DB_USER'] = 'external';
		$constants['DB_HOST'] = '192.168.50.4';
		$constants['DB_PASSWORD'] = 'external';
	}

	foreach ( $constants as $key => $value ) {
		if ( ! defined( $key ) ) {
			define( $key, $value );
		}
	}
} );

ini_set( 'error_log', '/tmp/php_errors.envato.log' );
