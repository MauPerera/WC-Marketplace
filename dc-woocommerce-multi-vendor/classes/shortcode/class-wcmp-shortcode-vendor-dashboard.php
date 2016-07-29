<?php
/**
 * WCMp Vendor Dashboard Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Vendor_Dashboard_Shortcode {

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
		$WCMp->nocache();		
		if ( ! defined( 'MNDASHBAOARD' ) ) define( 'MNDASHBAOARD', true ); 
		$suffix = defined( 'WCMP_SCRIPT_DEBUG' ) && WCMP_SCRIPT_DEBUG ? '' : '.min';		
		$frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';		
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WCMp->plugin_url );		
		wp_enqueue_script('wcmp_frontend_vdashboard_js', $frontend_script_path.'wcmp_vendor_dashboard'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		
		$user = wp_get_current_user();
		if(is_user_logged_in() )	{ 
			if(is_user_wcmp_vendor($user->ID)) {
				
				$vendor = get_wcmp_vendor($user->ID);
				$vendor_all_orders = $vendor->get_orders();
				if($vendor_all_orders) {
					$count_orders = count($vendor_all_orders);
				} else {
					$count_orders = 0;
				}
				$customer_orders = array();
				$customer_orders = $vendor->get_orders(5, 0);
				
				?>
				<div class="wcmp_remove_div">
					<div class="wcmp_main_page"> 
						<?php
							$WCMp->template->get_template( 'vendor_dashboard_menu.php', array('selected_item' => 'dashboard'));
							$WCMp->template->get_template( 'shortcode/vendor_dashboard.php', array('vendor' => $vendor, 'customer_orders' => $customer_orders));
						?> 
					</div>
				</div>
				<?php
			} else {
				
				$WCMp->template->get_template( 'shortcode/non_vendor_dashboard.php' );
			}
		}
	}
}
