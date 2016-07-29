<?php
/**
 * WCMp Report Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		DualCube
 */
 
class WCMp_Report {

	public function __construct() {
		
		add_action( 'woocommerce_admin_reports', array( $this, 'wcmp_report_tabs' ) );
		if ( is_user_wcmp_vendor(get_current_user_id()) ) {
			add_filter( 'woocommerce_reports_charts', array( $this, 'filter_tabs' ), 99 );
			add_filter( 'wcmp_filter_orders_report_overview', array( $this, 'filter_orders_report_overview' ), 99);
		}
	}
	
	/**
	 * Filter orders report for vendor
	 *
	 * @param object $orders
	 */
	public function filter_orders_report_overview($orders) {
		foreach( $orders as $order_key => $order ) {
			$vendor_item = false;
			$order_obj = new WC_Order( $order->ID );
			$items = $order_obj->get_items( 'line_item' );
			foreach( $items as $item_id => $item ) {
				$product_id = $order_obj->get_item_meta( $item_id, '_product_id', true );
				$vendor_id = $order_obj->get_item_meta( $item_id, '_vendor_id', true );
				$current_user = get_current_user_id();
				if( $vendor_id ) {
					if( $vendor_id == $current_user ) {
						$existsids[] = $product_id;
						$vendor_item = true;
					}
				} else {
					//for vendor logged in only
					if ( is_user_wcmp_vendor($current_user) ) {
						$vendor = get_wcmp_vendor($current_user);
						$vendor_products = $vendor->get_products();
						$existsids = array();
						foreach ( $vendor_products as $vendor_product ) {
							$existsids[] = ( $vendor_product->ID );
						}
						if ( in_array( $product_id, $existsids ) ) {
							$vendor_item = true;
						} 
					}
				}
			}
			if(!$vendor_item) unset($orders[$order_key]);
		}
		return $orders;
	}
	
	/**
	 * Show only reports that are useful to a vendor
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function filter_tabs( $tabs ){
		global $woocommerce;
		unset( $tabs[ 'wcmp_vendors' ]['reports']['vendor'] );		
		$return = array(
			'wcmp_vendors' => $tabs[ 'wcmp_vendors' ],
		);
		return $return;
	}

	/** 
	 * WCMp reports tab options
	 */
	function wcmp_report_tabs( $reports ) {
		global $WCMp;		
		$reports['wcmp_vendors'] = array(
			'title'  => __( 'WCMp', $WCMp->text_domain ),
			'reports' => array(
				"overview" => array(
					'title'       => __( 'Overview', $WCMp->text_domain ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'wcmp_get_report' )
				),
				"vendor" => array(
					'title'       => __( 'Vendor', $WCMp->text_domain ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'wcmp_get_report' )
				),
				"product" => array(
					'title'       => __( 'Product', $WCMp->text_domain ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'wcmp_get_report' )
				)
			)
		);
		
		return $reports;
	}
	
	/**
	 * Get a report from our reports subfolder
	 */
	public static function wcmp_get_report( $name ) {
		$name  = sanitize_title( str_replace( '_', '-', $name ) );
		$class = 'WCMp_Report_' . ucfirst( str_replace( '-', '_', $name ) );
		include_once( apply_filters( 'wcmp_admin_reports_path', 'reports/class-wcmp-report-' . $name . '.php', $name, $class ) );
		if ( ! class_exists( $class ) )
			return;
		$report = new $class();
		$report->output_report();
	}



	
	
	/**
	* get vendor commission by date
	*
	* @access public
	* @param mixed $vars
	* @return array
	*/
	public function vendor_sales_stat_overview( $vendor, $start_date = false, $end_date = false) {
		global $WCMp;
		$total_sales = 0;
		$total_vendor_earnings = 0;
		$total_order_count = 0;
		$total_purchased_products = 0;
		$total_coupon_used = 0;
		$total_coupon_discuont_value = 0;
		$total_earnings = 0;
		$total_customers = array();
		$vendor = get_wcmp_vendor(get_current_user_id());
		for( $date = strtotime($start_date); $date <= strtotime( '+1 day', strtotime($end_date)); $date = strtotime( '+1 day', $date ) ) {
			
			$year = date( 'Y', $date );
			$month = date( 'n', $date );
			$day = date( 'j', $date );
			
			$line_total = $sales = $comm_amount = $vendor_earnings = $earnings = 0;
			
			$args = array(
				'post_type' => 'shop_order',
				'posts_per_page' => -1,
				'post_status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded','wc-failed'),
				'meta_query' => array(
					array(
						'key' => '_commissions_processed',
						'value' => 'yes',
						'compare' => '='
					)
				),
				'date_query' => array(
					array(
						'year'  => $year,
						'month' => $month,
						'day'   => $day,
					),
				)
			);
			
			$qry = new WP_Query( $args );
			
			$orders = apply_filters('wcmp_filter_orders_report_overview' , $qry->get_posts(),  $vendor->id);
			if ( !empty($orders) ) {
				foreach($orders as $order_obj) {
					
					$order = new WC_Order( $order_obj->ID );
					$items = $order->get_items( 'line_item' );
					
					$commission_array = array();
					
					foreach( $items as $item_id => $item ) {
						if(!isset($item['item_meta']['_vendor_id'][0])) continue;
						$comm_pro_id = $product_id = $order->get_item_meta( $item_id, '_product_id', true );
						$line_total = $order->get_item_meta( $item_id, '_line_total', true );
						$variation_id = $order->get_item_meta( $item_id, '_variation_id', true );
						
						if($variation_id) $comm_pro_id = $variation_id;
						
						if( $product_id ) {
							
							$product_vendors = get_wcmp_product_vendors($product_id);
							
							if( $product_vendors ) {
								if($vendor->id == $item['item_meta']['_vendor_id'][0]) {
									$sales += $line_total;
									$total_sales += $line_total;
									
									$args = array(
										'post_type' =>  'dc_commission',
										'post_status' => array( 'publish', 'private' ),
										'posts_per_page' => -1,
										'meta_query' => array(
											array(
												'key' => '_commission_vendor',
												'value' => absint($product_vendors->term_id),
												'compare' => '='
											),
											array(
												'key' => '_commission_order_id',
												'value' => absint($order_obj->ID),
												'compare' => '='
											),
											array(
												'key' => '_commission_product',
												'value' => absint($comm_pro_id),
												'compare' => 'LIKE'
											),
										),
									);
									$commissions = get_posts( $args );
									$comm_amount = 0;
									if(!empty($commissions)) { 
										foreach($commissions as $commission) {
											
											
											if(in_array($commission->ID, $commission_array)) continue;
											
											$comm_amount += (float)get_post_meta($commission->ID, '_commission_amount', true);
											
											$commission_array[] = $commission->ID;
										}
									}
									
									$vendor_earnings += $comm_amount;
									$total_vendor_earnings  += $comm_amount;
									$earnings += ( $line_total - $comm_amount );
									$total_earnings += ( $line_total - $comm_amount );
									$total_purchased_products++;
								}
							}							
						}						
					}
					
					//coupons count
					$coupon_used = array();
					$coupons = $order->get_items( 'coupon' );
					foreach ( $coupons as $coupon_item_id => $item ) {
						$coupon = new WC_Coupon( trim( $item['name'] ));
						$coupon_post = get_post($coupon->id);
						$author_id = $coupon_post->post_author;
						if($vendor->id == $author_id) {
							$total_coupon_used++ ;
							$total_coupon_discuont_value += (float)wc_add_order_item_meta( $coupon_item_id, 'discount_amount', true);
						} 
					}
					++$total_order_count;
					
					//user count
					if( $order->customer_user != 0 && $order->customer_user != 1) array_push($total_customers, $order->customer_user);
				}
			}			
		}
		
		return array('total_order_count' => $total_order_count, 'total_vendor_sales' => $total_sales, 'total_vendor_earning' => $total_vendor_earnings, 'total_coupon_discuont_value' => $total_coupon_discuont_value, 'total_coupon_used' => $total_coupon_used, 'total_customers' => array_unique($total_customers), 'total_purchased_products' => $total_purchased_products);
	}
	
}
?>