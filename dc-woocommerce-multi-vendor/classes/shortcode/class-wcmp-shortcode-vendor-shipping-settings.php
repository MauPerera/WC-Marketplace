<?php
/**
 * WCMp Vendor Shipping Settings Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Vendor_Shipping_Settings_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the vendor shipping settings shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $attr ) {
		global $WCMp;
		$WCMp->nocache();
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
		wp_enqueue_script('wcmp_profile_edit_js', $frontend_script_path.'/profile_edit'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		?>
		<div class="wcmp_remove_div">
			<div class="wcmp_main_page">
		<?php		
		$WCMp->template->get_template( 'vendor_dashboard_menu.php', array('selected_item' => 'shipping') );
		$wcmp_payment_settings_name = get_option('wcmp_payment_settings_name');
		$_vendor_give_shipping = get_user_meta(get_current_user_id(), '_vendor_give_shipping', true);
		if(isset($wcmp_payment_settings_name['give_shipping']) && empty($_vendor_give_shipping)){
			$WCMp->template->get_template( 'shortcode/vendor_shipping.php' );
		}		
		else {
			?>
			<div class="wcmp_main_holder toside_fix">
				<div class="wcmp_headding1">
					<ul>
						<li><?php echo __('Shipping',$WCMp->text_domain); ?></li>
					</ul>
					
					<div class="clear"></div> 
				</div>
				<p><?php echo __('Sorry you are not authorized for this pages. Please contact with admin.',$WCMp->text_domain); ?></p>
			</div>			
			<?php			
		}
		?>
		</div>
		</div>
		<?php
		
		
	}
}
