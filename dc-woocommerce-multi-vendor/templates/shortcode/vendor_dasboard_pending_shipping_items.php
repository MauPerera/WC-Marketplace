<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_dasboard_pending_shipping_items.php
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
	$pending_orders_items = $wpdb->get_results( "SELECT * FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` like '".$today_date."%' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 and `shipping_status` != 1  order by order_id desc LIMIT ".$start.",".$to." ", OBJECT );
}
elseif($today_or_weekly == 'weekly') {
	$pending_orders_items = $wpdb->get_results( "SELECT * FROM ".$prefix."wcmp_vendor_orders WHERE vendor_id = ". $current_user_id ." and `created` >= '".$curent_week_range['start']."' and `created` <= '".$curent_week_range['end']."' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1  and `shipping_status` != 1 order by order_id desc LIMIT ".$start.",".$to." ", OBJECT );	
}
foreach ($pending_orders_items as $pending_orders_item ) {
	$product_id = $pending_orders_item->product_id;
	$order_id = $pending_orders_item->order_id;
	$commission_id = $pending_orders_item->commission_id;
	$order = wc_get_order($order_id);
	$product_meta = get_post_meta($product_id);
	$product = get_post($product_id);
	$commission_meta = get_post_meta($commission_id);	
	$vendot_term_id = get_user_meta($vendor->id,'_vendor_term_id',true);
	$commission_obj = new WCMp_Calculate_Commission();
	$vendor_commission_data = $vendor->wcmp_vendor_get_order_item_totals($order, $vendot_term_id);
	$shipping_val = $pending_orders_item->shipping; //$vendor_commission_data['shipping_subtotal'];
	$order_item_meta = get_metadata( 'order_item', $pending_orders_item->order_item_id);
	$product_name =  $product->post_title;
	$sku = $product_meta['_sku'][0];
	if(empty($shipping_val)){
	  $shipping_val = 0;
	}
	$_length = $product_meta['_length'][0];
	$_width = $product_meta['_width'][0];
	$_height = $product_meta['_height'][0];
	$_weight = $product_meta['_weight'][0];
	
	if( $product->post_type == 'product_variation'){	
		$_product = new WC_Product( $product_id );
		$item_meta = get_metadata('order_item',$pending_orders_item->order_item_id );	
		unset($item_meta['flat_shipping_per_item']);
		unset($item_meta['Sold By']);
		$item_meta_obj    = new WC_Order_Item_Meta( $item_meta, $_product );
		$product_parent = get_post($product->post_parent);
		$product_name =  $product_parent->post_title;
		$product_parent_meta = get_post_meta($product_parent->ID);		
		if(empty($sku )) {
			$sku = $product_parent_meta['_sku'][0];
		}
		if(empty($_length)){
			$_length = $product_parent_meta['_length'][0];
		}
		if(empty($_width)){
			$_width = $product_parent_meta['_width'][0];
		}
		if(empty($_height)){
			$_height = $product_parent_meta['_height'][0];
		}
		if(empty($_weight)){
			$_weight = $product_parent_meta['_weight'][0];
		}
				
	}
	else {
		if(isset($item_meta_obj)){
			unset($item_meta_obj);
		}		
	}	
	
?>
<tr>
	<td align="center" ><?php echo $product_name; ?> (<?php if(!empty($sku)) { echo "#".$sku; } else { echo "#---"; } ?>)<?php if(isset($item_meta_obj)) { $item_meta_obj->display(); }?></td>
	<td align="center" class="no_display" ><?php echo @date('d/m',strtotime($pending_orders_item->created)); ?></td>
	<td align="center" class="no_display" > <?php if(!empty($_length)) { echo $_length;}else {echo '--'; } ?>/<?php if(!empty($_width)) { echo $_width;}else {echo '--'; } ?>/<?php if(!empty($_height)) { echo $_height;}else {echo '--'; } ?>/<?php if(!empty($_weight)) { echo $_weight;}else {echo '--'; } ?> </td>
	<td align="left" ><?php echo $order->shipping_address_1; ?>, <?php echo $order->shipping_address_2; ?> <br>
		<?php echo $order->shipping_city; ?> , <?php echo $order->shipping_state; ?> <br/> <?php echo $order->shipping_postcode; ?> , <?php echo $order->shipping_country; ?></td>
	<td align="center" class="no_display" ><?php if(!empty($shipping_val)) { echo get_woocommerce_currency_symbol().number_format($shipping_val,2);} else {echo 'N/A';};?></td>
</tr>
<?php }?>