<?php
/**
 * WCMp Frontend Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		DualCube
 */
 
class WCMp_Frontend {
	
	public $wcmp_shipping_fee_cost = 0;
	
	public $pagination_sale = array();
	
	public function __construct() {
		//enqueue scripts
		add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
		//enqueue styles
		add_action( 'wp_enqueue_scripts', array(&$this, 'frontend_styles'));		
		add_action( 'woocommerce_archive_description', array(&$this, 'product_archive_vendor_info' ), 10);		
		add_filter( 'body_class', array( &$this, 'set_product_archive_class' ) );		
		add_action( 'template_redirect', array(&$this, 'template_redirect' ));		
		add_action( 'woocommerce_checkout_order_processed', array(&$this, 'wcmp_checkout_order_processed'), 30, 2);	
		add_action( 'woocommerce_order_details_after_order_table', array($this, 'display_vendor_msg_in_thank_you_page'),100);
	}
	
	
	public function display_vendor_msg_in_thank_you_page($order_id) {
		global $wpdb, $WCMp;
		$order = wc_get_order( $order_id );
		$items = $order->get_items( 'line_item' );
		$vendor_array = array();
		$author_id = '';
		$capability_settings = get_option('wcmp_capabilities_settings_name');
		$customer_support_details_settings = get_option('wcmp_general_customer_support_details_settings_name');
		$is_csd_by_admin = '';
		foreach( $items as $item_id => $item ) {			
			$product_id = $order->get_item_meta( $item_id, '_product_id', true );
			if( $product_id ) {				
				$author_id = $order->get_item_meta( $item_id, '_vendor_id', true );
				if( empty($author_id) ) {
					$product_vendors = get_wcmp_product_vendors($product_id);
					if(isset($product_vendors) && (!empty($product_vendors))) {
						$author_id = $product_vendors->id;
					}
					else {
						$author_id = get_post_field('post_author', $product_id);
					}
				}
				if(isset($vendor_array[$author_id])){
					$vendor_array[$author_id] = $vendor_array[$author_id].','.$item['name'];
				}
				else {
					$vendor_array[$author_id] = $item['name'];
				}								
			}						
		}
		if(!empty($vendor_array)) {
			echo '<div style="clear:both">';
			
			if( isset( $capability_settings['can_vendor_add_message_on_email_and_thankyou_page'] ) ) {				
				$WCMp->template->get_template( 'vendor_message_to_buyer.php', array( 'vendor_array'=>$vendor_array, 'capability_settings'=>$capability_settings, 'customer_support_details_settings'=>$customer_support_details_settings));				
			}
			elseif(isset($customer_support_details_settings['is_customer_support_details'])) {
				$WCMp->template->get_template( 'customer_support_details_to_buyer.php', array( 'vendor_array'=>$vendor_array, 'capability_settings'=>$capability_settings, 'customer_support_details_settings'=>$customer_support_details_settings ));
			}
			echo "</div>";
		}
		
	}
	
	
	
	/**
	 * WCMp Calculate shipping for order
	 *
	 * @support flat rate per item 
	 * @param int $order_id
	 * @param object $order_posted
	 * @return void
	 */
	 
	function wcmp_checkout_order_processed($order_id, $order_posted) {
		global $wpdb, $WCMp;
		
		$order = new WC_Order($order_id);

		if ( version_compare( WC_VERSION, '2.6.0', '>=' ) ) {
			$shipping_method = $order->get_shipping_methods();
			foreach ($shipping_method as $key => $method) {
				$method_id = $method['method_id'];
				break;
			}
			$method_arr = explode(':', $method_id);

			if(count($method_arr) >= 2){
				$method_name = $method_arr[0];
				$method_instance = $method_arr[1];

				if(!empty($method_name) && $method_name == 'flat_rate'){
	 				$woocommerce_shipping_method_settings = get_option('woocommerce_'.$method_name.'_'.$method_instance.'_settings');
					$line_items = $order->get_items('line_item');
	 				if($woocommerce_shipping_method_settings['type'] == 'class') {
						if (!empty($line_items)) {
							foreach ( $line_items as $item_id => $item ) {
								$shipping_item_qty = 0;
								$wc_flat_rate = new WC_Shipping_Flat_Rate();
								$product = $order->get_product_from_item( $item );
								$shipping_class = $product->get_shipping_class_id();
								$class_cost_string = $woocommerce_shipping_method_settings['class_cost_'.$shipping_class];//$shipping_class ? $wc_flat_rate->get_option( 'class_cost_' . $shipping_class, '' ) : $wc_flat_rate->get_option( 'no_class_cost', '' );								
								$cost_item_id = $this->calculate_flat_rate_shipping_cost( $class_cost_string, array(
									'qty'  => $item['qty'],
									'cost' => $order->get_line_subtotal( $item )
								) );								
								$searchStr= "qty";								
								if(strlen(strstr($class_cost_string,$searchStr)) == 0) {									
									$order_items = $order->get_items('line_item');
									if(!empty( $order_items )) {
										foreach ( $order_items as $order_item ) {
											$product_2 = $order->get_product_from_item( $order_item );
											$shipping_class_2 = $product_2->get_shipping_class_id();
											if( $shipping_class_2 == $shipping_class ) {
												$shipping_item_qty++;
											}																						
										}										
									}
									if( $cost_item_id > 0 && $shipping_item_qty > 1) {
										$cost_item_id = (($cost_item_id/$shipping_item_qty));										
									}
								}	
							
								$flat_shipping_per_item_val = wc_get_order_item_meta( $item_id, 'flat_shipping_per_item', true);
								if(!$flat_shipping_per_item_val) wc_add_order_item_meta( $item_id, 'flat_shipping_per_item',  round($cost_item_id, 2));
							}
						}
					}
	 			}
 			} else { // Deprecated shipping method
 				if( $order->has_shipping_method('legacy_flat_rate') ) {
					$woocommerce_flat_rate_settings = get_option('woocommerce_flat_rate_settings');
					$line_items = $order->get_items('line_item');
					if($woocommerce_flat_rate_settings['enabled'] == 'yes') {
						if($woocommerce_flat_rate_settings['type'] == 'class') {
							if (!empty($line_items)) {
								foreach ( $line_items as $item_id => $item ) {
									$shipping_item_qty = 0;
									//$wc_flat_rate = new WC_Shipping_Flat_Rate();
									$product = $order->get_product_from_item( $item );
									$shipping_class = $product->get_shipping_class_id();
									$class_cost_string = $woocommerce_flat_rate_settings['class_cost_'.$shipping_class];
									//$shipping_class ? $wc_flat_rate->get_option( 'class_cost_' . $shipping_class, '' ) : $wc_flat_rate->get_option( 'no_class_cost', '' );								

									$cost_item_id = $this->calculate_flat_rate_shipping_cost( $class_cost_string, array(
										'qty'  => $item['qty'],
										'cost' => $order->get_line_subtotal( $item )
									) );								
									$searchStr= "qty";								
									if(strlen(strstr($class_cost_string,$searchStr)) == 0) {									
										$order_items = $order->get_items('line_item');
										if(!empty( $order_items )) {
											foreach ( $order_items as $order_item ) {
												$product_2 = $order->get_product_from_item( $order_item );
												$shipping_class_2 = $product_2->get_shipping_class_id();
												if( $shipping_class_2 == $shipping_class ) {
													$shipping_item_qty++;
												}																						
											}										
										}
										if( $cost_item_id > 0 && $shipping_item_qty > 1) {
											$cost_item_id = (($cost_item_id/$shipping_item_qty));										
										}
									}								
									$flat_shipping_per_item_val = wc_get_order_item_meta( $item_id, 'flat_shipping_per_item', true);
									if(!$flat_shipping_per_item_val) wc_add_order_item_meta( $item_id, 'flat_shipping_per_item',  round($cost_item_id, 2));
								}
							}
						}			
					}
				}
 			}
 			if( $order->has_shipping_method('legacy_international_delivery') ){
				$woocommerce_international_delivery_settings = get_option('woocommerce_international_delivery_settings');
				$line_items = $order->get_items('line_item');
				
				if($woocommerce_international_delivery_settings['enabled'] == 'yes') {
					if($woocommerce_international_delivery_settings['type'] == 'class') {
						if (!empty($line_items)) {
							$item_id = false;
							foreach ( $line_items as $item_id => $item ) {
								//$wc_international_flat_rate = new WC_Shipping_International_Delivery();
								$product = $order->get_product_from_item( $item );
								$shipping_class = $product->get_shipping_class_id();
								$class_cost_string = $woocommerce_international_delivery_settings['class_cost_'.$shipping_class];//$shipping_class ? $wc_flat_rate->get_option( 'class_cost_' . $shipping_class, '' ) : $wc_flat_rate->get_option( 'no_class_cost', '' );								
								$cost_item_id = $this->calculate_flat_rate_shipping_cost( $class_cost_string, array(
									'qty'  => $item['qty'],
									'cost' => $order->get_line_subtotal( $item )
								) );
								$searchStr= "qty";								
								if(strlen(strstr($class_cost_string,$searchStr)) == 0) {									
									$order_items = $order->get_items('line_item');
									if(!empty( $order_items )) {
										foreach ( $order_items as $order_item ) {
											$product_2 = $order->get_product_from_item( $order_item );
											$shipping_class_2 = $product_2->get_shipping_class_id();
											if( $shipping_class_2 == $shipping_class ) {
												$shipping_item_qty++;
											}																						
										}										
									}
									if( $cost_item_id > 0 && $shipping_item_qty > 1) {
										$cost_item_id = (($cost_item_id/$shipping_item_qty));										
									}
								}								
								$international_flat_shipping_per_item_val = wc_get_order_item_meta( $item_id, 'international_flat_shipping_per_item', true);
								if(!$international_flat_shipping_per_item_val) wc_add_order_item_meta( $item_id, 'international_flat_shipping_per_item',  $cost_item_id);
							}
						}						
					}
				}
			}
		} else { // WC version < 2.6
			if( $order->has_shipping_method('flat_rate') ) {
				$woocommerce_flat_rate_settings = get_option('woocommerce_flat_rate_settings');
				$line_items = $order->get_items('line_item');			
				if($woocommerce_flat_rate_settings['enabled'] == 'yes') {				
					if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
						if($woocommerce_flat_rate_settings['type'] == 'class') {
							if (!empty($line_items)) {
								foreach ( $line_items as $item_id => $item ) {
									$shipping_item_qty = 0;
									$wc_flat_rate = new WC_Shipping_Flat_Rate();
									$product = $order->get_product_from_item( $item );
									$shipping_class = $product->get_shipping_class_id();
									
									$class_cost_string = $shipping_class ? $wc_flat_rate->get_option( 'class_cost_' . $shipping_class, '' ) : $wc_flat_rate->get_option( 'no_class_cost', '' );								
									$cost_item_id = $this->evaluate_flat_shipping_cost( $class_cost_string, array(
										'qty'  => $item['qty'],
										'cost' => $order->get_line_subtotal( $item )
									) );								
									$searchStr= "qty";								
									if(strlen(strstr($class_cost_string,$searchStr)) == 0) {									
										$order_items = $order->get_items('line_item');
										if(!empty( $order_items )) {
											foreach ( $order_items as $order_item ) {
												$product_2 = $order->get_product_from_item( $order_item );
												$shipping_class_2 = $product_2->get_shipping_class_id();
												if( $shipping_class_2 == $shipping_class ) {
													$shipping_item_qty++;
												}																						
											}										
										}
										if( $cost_item_id > 0 && $shipping_item_qty > 1) {
											$cost_item_id = (($cost_item_id/$shipping_item_qty));										
										}
									}								
									$flat_shipping_per_item_val = wc_get_order_item_meta( $item_id, 'flat_shipping_per_item', true);
									if(!$flat_shipping_per_item_val) wc_add_order_item_meta( $item_id, 'flat_shipping_per_item',  round($cost_item_id, 2));
								}
							}
						}
					}
					else if ( version_compare( WC_VERSION, '2.4.0', '>' ) ) {
						if($woocommerce_flat_rate_settings['type'] == 'class') {
							if (!empty($line_items)) {
								foreach ( $line_items as $item_id => $item ) {
									$wc_flat_rate = new WC_Shipping_Flat_Rate();
									$product = $order->get_product_from_item( $item );
									$shipping_class = $product->get_shipping_class();
									$class_cost_string = $shipping_class ? $wc_flat_rate->get_option( 'class_cost_' . $shipping_class, '' ) : $wc_flat_rate->get_option( 'no_class_cost', '' );
									$cost_item_id = $this->evaluate_flat_shipping_cost( $class_cost_string, array(
										'qty'  => $item['qty'],
										'cost' => $order->get_line_subtotal( $item )
									) );
									$searchStr= "qty";								
									if(strlen(strstr($class_cost_string,$searchStr)) == 0) {									
										$order_items = $order->get_items('line_item');
										if(!empty( $order_items )) {
											foreach ( $order_items as $order_item ) {
												$product_2 = $order->get_product_from_item( $order_item );
												$shipping_class_2 = $product_2->get_shipping_class_id();
												if( $shipping_class_2 == $shipping_class ) {
													$shipping_item_qty++;
												}																						
											}										
										}
										if( $cost_item_id > 0 && $shipping_item_qty > 1) {
											$cost_item_id = (($cost_item_id/$shipping_item_qty));										
										}
									}
									$flat_shipping_per_item_val = wc_get_order_item_meta( $item_id, 'flat_shipping_per_item', true);
									if(!$flat_shipping_per_item_val) wc_add_order_item_meta( $item_id, 'flat_shipping_per_item',  round($cost_item_id, 2));
								}
							}
						}
					} else {
						$woocommerce_flat_rate_settings_cost = $woocommerce_flat_rate_settings['cost'];
						$woocommerce_flat_rate_settings_fee = $woocommerce_flat_rate_settings['fee']; 
						$woocommerce_flat_rates = get_option('woocommerce_flat_rates');
						if($woocommerce_flat_rate_settings['type'] == 'item') {
							if (!empty($line_items)) {
								foreach ( $line_items as $item_id => $item ) {
									$fee = $cost = 0;
									$_product = $order->get_product_from_item( $item );
									$shipping_class = $_product->get_shipping_class();
									if (isset( $woocommerce_flat_rates[ $shipping_class ] ) ) {
										$cost = $woocommerce_flat_rates[ $shipping_class ]['cost'];
										$fee	= $this->get_fee( $woocommerce_flat_rates[ $shipping_class ]['fee'], $_product->get_price() );
									}  elseif ( $woocommerce_flat_rate_settings_cost !== '' ) {
										$cost 	= $woocommerce_flat_rate_settings_cost;
										$fee	= $this->get_fee( $woocommerce_flat_rate_settings_fee, $_product->get_price() );
										$matched = true;
									}
									$cost_item_id = ( ( $cost + $fee ) * $item['qty'] );
									$flat_shipping_per_item_val = wc_get_order_item_meta( $item_id, 'flat_shipping_per_item', true);
									if(!$flat_shipping_per_item_val) wc_add_order_item_meta( $item_id, 'flat_shipping_per_item',  round($cost_item_id, 2));
								}
							}
						}
					}
				}
			}
			
			if( $order->has_shipping_method('international_delivery') ){
				$woocommerce_international_delivery_settings = get_option('woocommerce_international_delivery_settings');
				$line_items = $order->get_items('line_item');
				
				if($woocommerce_international_delivery_settings['enabled'] == 'yes') {
					if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
						if($woocommerce_international_delivery_settings['type'] == 'class') {
							if (!empty($line_items)) {
								$item_id = false;
								foreach ( $line_items as $item_id => $item ) {
									$wc_international_flat_rate = new WC_Shipping_International_Delivery();
									$product = $order->get_product_from_item( $item );
									$shipping_class = $product->get_shipping_class_id();
									$class_cost_string = $shipping_class ? $wc_international_flat_rate->get_option( 'class_cost_' . $shipping_class, '' ) : $wc_international_flat_rate->get_option( 'no_class_cost', '' );
									$cost_item_id = $this->evaluate_flat_shipping_cost( $class_cost_string, array(
										'qty'  => $item['qty'],
										'cost' => $order->get_line_subtotal( $item )
									) );
									$searchStr= "qty";								
									if(strlen(strstr($class_cost_string,$searchStr)) == 0) {									
										$order_items = $order->get_items('line_item');
										if(!empty( $order_items )) {
											foreach ( $order_items as $order_item ) {
												$product_2 = $order->get_product_from_item( $order_item );
												$shipping_class_2 = $product_2->get_shipping_class_id();
												if( $shipping_class_2 == $shipping_class ) {
													$shipping_item_qty++;
												}																						
											}										
										}
										if( $cost_item_id > 0 && $shipping_item_qty > 1) {
											$cost_item_id = (($cost_item_id/$shipping_item_qty));										
										}
									}								
									$international_flat_shipping_per_item_val = wc_get_order_item_meta( $item_id, 'international_flat_shipping_per_item', true);
									if(!$international_flat_shipping_per_item_val) wc_add_order_item_meta( $item_id, 'international_flat_shipping_per_item',  $cost_item_id);
								}
							}						
						}
					}
					else if ( version_compare( WC_VERSION, '2.4.0', '>' ) ) {
						if($woocommerce_international_delivery_settings['type'] == 'class') {
							if (!empty($line_items)) {
								$item_id = false;
								foreach ( $line_items as $item_id => $item ) {
									$wc_international_flat_rate = new WC_Shipping_International_Delivery();
									$product = $order->get_product_from_item( $item );
									$shipping_class = $product->get_shipping_class();
									$class_cost_string = $shipping_class ? $wc_international_flat_rate->get_option( 'class_cost_' . $shipping_class, '' ) : $wc_international_flat_rate->get_option( 'no_class_cost', '' );
									$cost_item_id = $this->evaluate_flat_shipping_cost( $class_cost_string, array(
										'qty'  => $item['qty'],
										'cost' => $order->get_line_subtotal( $item )
									) );
									$searchStr= "qty";								
									if(strlen(strstr($class_cost_string,$searchStr)) == 0) {									
										$order_items = $order->get_items('line_item');
										if(!empty( $order_items )) {
											foreach ( $order_items as $order_item ) {
												$product_2 = $order->get_product_from_item( $order_item );
												$shipping_class_2 = $product_2->get_shipping_class_id();
												if( $shipping_class_2 == $shipping_class ) {
													$shipping_item_qty++;
												}																						
											}										
										}
										if( $cost_item_id > 0 && $shipping_item_qty > 1) {
											$cost_item_id = (($cost_item_id/$shipping_item_qty));										
										}
									}
									$international_flat_shipping_per_item_val = wc_get_order_item_meta( $item_id, 'international_flat_shipping_per_item', true);
									if(!$international_flat_shipping_per_item_val) wc_add_order_item_meta( $item_id, 'international_flat_shipping_per_item',  $cost_item_id);
								}
							}						
						}
					}
				}
			}
		}

		$vendor_shipping_array = get_post_meta($order_id, 'dc_pv_shipped', true);
		$order = new WC_Order( $order_id );
		$commission_array = array();
		$mark_ship = 0;
		$items = $order->get_items( 'line_item' );
		
		foreach( $items as $order_item_id => $item ) {
			
			$comm_pro_id = $product_id = $item['product_id'];
			
			$variation_id = $item['variation_id']; 
			
			if($variation_id) $comm_pro_id = $variation_id;
			
			if( $product_id ) {
				
				$product_vendors = get_wcmp_product_vendors($product_id);
				
				if( $product_vendors ) {
					if(isset($product_vendors->id) && is_array($vendor_shipping_array)) {
						if(in_array($product_vendors->id, $vendor_shipping_array)) {
							$mark_ship = 1;
						}
					}					
					
					$insert_query = $wpdb->query($wpdb->prepare("INSERT INTO `{$wpdb->prefix}wcmp_vendor_orders` ( order_id, commission_id, vendor_id, shipping_status, order_item_id, product_id )
													 VALUES
													 ( %d, %d, %d, %s, %d, %d ) ON DUPLICATE KEY UPDATE `created` = now()", $order_id, 0, $product_vendors->id, $mark_ship, $order_item_id, $comm_pro_id));
					
					
				}
			}
		}
		
	}
	
	/**
	 * Get shipping fee
	 *
	 * Now deprecated
	 */
	function get_fee( $fee, $total ) {
		$woocommerce_flat_rate_settings = get_option('woocommerce_flat_rate_settings');
		if ( strstr( $fee, '%' ) ) {
			$fee = ( $total / 100 ) * str_replace( '%', '', $fee );
		}
		if ( ! empty( $woocommerce_flat_rate_settings['minimum_fee'] ) && $woocommerce_flat_rate_settings['minimum_fee'] > $fee ) {
			$fee = $woocommerce_flat_rate_settings['minimum_fee'];
		}
		return $fee;
	}

	/**
	 * Add frontend scripts
	 * @return void
	 */
	function frontend_scripts() {
		global $WCMp;
		$frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WCMp->plugin_url );
		$suffix 				= defined( 'WCMP_SCRIPT_DEBUG' ) && WCMP_SCRIPT_DEBUG ? '' : '.min';
		
		// Enqueue your frontend javascript from here
		wp_enqueue_script('frontend_js', $frontend_script_path. 'frontend'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		if(is_shop_settings()) {
			$WCMp->library->load_upload_lib();
			wp_enqueue_script('edit_user_js', $WCMp->plugin_url.'assets/admin/js/edit_user'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		}
		
		if(is_vendor_order_by_product_page()) {
			wp_enqueue_script('vendor_order_by_product_js', $frontend_script_path. 'vendor_order_by_product'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		}
		
		if(is_single()) {
			wp_enqueue_script('simplepopup_js', $frontend_script_path. 'simplepopup'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		}
		
		wp_register_script( 'gmaps-api', '//maps.google.com/maps/api/js?sensor=false&amp;language=en', array( 'jquery' ) );
    wp_register_script( 'gmap3', $frontend_script_path . 'gmap3.min.js', array( 'jquery', 'gmaps-api' ), '6.0.0', false );
		if( is_tax( 'dc_vendor_shop' ) || is_singular( 'product' ) ) {
			wp_enqueue_script( 'gmap3' );
		}
		if(is_vendor_page()) {
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script('wcmp_new_vandor_dashboard_js', $frontend_script_path.'/vendor_dashboard'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		}
		if(is_tax('dc_vendor_shop') ) {
			$queried_object = get_queried_object();
			if(isset($queried_object->term_id) && !empty($queried_object)) {
				$vendor = get_wcmp_vendor_by_term( $queried_object->term_id );
				$vendor_id =  $vendor->id;								
			}
			
			wp_enqueue_script('wcmp_seller_review_rating_js', $frontend_script_path.'/vendor_review_rating'.$suffix.'.js', array('jquery'), $WCMp->version, true);
			$vendor_review_rating_msg_array = array(
				'rating_error_msg_txt' => __( 'Please rate the vendor', $WCMp->text_domain ),
				'review_error_msg_txt' => __( 'Please review your vendor and minimum 10 Character required', $WCMp->text_domain ),
				'review_success_msg_txt' => __( 'Your review submitted successfully', $WCMp->text_domain ),
				'review_failed_msg_txt' => __( 'Error in system please try again later', $WCMp->text_domain ),
				'ajax_url' => trailingslashit(get_admin_url()).'admin-ajax.php',
				'vendor_id'=> $vendor_id ? $vendor_id : ''				
			);
			wp_localize_script( 'wcmp_seller_review_rating_js', 'wcmp_review_rating_msg', $vendor_review_rating_msg_array );
		}
		if( is_singular( 'product' ) ) {
			wp_enqueue_script('wcmp_single_product_multiple_vendors', $frontend_script_path.'/single-product-multiple-vendors'.$suffix.'.js', array('jquery'), $WCMp->version, true);
		}
	}

  /**
	 * Add frontend styles
	 * @return void
	*/
	function frontend_styles() {
		global $WCMp;
		$frontend_style_path = $WCMp->plugin_url . 'assets/frontend/css/';
		$frontend_style_path = str_replace( array( 'http:', 'https:' ), '', $frontend_style_path );
		$suffix 				= defined( 'WCMP_SCRIPT_DEBUG' ) && WCMP_SCRIPT_DEBUG ? '' : '.min';
		
		if( is_tax( 'dc_vendor_shop' ) ) {
			wp_enqueue_style('frontend_css',  $frontend_style_path .'frontend'.$suffix.'.css', array(), $WCMp->version);
		}
		
		wp_enqueue_style('product_css',  $frontend_style_path .'product'.$suffix.'.css', array(), $WCMp->version);
		
		if(is_vendor_order_by_product_page()) {
			wp_enqueue_style('vendor_order_by_product_css',  $frontend_style_path .'vendor_order_by_product'.$suffix.'.css', array(), $WCMp->version);
		}
		
    $link_color = isset($WCMp->vendor_caps->frontend_cap['catalog_colorpicker']) ? $WCMp->vendor_caps->frontend_cap['catalog_colorpicker'] : '#000000';
    $hover_link_color = isset( $WCMp->vendor_caps->frontend_cap['catalog_hover_colorpicker']) ? $WCMp->vendor_caps->frontend_cap['catalog_hover_colorpicker'] : '#000000';
    
    $custom_css = "
                .by-vendor-name-link:hover{
                        color: {$hover_link_color} !important;
                }
                .by-vendor-name-link{
                        color: {$link_color} !important;
                }";
    wp_add_inline_style( 'product_css', $custom_css );
    if(is_vendor_page()) {
    	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
			wp_enqueue_style('wcmp_new_vandor_dashboard_css',  $frontend_style_path .'vendor_dashboard'.$suffix.'.css', array(), $WCMp->version);
			wp_enqueue_style('font-awesome',  'http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', array(), $WCMp->version);
		}
		if(is_tax('dc_vendor_shop') ) {
			$current_theme = get_option('template');
			if($current_theme == 'storefront') {
				wp_enqueue_style('wcmp_review_rating',  $frontend_style_path .'review_rating_storefront'.$suffix.'.css', array(), $WCMp->version);
			}else {
				wp_enqueue_style('wcmp_review_rating',  $frontend_style_path .'review_rating'.$suffix.'.css', array(), $WCMp->version);
			}
		}
		wp_enqueue_style('multiple_vendor',  $frontend_style_path .'multiple-vendor'.$suffix.'.css', array(), $WCMp->version);
	}
	
	/**
	 * Add html for vendor taxnomy page
	 * @return void
	*/
	function product_archive_vendor_info() {
		global $WCMp;
		if( is_tax( 'dc_vendor_shop' ) ) {
			// Get vendor ID
			$vendor_id = get_queried_object()->term_id;
			// Get vendor info
			$vendor = get_wcmp_vendor_by_term( $vendor_id );
			$image 	= '';
			$image 	= $vendor->image;
			if(!$image) $image = $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
			$description = $vendor->description;
			
			$address = '';
			
			if($vendor->city) {
				$address = $vendor->city .', ';
			}
			if($vendor->state) {
				$address .= $vendor->state .', ';
			}
			if($vendor->country) {
				$address .= $vendor->country;
			}
			$WCMp->template->get_template( 'archive_vendor_info.php', array('vendor_id' => $vendor->id, 'banner' => $vendor->banner, 'profile' => $image, 'description' => stripslashes($description), 'mobile' => $vendor->phone, 'location' => $address, 'email' => $vendor->user_data->user_email ) );
		}
  }
	
	
	/**
	 * Add 'woocommerce' class to body tag for vendor pages
	 *
	 * @param  arr $classes Existing classes
	 * @return arr          Modified classes
	*/
	public function set_product_archive_class( $classes ) {
		if( is_tax( 'dc_vendor_shop' ) ) {

			// Add generic classes
			$classes[] = 'woocommerce';
			$classes[] = 'product-vendor';

			// Get vendor ID
			$vendor_id = get_queried_object()->term_id;

			// Get vendor info
			$vendor = get_wcmp_vendor_by_term( $vendor_id );

			// Add vendor slug as class
			if( '' != $vendor->slug ) {
					$classes[] = $vendor->slug;
			}
		}
		return $classes;
	}
	
	
	/**
	 * template redirect function
	 * @return void
	*/
	function template_redirect() {
		$pages = get_option("wcmp_pages_settings_name");
		
		if(!empty($pages)) {
			
			//rediect to shop page when a non vendor loggedin user is on vendor pages but not in vendor dashboard page
			if( is_user_logged_in() && is_vendor_page() && ! is_user_wcmp_vendor( get_current_user_id() ) ) {
				if(is_page($pages['vendor_transaction_detail']) && !current_user_can('administrator') ) {
					wp_safe_redirect( get_permalink( $pages['vendor_dashboard'] ) );
					exit();
				}
				
				if(!is_page($pages['vendor_dashboard']) && !is_page($pages['vendor_transaction_detail'])) {
					wp_safe_redirect( get_permalink( $pages['vendor_dashboard'] ) );
					exit();
				}
			} 
			
			//rediect to myaccount page when a non loggedin user is on vendor pages
			if( !is_user_logged_in() && is_vendor_page() && ! is_page( woocommerce_get_page_id( 'myaccount' ) ) ) {
				wp_safe_redirect( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) );
				exit();
			}
			
			//rediect to vendor dashboard page when a  loggedin user is on vendor_order_detail page but order id query argument is not sent in url
			if(is_page( absint( $pages['vendor_order_detail'] ) ) && is_user_logged_in() &&  is_user_wcmp_vendor( get_current_user_id() ) ) {
				if(!isset($_GET['order_id']) && empty($_GET['order_id'])) {
					wp_safe_redirect( get_permalink($pages['vendor_dashboard'] ) );
					exit();
				}
			}
		
			
			//rediect to myaccount page when a non logged in user is on vendor_order_detail
			if( !is_user_logged_in() && is_page( absint( $pages['vendor_order_detail'] ) ) && ! is_page( woocommerce_get_page_id( 'myaccount' ) ) ) {
				wp_safe_redirect( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) );
				exit();
			}
		}
	}
	
	
	/** 
	 * Calculate order falt rate shipping
	 *
	 * @support WC 2.4
	 */
  public function evaluate_flat_shipping_cost($sum, $args = array()) {
		include_once( WC()->plugin_path() . '/includes/shipping/flat-rate/includes/class-wc-eval-math.php' );

		add_shortcode( 'fee', array($this, 'wcmp_shipping_fee_calculation') );
		$this->wcmp_shipping_fee_cost = $args['cost'];

		$sum = rtrim( ltrim( do_shortcode( str_replace(
				array(
						'[qty]',
						'[cost]'
				),
				array(
						$args['qty'],
						$args['cost']
				),
				$sum
		) ), "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

		remove_shortcode( 'fee', array($this, 'wcmp_shipping_fee_calculation') );

		return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
  }
  /** 
 * Calculate order flat rate shipping
 *
 * @support WC 2.6
 */
  public function calculate_flat_rate_shipping_cost($sum, $args = array()){
  	include_once( WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php' );
  	$WC_Shipping_Flat_Rate = new WC_Shipping_Flat_Rate();
	// Allow 3rd parties to process shipping cost arguments
	$args           = apply_filters( 'woocommerce_evaluate_shipping_cost_args', $args, $sum, $this );
	$locale         = localeconv();
	$decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'] );
	$this->fee_cost = $args['cost'];

	// Expand shortcodes
	add_shortcode( 'fee', array( $WC_Shipping_Flat_Rate, 'fee' ) );

	$sum = do_shortcode( str_replace(
		array(
			'[qty]',
			'[cost]'
		),
		array(
			$args['qty'],
			$args['cost']
		),
		$sum
	) );

	remove_shortcode( 'fee', array( $WC_Shipping_Flat_Rate, 'fee' ) );

	// Remove whitespace from string
	$sum = preg_replace( '/\s+/', '', $sum );

	// Remove locale from string
	$sum = str_replace( $decimals, '.', $sum );

	// Trim invalid start/end characters
	$sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

	// Do the math
	return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
  }
    
  /**
   * Calculate flat rate shipping fee
   *
   * @support WC 2.4
   */
	public function wcmp_shipping_fee_calculation( $atts ) {
		$atts = shortcode_atts( array(
				'percent' => '',
				'min_fee' => ''
		), $atts );
		
		$calculated_fee = 0;
		
		if ( $atts['percent'] ) {
				$calculated_fee = $this->wcmp_shipping_fee_cost * ( floatval( $atts['percent'] ) / 100 );
		}
		if ( $atts['min_fee'] && $calculated_fee < $atts['min_fee'] ) {
				$calculated_fee = $atts['min_fee'];
		}
		
		return $calculated_fee;
	}

}