<?php
/**
 * WCMp Vendor Transaction Details Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Vendor_Transaction_Detail_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the vendor transaction details shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $attr ) {
		global $WCMp;
		$WCMp->nocache();
		$transaction_ids = array();
		if ( ! defined( 'MNDASHBAOARD' ) ) define( 'MNDASHBAOARD', true ); 
		$suffix = defined( 'WCMP_SCRIPT_DEBUG' ) && WCMP_SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery_ui_css',  $WCMp->plugin_url.'assets/frontend/css/jquery-ui'.$suffix.'.css', array(), $WCMp->version);
		$frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WCMp->plugin_url );
		wp_enqueue_script('trans_dtl_js', $frontend_script_path.'transaction_detail'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		$user_id = get_current_user_id();
		if(is_user_wcmp_vendor($user_id)) {
			$vendor = get_wcmp_vendor($user_id);
			$start_date = date('01-m-Y');				
			$end_date =  date('t-m-Y'); 
			if($_SERVER['REQUEST_METHOD'] == 'GET') {
				if(!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
					$transaction_details = $WCMp->transaction->get_transactions($vendor->term_id, $_GET['from_date'], $_GET['to_date'], false);
				} else if(!empty($_GET['from_date'])) {
					$transaction_details = $WCMp->transaction->get_transactions($vendor->term_id, $_GET['from_date'], date('j-n-Y'), false);
				} else {
					$transaction_details = $WCMp->transaction->get_transactions($vendor->term_id,  $start_date, $end_date, false);
				}
			} else {
				$transaction_details = $WCMp->transaction->get_transactions($vendor->term_id, $start_date, $end_date, false);
			}
			if(!empty($transaction_details)) {
				foreach($transaction_details as $transaction_id => $detail ) {
					$transaction_ids[] = $transaction_id;
				}
			}			
			?>
			<div class="wcmp_remove_div">
				<div class="wcmp_main_page"> 
					<?php
						$WCMp->template->get_template( 'vendor_dashboard_menu.php', array('selected_item' => 'history'));
						$WCMp->template->get_template( 'shortcode/vendor_transactions.php', array('transactions' => $transaction_ids) );
						wp_localize_script('trans_dtl_js', 'wcmp_vendor_transactions_array', $transaction_ids);  
					?>
				</div>
			</div>
		<?php
		}
	}
}