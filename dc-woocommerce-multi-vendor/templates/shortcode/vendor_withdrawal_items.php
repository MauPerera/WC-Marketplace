<?php
/**
 * The template for displaying vendor orders item band called from vendor_orders.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_withdrawal_items.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;	

if(!empty($commissions)) { 
	foreach($commissions as  $commission_id => $order_id ) {
		$order_obj = new WC_Order ( $order_id );
		?>
		<tr>
			<td align="left"  width="20" class="extra_padding">
				<span class="input-group-addon beautiful">
					<input name="check_order_number[]" value="<?php echo $commission_id; ?>" class="select_withdrawal" type="checkbox" >
				</span>
			</td>
			<td align="left"  class="extra_padding">#<?php echo $order_id; ?></td>
			<td align="right" valign="middle" class="extra_ending" >
				<?php 
					$vendor_share = $vendor->wcmp_get_vendor_part_from_order($order_obj, $vendor->term_id);
					if(!isset($vendor_share['total'])) $vendor_share['total'] = 0;
					echo  get_woocommerce_currency_symbol().$vendor_share['total']; 
				?>
			</td>
		</tr>
		<?php
	}
}	
?>
