<?php
namespace LIOAUTH_Classes\api;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}



class Constants {

	const LIOAUTH_GENERAL_SETTING = 'LIOUTH_general_settings';

	const LIOAUTH_API_URL = 'https://www.linkedin.com/oauth/v2/authorization';

	const LIOAUTH_PROFILE_SCOPE = 'r_liteprofile';

	const LIOAUTH_EMAIL_SCOPE = 'r_emailaddress';

	CONST LIOAUTH_SCHARING_SCOPE = 'w_member_social';


	public static function get_client_id() : string {
		if(defined('LINKEDIN_CLIENT_ID')){
			return LINKEDIN_CLIENT_ID;
		}else{
			$options = get_option(Constants::LIOAUTH_GENERAL_SETTING);
			if(isset($options['client_id'])){
				return $options['client_id'];
			}
		}

		return false;
	}

	public static function get_client_secret() : string {
		if(defined('LINKEDIN_CLIENT_SECRET')){
			return LINKEDIN_CLIENT_SECRET;
		}else{
			$options = get_option(Constants::LIOAUTH_GENERAL_SETTING);
			if(isset($options['client_secret'])){
				return $options['client_secret'];
			}
		}

		return false;
	}

}