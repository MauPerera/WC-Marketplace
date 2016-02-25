<?php
/*
Plugin Name: WC Marketplace
Plugin URI: http://dualcube.com
Description: A Free Extension That Transforms Your WooCommerce Site into a Marketplace.
Author: DualCube, Mousumi Saha, Prabhakar Kumar Shaw, Arim Ghosh, Aveek Kr Saha, Sandeep Kumar
Version: 2.3.4
Author URI: http://dualcube.com
*/


if ( ! class_exists( 'WC_Dependencies_Product_Vendor' ) ) require_once 'includes/class-wcmp-dependencies.php';
require_once 'includes/wcmp-core-functions.php';
require_once 'wcmp_config.php';

if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('WCMp_PLUGIN_TOKEN')) exit;
if(!defined('WCMp_TEXT_DOMAIN')) exit;

// Activation Hooks
register_activation_hook( __FILE__, 'wcmp_check_if_another_vendor_plugin_exits' );
register_activation_hook( __FILE__, 'activate_wcmp_plugin');
register_activation_hook( __FILE__, 'flush_rewrite_rules' );


if(!class_exists('WCMp') && WC_Dependencies_Product_Vendor::is_woocommerce_active() ) {
	global $WCMp;
	
	require_once( 'classes/class-wcmp.php' );
	$WCMp = new WCMp( __FILE__ );
	$GLOBALS['WCMp'] = $WCMp;	
	
	if( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'WCMp_action_links');
	}
} else {
	add_action( 'admin_notices', 'wcmp_admin_notice' );
	function wcmp_admin_notice() {
		?>
    <div class="error">
        <p><?php _e( 'WCMp plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugins to be active!', WCMp_TEXT_DOMAIN ); ?></p>
    </div>
    <?php
	}
}
?>
