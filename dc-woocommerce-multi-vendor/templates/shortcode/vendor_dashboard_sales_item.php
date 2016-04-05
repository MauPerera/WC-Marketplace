<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_dashboard_sales_item.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp, $wpdb;
$prefix = $wpdb->prefix;
$current_user = wp_get_current_user();
$current_user_id =  $current_user->ID;
$today_date = @date('Y-m-d');
$curent_week_range = wcmp_rangeWeek($today_date);
if($today_or_weekly == 'today') {	
	$sale_orders = $wpdb->get_results( "SELECT `order_id` FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` like '".$today_date."%' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1  group by order_id order by order_id desc LIMIT ".$start.",".$to." ", OBJECT );
}
elseif($today_or_weekly == 'weekly') {
	$sale_orders = $wpdb->get_results( "SELECT `order_id` FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` >= '".$curent_week_range['start']."' and `created` <= '".$curent_week_range['end']."' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1  group by order_id order by order_id desc LIMIT ".$start.",".$to." ", OBJECT );
	
}
foreach ($sale_orders as $sale_order) {
	$sale_results = $wpdb->get_results( "SELECT * FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `order_id` = ".$sale_order->order_id." and `is_trashed` != 1 ", OBJECT );
	$sku = array();
	$item_total = 0;
	$item_sub_total = 0;
	$vendor_earning = 0;
	foreach ( $sale_results as $sale_result ) {
		$post_meta = get_post_meta ( $sale_result->product_id);
		$product = get_post($sale_result->product_id);
		
		if(isset($post_meta['_sku'][0])) {
			if(empty($post_meta['_sku'][0])) {
				if( $product->post_type == 'product_variation') {
					$parent_product = get_post($product->post_parent);
					$post_parent_meta = get_post_meta ( $parent_product->ID);
					if(empty($post_meta['_sku'][0])) {					
						$sku[] = '#'.$post_parent_meta['_sku'][0];
					}
				}
				else { 
					$sku[] = '---';	
				}
			}
			else {
				$sku[] = '#'.$post_meta['_sku'][0];
			}
			
		}
		$item_total += get_metadata( 'order_item', $sale_result->order_item_id, '_line_total', true );
		$item_sub_total += get_metadata( 'order_item', $sale_result->order_item_id, '_line_subtotal', true );		
	}
	$diff = $item_sub_total - $item_total;
	
	
	$comission_meta = get_post_meta($sale_result->commission_id);
	$shipping_value = $comission_meta['_shipping'][0];
	$tax_value = $comission_meta['_tax'][0];
	$item_total += ($shipping_value + $tax_value);
	$vendor_earnings = $comission_meta['_commission_amount'][0] + $shipping_value + $tax_value;
	
?>
	<tr>
	<td align="center" >#<?php echo $sale_order->order_id; ?> </td>
	<td align="center" ><?php echo implode(', ',$sku) ?> </td>
	<td align="center" class="no_display" ><?php echo get_woocommerce_currency_symbol();?><?php echo number_format($item_total,2) ?></td>
	<td align="center" class="no_display" ><?php echo get_woocommerce_currency_symbol();?><?php echo number_format($diff,2); ?> </td>
	<td align="center" ><?php echo get_woocommerce_currency_symbol();?><?php echo number_format($vendor_earnings,2);?></td>
</tr>
<?php
}
?>
