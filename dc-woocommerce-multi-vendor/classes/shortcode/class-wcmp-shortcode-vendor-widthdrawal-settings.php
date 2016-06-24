<?php
/**
 * WCMp Vendor Dashboard Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Vendor_Widthdrawal_Settings_Shortcode {

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
		
		$user = wp_get_current_user();
		$vendor = get_wcmp_vendor($user->ID);
		
		if($vendor) {
			
			$frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
			$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
			$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WCMp->plugin_url );
			$suffix 				= defined( 'WCMP_SCRIPT_DEBUG' ) && WCMP_SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script('vendor_withdrawal_js', $frontend_script_path. 'vendor_withdrawal'.$suffix.'.js', array('jquery'), $WCMp->version, true);
			
			$meta_query['meta_query'] = array(
				array(
					'key' => '_paid_status',
					'value' => 'unpaid',
					'compare' => '='
				),
				array(
						'key' => '_commission_vendor',
						'value' => absint($vendor->term_id),
						'compare' => '='
				)
			);
			$vendor_all_orders = $vendor->get_orders(false, false, $meta_query);
			
			if($vendor_all_orders) {
				$count_orders = count($vendor_all_orders);
			} else {
				$count_orders = 0;
			}
		
		
			$customer_orders = array();
			$customer_orders = $vendor->get_orders(6, 0, $meta_query);
			?>
			<div class="wcmp_remove_div">
				<div class="wcmp_main_page"> 
					<?php
						$WCMp->template->get_template( 'vendor_dashboard_menu.php', array( 'selected_item' => 'widthdrawal'));
						$WCMp->template->get_template( 'shortcode/vendor_withdrawal.php', array('vendor' => $vendor, 'commissions' => $customer_orders, 'total_orders' => $count_orders));
					?>
				</div>
			</div>
			<?php
		}
	}
}