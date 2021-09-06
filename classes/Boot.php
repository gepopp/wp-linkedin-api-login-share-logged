<?php


namespace LIOAUTH_Classes;



class Boot {


	private static ?Boot $instance = null;

	private $bootClasses = ['admin\PluginTables', 'admin\SettingsPage'];

	private function __construct() {

	    $this->check_keys();

		foreach ($this->bootClasses as $class){
		    $classname =  'LIOAUTH_Classes\\' . $class;
		    if(class_exists($classname)){
		        new $classname();
            }else{
		        throw new \Exception('Class ' . $classname . ' does not exist');
            }
        }


	}

	public static function instantiate(): Boot {

		if ( static::$instance == null ) {
			static::$instance = new Boot();
		}

		return static::$instance;
	}


	protected function check_keys() {

		$options = get_option( 'LIOUTH_general_settings' );

		if ( ( ! defined( 'LINKEDIN_CLIENT_ID' ) || ! defined( 'LINKEDIN_CLIENT_SECRET' ) )
		     &&
		     ( empty( $options['client_id'] ) || empty( $options['client_secret'] ) )
		) {

			add_action( 'admin_notices', function () {
				?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e( 'Um das LinkedIn OAuth Plugin zu verwenden geben Sie bitte die Client ID und Client Secret aus Ihrer LinkedIn App ein.', 'linkedinoauth' ); ?></p>
                </div>
				<?php
			} );
		}
	}

	private function clone() {}

	public function __wakeup() {
		throw new Exception( "Cannot unserialize singleton" );
	}


}