<?php
/**
 * The template for displaying single product page vendor tab 
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor_dashboard_menu.php
 *
 * @author 		dualcube
 * @package 	dc-product-vendor/Templates
 * @version   2.3.0
 */
global $WCMp;
$pages = get_option('wcmp_pages_settings_name');
$vendor = get_wcmp_vendor(get_current_user_id());
$notice_data = get_option('wcmp_notices_settings_name'); 
$notice_to_be_display = '';
if(!isset($selected_item)) $selected_item = '';
if(!$vendor->image) $vendor->image = $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
$wcmp_payment_settings_name = get_option('wcmp_payment_settings_name');
$_vendor_give_shipping = get_user_meta(get_current_user_id(), '_vendor_give_shipping', true);
$wcmp_capabilities_settings_name = get_option('wcmp_capabilities_settings_name');
$_vendor_submit_coupon = get_user_meta(get_current_user_id(), '_vendor_submit_coupon', true);
$policies_settings = get_option('wcmp_general_policies_settings_name');
$customer_support_details_settings = get_option('wcmp_general_customer_support_details_settings_name');
$is_policy_show_in_menu = 0;
$is_university_show_in_menu = 0;
if((isset($policies_settings['is_policy_on']) && (isset($policies_settings['is_cancellation_on']) || isset($policies_settings['is_refund_on']) || isset($policies_settings['is_shipping_on'])) && (isset($wcmp_capabilities_settings_name['can_vendor_edit_cancellation_policy']) || isset($wcmp_capabilities_settings_name['can_vendor_edit_refund_policy']) || isset($wcmp_capabilities_settings_name['can_vendor_edit_shipping_policy']) )) || (isset($customer_support_details_settings['is_customer_support_details']) &&  isset($wcmp_capabilities_settings_name['can_vendor_add_customer_support_details'] ) )) {
	$is_policy_show_in_menu = 1;
}
$general_settings = get_option('wcmp_general_settings_name');
if(isset($general_settings['is_university_on'])){
	$is_university_show_in_menu = 1;
}
$active_plugins = (array) get_option( 'active_plugins', array() );
if(in_array('wcmp-vendor_shop_seo/wcmp_vendor_shop_seo.php',$active_plugins)){
	$seo_active = true;
}
?>
<div class="wcmp_side_menu">
	<div class="wcmp_top_logo_div"> <img src="<?php echo $vendor->image;?>" alt="vendordavatar">
		<h3><?php $shop_name =  get_user_meta(get_current_user_id(),'_vendor_page_title',true); if(!empty($shop_name)) { echo $shop_name; } else { _e( 'Shop Name', $WCMp->text_domain );} ?></h3>
		<ul>
			<li><a target="_blank" href="<?php echo $vendor->permalink; ?>"><?php _e( 'Shop', $WCMp->text_domain ); ?></a> </li>			
			<li><a target="_self" href="<?php  echo isset($pages['vendor_announcements']) ? get_permalink($pages['vendor_announcements']) : ''; ?>"><?php _e( 'Announcements', $WCMp->text_domain ); ?></a></li>
		</ul>
	</div>
	<div class="wcmp_main_menu">
		<ul>
			<li class="ic_shop"><a target="_blank" href="<?php echo $vendor->permalink; ?>" data-menu_item="Vendor_shop" ><i class="icon_stand ic10"> </i> <span class="writtings"><?php _e( 'Shop', $WCMp->text_domain ); ?></span></a></li>
			<li class="ic_announment"><a  target="_self"  href="<?php echo isset($pages['vendor_announcements']) ? get_permalink($pages['vendor_announcements']) : ''; ?>" data-menu_item="vendor_announcements" ><i class="icon_stand ic9"> </i> <span class="writtings"><?php _e( 'Announcements', $WCMp->text_domain ); ?></span></a></li>
			<li><a <?php if($selected_item == "dashboard") { echo 'class="active"'; } ?> data-menu_item="dashboard" href="<?php echo isset($pages['vendor_dashboard']) ? get_permalink($pages['vendor_dashboard']) : ''; ?>" data-menu_item="dashboard" ><i class="icon_stand ic1"> </i> <span class="writtings"><?php _e( 'Dashboard', $WCMp->text_domain ); ?></span></a></li>
			<li class="hasmenu"><a <?php if(in_array($selected_item, array('shop_front', 'policies', 'billing', 'shipping'))) {  echo 'class="active"'; } ?> href="#"><i class="icon_stand ic2"> </i> <span class="writtings"><?php _e( 'Store Settings', $WCMp->text_domain ); ?></span></a>
				<ul class="submenu" <?php if(!in_array($selected_item, array('shop_front', 'policies', 'billing', 'shipping'))) { ?> style="display:none;"<?php } ?>>
					<li><a href="<?php echo isset($pages['shop_settings']) ? get_permalink($pages['shop_settings']) : ''; ?>" <?php if($selected_item == "shop_front") { echo 'class="selected_menu"'; } ?> data-menu_item="shop_front"><?php _e( '- Shop front', $WCMp->text_domain ); ?></a></li>
					<?php if($is_policy_show_in_menu == 1) {?>
					<li><a href="<?php echo isset($pages['vendor_policies']) ? get_permalink($pages['vendor_policies']) : ''; ?>" <?php if($selected_item == "policies") { echo 'class="selected_menu"'; } ?> data-menu_item="policies"><?php _e( '- Policies', $WCMp->text_domain ); ?></a></li>
					<?php }?>
					<li><a href="<?php echo isset($pages['vendor_billing']) ? get_permalink($pages['vendor_billing']) : ''; ?>" <?php if($selected_item == "billing") { echo 'class="selected_menu"'; } ?> data-menu_item="billing"><?php _e( '- Billing', $WCMp->text_domain ); ?></a></li>
					<?php if(isset($wcmp_payment_settings_name['give_shipping'])) { if(empty($_vendor_give_shipping)) {?>
					<li><a href="<?php echo isset($pages['vendor_shipping']) ? get_permalink($pages['vendor_shipping']) : ''; ?>" <?php if($selected_item == "shipping") { echo 'class="selected_menu"'; } ?> data-menu_item="shipping"><?php _e( '- Shipping', $WCMp->text_domain ); ?></a></li>
					<?php } }?>
					<?php do_action('wcmp_store_settings_sub_menu'); ?>
				</ul>
			</li>			
			<?php if($WCMp->vendor_caps->vendor_capabilities_settings('is_submit_product') && get_user_meta($vendor->id, '_vendor_submit_product' ,true)) { ?>
					<li class="hasmenu"><a <?php if(in_array($selected_item, array('product_manager', 'add_product_manager', 'pending_product_manager'))) { echo 'class="active"'; } ?>  data-menu_item="product_manager" href="<?php if(class_exists('WCMp_Frontend_Product_Manager')) { echo '#'; } else { echo apply_filters('wcmp_vendor_submit_product', admin_url( 'edit.php?post_type=product' )); } ?>"><span class="icon_stand ic3 shop_url"> </span> <span class="writtings"><?php _e( 'Product Manager', $WCMp->text_domain ); ?></span></a>
					<?php do_action('after_product_manager', $vendor, $selected_item); ?>
					</li>
			<?php } ?>
			<?php if((isset($seo_active) &&	$seo_active == true) || (isset($wcmp_capabilities_settings_name['is_submit_coupon']) && !empty($_vendor_submit_coupon))) {?>		
			<li class="hasmenu"><a href="#"><span class="icon_stand ic4"> </span> <span class="writtings"><?php _e( 'Promote', $WCMp->text_domain ); ?></span></a>
				<ul class="submenu" <?php if($selected_item != "coupon") { ?> style="display:none;" <?php } ?>>
					<?php if(isset($wcmp_capabilities_settings_name['is_submit_coupon']) && !empty($_vendor_submit_coupon)) {?>
					<li><a <?php if($selected_item == "coupon") { echo 'class="selected_menu"'; } ?> data-menu_item="coupon" href="<?php echo admin_url( 'edit.php?post_type=shop_coupon' );?>"><?php _e( '- Coupons', $WCMp->text_domain ); ?></a></li>
					<?php }?>
				</ul>
			</li>
			<?php }?>
			<li class="hasmenu"><a <?php if($selected_item == "vendor_report") { echo 'class="active"'; } ?> href="#"><span class="icon_stand ic5"> </span> <span class="writtings"><?php _e( 'Stats/Reports', $WCMp->text_domain ); ?></span></a>
				<ul class="submenu" <?php if($selected_item != "vendor_report") { ?> style="display:none;" <?php } ?>>
					<li><a <?php if($selected_item == "vendor_report") { echo 'class="selected_menu"'; } ?> data-menu_item="overview" href="<?php echo isset($pages['vendor_report']) ? get_permalink($pages['vendor_report']) : ''; ?>"><?php _e( '- Overview', $WCMp->text_domain ); ?></a></li>
				</ul>
			</li>
			<li><a <?php if($selected_item == "orders") { echo 'class="active"'; } ?> data-menu_item="orders" href="<?php echo isset($pages['view_order']) ? get_permalink($pages['view_order']) : ''; ?>"><span class="icon_stand ic6"> </span> <span class="writtings"><?php _e( 'Orders', $WCMp->text_domain ); ?></span></a></li>
			<li class="hasmenu"><a <?php if(in_array($selected_item, array('widthdrawal', 'history'))) {  echo 'class="active"'; } ?> href="#"><span class="icon_stand ic7"> </span><span class="writtings"><?php _e( 'Payments', $WCMp->text_domain ); ?></span></a>
				<ul class="submenu" <?php if(!in_array($selected_item, array('widthdrawal', 'history'))) { ?> style="display:none;"<?php } ?>>
					<li><a <?php if($selected_item == "widthdrawal") { echo 'class="selected_menu"'; } ?> data-menu_item="widthdrawal" href="<?php echo isset($pages['vendor_widthdrawals']) ? get_permalink($pages['vendor_widthdrawals']) : ''; ?>"><?php _e( '- Withdrawal', $WCMp->text_domain ); ?></a></li>
					<li><a <?php if($selected_item == "history") { echo 'class="selected_menu"'; } ?> data-menu_item="history" href="<?php echo isset($pages['vendor_transaction_detail']) ? get_permalink($pages['vendor_transaction_detail']) : ''; ?>"><?php _e( '- History', $WCMp->text_domain ); ?></a></li>
				</ul>
			</li>
			<?php if( $is_university_show_in_menu == 1) {?>
			<li><a <?php if($selected_item == "university") { echo 'class="active"'; } ?> data-menu_item="uiversity" href="<?php echo isset($pages['vendor_university']) ? get_permalink($pages['vendor_university']) : ''; ?>"><span class="icon_stand ic8"> </span> <span class="writtings"><?php _e( 'University', $WCMp->text_domain ); ?></span></a></li>
			<?php }?>
		</ul>
	</div>
</div>