<?php
/**
 * WCMp Ajax Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		DualCube
 */
 
class WCMp_Ajax {

	public function __construct() {
		add_action('wp_ajax_woocommerce_json_search_vendors', array( &$this, 'woocommerce_json_search_vendors'));
		add_action('wp_ajax_activate_pending_vendor', array(&$this, 'activate_pending_vendor'));
		add_action('wp_ajax_reject_pending_vendor', array(&$this, 'reject_pending_vendor'));
	  add_action('wp_ajax_send_report_abuse', array( &$this, 'send_report_abuse' ) );
    add_action('wp_ajax_nopriv_send_report_abuse', array( &$this, 'send_report_abuse' ) );
    add_action('wp_ajax_dismiss_vendor_to_do_list', array( &$this, 'dismiss_vendor_to_do_list' ) );
    add_action('wp_ajax_get_more_orders', array( &$this, 'get_more_orders' ) );
    add_action('wp_ajax_withdrawal_more_orders', array( &$this, 'withdrawal_more_orders' ) );
    add_action('wp_ajax_show_more_transaction', array( &$this, 'show_more_transaction' ) );
    add_action('wp_ajax_nopriv_get_more_orders', array( &$this, 'get_more_orders' ) );
    add_action('wp_ajax_order_mark_as_shipped', array( &$this, 'order_mark_as_shipped' ) );
    add_action('wp_ajax_nopriv_order_mark_as_shipped', array( &$this, 'order_mark_as_shipped' ) );  
    add_action('wp_ajax_transaction_done_button', array( &$this, 'transaction_done_button' ) );
    add_action('wp_ajax_wcmp_vendor_csv_download_per_order', array( &$this, 'wcmp_vendor_csv_download_per_order' ) );    
    add_filter('ajax_query_attachments_args', array( &$this, 'show_current_user_attachments'), 10, 1 );    
    add_filter('wp_ajax_vendor_report_sort', array( $this, 'vendor_report_sort' ));
    add_filter('wp_ajax_vendor_search', array( $this, 'search_vendor_data' ));    
    add_filter( 'wp_ajax_product_report_sort', array( $this, 'product_report_sort' ) );
    add_filter( 'wp_ajax_product_search', array( $this, 'search_product_data' ) );
    // woocommerce product enquiry form support
    if( WC_Dependencies_Product_Vendor::woocommerce_product_enquiry_form_active_check() ) {
    	add_filter( 'product_enquiry_send_to', array($this, 'send_enquiry_to_vendor'), 10, 2 );
    }
    
    // Unsign vendor from product
    add_action( 'wp_ajax_unassign_vendor', array($this, 'unassign_vendor') );    
    add_action('wp_ajax_wcmp_frontend_sale_get_row', array( &$this, 'wcmp_frontend_sale_get_row_callback'));
		add_action('wp_ajax_nopriv_wcmp_frontend_sale_get_row', array(&$this, 'wcmp_frontend_sale_get_row_callback'));
		add_action('wp_ajax_wcmp_frontend_pending_shipping_get_row', array( &$this, 'wcmp_frontend_pending_shipping_get_row_callback'));
		add_action('wp_ajax_nopriv_wcmp_frontend_pending_shipping_get_row', array(&$this, 'wcmp_frontend_pending_shipping_get_row_callback'));
		
		add_action('wp_ajax_wcmp_vendor_announcements_operation', array($this, 'wcmp_vendor_messages_operation'));
		add_action('wp_ajax_nopriv_wcmp_vendor_announcements_operation', array($this, 'wcmp_vendor_messages_operation'));
		add_action('wp_ajax_wcmp_announcements_refresh_tab_data', array($this, 'wcmp_msg_refresh_tab_data'));
		add_action('wp_ajax_nopriv_wcmp_announcements_refresh_tab_data', array($this, 'wcmp_msg_refresh_tab_data'));
		add_action('wp_ajax_wcmp_dismiss_dashboard_announcements', array($this, 'wcmp_dismiss_dashboard_message'));
		add_action('wp_ajax_nopriv_wcmp_dismiss_dashboard_announcements', array($this, 'wcmp_dismiss_dashboard_message'));
		
		// Sort vendors by category
    add_action('wp_ajax_vendor_list_by_category', array($this, 'vendor_list_by_category'));
    add_action('wp_ajax_nopriv_vendor_list_by_category', array($this, 'vendor_list_by_category'));
        
  }
  
  
  
  function vendor_list_by_category() {
  	global $WCMp;
  	$html = '';
  	
  	$category_terms = get_terms('product_cat');
  	
  	$html = '&nbsp&nbsp&nbsp<select class="select" id="vendor_sort_category" name="vendor_sort_category">';
  	
  	foreach( $category_terms as $terms ) {
  		$html .= '<option value="' . $terms->term_id . '">' . $terms->name . '</option>';
  	}
  	
  	$html .= '</select>';
  	
  	echo $html; 
  	
  	die;
  }
  
  public function wcmp_dismiss_dashboard_message() {
  	global $wpdb, $WCMp;
  	$post_id = $_POST['post_id'];
  	$current_user = wp_get_current_user();
  	$current_user_id = $current_user->ID;
  	$data_msg_deleted = get_user_meta($current_user_id, '_wcmp_vendor_message_deleted', true);
		if(!empty($data_msg_deleted)) {
			$data_arr = explode(',', $data_msg_deleted);
			$data_arr[] = $post_id;
			$data_str = implode(',', $data_arr);  			
		}
		else {
			$data_arr[] = $post_id;
			$data_str = implode(',', $data_arr);
		}
		$is_updated = update_user_meta($current_user_id, '_wcmp_vendor_message_deleted', $data_str);
		if($is_updated) {
			$dismiss_notices_ids_array = array();
			$dismiss_notices_ids = get_user_meta($current_user_id,'_wcmp_vendor_message_deleted', true);
			if(!empty($dismiss_notices_ids)) {
				$dismiss_notices_ids_array = explode(',',$dismiss_notices_ids);
			}else {
				$dismiss_notices_ids_array = array();
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
				<h2><?php echo __('Admin Message:',$WCMp->text_domain); ?> </h2>
				<span> <?php echo $msg->post_title; ?> </span><br/>
				<span class="mormaltext" style="font-weight:normal;"> <?php echo $short_content = substr(stripslashes(strip_tags($msg->post_content)),0,155); if(strlen(stripslashes(strip_tags($msg->post_content))) > 155) {echo '...'; } ?> </span><br/>
				<a href="<?php echo get_permalink(get_option('wcmp_product_vendor_messages_page_id')); ?>"><button><?php echo __('DETAILS',$WCMp->text_domain);?></button></a>
				<div class="clear"></div>
				<a href="#" id="cross-admin" data-element = "<?php echo $msg->ID; ?>"  class="wcmp_cross wcmp_delate_message_dashboard"><i class="fa fa-times-circle"></i></a>
				<?php				
			}
			else {
				?>
				<h2><?php echo __('No Messages Found:',$WCMp->text_domain); ?> </h2>
				<?php
			}			
		}
		else {
			?>
			<h2><?php echo __('Error in process:',$WCMp->text_domain); ?> </h2>
			<?php			
		}
		die;
  }
  
  
  public function wcmp_msg_refresh_tab_data() {
  	global $wpdb, $WCMp;
  	$tab = $_POST['tabname'];
  	$WCMp->template->get_template( 'shortcode/vendor_announcements'.$tab.'.php');
  	die;
  }
  
  
  public function wcmp_vendor_messages_operation() {
  	global $wpdb, $WCMp;
  	$current_user = wp_get_current_user();
  	$current_user_id = $current_user->ID;
  	$post_id = $_POST['msg_id'];
  	$actionmode = $_POST['actionmode'];
  	if($actionmode == "mark_delete") {
  		$data_msg_deleted = get_user_meta($current_user_id, '_wcmp_vendor_message_deleted', true);
  		if(!empty($data_msg_deleted)) {
  			$data_arr = explode(',', $data_msg_deleted);
  			$data_arr[] = $post_id;
  			$data_str = implode(',', $data_arr);  			
  		}
  		else {
  			$data_arr[] = $post_id;
  			$data_str = implode(',', $data_arr);
  		}
  		if(update_user_meta($current_user_id, '_wcmp_vendor_message_deleted', $data_str)) {
  			echo 1;
  		}
  		else {
  			echo 0;
  		}
  	}
  	elseif($actionmode == "mark_read") {
  		$data_msg_readed = get_user_meta($current_user_id, '_wcmp_vendor_message_readed', true);
  		if(!empty($data_msg_readed)) {
  			$data_arr = explode(',', $data_msg_readed);
  			$data_arr[] = $post_id;
  			$data_str = implode(',', $data_arr);  			
  		}
  		else {
  			$data_arr[] = $post_id;
  			$data_str = implode(',', $data_arr);
  		}
  		if(update_user_meta($current_user_id, '_wcmp_vendor_message_readed', $data_str)) {
  			echo __('Mark Unread',$WCMp->text_domain);
  		}
  		else {
  			echo 0;
  		} 		
  	}
  	elseif($actionmode == "mark_unread") {
  		$data_msg_readed = get_user_meta($current_user_id, '_wcmp_vendor_message_readed', true);
  		if(!empty($data_msg_readed)) {
  			$data_arr = explode(',', $data_msg_readed);
  			if( is_array($data_arr) ) {
  				if(($key = array_search($post_id, $data_arr)) !== false) {
						unset($data_arr[$key]);
					}  				
  			}  			
  			$data_str = implode(',', $data_arr);  			
  		}  		
  		if(update_user_meta($current_user_id, '_wcmp_vendor_message_readed', $data_str)) {
  			echo __('Mark Read',$WCMp->text_domain);
  		}
  		else {
  			echo 0;
  		} 		
  	}
  	elseif($actionmode == "mark_restore") {
  		$data_msg_deleted = get_user_meta($current_user_id, '_wcmp_vendor_message_deleted', true);
  		if(!empty($data_msg_deleted)) {
  			$data_arr = explode(',', $data_msg_deleted);
  			if( is_array($data_arr) ) {
  				if(($key = array_search($post_id, $data_arr)) !== false) {
						unset($data_arr[$key]);
					}  				
  			}  			
  			$data_str = implode(',', $data_arr);  			
  		}  		
  		if(update_user_meta($current_user_id, '_wcmp_vendor_message_deleted', $data_str)) {
  			echo __('Mark Restore',$WCMp->text_domain);
  		}
  		else {
  			echo 0;
  		} 		
  	}
  	die;
  }
  
  public function wcmp_frontend_sale_get_row_callback () {
		global $wpdb, $WCMp;
		$user = wp_get_current_user();
		$vendor = get_wcmp_vendor($user->ID);
		$today_or_weekly = $_POST['today_or_weekly'];
		$current_page = $_POST['current_page'];
		$next_page = $_POST['next_page'];
		$total_page = $_POST['total_page'];
		$perpagedata = $_POST['perpagedata'];		
		if($next_page <= $total_page ) {
			if($next_page > 1) {
				$start = ($next_page - 1) * $perpagedata;			
				$WCMp->template->get_template( 'shortcode/vendor_dashboard_sales_item.php', array('vendor' => $vendor, 'today_or_weekly' => $today_or_weekly, 'start'=> $start, 'to'=> $perpagedata));
			}			
		}
		else {
			echo "<tr><td colspan='5'>".__('no more data found',$WCMp->text_domain)."</td></tr>";			
		}		
		die;		
	}
	
	public function wcmp_frontend_pending_shipping_get_row_callback () {
		global $wpdb, $WCMp;
		$user = wp_get_current_user();
		$vendor = get_wcmp_vendor($user->ID);
		$today_or_weekly = $_POST['today_or_weekly'];
		$current_page = $_POST['current_page'];
		$next_page = $_POST['next_page'];
		$total_page = $_POST['total_page'];
		$perpagedata = $_POST['perpagedata'];		
		if($next_page <= $total_page ) {
			if($next_page > 1) {
				$start = ($next_page - 1) * $perpagedata;			
				$WCMp->template->get_template( 'shortcode/vendor_dasboard_pending_shipping_items.php', array('vendor' => $vendor, 'today_or_weekly' => $today_or_weekly, 'start'=> $start, 'to'=> $perpagedata));
			}			
		}
		else {
			echo "<tr><td colspan='5'>".__('no more data found',$WCMp->text_domain)."</td></tr>";			
		}		
		die;		
	}
	
  
  function show_more_transaction() {
  	global $WCMp;
  	$data_to_show = $_POST['data_to_show'];
  	$WCMp->template->get_template('shortcode/vendor_transaction_items.php', array('transactions' =>  $data_to_show ));
		die;
  }
  
  function withdrawal_more_orders() {
  	global $WCMp;
  	$user = wp_get_current_user();
  	$vendor = get_wcmp_vendor($user->ID);
  	$offset = $_POST['offset'];
  	$meta_query['meta_query'] = array(
			array(
				'key' => '_paid_status',
				'value' => 'unpaid',
				'compare' => '='
			),
			array(
				'key' => '_commission_vendor',
				'value' => absint($vendor->term_id),
				'compare' => '='
			)
		);
		$customer_orders = $vendor->get_orders(6, $offset, $meta_query);
		$WCMp->template->get_template( 'shortcode/vendor_withdrawal_items.php', array('vendor' => $vendor, 'commissions' => $customer_orders));
		die;
  }
  
  function wcmp_vendor_csv_download_per_order() {
  	global $WCMp, $wpdb;
		
		if ( isset( $_GET['action'] ) && isset( $_GET['order_id'] ) && isset( $_GET['nonce'] ) ) {
			$action   = $_GET['action'];
			$order_id = $_GET['order_id'];
			$nonce    = $_REQUEST["nonce"];

			if ( ! wp_verify_nonce( $nonce, $action ) )
			die( 'Invalid request' );
			
			$vendor = get_wcmp_vendor(get_current_user_id());
			if(!$vendor) die( 'Invalid request' );
			$order_data = array();
			$customer_orders = $wpdb->get_results( "SELECT DISTINCT commission_id from `{$wpdb->prefix}wcmp_vendor_orders` where vendor_id = ".$vendor->id." AND order_id = ".$order_id, ARRAY_A);
			if(!empty($customer_orders)) {
				$commission_id = $customer_orders[0]['commission_id'];
				$order_data[$commission_id] = $order_id ;
				$WCMp->vendor_dashboard->generate_csv($order_data, $vendor);
			}
			die;
		}
  }
  
  /**
	 * Unassign vendor from a product
	 */
  function unassign_vendor() {
  	global $WCMp;
  	
  	$product_id = $_POST['product_id'];
  	$vendor = get_wcmp_product_vendors($product_id); 
  	$admin_id = get_current_user_id();
  	
  	$_product = get_product($product_id);
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
		
		foreach( $orders as $order_id ) {
			$order = new WC_Order($order_id);
			$items = $order->get_items( 'line_item' );
			foreach( $items as $item_id => $item ) {
				woocommerce_add_order_item_meta($item_id, '_vendor_id', $vendor->id);
			}
		}
		
  	wp_delete_object_term_relationships( $product_id, 'dc_vendor_shop' );
  	wp_delete_object_term_relationships( $product_id, 'product_shipping_class' );
  	wp_update_post( array('ID' => $product_id, 'post_author'  => $admin_id) );
  	delete_post_meta( $product_id, '_commission_per_product' );
  	delete_post_meta( $product_id, '_commission_percentage_per_product' );
  	delete_post_meta( $product_id, '_commission_fixed_with_percentage_qty' );
  	delete_post_meta( $product_id, '_commission_fixed_with_percentage' );
  	
  	$product_obj = wc_get_product($product_id);
  	if( $product_obj->is_type('variable') ) {
  		$child_ids = $product_obj->get_children();
  		if( isset($child_ids) && !empty($child_ids) ) {
				foreach( $child_ids as $child_id ) {
					delete_post_meta( $child_id, '_commission_fixed_with_percentage' );
					delete_post_meta( $child_id, '_product_vendors_commission_percentage' );
					delete_post_meta( $child_id, '_product_vendors_commission_fixed_per_trans' );
					delete_post_meta( $child_id, '_product_vendors_commission_fixed_per_qty' );
				}
			}
  	}
  	
  	die;
  }
  
  /**
	 * WCMp Product Report sorting
	 */
  function product_report_sort() {
  	global $WCMp;
  	
  	$sort_choosen = isset($_POST['sort_choosen']) ? $_POST['sort_choosen'] : '';
  	$report_array = isset($_POST['report_array']) ? $_POST['report_array'] : array();
  	$report_bk = isset($_POST['report_bk']) ? $_POST['report_bk'] : array();
  	$max_total_sales = isset($_POST['max_total_sales']) ? $_POST['max_total_sales'] : 0;
  	$total_sales_sort = isset($_POST['total_sales_sort']) ? $_POST['total_sales_sort'] : array();
  	$admin_earning_sort = isset($_POST['admin_earning_sort']) ? $_POST['admin_earning_sort'] : array();;
  	
  	$i = 0;
  	$max_value = 10;
  	$report_sort_arr = array();
  	
  	if( $sort_choosen == 'total_sales_desc' ) {
			arsort($total_sales_sort);
			foreach( $total_sales_sort as $product_id => $value ) {
				if( $i++ < $max_value ) {
					$report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
					$report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
				}
			}
		} else if( $sort_choosen == 'total_sales_asc' ) {
			asort($total_sales_sort);
			foreach( $total_sales_sort as $product_id => $value ) {
				if( $i++ < $max_value ) {
					$report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
					$report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
				}
			}
		} else if( $sort_choosen == 'admin_earning_desc' ) {
			arsort($admin_earning_sort);
			foreach( $admin_earning_sort as $product_id => $value ) {
				if( $i++ < $max_value ) {
					$report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
					$report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
				}
			}
		} else if( $sort_choosen == 'admin_earning_asc' ) {
			asort($admin_earning_sort);
			foreach( $admin_earning_sort as $product_id => $value ) {
				if( $i++ < $max_value ) {
					$report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
					$report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
				}
			}
		}
		
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
  	
  	echo $report_html;
  	
  	die;
  }
  
  function send_enquiry_to_vendor($send_to, $product_id) {
		global $WCMp;	 
		$vendor = get_wcmp_product_vendors($product_id);
		if( $vendor ) {
			$send_to = $vendor->user_data->data->user_email;
		}	 
		return $send_to;
  }
  
  /**
	 * WCMp Product Data Searching
	 */
  function search_product_data() {
  	global $WCMp;
  	
  	$product_id = $_POST['product_id'];
  	$start_date = $_POST['start_date'];
  	$end_date = $_POST['end_date'];
  	
  	$report_chart = $report_html = '';
  	
  	if($product_id) {
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
				$variatin_parent = wp_get_post_parent_id($product_id);
				$vendor = get_wcmp_product_vendors($variatin_parent);
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
							if ( $line_item[ 'product_id' ] == $product_id || $line_item[ 'variation_id' ] == $product_id ) {
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
			
			$total_sales = $admin_earnings = array();
			$max_total_sales = 0;
			if( isset($order_items) && !empty($order_items) ) {
				foreach( $order_items as $order_item ) {
					if ( $order_item['line_total'] == 0 && $order_item['item_quantity'] == 0 )
						continue;
	
					// Get date
					$date 	= date( 'Ym', strtotime( $order_item['post_date'] ) );
					
					if( $order_item['variation_id'] != 0 ) {
						$variation_id = $order_item['variation_id'];
						$product_id_1 = $order_item['variation_id'];
					} else {
						$variation_id = 0;
						$product_id_1 = $order_item['product_id'];
					}
					
					if(!$vendor) {
						break;
					}
					
					$vendor_earnings = 0;
					if( $order_item['multiple_product'] == 0 ) {
						$commissions = false;
						
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
							foreach($commissions as $commission) {
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
								if( $vendor_item['variation_id'] == $variation_id ) {
									$item = $vendor_item;
									break;
								}
							}
						}
						if( !$is_variation ) {
							$commission_obj = new WCMp_Calculate_Commission();
							$vendor_earnings = $commission_obj->get_item_commission( $product_id, $variation_id, $item, $order_item['order_id'] );
						} else {
							$commission_obj = new WCMp_Calculate_Commission();
							$vendor_earnings = $commission_obj->get_item_commission( $variatin_parent, $variation_id, $item, $order_item['order_id'] );
						}
					}
					
					$total_sales[$date] = isset($total_sales[$date]) ? ( $total_sales[$date] + $order_item['line_total'] ) : $order_item['line_total'];
					$admin_earnings[$date] = isset($admin_earnings[$date]) ? ( $admin_earnings[$date] + $order_item['line_total'] - $vendor_earnings ) : $order_item['line_total'] - $vendor_earnings;
					
					if ( $total_sales[ $date ] > $max_total_sales )
						$max_total_sales = $total_sales[ $date ];
				}
			}
			
			if ( sizeof( $total_sales ) > 0 ) {
				foreach ( $total_sales as $date => $sales ) {
					$width = ( $sales > 0 ) ? ( round( $sales ) / round( $max_total_sales ) ) * 100 : 0;
					$width2 = ( $admin_earnings[$date] > 0 ) ? ( round( $admin_earnings[$date] ) / round( $max_total_sales ) ) * 100 : 0;
	
					$report_chart .= '<tr><th>' . date_i18n( 'F', strtotime( $date . '01' ) ) . '</th>
						<td width="1%"><span>' . woocommerce_price( $sales ) . '</span><span class="alt">' . woocommerce_price( $admin_earnings[ $date ] ) . '</span></td>
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
							'.$report_chart.'
						</tbody>
					</table>
				';
			} else {
				$report_html = '<tr><td colspan="3">' . __( 'This product was not sold in the given period.', $WCMp->text_domain ) . '</td></tr>';
			}
			
			echo $report_html;
		} else {
			echo '<tr><td colspan="3">' . __( 'Please select a product.', $WCMp->text_domain ) . '</td></tr>';
		}
  	
  	die;
  }
  
  /**
	 * WCMp Vendor Data Searching
	 */
  function search_vendor_data() {
  	global $WCMp, $wpdb;
  	
  	$chosen_product_ids = $vendor_id = $vendor = false;
  	
  	$vendor_id = $_POST['vendor_id'];
  	$start_date = $_POST['start_date'];
  	$end_date = $_POST['end_date'];
  	
		if( $vendor_id ) {
			$vendor = get_wcmp_vendor_by_term( $vendor_id );
			if($vendor) $products = $vendor->get_products();
			if(!empty($products)) {
				foreach( $products as $product ) {
					$chosen_product_ids[] = $product->ID;
				}
			}
		}
  	
  	if( $vendor_id && empty($products) ) {
  		$no_vendor = '<h4>' . __( "Sales and Earnings", $WCMp->text_domain ) . '</h4>
			<table class="bar_chart">
				<thead>
					<tr>
						<th>' . __( "Month", $WCMp->text_domain ) . '</th>
						<th colspan="2">' . __( "Sales", $WCMp->text_domain ) . '</th>
					</tr>
				</thead>
				<tbody> 
					<tr><td colspan="3">' . __( "No Sales :(", $WCMp->text_domain ) . '</td></tr>
				</tbody>
			</table>';
			
			echo $no_vendor;
			die;
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
			
			$total_sales = $admin_earning = array();
			$max_total_sales = 0;
			if ( $order_items ) {
				foreach ( $order_items as $order_item ) {
	
					if ( $order_item->line_total == 0 && $order_item->item_quantity == 0 )
						continue;
	
					// Get date
					$date 	= date( 'Ym', strtotime( $order_item->post_date ) );
					
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
					
					if( !empty($commissions) ) {
						foreach($commissions as $commission) {
							$vendor_earnings = $vendor_earnings + get_post_meta($commission->ID, '_commission_amount', true);
						}
					}
					
					if( $vendor_earnings <= 0 ) {
						continue;
					}
					
					// Set values
					$total_sales[$date] = isset( $total_sales[$date] ) ? ( $total_sales[$date] + $order_item->line_total ) : $order_item->line_total;
					$admin_earning[$date] = isset( $admin_earning[$date] ) ? ( $admin_earning[$date] + $order_item->line_total - $vendor_earnings ) : $order_item->line_total - $vendor_earnings;
					
					if ( $total_sales[$date] > $max_total_sales )
						$max_total_sales = $total_sales[$date];
				}
			}
			
			$report_chart = $report_html = '';
			if ( count( $total_sales ) > 0 ) {
				foreach ( $total_sales as $date => $sales ) {
					$width = ( $sales > 0 ) ? ( round( $sales ) / round( $max_total_sales ) ) * 100 : 0;
					$width2 = ( $admin_earning[$date] > 0 ) ? ( round( $admin_earning[$date] ) / round( $max_total_sales ) ) * 100 : 0;

					$orders_link = admin_url( 'edit.php?s&post_status=all&post_type=shop_order&action=-1&s=' . urlencode( implode( ' ', $chosen_product_titles ) ) . '&m=' . date( 'Ym', strtotime( $date . '01' ) ) . '&shop_order_status=' . implode( ",", apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) );
					$orders_link = apply_filters( 'woocommerce_reports_order_link', $orders_link, $chosen_product_ids, $chosen_product_titles );

					$report_chart .= '<tr><th><a href="' . esc_url( $orders_link ) . '">' . date_i18n( 'F', strtotime( $date . '01' ) ) . '</a></th>
						<td width="1%"><span>' . woocommerce_price( $sales ) . '</span><span class="alt">' . woocommerce_price( $admin_earning[ $date ] ) . '</span></td>
						<td class="bars">
							<span class="main" style="width:' . esc_attr( $width ) . '%">&nbsp;</span>
							<span class="alt" style="width:' . esc_attr( $width2 ) . '%">&nbsp;</span>
						</td></tr>';
				}
			
				$report_html = '
					<h4>'. $vendor_title .'</h4>
					<div class="bar_indecator">
						<div class="bar1">&nbsp;</div>
						<span class="">' . __( "Gross Sales", $WCMp->text_domain ) . '</span>
						<div class="bar2">&nbsp;</div>
						<span class="">' . __( "My Earnings", $WCMp->text_domain ) . '</span>
					</div>
					<table class="bar_chart">
						<thead>
							<tr>
								<th>'. __( "Month", $WCMp->text_domain ) .'</th>
								<th colspan="2">'. __( "Vendor Earnings", $WCMp->text_domain ) .'</th>
							</tr>
						</thead>
						<tbody>
							' . $report_chart . '
						</tbody>
					</table>
				';
			} else {
				$report_html = '<tr><td colspan="3">' . __( 'This vendor did not generate any sales in the given period.', $WCMp->text_domain ) . '</td></tr>';
			}
		}
		
		echo $report_html;
		
		die;
  }
  
  /**
	 * WCMp Vendor Report sorting
	 */
  function vendor_report_sort() {
  	global $WCMp;
  	
  	$dropdown_selected = isset($_POST['sort_choosen']) ? $_POST['sort_choosen'] : '';
  	$vendor_report = isset($_POST['report_array']) ? $_POST['report_array'] : array();
  	$report_bk = isset($_POST['report_bk']) ? $_POST['report_bk'] : array();
  	$max_total_sales = isset($_POST['max_total_sales']) ? $_POST['max_total_sales'] : 0;
  	$total_sales_sort = isset($_POST['total_sales_sort']) ? $_POST['total_sales_sort'] : array();
  	$admin_earning_sort = isset($_POST['admin_earning_sort']) ? $_POST['admin_earning_sort'] : array();
  	$report_sort_arr = array();
  	$chart_arr = '';
  	$i = 0;
  	$max_value = 10;
  	
		if( $dropdown_selected == 'total_sales_desc' ) {
			arsort($total_sales_sort);
			foreach( $total_sales_sort as $key => $value ) {
				if( $i++ < $max_value ) {
					$report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
					$report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
				}
			}
		} else if( $dropdown_selected == 'total_sales_asc' ) {
			asort($total_sales_sort);
			foreach( $total_sales_sort as $key => $value ) {
				if( $i++ < $max_value ) {
					$report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
					$report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
				}
			}
		} else if( $dropdown_selected == 'admin_earning_desc' ) {
			arsort($admin_earning_sort);
			foreach( $admin_earning_sort as $key => $value ) {
				if( $i++ < $max_value ) {
					$report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
					$report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
				}
			}
		} else if( $dropdown_selected == 'admin_earning_asc' ) {
			asort($admin_earning_sort);
			foreach( $admin_earning_sort as $key => $value ) {
				if( $i++ < $max_value ) {
					$report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
					$report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
				}
			}
		}
		
		if ( sizeof( $report_sort_arr ) > 0 ) {
			foreach ( $report_sort_arr as $vendor_id => $sales_report ) {
				$total_sales_width = ( $sales_report['total_sales'] > 0 ) ? $sales_report['total_sales'] / round($max_total_sales) * 100 : 0;
				$admin_earning_width = ( $sales_report['admin_earning'] > 0 ) ? ( $sales_report['admin_earning'] / round($max_total_sales) ) * 100 : 0;
				
				$user = get_userdata($vendor_id);
				$user_name = $user->data->display_name;
				
				$chart_arr .= '<tr><th><a href="user-edit.php?user_id='.$vendor_id.'">' . $user_name . '</a></th>
				<td width="1%"><span>' . woocommerce_price( $sales_report['total_sales'] ) . '</span><span class="alt">' . woocommerce_price($sales_report['admin_earning']) . '</span></td>
				<td class="bars">
					<span class="main" style="width:' . esc_attr( $total_sales_width ) . '%">&nbsp;</span>
					<span class="alt" style="width:' . esc_attr( $admin_earning_width ) . '%">&nbsp;</span>
				</td></tr>';
			}
			
			$html_chart = '
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
		
		echo $html_chart;
  	
  	die;
  }
  
  /**
	 * WCMp Order mark as shipped
	 */
  function order_mark_as_shipped() {
  	global $WCMp, $wpdb;
  	$order_id = $_POST['order_id'];
  	$user_id = get_current_user_id();
		$vendor = get_wcmp_vendor($user_id);
  	$shippers = (array) get_post_meta( $order_id, 'dc_pv_shipped', true );  	
		if(!in_array($user_id, $shippers)) {
			$shippers[] = $user_id;
			$mails = WC()->mailer()->emails['WC_Email_Notify_Shipped'];
			if ( !empty( $mails ) ) {
				$customer_email = get_post_meta($order_id, '_billing_email', true);
				$mails->trigger( $order_id, $customer_email, $vendor->term_id );
			}
			do_action('wcmp_vendors_vendor_ship', $order_id, $vendor->term_id);
			array_push($shippers, $user_id);
			update_post_meta( $order_id, 'dc_pv_shipped', $shippers );
		}
		$wpdb->query( "UPDATE {$wpdb->prefix}wcmp_vendor_orders SET shipping_status = '1' WHERE order_id = $order_id and vendor_id = $user_id" );		
		$order = new WC_Order( $order_id );
		$order->add_order_note('Vendor '.$vendor->user_data->display_name .' has shipped his part of order to customer.');
		die;
  }
  
  /**
	 * WCMp Transaction complete mark
	 */
  function transaction_done_button() {
  	global $WCMp;
  	$transaction_id = $_POST['trans_id'];
  	$vendor_id = $_POST['vendor_id'];
  	update_post_meta($transaction_id, 'paid_date', date("Y-m-d H:i:s"));
  	$commission_detail = get_post_meta($transaction_id, 'commission_detail', true);
  	foreach($commission_detail as $commission_id => $order_id) {
			wcmp_paid_commission_status($commission_id);
		}
		$email_admin = WC()->mailer()->emails['WC_Email_Vendor_Commission_Transactions'];
		$email_admin->trigger( $transaction_id, $vendor_id );
		update_post_meta($transaction_id, '_dismiss_to_do_list', 'true');
		die;
  }

  /**
	 * WCMp get more orders
	 */
  function get_more_orders() {
  	global $WCMp;
  	$data_to_show = isset($_POST['data_to_show']) ? $_POST['data_to_show'] : '';
  	$order_status = isset($_POST['order_status']) ? $_POST['order_status'] : '';
  	$vendor = get_wcmp_vendor(get_current_user_id());
		$WCMp->template->get_template( 'shortcode/vendor_orders_item.php', array('vendor' => $vendor, 'orders' => $data_to_show, 'order_status' => $order_status));
		die;
  }
  
  /**
	 * WCMp dismiss todo list
	 */
  function dismiss_vendor_to_do_list() {
  	global $WCMp;
  	
  	$id = $_POST['id'];
  	$type = $_POST['type'];
  	if($type == 'user') {
  		update_user_meta($id, '_dismiss_to_do_list', 'true');
  	} else if($type == 'shop_coupon') {
  		update_post_meta($id, '_dismiss_to_do_list', 'true');
  	} else if($type == 'product') {
  		update_post_meta($id, '_dismiss_to_do_list', 'true');
  	} else if($type == 'dc_commission') {
  		update_post_meta($id, '_dismiss_to_do_list', 'true');
  	}
  	die();
  }
  
  /**
	 * WCMp current user attachment
	 */
	function show_current_user_attachments( $query = array() ) {
		$user_id = get_current_user_id();
		if(is_user_wcmp_vendor($user_id)) {
			$query['author'] = $user_id;
		}
		return $query;
	}

	/**
	 * Search vendors via AJAX
	 *
	 * @return void
	 */
	function woocommerce_json_search_vendors() {
		global $WCMp;
	
		//check_ajax_referer( 'search-vendors', 'security' );
	
		header( 'Content-Type: application/json; charset=utf-8' );
	
		$term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
		
		if ( empty( $term ) )
			die();
	
		$found_vendors = array();
	
		$args = array(
			'search' => '*'.$term.'*',
			'search_columns' => array( 'user_login', 'display_name', 'user_email' )
		);
		
		$vendors = get_wcmp_vendors( $args );
	
		if ( $vendors ) {
			foreach ( $vendors as $vendor ) {
				$found_vendors[ $vendor->term_id ] = $vendor->user_data->display_name;
			}
		}
	
		echo json_encode( $found_vendors );
		die();
	}
	
	/**
	 * Activate Pending Vendor via AJAX
	 *
	 * @return void
	 */
	function activate_pending_vendor() {
		global $WCMp;
		$user_id = $_POST['user_id'];
		$user = new WP_User( absint( $user_id ) );
		$user->remove_role( 'dc_pending_vendor' );
		$user->remove_role( 'dc_rejected_vendor' );
		$WCMp->user->update_vendor_meta($user_id);
		$user->add_role( 'dc_vendor' );
		$WCMp->user->add_vendor_caps( $user_id );
  	$vendor = get_wcmp_vendor( $user_id );
		$vendor->generate_term();
		$user_dtl = get_userdata( absint( $user_id ) );
		$email = WC()->mailer()->emails['WC_Email_Approved_New_Vendor_Account'];
		$email->trigger( $user_id, $user_dtl->user_pass );
		$shipping_class_id = get_user_meta($user_id,'shipping_class_id',true);
		if( empty($shipping_class_id) ) {
			$shipping_term = wp_insert_term( $user->user_login.'-'.$user_id, 'product_shipping_class' );
			update_user_meta($user_id, 'shipping_class_id', $shipping_term['term_id']);
		}
		die();
	}
	
	/**
	 * Reject Pending Vendor via AJAX
	 *
	 * @return void
	 */
	function reject_pending_vendor() {
		global $WCMp;
		$user_id = $_POST['user_id'];
		$user = new WP_User( absint( $user_id ) );
		if(is_array( $user->roles ) && in_array( 'dc_pending_vendor', $user->roles )) {
			$user->remove_role( 'dc_pending_vendor' );
		}
		$user->add_role( 'dc_rejected_vendor' );
		$user_dtl = get_userdata( absint( $user_id ) );
		$email = WC()->mailer()->emails['WC_Email_Rejected_New_Vendor_Account'];
		$email->trigger( $user_id, $user_dtl->user_pass );		
		
		if(is_array( $user->roles ) && in_array( 'dc_vendor', $user->roles )) {
			$vendor = get_wcmp_vendor($user_id);
			if($vendor) wp_delete_term( $vendor->term_id, 'dc_vendor_shop' );
			$caps = $this->get_vendor_caps( $user_id );
			foreach( $caps as $cap ) {
				$user->remove_cap( $cap );
			}
			$user->remove_cap('manage_woocommerce');
		}
		//wp_delete_user($user_id);
		die();
	}
	
	/**
	 * Report Abuse Vendor via AJAX
	 *
	 * @return void
	 */
	function send_report_abuse()  {
		global $WCMp;
		$check = false;
		$name           = sanitize_text_field( $_POST['name'] );
		$from_email     = sanitize_email( $_POST['email'] );
		$user_message   = sanitize_text_field( $_POST['msg'] );
		$product_id     = sanitize_text_field( $_POST['product_id'] );

		$check = ! empty( $name ) && ! empty( $from_email ) && ! empty( $user_message );

		if( $check ) {
			$product = get_post( absint($product_id) );
			$vendor = get_wcmp_product_vendors( $product_id );
 
			$subject    =  __('Report an abuse for product', $WCMp->text_domain ).get_the_title($product_id);
			
			$to         = sanitize_email( get_option( 'admin_email' ) );
			$from_email = sanitize_email( $from_email );
			$headers = "From: {$name} <{$from_email}>" . "\r\n";

			$message = sprintf( __( "User %s (%s) is reporting an abuse on the following product: \n", $WCMp->text_domain ), $name, $from_email );
			$message .= sprintf( __( "Product details: %s (ID: #%s) \n", $WCMp->text_domain ), $product->post_title, $product->ID );

			$message .= sprintf( __( "Vendor shop: %s \n", $WCMp->text_domain ), $vendor->user_data->display_name  );

			$message .= sprintf( __( "Message: %s\n", $WCMp->text_domain ), $user_message  );
			$message .= "\n\n\n";

			$message .= sprintf( __( "Product page:: %s\n", $WCMp->text_domain ), get_the_permalink( $product->ID ) );

			/* === Send Mail === */
			$response = wp_mail( $to, $subject, $message, $headers );
		}
		die();
	}
}