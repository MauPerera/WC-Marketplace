<?php
/**
 * WCMp Vendor Report Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Vendor_Report_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the vendor report shortcode.
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
		if(is_user_logged_in() )	{ 
			if(is_user_wcmp_vendor($user->ID)) {
				
				
				if(isset($_GET['wcmp_stat_start_dt'])) $start_date = $_GET['wcmp_stat_start_dt'];
				else $start_date = date('01-m-Y'); // hard-coded '01' for first day     
				
				if(isset($_GET['wcmp_stat_end_dt'])) $end_date = $_GET['wcmp_stat_end_dt'];
				else $end_date = date('t-m-Y'); // hard-coded '01' for first day
				
				$vendor = get_wcmp_vendor($user->ID);
				
				$WCMp_Plugin_Post_Reports = new WCMp_Report();
				$array_report = $WCMp_Plugin_Post_Reports->vendor_sales_stat_overview( $vendor, $start_date, $end_date);
				?>
				<div class="wcmp_remove_div">
					<div class="wcmp_main_page"> 
						<?php							
							$WCMp->template->get_template( 'vendor_dashboard_menu.php', array('selected_item' => 'vendor_report'));
							$WCMp->template->get_template( 'shortcode/vendor_report.php', $array_report);
						?> 
					</div>
				</div>
				<?php
			}
		}
	}
}