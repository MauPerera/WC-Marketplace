<?php
/**
 * WCMp Vendor Order Detail Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Vendor_Order_Detail_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the vendor order dtail shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $attr ) {
		global $WCMp;
		$WCMp->nocache();
		if ( ! defined( 'MNDASHBAOARD' ) ) define( 'MNDASHBAOARD', true );
		$WCMp->template->get_template( 'shortcode/vendor_order_detail.php' );
	}
}