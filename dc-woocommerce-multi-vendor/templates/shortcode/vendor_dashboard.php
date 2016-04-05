<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_dashboard.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.3.0
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;
$user = wp_get_current_user();
$vendor = get_wcmp_vendor($user->ID);


if(is_user_wcmp_vendor($user->ID)) {
	$pages = get_option("wcmp_pages_settings_name");
	$address1 = apply_filters( 'woocommerce_my_account_my_address_formatted_address', array(
						'address_1'		=> $vendor->address_1,
						'address_2'		=> $vendor->address_2,
						'city'			=> $vendor->city,
						'state'			=> $vendor->state,
						'postcode'		=> $vendor->postcode,
						'country'		=> $vendor->country
					), $user->ID, 'billing' );
	
	$formatted_address = $woocommerce->countries->get_formatted_address( $address1 );
	$image = $vendor->image;
	if(!$vendor->image) $vendor->image = $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';	
	?>
	<div class="wcmp_main_holder toside_fix">
		<div class="wcmp_headding1">
			<ul>
				<li><?php echo __('Dashboard',$WCMp->text_domain); ?></li>
			</ul>
			<span><?php echo Date('d M Y'); ?></span>
			<div class="clear"></div>
		</div>
		<?php 
		if( isset($WCMp->vendor_caps->general_cap['notify_configure_vendor_store']) ){ 
			$user_meta_data = get_user_meta($user->ID);
			if(!isset($user_meta_data['_vendor_image']) || !isset($user_meta_data['_vendor_banner']) || !isset($user_meta_data['_vendor_address_1']) || !isset($user_meta_data['_vendor_city']) || 
				!isset($user_meta_data['_vendor_state']) || !isset($user_meta_data['_vendor_country']) || !isset($user_meta_data['_vendor_phone']) || !isset($user_meta_data['_vendor_postcode']) ) {
			?>
			<div class="vendor_non_configuration_msg">
			<?php _e('<h4>You have not configured your store properly missing some required fields!</h4>', $WCMp->text_domain); ?>
			</div>
			
		<?php } }?>	
		<?php		
		$notice_data = get_option('wcmp_notices_settings_name'); 
		$notice_to_be_display = '';
		
			$dismiss_notices_ids_array = array();
			$dismiss_notices_ids = get_user_meta($user->ID,'_wcmp_vendor_message_deleted', true);
			if(!empty($dismiss_notices_ids)) {
				$dismiss_notices_ids_array = explode(',',$dismiss_notices_ids);
			}
			$args_msg = array(
				'posts_per_page'   => 1,
				'offset'           => 0,
				'post__not_in'     => $dismiss_notices_ids_array,				
				'orderby'          => 'date',
				'order'            => 'DESC',				
				'post_type'        => 'wcmp_vendor_notice',				
				'post_status'      => 'publish',
				'suppress_filters' => true 
			);
			$msgs_array = get_posts( $args_msg );
			if(is_array($msgs_array) && !empty($msgs_array) && count($msgs_array) > 0) {			
				$msg = $msgs_array[0];				
				?>
				<div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
				<div class="wcmp_admin_massege" id="admin-massege">
					<h2><?php echo __('Admin Message:',$WCMp->text_domain); ?> </h2>
					<span> <?php echo $msg->post_title; ?> </span><br/>
					<span class="mormaltext" style="font-weight:normal;"> <?php echo $short_content = substr(stripslashes(strip_tags($msg->post_content)),0,155); if(strlen(stripslashes(strip_tags($msg->post_content))) > 155) {echo '...'; } ?> </span><br/>
					<a href="<?php echo get_permalink(get_option('wcmp_product_vendor_announcements_page_id')); ?>"><button><?php echo __('DETAILS',$WCMp->text_domain);?></button></a>
					<div class="clear"></div>
					<a href="#" id="cross-admin" data-element = "<?php echo $msg->ID; ?>"  class="wcmp_cross wcmp_delate_announcements_dashboard"><i class="fa fa-times-circle"></i></a> 
				</div>		
		<?php }  ?>
		
		<div class="wcmp_tab">
			<ul>
				<li><a href="#today" id="today_click" class="active"><?php echo __('Today',$WCMp->text_domain);?></a></li>
				<li><a href="#theweek" id="theweek_click" ><?php echo __(' This Week',$WCMp->text_domain);?></a></li>
			</ul>
			<div class="wcmp_tabbody"  id="today" >
			<?php 
			global $wpdb;
			$prefix = $wpdb->prefix;
			$current_user = wp_get_current_user();
			$current_user_id =  $current_user->ID;			
			$today_date = @date('Y-m-d');
			
			$sale_results_whole_today = $wpdb->get_results( "SELECT * FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` like '".$today_date."%' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 ", OBJECT );	
			$sale_results_whole_today_row_show = $wpdb->get_results( "SELECT * FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` like '".$today_date."%' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 group by order_id ", OBJECT );
			$shipping_pending_results_whole_today = $wpdb->get_results( "SELECT * FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` like '".$today_date."%' and `commission_id` != 0 and `commission_id` != '' and `shipping_status` != 1 and `is_trashed` != 1 ", OBJECT );
			
			
			
			$number_of_pending_shipping_whole_today = count($shipping_pending_results_whole_today);
			if($number_of_pending_shipping_whole_today <= 6) {
				$number_of_pending_shipping_show_today = $number_of_pending_shipping_whole_today;
			}
			else {
				$number_of_pending_shipping_show_today = 6;
			}
			$total_page_pending_shipping_today = ceil( $number_of_pending_shipping_whole_today / 6 );
			$whole_row_today = count($sale_results_whole_today_row_show);
			if($whole_row_today > 0 && $whole_row_today <= 6) {
				$displayed_row = 	$whole_row_today ;				
			}
			else if ( $whole_row_today > 6 ) {
				$displayed_row = 	6;
			}
			else {
				$displayed_row = 	0;
			}
			$total_page_sale_today = ceil($no_of_pege_today_sale = $whole_row_today / 6);			
			
			$item_total = 0;
			$comission_total_arr = array();
			$total_comission = 0;
			$shipping_total = 0;
			$tax_total = 0;
			$net_balance_today = 0;
			foreach ($sale_results_whole_today as $sale_row) {
				$order_item_id = $sale_row->order_item_id;
				$item_total += get_metadata( 'order_item', $sale_row->order_item_id, '_line_total', true );
				if(!in_array($sale_row->commission_id,$comission_total_arr)) {
					$comission_total_arr[] = $sale_row->commission_id;
				}
				//$comission_total = get_metadata('post', $object_id, $meta_key, true);
			}	
			foreach( $comission_total_arr as $comission_id ) {
				$vendor_comission = get_metadata('post', $comission_id, '_commission_amount', true);
				$shipping_comission = get_metadata('post', $comission_id, '_shipping', true);
				$shipping_total += $shipping_comission;
				$tax_comission = get_metadata('post', $comission_id, '_tax', true);
				$tax_total += $tax_comission; 
				$total_comission += ($vendor_comission + $shipping_comission + $tax_comission);
				$paid_status = get_metadata('post', $comission_id, '_paid_status', true);
				if( $paid_status == "unpaid" ) {
					$net_balance_today += ($vendor_comission + $shipping_comission + $tax_comission);					
				}
				
			}
			
			//$total_comission = number_format($total_comission , 2);
			$int_comission = intval($total_comission);
			if($total_comission == $int_comission ) {
				$precision_value_comission = '00';
			}
			else {
				$precision_value_comission = ($total_comission - $int_comission) * 100;
			}
			$item_total += ($shipping_total + $tax_total);
			//$item_total = number_format($item_total,2);
			$int_item_total = intval($item_total);
			
			if($item_total == $int_item_total ) {
				$precision_value_item = '00';
			}
			else {
				$precision_value_item = ($item_total - $int_item_total) * 100;
			}
			
			//$net_balance_today = number_format($net_balance_today,2);
			$int_net_balance_today = intval($net_balance_today);
			
			if($net_balance_today == $int_net_balance_today ) {
				$precision_net_balance = '00';
			}
			else {
				$precision_net_balance = ($net_balance_today - $int_net_balance_today) * 100;
			}			
			?>
				<input type = "hidden" name="today_sale_current_page" id="today_sale_current_page" value="1">
				<input type = "hidden" name="today_sale_next_page" id="today_sale_next_page" value="<?php if($total_page_sale_today > 1){ echo 2;}else { echo 1;} ?>">
				<input type = "hidden" name="today_sale_total_page" id="today_sale_total_page" value="<?php echo $total_page_sale_today; ?>">
				
				<div class="wcmp_dashboard_display_box">
          <h4><?php echo __('Todays Sales',$WCMp->text_domain);?></h4>
          <h3><sup><?php echo get_woocommerce_currency_symbol();  ?></sup><?php echo number_format($int_item_total,0); ?><span>.<?php echo substr($precision_value_item,0,2); ?></span></h3>
        </div>
        <div class="wcmp_dashboard_display_box">
					<h4><?php echo __('Todays Earnings',$WCMp->text_domain);?></h4>
					<h3><sup><?php echo get_woocommerce_currency_symbol();  ?></sup><?php echo number_format($int_comission,0); ?><span>.<?php echo substr($precision_value_comission,0,2); ?></span></h3>
				</div>
				<div class="wcmp_dashboard_display_box">
					<h4><?php echo __('Net Balance',$WCMp->text_domain); ?></h4>
					<h3><sup><?php echo get_woocommerce_currency_symbol();  ?></sup><?php echo number_format($int_net_balance_today,0); ?><span>.<?php echo substr($precision_net_balance,0,2); ?></span></h3>
				</div>
				<div class="clear"></div>
				<h3 class="wcmp_black_headding"><?php echo __('Sales', $WCMp->text_domain); ?></h3>
				<div class="wcmp_table_holder">
					<table id="wcmp_sale_report_table_today" width="100%" border="0" cellspacing="0" cellpadding="0">
						<?php 
							//show sales items
							$WCMp->template->get_template( 'shortcode/vendor_dashboard_sales_item_header.php');
						?>
						<?php 
							//show sales items
							$WCMp->template->get_template( 'shortcode/vendor_dashboard_sales_item.php', array('vendor' => $vendor, 'today_or_weekly' => 'today', 'start'=> 0, 'to'=> 6));
						?>
					</table>
				</div>
				
				
				
				<div class="wcmp_table_loader">
				<div class="ajax_loader_class"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
				<?php  echo __('Showing Results', $WCMp->text_domain); ?><span> <span class="wcmp_front_count_first_num_today"><?php echo $displayed_row; ?></span> <?php echo __('  out of  ',$WCMp->text_domain); echo $whole_row_today; ?></span>
					<?php if( $whole_row_today > 6){?><button class="wcmp_black_btn wcmp_frontend_sale_show_more_button" element-data="sale_today_more" style="float:right"><?php echo __('Show More',$WCMp->text_domain);?></button><?php }?>
					<div class="clear"></div>
				</div>
				
				
				
				
				<h3 class="wcmp_black_headding"><?php echo __('Pending Shipping', $WCMp->text_domain); ?></h3>
				<div class="wcmp_table_holder">
					<table id="wcmp_pending_shipping_report_table_today" width="100%" border="0" cellspacing="0" cellpadding="0">
						<?php 
							//show pending shipping items
							$WCMp->template->get_template( 'shortcode/vendor_dasboard_pending_shipping_items_header.php');
							$WCMp->template->get_template( 'shortcode/vendor_dasboard_pending_shipping_items.php', array('vendor' => $vendor, 'today_or_weekly' => 'today', 'start'=> 0, 'to'=> 6));
						?>
					</table>
				</div>
				<input type = "hidden" name="today_pending_shipping_current_page" id="today_pending_shipping_current_page" value="1">
				<input type = "hidden" name="today_pending_shipping_next_page" id="today_pending_shipping_next_page" value="<?php if($total_page_pending_shipping_today > 1){ echo 2;}else { echo 1;} ?>">
				<input type = "hidden" name="today_pending_shipping_total_page" id="today_pending_shipping_total_page" value="<?php echo $total_page_pending_shipping_today; ?>">
				<div class="wcmp_table_loader">
					<div class="ajax_loader_class"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
					<?php echo __('Showing Results',$WCMp->text_domain);?> <span> <span class="wcmp_front_count_first_num_today_ps"><?php echo $number_of_pending_shipping_show_today; ?></span> <?php echo __(' out of ',$WCMp->text_domain);?> <?php echo $number_of_pending_shipping_whole_today; ?></span>
					<?php if($number_of_pending_shipping_whole_today > 6) {?><button class="wcmp_black_btn wcmp_frontend_pending_shipping_show_more_button" element-data="pending_shipping_today_more" style="float:right"><?php echo __('Show More',$WCMp->text_domain);?></button><?php }?>
					<div class="clear"></div>
				</div>
			</div>
			
			
			
			<div class="wcmp_tabbody" id="theweek" >
			<?php	
			$curent_week_range = wcmp_rangeWeek($today_date);			
			$sale_results_whole_week = $wpdb->get_results( "SELECT * FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` >= '".$curent_week_range['start']."' and  `created` <= '".$curent_week_range['end']."' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 ", OBJECT );	
			$sale_results_whole_week_row_show = $wpdb->get_results( "SELECT * FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` >= '".$curent_week_range['start']."' and `created` <= '".$curent_week_range['end']."' and  `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 group by order_id ", OBJECT );			
			$pending_shipping_results_whole_week_row = $wpdb->get_results( "SELECT * FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` >= '".$curent_week_range['start']."' and `created` <= '".$curent_week_range['end']."' and  `commission_id` != 0 and `commission_id` != '' and `shipping_status` != 1 and `is_trashed` != 1 ", OBJECT );
			
			$week_pending_shipping_whole = count($pending_shipping_results_whole_week_row);
			if($week_pending_shipping_whole <= 6) {
				$week_pending_shipping_show = $week_pending_shipping_whole;
			}
			else {
				$week_pending_shipping_show = 6;
			}
			
			$total_page_pending_shipping_week = ceil( $week_pending_shipping_whole / 6 );
			
			$whole_row_week = count($sale_results_whole_week_row_show);
			if($whole_row_week > 0 && $whole_row_week <= 6) {
				$displayed_row_week = 	$whole_row_week ;				
			}
			else if ( $whole_row_week > 6 ) {
				$displayed_row_week = 	6;
			}
			else {
				$displayed_row_week = 	0;
			}	
			$total_page_sale_week = ceil($no_of_pege_week_sale = $whole_row_week / 6);
			
			$item_total_week = 0;
			$comission_total_arr_week = array();
			$total_comission_week = 0;
			$shipping_total_week = 0;
			$tax_total_week = 0;
			$net_balance_week = 0;
			foreach ($sale_results_whole_week as $sale_row_week) {
				$order_item_id_week = $sale_row_week->order_item_id;
				$item_total_week += get_metadata( 'order_item', $sale_row_week->order_item_id, '_line_total', true );
				if(!in_array($sale_row_week->commission_id,$comission_total_arr_week)) {
					$comission_total_arr_week[] = $sale_row_week->commission_id;
				}
				//$comission_total = get_metadata('post', $object_id, $meta_key, true);
			}	
			foreach( $comission_total_arr_week as $comission_id_week ) {
				$vendor_comission_week = get_metadata('post', $comission_id_week, '_commission_amount', true);
				$shipping_comission_week = get_metadata('post', $comission_id_week, '_shipping', true);
				$shipping_total_week += $shipping_comission_week;
				$tax_comission_week = get_metadata('post', $comission_id_week, '_tax', true);
				$tax_total_week += $tax_comission_week; 
				$total_comission_week += ($vendor_comission_week + $shipping_comission_week + $tax_comission_week);
				$paid_status_week = get_metadata('post', $comission_id_week, '_paid_status', true);
				if( $paid_status_week == "unpaid" ) {
					$net_balance_week += ($vendor_comission_week + $shipping_comission_week + $tax_comission_week);					
				}
				
			}
			//$total_comission = number_format($total_comission , 2);
			$int_comission_week = intval($total_comission_week);
			if($total_comission_week == $int_comission_week ) {
				$precision_value_comission_week = '00';
			}
			else {
				$precision_value_comission_week = ($total_comission_week - $int_comission_week) * 100;
			}
			$item_total_week += ($shipping_total_week + $tax_total_week);
			//$item_total = number_format($item_total,2);
			$int_item_total_week = intval($item_total_week);
			
			if($item_total_week == $int_item_total_week ) {
				$precision_value_item_week = '00';
			}
			else {
				$precision_value_item_week = ($item_total_week - $int_item_total_week) * 100;
			}
			
			//$net_balance_today = number_format($net_balance_today,2);
			$int_net_balance_week = intval($net_balance_week);
			
			if($net_balance_week == $int_net_balance_week ) {
				$precision_net_balance_week = '00';
			}
			else {
				$precision_net_balance_week = ($net_balance_week - $int_net_balance_week) * 100;
			}			
			?>
				<input type = "hidden" name="week_sale_current_page" id="week_sale_current_page" value="1">
				<input type = "hidden" name="week_sale_next_page" id="week_sale_next_page" value="<?php if($total_page_sale_week > 1){ echo '2';} ?>">
				<input type = "hidden" name="week_sale_total_page" id="week_sale_total_page" value="<?php echo $total_page_sale_week; ?>">
				
				<div class="wcmp_dashboard_display_box">
          <h4><?php echo __('Weekly Sales',$WCMp->text_domain);?></h4>
          <h3><sup><?php echo get_woocommerce_currency_symbol();  ?></sup><?php echo number_format($int_item_total_week,0); ?><span>.<?php echo substr($precision_value_item_week,0,2); ?></span></h3>
        </div>
        <div class="wcmp_dashboard_display_box">
					<h4><?php echo __('Weekly Earnings',$WCMp->text_domain);?></h4>
					<h3><sup><?php echo get_woocommerce_currency_symbol();  ?></sup><?php echo number_format($int_comission_week,0); ?><span>.<?php echo substr($precision_value_comission_week,0,2); ?></span></h3>
				</div>
				<div class="wcmp_dashboard_display_box">
					<h4><?php echo __('Weekly Balance',$WCMp->text_domain); ?></h4>
					<h3><sup><?php echo get_woocommerce_currency_symbol();  ?></sup><?php echo number_format($int_net_balance_week,0); ?><span>.<?php echo substr($precision_net_balance_week,0,2); ?></span></h3>
				</div>
				<div class="clear"></div>
				<h3 class="wcmp_black_headding"><?php echo __('Sales', $WCMp->text_domain); ?></h3>
				<div class="wcmp_table_holder">
					<table id="wcmp_sale_report_table_week" width="100%" border="0" cellspacing="0" cellpadding="0">
						<?php 
							//show sales items
							$WCMp->template->get_template( 'shortcode/vendor_dashboard_sales_item_header.php');
						?>
						<?php 
							//show sales items
							$WCMp->template->get_template( 'shortcode/vendor_dashboard_sales_item.php', array('vendor' => $vendor, 'today_or_weekly' => 'weekly', 'start'=> 0, 'to'=> 6));
						?>
					</table>
				</div>
				<div class="wcmp_table_loader">
				<div class="ajax_loader_class"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
				<?php  echo __('Showing Results', $WCMp->text_domain); ?><span> <span class="wcmp_front_count_first_num_week"><?php echo $displayed_row_week;?></span> <?php echo __('  out of  ',$WCMp->text_domain); echo $whole_row_week; ?></span>
					<?php if( $whole_row_week > 6){?><button class="wcmp_black_btn wcmp_frontend_sale_show_more_button" element-data="sale_weekly_more" style="float:right"><?php echo __('Show More',$WCMp->text_domain);?></button><?php }?>
					<div class="clear"></div>
				</div>
				<h3 class="wcmp_black_headding"><?php echo __('Pending Shipping', $WCMp->text_domain); ?></h3>
				<div class="wcmp_table_holder">
					<table id="wcmp_pending_shipping_report_table_week" width="100%" border="0" cellspacing="0" cellpadding="0">
						<?php 
							$WCMp->template->get_template( 'shortcode/vendor_dasboard_pending_shipping_items_header.php');
							//show pending shipping items
							$WCMp->template->get_template( 'shortcode/vendor_dasboard_pending_shipping_items.php', array('vendor' => $vendor, 'today_or_weekly' => 'weekly', 'start'=> 0, 'to'=> 6));
						?>
					</table>
				</div>
				<input type = "hidden" name="week_pending_shipping_current_page" id="week_pending_shipping_current_page" value="1">
				<input type = "hidden" name="week_pending_shipping_next_page" id="week_pending_shipping_next_page" value="<?php if($total_page_pending_shipping_week > 1){ echo '2';} ?>">
				<input type = "hidden" name="week_pending_shipping_total_page" id="week_pending_shipping_total_page" value="<?php echo $total_page_pending_shipping_week; ?>">
				<div class="wcmp_table_loader">
				<div class="ajax_loader_class"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
				<?php echo __('Showing Results',$WCMp->text_domain);?> <span> <span class="wcmp_front_count_first_num_week_ps"><?php echo $week_pending_shipping_show; ?></span> <?php echo __(' out of ',$WCMp->text_domain);?> <?php echo $week_pending_shipping_whole; ?></span>
					<?php if( $week_pending_shipping_whole > 6) {?><button class="wcmp_black_btn wcmp_frontend_pending_shipping_show_more_button" element-data="pending_shipping_weekly_more" style="float:right"><?php echo __('Show More',$WCMp->text_domain);?></button><?php }?>
					<div class="clear"></div>
				</div>		
			</div>
		</div>
	</div>
	<?php
} ?>
<style type="text/css">
.ajax_loader_class {
	width:100%;
	position:absolute;
	z-index:8888;
	background-color:#ddd;
	opacity:0.7;
	height:100%;
	text-align:center;
	display:none;
}
.ajax_loader_class img{	
	margin:50px auto;	
}
</style>
