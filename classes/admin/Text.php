<?php


namespace LIOAUTH_Classes\admin;


trait Text {


	public function text( $index ) {

		$text = [
			'admin_page_heading'       => __( 'LinkedIn API Settings', 'linkedinoauth' ),
			'menu_entry'               => __( 'LinkedIn Settings', 'linkedinoauth' ),
			'general_settings_heading' => __( 'LinkedIn API Settings', 'linkedinoauth' ),
			'api_keys_heading'         => __( 'API Client ID and API Secret', 'linkedinoauth' ),
			'api_keys_label'           => __( '<p>You find a tutorial, how to get your API credentials  
                            						<a href="https://www.linkedin.com/pulse/how-get-signin-linkedin-work-taric-andrade/" target="_blank">here.</a>
                            					</p>
                            					<p>If you need help with the installation dont hesitate to contact us 
                            						<a href="https://poppgerhard.at" target="_blank">here.</a> 
                            					</p>', 'linkedinouth' ),
			'redirect_url_label'       => __( sprintf( '<p>Per default the redirect URL is set to %s. 
                            					<br>If you need to use another url, please enter the URL without %s.</p>
                            					<p>This URL must be entered in your <a href="https://developer.linkedin.com/" target="_blank">LinkedIn App</a></p>',
				home_url(),
				home_url() ),
				'linkedioauth' ),
		];

		return $text[ $index ];
	}


}