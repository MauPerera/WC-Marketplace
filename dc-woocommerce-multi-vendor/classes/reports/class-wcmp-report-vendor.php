<?php
/**
 * WCMp Report Sales By Vendor
 *
 * @author      Dualcube
 * @category    Vendor
 * @package     WCMp/Reports
 * @version     2.2.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WCMp_Report_Vendor extends WC_Admin_Report {

	/**
	 * Output the report
	 */
	public function output_report() {
		global $wpdb, $woocommerce, $WCMp;
		
		$vendor = $vendor_id = $order_items = false;
		
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
		
		if( isset( $_POST['vendor'] ) ) {
			$vendor_id = $_POST['vendor'];
			$vendor = get_wcmp_vendor_by_term( $vendor_id );
			if($vendor) $products = $vendor->get_products();
			if(!empty($products)) {
				foreach( $products as $product ) {
					$chosen_product_ids[] = $product->ID;
				}
			}
		}
		
		if( $vendor_id && $vendor ) {
			$option = '<option value="' . $vendor_id. '" selected="selected">' . $vendor->user_data->display_name . '</option>';
		} else {
			$option = '<option></option>';
		}
		
		$all_vendors = get_wcmp_vendors();
		
		$start_date_str = $this->start_date;
		$end_date_str = $this->end_date;
		$end_date_str = strtotime( '+1 day', $end_date_str);
		$start_date = date("Y-m-d H:i:s", $start_date_str);
		$end_date = date("Y-m-d H:i:s", $end_date_str);
		
		$total_sales = $admin_earning = $vendor_report = $report_bk = array();
		$max_total_sales = $i = 0;
		
		if( isset($all_vendors) && !empty($all_vendors) ) {
			foreach( $all_vendors as $all_vendor ) {
				$chosen_product_ids = array();
				$vendor_id = $all_vendor->id;
				$vendor = get_wcmp_vendor($vendor_id);
				if($vendor) $products = $vendor->get_products();
				if(!empty($products)) {
					foreach( $products as $product ) {
						$chosen_product_ids[] = $product->ID;
					}
				}
				
				if ( $chosen_product_ids && is_array( $chosen_product_ids ) ) {
					// Get titles and ID's related to product
					$chosen_product_titles = array();
					$children_ids = array();
			
					foreach ( $chosen_product_ids as $product_id ) {
						$children = (array) get_posts( 'post_parent=' . $product_id . '&fields=ids&post_status=any&numberposts=-1' );
						$children_ids = $children_ids + $children;
						$chosen_product_titles[] = get_the_title( $product_id );
					}
			
			    // Get order items
					$order_items = apply_filters( 'woocommerce_reports_product_sales_order_items', $wpdb->get_results( "
						SELECT posts.ID as order_id, order_item_meta_2.meta_value as product_id, order_item_meta_1.meta_value as variation_id, posts.post_date, SUM( order_item_meta.meta_value ) as item_quantity, SUM( order_item_meta_3.meta_value ) as line_total
						FROM {$wpdb->prefix}woocommerce_order_items as order_items
			
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_1 ON order_items.order_item_id = order_item_meta_1.order_item_id
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_3 ON order_items.order_item_id = order_item_meta_3.order_item_id
						LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			
						WHERE posts.post_type 	= 'shop_order'
						AND 	order_item_meta_2.meta_value IN ('" . implode( "','", array_merge( $chosen_product_ids, $children_ids ) ) . "')
						AND posts.post_status IN ('wc-pending','wc-processing','wc-on-hold','wc-completed','wc-cancelled','wc-refunded','wc-failed')
						AND 	order_items.order_item_type = 'line_item'
						AND 	order_item_meta.meta_key = '_qty'
						AND 	order_item_meta_2.meta_key = '_product_id'
						AND 	order_item_meta_1.meta_key = '_variation_id'
						AND 	order_item_meta_3.meta_key = '_line_total'
						AND   posts.post_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'
						GROUP BY order_items.order_id
						ORDER BY posts.post_date ASC
					" ), array_merge( $chosen_product_ids, $children_ids ) );
					
					if ( $order_items ) {
						foreach ( $order_items as $order_item ) {
							
							if ( $order_item->line_total == 0 && $order_item->item_quantity == 0 )
								continue;
			
							if( $order_item->variation_id != '0' ) {
								$product_id = $order_item->variation_id;
								$variation_id = $order_item->variation_id;
							} else {
								$product_id = $order_item->product_id;
								$variation_id = 0;
							}
							
							$commissions = false;
							$vendor_earnings = 0;
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
										'value' => absint($order_item->order_id),
										'compare' => '='
									),
									array(
										'key' => '_commission_product',
										'value' => absint($product_id),
										'compare' => 'LIKE'
									),
								),
							);
							$commissions = get_posts( $args );
							
							if(!empty($commissions)) {
								foreach($commissions as $commission) {
									$vendor_earnings = $vendor_earnings + get_post_meta($commission->ID, '_commission_amount', true);
								}
							}
							
							if( $vendor_earnings <= 0 ) {
								continue;
							}
							
							$total_sales[$vendor_id] = isset( $total_sales[$vendor_id] ) ? ( $total_sales[$vendor_id] + $order_item->line_total ) : $order_item->line_total;
							$admin_earning[$vendor_id] = isset( $admin_earning[$vendor_id] ) ? ( $admin_earning[$vendor_id] + $order_item->line_total - $vendor_earnings ) : ( $order_item->line_total - $vendor_earnings );
							
							if ( $total_sales[ $vendor_id ] > $max_total_sales )
								$max_total_sales = $total_sales[ $vendor_id ];
						}
					}
				}
					
				if( isset( $total_sales[$vendor_id] ) && isset( $admin_earning[$vendor_id] ) ) {
					$vendor_report[$i]['vendor_id'] = $vendor_id;
					$vendor_report[$i]['total_sales'] = $total_sales[ $vendor_id ];
					$vendor_report[$i++]['admin_earning'] = $admin_earning[ $vendor_id ];
					
					$report_bk[$vendor_id]['total_sales'] = $total_sales[ $vendor_id ];
					$report_bk[$vendor_id]['admin_earning'] = $admin_earning[ $vendor_id ];
				}
			}
			
			$i = 0;
			$max_value = 10;
			$report_sort_arr = array();
			if( isset($vendor_report) && isset($report_bk) ) {
				$total_sales_sort = wp_list_pluck( $vendor_report, 'total_sales', 'vendor_id' );
				$admin_earning_sort = wp_list_pluck( $vendor_report, 'admin_earning', 'vendor_id' );
				
				foreach( $total_sales_sort as $key => $value ) {
					$total_sales_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
					$total_sales_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
				}
			
				
				arsort($total_sales_sort);
				foreach( $total_sales_sort as $key => $value ) {
					if( $i++ < $max_value ) {
						$report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
						$report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
					}
				}
			}
			
			wp_localize_script('wcmp_report_js', 'wcmp_report_vendor', array('vendor_report' => $vendor_report, 
																																			'report_bk' => $report_bk,
																																			'total_sales_sort' => $total_sales_sort,
																																			'admin_earning_sort' => $admin_earning_sort,
																																			'max_total_sales' => $max_total_sales,
																																			'start_date' => $start_date,
																																			'end_date' => $end_date
																																			));
			
			$chart_arr = $html_chart = '';
			if ( count( $report_sort_arr ) > 0 ) {
				foreach ( $report_sort_arr as $vendor_id => $sales_report ) {
					$total_sales_width = ( $sales_report['total_sales'] > 0 ) ? $sales_report['total_sales'] / round($max_total_sales) * 100 : 0;
					$admin_earning_width = ( $sales_report['admin_earning'] > 0 ) ? ( $sales_report['admin_earning'] / round($max_total_sales) ) * 100 : 0;
					
					$user = get_userdata($vendor_id);
					$user_name = $user->data->display_name;
					
					$chart_arr .= '<tr><th><a href="user-edit.php?user_id='.$vendor_id.'">' . $user_name . '</a></th>
					<td width="1%"><span>' . woocommerce_price( $sales_report['total_sales'] ) . '</span><span class="alt">' . woocommerce_price($sales_report['admin_earning']) . '</span></td>
					<td class="bars">
						<span style="width:' . esc_attr( $total_sales_width ) . '%">&nbsp;</span>
						<span class="alt" style="width:' . esc_attr( $admin_earning_width ) . '%">&nbsp;</span>
					</td></tr>';
				}
				
				$html_chart = '
					<h4>' . __( "Sales and Earnings", $WCMp->text_domain ) . '</h4>
					<div class="bar_indecator">
						<div class="bar1">&nbsp;</div>
						<span class="">' . __( 'Gross Sales', $WCMp->text_domain ) . '</span>
						<div class="bar2">&nbsp;</div>
						<span class="">' . __( 'My Earnings', $WCMp->text_domain ) . '</span>
					</div>
					<table class="bar_chart">
						<thead>
							<tr>
								<th>' . __( "Vendors", $WCMp->text_domain ) . '</th>
								<th colspan="2">' . __( "Sales Report", $WCMp->text_domain ) . '</th>
							</tr>
						</thead>
						<tbody>
							' . $chart_arr . '
						</tbody>
					</table>
				';
			} else {
				$html_chart = '<tr><td colspan="3">' . __( 'Any vendor did not generate any sales in the given period.', $WCMp->text_domain ) . '</td></tr>';
			}
		} else {
			$html_chart = '<tr><td colspan="3">' . __( 'Your store has no vendors.', $WCMp->text_domain ) . '</td></tr>';
		}
		
		include( $WCMp->plugin_path . '/classes/reports/views/html-wcmp-report-by-vendor.php');
	}
	
}
