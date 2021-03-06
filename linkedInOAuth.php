<?php
/**
 * LikedInOAuth Login and Share
 *
 * @package     LinkedInOauth
 * @author      Popp Gerhrad
 * @copyright   2021 https://poppgerhard.at
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: LinkedInOAuth
 * Plugin URI:  https://poppgerhard.at/wordpress-linkedin-oauth
 * Description: Connects to the LinkedIn API and leverages the API to Login and logged Shares of Articles
 * Version:     1.0.0
 * Author:      Popp Gerhard
 * Author URI:  https://poppgerhard.at
 * Text Domain: linkedinoauth
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace linkedinoauth;

use LIOAUTH_Classes\Authorization;

define( 'LIOATUH_VERSION', '1.2.11' );
define( 'LIOATUH_DIR', __DIR__ );
define( 'LIOATUH_URL', plugin_dir_url( __FILE__ ) );

$loader = require_once( LIOATUH_DIR . '/vendor/autoload.php' );
$loader->addPsr4( 'LIOAUTH_Classes\\', __DIR__ . '/classes' );

\A7\autoload( __DIR__ . '/src' );


if ( ! defined( 'LINKEDIN_CLIENT_ID' ) || ! defined( 'LINKEDIN_CLIENT_SECRET' ) ) {

	add_action( 'admin_notices', function () {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'To use the Linkedin OAuth Plugin define the API credentials in the wp-config.php file!', 'linkedinoauth' ); ?></p>
        </div>
		<?php
	} );
}else{
	CreateAndUpdateTables();
	new Authorization();
}


