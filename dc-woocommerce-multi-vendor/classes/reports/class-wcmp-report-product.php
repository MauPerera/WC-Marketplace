<?php
/**
 * WCMp Report Sales By Product
 *
 * @author      Dualcube
 * @category    Vendor
 * @package     WCMp/Reports
 * @version     2.2.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WCMp_Report_Product extends WC_Admin_Report {
	
	/**
	 * Output the report
	 */
	public function output_report() {
		global $wpdb, $woocommerce, $WCMp;
		
		$ranges = array(
			'year'         => __( 'Year', $WCMp->text_domain ),
			'last_month'   => __( 'Last Month', $WCMp->text_domain ),
			'month'        => __( 'This Month', $WCMp->text_domain ),
			'7day'         => __( 'Last 7 Days', $WCMp->text_domain )
		);
		
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );
		
		if(isset($_POST['search_product'])) {
			$is_variation = false;
			$product_id = $_POST['search_product'];
		
			$_product = get_product($product_id);
		
			if( $_product->is_type( 'variation' ) ) {
				$title = $_product->get_formatted_name();
				$is_variation = true;
			} else {
				$title = $_product->get_title();
			}
		}
			
		if( isset( $product_id ) ) {
			$option = '<option value="' .$product_id. '" selected="selected">' . $title . '</option>';
		} else {
			$option = '<option></option>';
		}
		
		$start_date = $this->start_date;
		$end_date = $this->end_date;
		$end_date = strtotime( '+1 day', $end_date);
		
		$products = $product_ids = array();
		$vendor = false;
		$current_user_id = '';
		
		$current_user_id = get_current_user_id();
		$vendor = get_wcmp_vendor($current_user_id);
		if( $vendor ) {
			$products = $vendor->get_products();
			foreach( $products as $product ) {
				$product_ids[] = $product->ID;
			}
		} else {
			$args = array(
				'posts_per_page'   => -1,
				'offset'           => 0,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => 'product',
				'post_status'      => 'publish'
			);
			$products = get_posts( $args );
			foreach( $products as $product ) {
				$product_ids[] = $product->ID;
			}
		}
		
		$total_sales = $admin_earnings = array();
		$max_total_sales = $index = 0;
		$product_report = $report_bk = array();
		if( isset($product_ids) && !empty($product_ids) ) {
			foreach( $product_ids as $product_id ) {
				$is_variation = false;
				$_product = array();
				$vendor = false;
				
				$_product = get_product($product_id);
				
				if( $_product->is_type( 'variation' ) ) {
					$title = $_product->get_formatted_name();
					$is_variation = true;
				} else {
					$title = $_product->get_title();
				}
				
				if( isset( $product_id ) && !$is_variation) {
					$vendor = get_wcmp_product_vendors($product_id); 
				} else if(isset( $product_id ) && $is_variation) {
					$variation_parent = wp_get_post_parent_id($product_id);
					$vendor = get_wcmp_product_vendors($variation_parent);
				}
				
				if($vendor) {
					$orders = array();
					if( $_product->is_type( 'variable' ) ) {
						$get_children = $_product->get_children();
						if(!empty($get_children)) {
							foreach($get_children as $child) {
								$orders = array_merge($orders, $vendor->get_vendor_orders_by_product($vendor->term_id, $child));
							}
							$orders = array_unique($orders);
						}
					} else {
						$orders = array_unique($vendor->get_vendor_orders_by_product($vendor->term_id, $product_id));
					}
				}
				
				$order_items = array();
				$i = 0;
				if(!empty($orders)) {
					foreach($orders as $order_id) {
						$order = new WC_Order ( $order_id );
						$order_line_items = $order->get_items('line_item');
						
						if(!empty($order_line_items)) {
							foreach($order_line_items as $line_item) {
								if ( $line_item['product_id'] == $product_id ) {
									if( $_product->is_type( 'variation' ) ) {
										$order_items_product_id = $line_item['product_id'];
										$order_items_variation_id = $line_item['variation_id'];
									} else {
										$order_items_product_id = $line_item['product_id'];
										$order_items_variation_id = $line_item['variation_id'];
									}
									$order_date_str = strtotime($order->order_date);
									if( $order_date_str > $start_date && $order_date_str < $end_date ) {
										$order_items[$i] = array(
											'order_id' => $order_id,
											'product_id' => $order_items_product_id,
											'variation_id' => $order_items_variation_id,
											'line_total' => $line_item['line_total'],
											'item_quantity' => $line_item['qty'],
											'post_date' => $order->order_date,
											'multiple_product' => 0
										);
										if( count($order_line_items) > 1 ) {
											$order_items[$i]['multiple_product'] = 1;
										}
										$i++;
									}
								}
							}
						}
					}
				}
				
				if( isset($order_items) && !empty($order_items) ) {
					foreach( $order_items as $item_id => $order_item ) {
						if ( $order_item['line_total'] == 0 && $order_item['item_quantity'] == 0 )
							continue;
	
						if( $order_item['variation_id'] != 0 ) {
							$variation_id = $order_item['variation_id'];
							$product_id_1 = $order_item['variation_id'];
						} else {
							$variation_id = 0;
							$product_id_1 = $order_item['product_id'];
						}
						
						$vendor = get_wcmp_product_vendors($product_id);
						if(!$vendor) {
							break;
						}
						
						$commissions = false;
						$vendor_earnings = 0;
						if( $order_item['multiple_product'] == 0 ) {
							
							$args = array(
								'post_type' =>  'dc_commission',
								'post_status' => array( 'publish', 'private' ),
								'posts_per_page' => -1,
								'meta_query' => array(
									array(
										'key' => '_commission_vendor',
										'value' => absint($vendor->term_id),
										'compare' => '='
									),
									array(
										'key' => '_commission_order_id',
										'value' => absint($order_item['order_id']),
										'compare' => '='
									),
									array(
										'key' => '_commission_product',
										'value' => absint($product_id_1),
										'compare' => 'LIKE'
									)
								)
							);
							
							$commissions = get_posts( $args );
							
							if( !empty($commissions) ) {
								foreach($commissions as $item_id => $commission) {
									$vendor_earnings = $vendor_earnings + get_post_meta($commission->ID, '_commission_amount', true);
								}
							}
							
						} else if( $order_item['multiple_product'] == 1 ) {
							
							$vendor_obj = new WCMp_Vendor(); 
							$vendor_items = $vendor_obj->get_vendor_items_from_order($order_item['order_id'], $vendor->term_id);
							foreach( $vendor_items as $vendor_item ) {
								if( $variation_id == 0 ) {
									if( $vendor_item['product_id'] == $product_id ) {
										$item = $vendor_item;
										break;
									}
								} else {
									if( $vendor_item['product_id'] == $product_id && $vendor_item['variation_id'] == $variation_id ) {
										$item = $vendor_item;
										break;
									}
								}
							}
							$commission_obj = new WCMp_Calculate_Commission();
							$vendor_earnings = $commission_obj->get_item_commission( $product_id, $variation_id, $item, $order_item['order_id'], $item_id );
						}
						
						if( $vendor_earnings <= 0 ) {
							continue;
						}
						
						$total_sales[$product_id] = isset($total_sales[$product_id]) ? ( $total_sales[$product_id] + $order_item['line_total'] ) : $order_item['line_total'];
						$vendor = get_wcmp_vendor($current_user_id);
						if(!$vendor) {
							$admin_earnings[$product_id] = isset($admin_earnings[$product_id]) ? ( $admin_earnings[$product_id] + $order_item['line_total'] - $vendor_earnings ) : $order_item['line_total'] - $vendor_earnings;
						} else {
							$admin_earnings[$product_id] = isset($admin_earnings[$product_id]) ? ( $admin_earnings[$product_id] + $vendor_earnings ) : $vendor_earnings;
						}
						
						if ( $total_sales[ $product_id ] > $max_total_sales )
							$max_total_sales = $total_sales[ $product_id ];
					}
				}
				
				if( !empty( $total_sales[$product_id] ) && !empty( $admin_earnings[$product_id] ) ) {
					$product_report[$index]['product_id'] = $product_id;
					$product_report[$index]['total_sales'] = $total_sales[$product_id];
					$product_report[$index++]['admin_earning'] = $admin_earnings[$product_id];
					
					$report_bk[$product_id]['total_sales'] = $total_sales[$product_id];
					$report_bk[$product_id]['admin_earning'] = $admin_earnings[$product_id];
				}
			}
			
			$i = 0;
			$max_value = 10;
			$report_sort_arr = array();
			$total_sales_sort = $admin_earning_sort = array();
			if( !empty($product_report) && !empty($report_bk) ) {
				$total_sales_sort = wp_list_pluck( $product_report, 'total_sales', 'product_id' );
				$admin_earning_sort = wp_list_pluck( $product_report, 'admin_earning', 'product_id' );
				
				foreach( $total_sales_sort as $key => $value ) {
					$total_sales_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
					$total_sales_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
				}
			
				arsort($total_sales_sort);
				foreach( $total_sales_sort as $product_id => $value ) {
					if( $i++ < $max_value ) {
						$report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
						$report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
					}
				}
			}
			
			wp_localize_script('wcmp_report_js', 'wcmp_report_product', array( 'product_report' => $product_report, 
																																					'report_bk' => $report_bk,
																																					'total_sales_sort' => $total_sales_sort,
																																					'admin_earning_sort' => $admin_earning_sort,
																																					'max_total_sales' => $max_total_sales,
																																					'start_date' => $start_date,
																																					'end_date' => $end_date
																																				));
				
			$report_chart = $report_html = '';
			if ( sizeof( $report_sort_arr ) > 0 ) {
				foreach ( $report_sort_arr as $product_id => $sales_report ) {
					$width = ( $sales_report['total_sales'] > 0 ) ? ( round( $sales_report['total_sales'] ) / round( $max_total_sales ) ) * 100 : 0;
					$width2 = ( $sales_report['admin_earning'] > 0 ) ? ( round( $sales_report['admin_earning'] ) / round( $max_total_sales ) ) * 100 : 0;

					$product = new WC_Product($product_id);
					$product_url = admin_url('post.php?post='. $product_id .'&action=edit');
					
					$report_chart .= '<tr><th><a href="' . $product_url . '">' . $product->get_title() . '</a></th>
						<td width="1%"><span>' . woocommerce_price( $sales_report['total_sales'] ) . '</span><span class="alt">' . woocommerce_price( $sales_report['admin_earning'] ) . '</span></td>
						<td class="bars">
							<span style="width:' . esc_attr( $width ) . '%">&nbsp;</span>
							<span class="alt" style="width:' . esc_attr( $width2 ) . '%">&nbsp;</span>
						</td></tr>';
				}
				
				$report_html = '
					<h4>' . __( "Sales and Earnings", $WCMp->text_domain ) . '</h4>
					<div class="bar_indecator">
						<div class="bar1">&nbsp;</div>
						<span class="">' . __( "Gross Sales", $WCMp->text_domain ) . '</span>
						<div class="bar2">&nbsp;</div>
						<span class="">' . __( "My Earnings", $WCMp->text_domain ) . '</span>
					</div>
					<table class="bar_chart">
						<thead>
							<tr>
								<th>' . __( "Month", $WCMp->text_domain ) . '</th>
								<th colspan="2">' . __( "Sales Report", $WCMp->text_domain ) . '</th>
							</tr>
						</thead>
						<tbody>
							' . $report_chart . '
						</tbody>
					</table>
				';
			} else {
				$report_html = '<tr><td colspan="3">' . __( 'No product was sold in the given period.', $WCMp->text_domain ) . '</td></tr>';
			}
				
		}else {
			$report_html = '<tr><td colspan="3">' . __( 'Your store has no products.', $WCMp->text_domain ) . '</td></tr>';
		}
		
		include( $WCMp->plugin_path . '/classes/reports/views/html-wcmp-report-by-product.php');
	}
}	

?>
