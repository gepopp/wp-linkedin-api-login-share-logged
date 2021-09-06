<?php


namespace LIOAUTH_Classes\admin;


class SettingsPage {


	use Text;


	public function __construct() {

		add_action( 'admin_menu', [ $this, 'LIOUTH_add_settings_page' ] );
		add_action( 'admin_init', [ $this, 'LIOUTH_add_settings_section' ] );
	}


	public function LIOUTH_add_settings_page() {

		add_menu_page(
			$this->text( 'admin_page_heading' ),
			$this->text( 'menu_entry' ),
			'administrator',
			'lioauth_settings_page',
			[ $this, 'LIOUTH_settings_page_content' ],
			LIOATUH_URL . 'assets/images/icon.jpg'
		);

	}


	public function LIOUTH_settings_page_content() {

		?>
        <div class="wrap">
            <h2><?php echo $this->text( 'general_settings_heading' ) ?></h2>
            <!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
			<?php settings_errors(); ?>
            <!-- Create the form that will be used to render our options -->
            <form method="post" action="options.php">
				<?php settings_fields( 'LIOUTH_general_settings' ); ?>
				<?php do_settings_sections( 'lioauth_settings_page' ); ?>
				<?php submit_button(); ?>
            </form>
        </div>
		<?php

	}

	public function LIOUTH_add_settings_section() {


		// If the theme options don't exist, create them.
		if ( false == get_option( 'LIOUTH_general_settings' ) ) {
			add_option( 'LIOUTH_general_settings' );
		} // end if


		add_settings_section(
			'LIOUTH_general_settings_section',         // ID used to identify this section and with which to register options
			$this->text( 'api_keys_heading' ),                  // Title to be displayed on the administration page
			[ $this, 'LIOUTH_settings_section_content' ], // Callback used to render the description of the section
			'lioauth_settings_page'                           // Page on which to add this section of options
		);

		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(
			'linkedin_api_client_id',                      // ID used to identify the field throughout the theme
			'Client ID',                           // The label to the left of the option interface element
			[
				$this,
				'LIOAUTH_client_id_input',
			],   // The name of the function responsible for rendering the option interface
			'lioauth_settings_page',    // The page on which this option will be displayed
			'LIOUTH_general_settings_section',         // The name of the section to which this field belongs
			[
				'type' => 'password',
				'name' => 'client_id',
			]
		);

		add_settings_field(
			'linkedin_api_client_secret',                      // ID used to identify the field throughout the theme
			'Client Secret',                           // The label to the left of the option interface element
			[
				$this,
				'LIOAUTH_client_id_input',
			],   // The name of the function responsible for rendering the option interface
			'lioauth_settings_page',    // The page on which this option will be displayed
			'LIOUTH_general_settings_section',         // The name of the section to which this field belongs
			[
				'type'  => 'password',
				'name'  => 'client_secret',
				'label' => $this->text( 'api_keys_label' ),
			]
		);


		add_settings_field(
			'linkedin_api_redirect_url',                      // ID used to identify the field throughout the theme
			'Weiterleitungsurl',                           // The label to the left of the option interface element
			[
				$this,
				'LIOAUTH_client_id_input',
			],   // The name of the function responsible for rendering the option interface
			'lioauth_settings_page',    // The page on which this option will be displayed
			'LIOUTH_general_settings_section',         // The name of the section to which this field belongs
			[
				'type'        => 'text',
				'name'        => 'redirect_url',
				'placeholder' => 'linkedinoauth',
				'label'       => $this->text( 'redirect_url_label' ),
			]
		);

		register_setting(
			'LIOUTH_general_settings',
			'LIOUTH_general_settings'
		);
	}

	public function LIOUTH_settings_section_content() {
	} // end sandbox_general_options_callback


	public function LIOAUTH_client_id_input( $args ) {
		// First, we read the options collection
		$options = get_option( 'LIOUTH_general_settings' );
		?>
        <input type="<?php echo $args['type'] ?>"
               id="<?php echo $args['name'] ?>"
               name="LIOUTH_general_settings[<?php echo $args['name'] ?>]"
               value="<?php echo $options[ $args['name'] ] ?? '' ?>"
               class="regular-text"
			<?php echo isset( $args['placeholder'] ) ? 'placeholder="' . $args['placeholder'] . '" ' : '' ?>
        />
		<?php
		if ( isset( $args['label'] ) ) {
			echo $args['label'];
		}
	}
}