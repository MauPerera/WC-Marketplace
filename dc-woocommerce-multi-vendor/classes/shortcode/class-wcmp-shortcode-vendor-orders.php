<?php
/**
 * WCMp Vendor Orders Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Vendor_Orders_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the Vendor Orders shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $attr ) {
		global $woocommerce, $WCMp, $wpdb;
		$WCMp->nocache();
		if ( ! defined( 'MNDASHBAOARD' ) ) define( 'MNDASHBAOARD', true );
		
		$frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WCMp->plugin_url );
		$suffix 				= defined( 'WCMP_SCRIPT_DEBUG' ) && WCMP_SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script('vendor_orders_js', $frontend_script_path. 'vendor_orders'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		wp_localize_script('vendor_orders_js', 'wcmp_mark_shipped_text', array('text' => __('Order is marked as shipped.', $WCMp->text_domain),'image'=> $WCMp->plugin_url.'assets/images/roket-green.png' ));
		
		$user = wp_get_current_user();
		$vendor = get_wcmp_vendor($user->ID);
		
		if($vendor) {
			
			if(!empty($_GET['wcmp_start_date_order'])) $start_date = $_GET['wcmp_start_date_order'];
			else $start_date = date('01-m-Y'); // hard-coded '01' for first day     
				
			if(!empty($_GET['wcmp_end_date_order'])) $end_date = $_GET['wcmp_end_date_order'];
			else $end_date =  date('t-m-Y'); // hard-coded '01' for first day
			
			$start_date = date('Y-m-d G:i:s', strtotime($start_date));
			$end_date = date('Y-m-d G:i:s', strtotime($end_date. ' +1 day'));	
			$customer_orders = $wpdb->get_results( "SELECT DISTINCT order_id from `{$wpdb->prefix}wcmp_vendor_orders` where commission_id > 0 AND vendor_id = '".$vendor->id."' AND (`created` >= '".$start_date."' AND `created` <= '".$end_date."') and `is_trashed` != 1 ORDER BY `created` DESC" , ARRAY_A);
			$orders_array = array();
			if(!empty($customer_orders)) {
				foreach($customer_orders as $order_obj) {
					if(isset($order_obj['order_id'])) {
						if(get_post_status($order_obj['order_id']) == 'wc-completed') {
							$orders_array['completed'][] = $order_obj['order_id'];
						} else if(get_post_status($order_obj['order_id']) == 'wc-processing') {
							$orders_array['processing'][] = $order_obj['order_id'];
						}
						$orders_array['all'][] = $order_obj['order_id'];
					}
				}
			}
			if(!isset($orders_array['all'])) $orders_array['all'] = array();
			if(!isset($orders_array['processing'])) $orders_array['processing'] = array();
			if(!isset($orders_array['completed'])) $orders_array['completed'] = array();
			?>
			<div class="wcmp_remove_div">
				<div class="wcmp_main_page"> 
					<?php
						$WCMp->template->get_template( 'vendor_dashboard_menu.php', array('selected_item' => 'orders'));
						$WCMp->template->get_template( 'shortcode/vendor_orders.php', array('vendor' => $vendor, 'customer_orders' => $orders_array));
						wp_localize_script('vendor_orders_js', 'wcmp_vendor_all_orders_array', $orders_array['all']);  
						wp_localize_script('vendor_orders_js', 'wcmp_vendor_processing_orders_array', $orders_array['processing']);
						wp_localize_script('vendor_orders_js', 'wcmp_vendor_completed_orders_array', $orders_array['completed']);
					?> 
				</div>
			</div>
			<?php
		}
	}
}