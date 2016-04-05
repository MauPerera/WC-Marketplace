<?php
/**
 * WCMp Vendor Shop Settings Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		DualCube
 */
 
class WCMp_Shop_Setting_Shortcode {
	public $error_msg;

	public function __construct() {

	}

	/**
	 * Output the shop settings shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $attr ) {
		global $WCMp;
		$WCMp->nocache();
		if ( ! defined( 'MNDASHBAOARD' ) ) define( 'MNDASHBAOARD', true );
		
		$frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WCMp->plugin_url );
		$suffix 				= defined( 'WCMP_SCRIPT_DEBUG' ) && WCMP_SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script('wcmp_profile_edit_js', $frontend_script_path.'/profile_edit'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		
		$user_id = get_current_user_id();
		$vendor = get_wcmp_vendor($user_id);
		$is_saved = 0;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(isset($_POST['store_save'])) {
				$error_msg = $WCMp->vendor_dashboard->save_store_settings($vendor->id, $_POST);
				if(empty($error_msg)) {
					$is_saved = 1;
				}
				else {
					$is_saved = $error_msg;
				}				
			}
		}
		
		$user_array =  $WCMp->user->get_vendor_fields( $vendor->id );
		$user_array['is_shop_settings_saved'] = $is_saved;
		?>
		<?php
					if($user_array['is_shop_settings_saved'] == 1) { ?>
						<div style="margin-bottom:10px; width:98%;" class="green_massenger"><i class="fa fa-check"></i> &nbsp; <?php _e( 'All Options Saved', $WCMp->text_domain );?></div>
						
					<?php } else { if(!empty($user_array['is_shop_settings_saved'])){ ?>
						<div style="margin-bottom:10px; width:98%;" class="red_massenger"><i class="fa fa-times"></i> &nbsp; <?php echo $user_array['is_shop_settings_saved'];?></div>
					<?php } }?>	
		<div class="wcmp_remove_div">
		
			<div class="wcmp_main_page">  <?php 
				$WCMp->template->get_template( 'vendor_dashboard_menu.php', array('selected_item' => 'shop_front'));
				$WCMp->template->get_template( 'shortcode/shop_settings.php', $user_array);
			?>
			</div>
		</div>
		<?php
	}
}
