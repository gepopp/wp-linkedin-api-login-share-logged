<?php


namespace LIOAUTH_Classes\admin;


class PluginTables {

	const TOKENS_TABLE = 'linkedin_oauth_token';
	const SHARES_TABLE = 'linkedin_shares';

	public function __construct() {



	}

	function CreateAndUpdateTables() {

		wp_die('hier');

		$installed_ver = get_option( "linkedinoauthversion" );

		if ( $installed_ver == LIOATUH_VERSION ) {
			return;
		}

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_name = $wpdb->prefix . self::TOKENS_TABLE;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		id BIGINT NOT NULL AUTO_INCREMENT,
		linkedin_id VARCHAR(255) NOT NULL,
		email VARCHAR(255) NOT NULL,
		name VARCHAR(255) NOT NULL,
		access_token TEXT NOT NULL,
		expires_in BIGINT NOT NULL,
		created_at TIMESTAMP NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

		dbDelta( $sql );

		$table_name = $wpdb->prefix . self::SHARES_TABLE;

		$sql = "CREATE TABLE $table_name (
		id BIGINT NOT NULL AUTO_INCREMENT,
		linkedin_id VARCHAR(255) NOT NULL,
		email VARCHAR(255) NOT NULL,
		name VARCHAR(255) NOT NULL,
		share_id TEXT NOT NULL,
		post_id BIGINT NOT NULL,
		created_at TIMESTAMP NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

		dbDelta( $sql );


		update_option( 'linkedinoauthversion', LIOATUH_VERSION );

	}
}