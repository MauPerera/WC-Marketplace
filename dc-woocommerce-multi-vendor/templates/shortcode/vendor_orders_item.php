<?php
/**
 * The template for displaying vendor orders item band called from vendor_orders.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_orders_item.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;	

$pages = get_option('wcmp_pages_settings_name');
$vendor_detail_page = $pages['vendor_order_detail'];

if(!empty($orders)) { 
	foreach($orders as $order) {
		$order_obj = new WC_Order ( $order );
		$mark_ship =  $WCMp->vendor_dashboard->is_order_shipped($order, get_wcmp_vendor(get_current_user_id()));
		$user_id = get_current_user_id();
		?>
		<tr>
			<td align="center"  width="20" ><span class="input-group-addon beautiful">
				<input type="checkbox" class="select_<?php echo $order_status;?>" name="select_<?php echo $order_status;?>[<?php echo $order; ?>]" >
				</span></td>
			<td align="center" ><?php echo $order; ?> </td>
			<td align="center" ><?php echo date('d/m', strtotime($order_obj->order_date)); ?></td>
			<td class="no_display" align="center" >
				<?php 
					$vendor_share = $vendor->wcmp_get_vendor_part_from_order($order_obj, $vendor->term_id);
					if(!isset($vendor_share['total'])) $vendor_share['total'] = 0;
					echo  get_woocommerce_currency_symbol().$vendor_share['total']; 
				?>
			</td>
			<td class="no_display" align="center" ><?php echo $order_obj->get_status(); ?></td>
			<td align="center" valign="middle" >
				<?php
					$actions = array();
					$is_shipped = get_post_meta( $order, 'dc_pv_shipped', true );
					if($is_shipped) {
						$mark_ship_title = __('Shipped', $WCMp->text_domain);
					} else  {
						$mark_ship_title = __('Mark as shipped', $WCMp->text_domain);
					}
					$actions['view'] = array(
						'url'  => esc_url( add_query_arg( array( 'order_id' => $order ), get_permalink($vendor_detail_page))),
						'img' => $WCMp->plugin_url . 'assets/images/view.png',
						'title' => __('View' ,$WCMp->text_domain),
					);    
					
					$actions['wcmp_vendor_csv_download_per_order'] = array(
						'url'  => admin_url( 'admin-ajax.php?action=wcmp_vendor_csv_download_per_order&order_id=' . $order . '&nonce=' . wp_create_nonce( 'wcmp_vendor_csv_download_per_order' ) ),
						'img' => $WCMp->plugin_url . 'assets/images/download.png',
						'title' => __('Download' ,$WCMp->text_domain),
					);
					
					$actions['mark_ship'] = array(
						'url'  => '#',
						'title' => $mark_ship_title,
					);
					
					$actions = apply_filters( 'wcmp_my_account_my_orders_actions', $actions, $order );
	
					if ($actions) {
						foreach ( $actions as $key => $action ) { ?>
							<?php if ($key == 'view') { ?> 
								<a title="<?php echo $action['title']; ?>" target="_blank" href="<?php echo $action['url']; ?>"><i><img src="<?php echo $action['img']; ?>" alt=""></i></a>&nbsp; 
							<?php } elseif ($key == 'mark_ship') { ?>
								<a href="#" data-id="<?php echo $order; ?>" data-user="<?php echo $user_id; ?>" class="mark_ship" <?php if($mark_ship) { ?> title="Shipped" style="pointer-events: none; cursor: default;" <?php } else { ?> title="mark as shipped" <?php } ?> ><i><img src="<?php if(!$mark_ship) echo $WCMp->plugin_url.'assets/images/roket_deep.png'; else echo $WCMp->plugin_url.'assets/images/roket-green.png'; ?>"  alt=""></i></a>
							<?php } else { ?>
								<a title="<?php echo $action['title']; ?>" href="<?php echo $action['url']; ?>" data-id="<?php echo $order; ?>" class="<?php echo sanitize_html_class( $key ); ?>" href="#"><i><img src="<?php echo $action['img']; ?>" alt=""></i></a>&nbsp;
							<?php 
							}
						}
					}
					?>
			</td>
		</tr>
		<?php 
	}
}	
?>

