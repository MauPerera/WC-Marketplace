<?php
/**
 * WCMp Vendor Withdrawal Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Vendor_Withdrawal_Request_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the vendor withdrawal shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $attr ) {
		global $WCMp;
		$WCMp->nocache();
		if ( ! defined( 'MNDASHBAOARD' ) ) define( 'MNDASHBAOARD', true );
		$WCMp->template->get_template( 'shortcode/vendor_withdrawal_request.php' );
	}
}