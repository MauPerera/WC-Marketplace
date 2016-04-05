<?php
/**
 * WCMp Vendor Dashboard Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Vendor_University_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the vendor dashboard shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $attr ) {
		global $WCMp;
		$frontend_style_path = $WCMp->plugin_url . 'assets/frontend/css/';
		$frontend_style_path = str_replace( array( 'http:', 'https:' ), '', $frontend_style_path );
		$suffix 				= defined( 'WCMP_SCRIPT_DEBUG' ) && WCMP_SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style('wcmp_new_vandor_dashboard_css',  $frontend_style_path .'vendor_dashboard'.$suffix.'.css', array(), $WCMp->version);
		wp_enqueue_style('font-awesome',  $frontend_style_path . 'font-awesome.min.css', array(), $WCMp->version);
		
		$frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WCMp->plugin_url );
		$suffix 				= defined( 'WCMP_SCRIPT_DEBUG' ) && WCMP_SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script('wcmp_new_vandor_dashboard_js', $frontend_script_path.'/vendor_dashboard'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		
		$WCMp->template->get_template( 'vendor_dashboard_menu.php', array('selected_item' => 'university') );
		$WCMp->template->get_template( 'shortcode/vendor_university.php' );
	}
	
}