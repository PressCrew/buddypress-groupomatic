<?php
/*
Plugin Name: BuddyPress Group-O-Matic
Plugin URI: http://community.presscrew.com/
Description: Easily connect members to groups based on their profile fields
Author: Marshall Sorenson (MrMaz)
Author URI: http://marshallsorenson.com/
License: GNU GENERAL PUBLIC LICENSE 2.0 or later http://www.gnu.org/licenses/gpl.txt
Version: 1.0.4
Text Domain: buddypress-groupomatic
Network: true
*/

////////////////////////////////
// Important Internal Constants
// *** DO NOT MODIFY THESE ***

// Configuration
define( 'BP_GOM_VERSION', '1.0.4' );
define( 'BP_GOM_PLUGIN_NAME', 'buddypress-groupomatic' );
define( 'BP_GOM_PLUGIN_TEXTDOMAIN', 'buddypress-groupomatic' );

// Core Paths
define( 'BP_GOM_PLUGIN_DIR', dirname( __FILE__ ) );

// Core URLs
define( 'BP_GOM_PLUGIN_URL', WP_PLUGIN_URL . '/' . BP_GOM_PLUGIN_NAME );

// user meta keys
define( 'BP_GOM_META_KEY_USER_GROUPS', 'bp_gom_user_groups' );

// replacement tokens
define( 'BP_GOM_TOKEN_ANSWER', '%answer%' );

// ***************************
///////////////////////////////

//
// Plugin Bootstrap Functions
//

/**
 * Handle special BP initialization
 */
function bp_gom_init() {

	// this plugin is useless without profiles and groups
	switch ( false ) {
		case ( bp_is_active( 'xprofile' ) ):
		case ( bp_is_active( 'groups' ) ):
			return;
	}

	// load core
	require_once BP_GOM_PLUGIN_DIR . '/bp-gom-classes.php';
	require_once BP_GOM_PLUGIN_DIR . '/bp-gom-matching.php';

	if ( is_admin() ) {
		require_once BP_GOM_PLUGIN_DIR . '/bp-gom-admin.php';
	} else {
		require_once BP_GOM_PLUGIN_DIR . '/bp-gom-autojoin.php';
	}

	do_action( 'bp_gom_init' );
}

//
// Hook into BuddyPress!
//
add_action( 'bp_include', 'bp_gom_init' );

?>
