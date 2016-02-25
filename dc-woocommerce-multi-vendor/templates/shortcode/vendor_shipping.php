<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_shipping.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;
$vendor_user_id = get_current_user_id();
$vendor_data = get_wcmp_vendor($vendor_user_id);
if($vendor_data) {
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		if(isset( $_POST['vendor_shipping_data'] )) {
			$fee = 0;
			$vendor_shipping_data = get_user_meta($vendor_user_id, 'vendor_shipping_data', true);
			$cost = $_POST['vendor_shipping_data']['shipping_amount'];
			$international_cost = $_POST['vendor_shipping_data']['international_shipping_amount'];
			$fee = isset($_POST['vendor_shipping_data']['handling_amount']) ? $_POST['vendor_shipping_data']['handling_amount'] : '';
			if( isset($_POST['vendor_shipping_data']['shipping_amount']) ) {
				$shipping_updt = true; 
				$dc_flat_rates = array();	
				$shipping_class_id = get_user_meta($vendor_user_id,'shipping_class_id',true);
				if(!empty($shipping_class_id)) {
					$term_shipping_obj = get_term_by( 'id', $shipping_class_id, 'product_shipping_class');
				}
				if(!(isset($term_shipping_obj) && isset($term_shipping_obj->term_id) && !empty($term_shipping_obj))) {
					$shipping_term = wp_insert_term( $vendor_data->user_data->user_login.'-'.$vendor_user_id, 'product_shipping_class' );					
					if(!is_wp_error($shipping_term)) {
						$shipping_term_id = $shipping_term['term_id'];
						update_user_meta($vendor_user_id, 'shipping_class_id', $shipping_term['term_id']);
						add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_id', $vendor_user_id); 
						add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_shipping_origin',  $_POST['vendor_shipping_data']['ship_from']);
					} else {
						$shipping_updt = false;
					}
				} 
				else {
					$shipping_class_id = get_user_meta($vendor_user_id, 'shipping_class_id', true);
					if(empty($shipping_class_id)){
						$sterm = get_term_by( 'slug', $vendor_data->user_data->user_login.'-'.$vendor_user_id, 'shipping_class_id');
						if(isset($sterm->term_id) && !empty($sterm->term_id)) {
							$shipping_class_id = $sterm->term_id;
							update_user_meta( $vendor_user_id, 'shipping_class_id', $shipping_class_id);
						}
					}
					$shipping_term_id = $shipping_class_id;
					update_woocommerce_term_meta($shipping_class_id, 'vendor_shipping_origin',  $_POST['vendor_shipping_data']['ship_from']);
				}
				$term_shipping_obj = get_term_by( 'id', $shipping_class_id, 'product_shipping_class');
				if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
					$woocommerce_flat_rate_settings = get_option('woocommerce_flat_rate_settings');
					$woocommerce_flat_rate_settings['class_cost_' . $shipping_term_id] = stripslashes($cost);
					update_option('woocommerce_flat_rate_settings', $woocommerce_flat_rate_settings);					
					$woocommerce_international_delivery_settings = get_option('woocommerce_international_delivery_settings');
					$woocommerce_international_delivery_settings['class_cost_' . $shipping_term_id] = stripslashes($international_cost);
					update_option('woocommerce_international_delivery_settings', $woocommerce_international_delivery_settings);					
				}				
				else if ( version_compare( WC_VERSION, '2.4.0', '>' ) ) {
					$woocommerce_flat_rate_settings = get_option('woocommerce_flat_rate_settings');
					$woocommerce_flat_rate_settings['class_cost_' . sanitize_title($term_shipping_obj->slug)] = stripslashes($cost);
					update_option('woocommerce_flat_rate_settings', $woocommerce_flat_rate_settings);					
					$woocommerce_international_delivery_settings = get_option('woocommerce_international_delivery_settings');
					$woocommerce_international_delivery_settings['class_cost_' . sanitize_title($term_shipping_obj->slug)] = stripslashes($international_cost);
					update_option('woocommerce_international_delivery_settings', $woocommerce_international_delivery_settings);					
				} 
				else {
					$woocommerce_flat_rates = get_option('woocommerce_flat_rates');
					$woocommerce_flat_rates[sanitize_title($term_shipping_obj->slug)] = array('cost' => $cost, 'fee' => $fee);
					update_option('woocommerce_flat_rates', $woocommerce_flat_rates);
				}
				update_user_meta($vendor_user_id, 'vendor_shipping_data', $_POST['vendor_shipping_data']);
				if($shipping_updt) {
					$is_updated_shipping = $shipping_updt;					
				} else {
					$error_msg = '<div class="error">'.__( "Shipping Data Not Updated.", $WCMp->text_domain ).'</div>';
					delete_user_meta($vendor_user_id, 'vendor_shipping_data');
				}
			} else {
				$error_msg = '<div class="error">'.__( "Specify Shipping Amount.", $WCMp->text_domain ).'</div>';
			}
		}
	} 
	
	$vendor_shipping_data = get_user_meta($vendor_user_id, 'vendor_shipping_data', true);		
	?>
	
	
	
	<div class="wcmp_main_holder toside_fix">

		
		<div class="wcmp_headding1">
		<ul>
			<li><?php _e( 'Store Settings ', $WCMp->text_domain );?></li>
			<li class="next"> < </li>
			<li><?php _e( 'Shipping', $WCMp->text_domain );?></li>
		</ul>
			<button class="wcmp_ass_btn edit_shipping"><?php _e( 'Edit', $WCMp->text_domain );?></button>
			<div class="clear"></div> 
		</div>
		

		<form name="vendor_shipping_form" class="wcmp_shipping_form" method="post">
			<table class="shipping_table">
				<tbody>
					<?php
					if ( version_compare( WC_VERSION, '2.4.0', '>' ) ) { ?>
						<tr>
							<td><label><?php _e('Enter Shipping Amount for "Flat Rate" :', $WCMp->text_domain); ?></label></td>
                            </tr>
						<tr>
							<td><input name="vendor_shipping_data[shipping_amount]" class="no_input" readonly type="text" step="0.01" value='<?php echo isset($vendor_shipping_data['shipping_amount']) ?  $vendor_shipping_data['shipping_amount'] :  ''; ?>' /></td>
                            </tr>
						</tr>
						<tr><td class="hints">
						<div>
                        <div class="aar"></div>
						<?php _e( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>. Supports the following placeholders: <code>[qty]</code> = number of items, <code>[cost]</code> = cost of items,<br><code>[fee percent="10" min_fee="20"]</code> = Percentage based fee.', $WCMp->text_domain );?> <br><br>
                        </div>
                        </td></tr>
						<tr>
							<td><label><?php _e('Enter Shipping Amount for "International Flat Rate" :', $WCMp->text_domain); ?></label></td>
							</tr>
						<tr>
                            <td><input class="no_input" readonly name="vendor_shipping_data[international_shipping_amount]" type="text" step="0.01" value='<?php echo isset($vendor_shipping_data['international_shipping_amount']) ?  $vendor_shipping_data['international_shipping_amount'] :  ''; ?>' /></td>
						</tr>
						<tr><td class="hints">
						<div>
                        <div class="aar"></div>
						<?php _e( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>. Supports the following placeholders: <code>[qty]</code> = number of items, <code>[cost]</code> = cost of items,<br><code>[fee percent="10" min_fee="20"]</code> = Percentage based fee.', $WCMp->text_domain );?> <br><br>
                        </div></td></tr>
					<?php } else { ?>
						<tr>
						<td><label><?php _e('Enter Shipping Amount :', $WCMp->text_domain); ?></label></td>
                        </tr>
						<tr>
						<td><input class="no_input" readonly name="vendor_shipping_data[shipping_amount]" type="text" step="0.01" value='<?php echo isset($vendor_shipping_data['shipping_amount']) ?  $vendor_shipping_data['shipping_amount'] :  ''; ?>' /></td>
					</tr>
						<tr>
							<td><label><?php _e('Enter Handling Fee :', $WCMp->text_domain); ?></label></td>
                            </tr>
						<tr>
							<td><input class="no_input" name="vendor_shipping_data[handling_amount]" readonly type="number" step="0.01" value='<?php echo isset($vendor_shipping_data['handling_amount']) ?  $vendor_shipping_data['handling_amount'] :  '';?>' /></td>
						</tr>
					<?php } ?>
					<tr>
						<td><label><?php _e('Ship from :', $WCMp->text_domain); ?></label></td>
                        </tr>
						<tr>
						<td><input class="no_input" readonly name="vendor_shipping_data[ship_from]" type="text" value="<?php echo isset($vendor_shipping_data['ship_from']) ? $vendor_shipping_data['ship_from'] :  ''; ?>" /></td>
					</tr>
					<?php do_action('wcmp_before_shipping_form_end_vendor_dashboard'); ?>
					<tr>
						<td>
							<input type="submit" class="wcmp_orange_btn" value="<?php _e( 'Submit', $WCMp->text_domain ) ?>" />
						</td>
					</tr>
						
				</tbody>
			</table>
			
			
		</form>
		<br class="clear"/>
			<div class="action_div_space"> 
	<div class="action_div">
				
		
	<?php 
	if(isset($is_updated_shipping) && !empty($is_updated_shipping)) {
		?>					
		<div style="clear:both; width:100%;" ></div>
		<div style="margin-bottom:10px; width:98%;" class="green_massenger"><i class="fa fa-check"></i> &nbsp; <?php _e( 'Shipping Data Updated.', $WCMp->text_domain );?></div>
		<div style="clear:both; width:100%;" ></div>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			$(".wcmp_remove_div").prepend('<div style="margin-bottom:10px; width:98%;" class="green_massenger"><i class="fa fa-check"></i> &nbsp; <?php _e( 'Shipping Data Updated.', $WCMp->text_domain );?></div>');				
		});
		</script>
		<?php	
	}
	else {
		if(isset( $error_msg ) ) {
			?>
			<script type="text/javascript">
		jQuery(document).ready(function($){
			$(".wcmp_remove_div").prepend('<div style="margin-bottom:10px; width:98%;" class="red_massenger"><i class="fa fa-times"></i> &nbsp; <?php echo $error_msg;?></div>');				
		});
		</script>
			<?php
		}	
	}
	?>
	
	<?php
}
?>
</div>
	</div>

	</div>




	