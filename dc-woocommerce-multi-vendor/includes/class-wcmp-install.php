<?php

/**
 * Demo plugin Install
 *
 * Plugin install script which adds default pages, taxonomies, and database tables to WordPress. Runs on activation and upgrade.
 *
 * @author 		Dualcube
 * @package 	wcmp/Admin/Install
 * @version    0.0.1
 */
class WCMp_Install {
  public  $arr = array();
  public function __construct() {
  	global $WCMp;
  	if(get_option( "dc_product_vendor_plugin_page_install") == 1) {
  		$wcmp_pages = get_option('wcmp_pages_settings_name');
  		if(isset($wcmp_pages['vendor_dashboard'])) {
  			wp_update_post(array('ID' => $wcmp_pages['vendor_dashboard'], 'post_content' => '[vendor_dashboard]'));
  		}
			if(isset($wcmp_pages['vendor_messages'])) {
  			wp_update_post(array('ID' => $wcmp_pages['vendor_messages'], 'post_content' => '[vendor_announcements]', 'post_name'=> 'vendor_announcements', 'post_title' => 'Vendor Announcements' ));
  			$page_id = $wcmp_pages['vendor_messages'];
  			unset($wcmp_pages['vendor_messages']);
  			$wcmp_pages['vendor_announcements'] = $page_id;
  			update_option('wcmp_pages_settings_name', $wcmp_pages);
  		}  		
  	}	
    if(!get_option( "dc_product_vendor_plugin_page_install")) $this->wcmp_product_vendor_plugin_create_pages();    
    update_option( "dc_product_vendor_plugin_db_version", $WCMp->version );    
    update_option( "dc_product_vendor_plugin_page_install", 1 );    
    $this->save_default_plugin_settings();    
    $this->wcmp_plugin_tables_install();
		$this->remove_other_vendors_plugin_role();		
  }
  
  /**
  * Remove other vendor role created by other plugin
  *
  * @access public
  * @return void
  */
  
  function remove_other_vendors_plugin_role() {
  	$this->arr[] = 'seller';
		$this->arr[] = 'yith_vendor';
		$this->arr[] = 'pending_vendor';
		$this->arr[] = 'vendor';
		foreach($this->arr as $element ) {
			if(wcmp_role_exists($element) ) {
				remove_role( $element );				
			}
		}  	
  }
  
  /**
   * Create a page
   *
   * @access public
   * @param mixed $slug Slug for the new page
   * @param mixed $option Option name to store the page's ID
   * @param string $page_title (default: '') Title for the new page
   * @param string $page_content (default: '') Content for the new page
   * @param int $post_parent (default: 0) Parent for the new page
   * @return void
   */
  function wcmp_product_vendor_plugin_create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
    global $wpdb;
    $option_value = get_option( $option );
    if ( $option_value > 0 && get_post( $option_value ) )
      return;
    $page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$slug' LIMIT 1;");
    if ( $page_found ) :
      if ( ! $option_value )
        update_option( $option, $page_found );
      return;
    endif;
    $page_data = array(
          'post_status' 		=> 'publish',
          'post_type' 		=> 'page',
          'post_author' 		=> 1,
          'post_name' 		=> $slug,
          'post_title' 		=> $page_title,
          'post_content' 		=> $page_content,
          'post_parent' 		=> $post_parent,
          'comment_status' 	=> 'closed'
      );
      $page_id = wp_insert_post( $page_data );
      update_option( $option, $page_id );
  }
  
  /**
   * Create pages that the plugin relies on, storing page id's in variables.
   *
   * @access public
   * @return void
   */
  function wcmp_product_vendor_plugin_create_pages() {
  	global $WCMp;
  	
    // Dc_demo_plugins test page
    $this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_dashboard', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_vendor_dashboard_page_id', __('Vendor Dashboard', $WCMp->text_domain), '[vendor_dashboard]' );
    $this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_shop_settings', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_shop_settings_page_id', __('Shop Settings', $WCMp->text_domain), '[shop_settings]' );
    $this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_orders', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_vendor_orders_page_id', __('Vendor Orders', $WCMp->text_domain), '[vendor_orders]' );
    $this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_order_detail', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_vendor_order_detail_page_id', __('Vendor Order Details', $WCMp->text_domain), '[vendor_order_detail]' );
    $this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_withdrawal_request', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_transaction_widthdrawal_page_id', __('Withdrawal Request Status', $WCMp->text_domain), '[transaction_thankyou]' );
		$this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_transaction_details', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_transaction_details_page_id', __('Transaction Details', $WCMp->text_domain), '[transaction_details]' );
		$this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_policies', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_policies_page_id', __('Vendor Policies', $WCMp->text_domain), '[vendor_policies]' );
		$this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_billing', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_billing_page_id', __('Vendor Billing', $WCMp->text_domain), '[vendor_billing]' );
		$this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_shipping', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_shipping_page_id', __('Vendor Shipping', $WCMp->text_domain), '[vendor_shipping_settings]' );
		$this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_report', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_report_page_id', __('Vendor Reports', $WCMp->text_domain), '[vendor_report]' );
		$this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_widthdrawals', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_widthdrawals_page_id', __('Vendor Widthdrawals', $WCMp->text_domain), '[vendor_widthdrawals]' );
		$this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_university', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_university_page_id', __('Vendor University', $WCMp->text_domain), '[vendor_university]' );
		$this->wcmp_product_vendor_plugin_create_page( esc_sql( _x('wcmp_vendor_announcements', 'page_slug', $WCMp->text_domain) ), 'wcmp_product_vendor_announcements_page_id', __('Vendor Announcements', $WCMp->text_domain), '[vendor_announcements]' );
    $array_pages = array();
		$array_pages['vendor_dashboard'] = get_option('wcmp_product_vendor_vendor_dashboard_page_id');
		$array_pages['shop_settings'] = get_option('wcmp_product_vendor_shop_settings_page_id');
		$array_pages['view_order'] =  get_option('wcmp_product_vendor_vendor_orders_page_id');
		$array_pages['vendor_order_detail'] = get_option('wcmp_product_vendor_vendor_order_detail_page_id');
		$array_pages['vendor_transaction_thankyou'] = get_option('wcmp_product_vendor_transaction_widthdrawal_page_id');
		$array_pages['vendor_transaction_detail'] = get_option('wcmp_product_vendor_transaction_details_page_id');
		$array_pages['vendor_policies'] = get_option('wcmp_product_vendor_policies_page_id');
		$array_pages['vendor_billing'] = get_option('wcmp_product_vendor_billing_page_id');
		$array_pages['vendor_shipping'] = get_option('wcmp_product_vendor_shipping_page_id');
		$array_pages['vendor_report'] = get_option('wcmp_product_vendor_report_page_id');
		$array_pages['vendor_widthdrawals'] = get_option('wcmp_product_vendor_widthdrawals_page_id');
		$array_pages['vendor_university'] = get_option('wcmp_product_vendor_university_page_id');
		$array_pages['vendor_announcements'] = get_option('wcmp_product_vendor_announcements_page_id');	
		
		update_option('wcmp_pages_settings_name', $array_pages);
  }
  
  /**
   * save default product vendor plugin settings
   *
   * @access public
   * @return void
   */
  function save_default_plugin_settings() {
		$general_settings = get_option('wcmp_general_settings_name');
		if(empty($general_settings)) {
			$general_settings = array (
				'enable_registration' => 'Enable',
				'approve_vendor_manually' => 'Enable',
			);
			update_option('wcmp_general_settings_name', $general_settings);
		}
		$product_settings = get_option('wcmp_product_settings_name');
		if(empty($product_settings)) {
			$product_settings = array (
				'inventory' => 'Enable',
				'shipping' => 'Enable',
				'linked_products' => 'Enable',
				'attribute' => 'Enable',
				'advanced' => 'Enable',
				'simple' => 'Enable',
				'variable' => 'Enable',
				'grouped' => 'Enable',
				'virtual' => 'Enable',
				'external' => 'Enable',
				'downloadable' => 'Enable',
				'taxes' => 'Enable',
				'add_comment' => 'Enable',
				'comment_box' => 'Enable',
				'sku' => 'Enable',
			);
			update_option('wcmp_product_settings_name', $product_settings);
		}
		$capabilities_settings = get_option('wcmp_capabilities_settings_name');
		if(empty($capabilities_settings)) {
			$capabilities_settings = array (
				'is_upload_files' => 'Enable',
				'is_submit_product' => 'Enable',
				'is_order_csv_export' => 'Enable',
				'is_show_email' => 'Enable',
				'is_vendor_view_comment' => 'Enable',
				'show_cust_billing_add' => 'Enable',
				'show_cust_shipping_add' => 'Enable',
				'show_cust_order_calulations' => 'Enable',
				'show_customer_dtl' => 'Enable',
				'show_customer_billing' => 'Enable',
				'show_customer_shipping' => 'Enable',
				'show_cust_add' => 'Enable',
				'is_hide_option_show' => 'Enable',
			);
			update_option('wcmp_capabilities_settings_name', $capabilities_settings);
		}
		
		$payment_settings = get_option('wcmp_payment_settings_name');
		if(empty($payment_settings)) {
			$payment_settings = array (
				'commission_include_coupon' => 'Enable',
				'give_tax' => 'Enable',
				'give_shipping' => 'Enable',
				'commission_type' => 'percent',
			);
			update_option('wcmp_payment_settings_name', $payment_settings);
		}     
		$frontend_settings = get_option('wcmp_frontend_settings_name');
		if(empty($frontend_settings)) {
			$frontend_settings = array (
				'sold_by_cart_and_checkout' => 'Enable',
				'sold_by_catalog' =>  'Enable',
				'catalog_colorpicker' => '#000000',
				'catalog_hover_colorpicker' => '#000000',
			);
			update_option('wcmp_frontend_settings_name', $frontend_settings);
		}
		$general_singleproductmultisellersettings = get_option('wcmp_general_singleproductmultiseller_settings_name');
		if(empty($general_singleproductmultisellersettings)) {
			$general_singleproductmultisellersettings = array (
				'is_singleproductmultiseller' => 'Enable',				
			);
			update_option('wcmp_general_singleproductmultiseller_settings_name', $general_singleproductmultisellersettings);
		}
	}
	
	function wcmp_plugin_tables_install() {
    global $wpdb, $WCMp;
    
    if ( ! empty($wpdb->charset) )
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    if ( ! empty($wpdb->collate) )
        $charset_collate .= " COLLATE $wpdb->collate";
    $migs = array();
    
    // Create course_purchase table
    
    $migs[] = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."wcmp_vendor_orders` (
		`ID` bigint(20) NOT NULL AUTO_INCREMENT,
		`order_id` bigint(20) NOT NULL,
		`commission_id` bigint(20) NOT NULL,
		`vendor_id` bigint(20) NOT NULL,
		`shipping_status` varchar(255) NOT NULL,
		`order_item_id` bigint(20) NOT NULL,
		`product_id` bigint(20) NOT NULL,
		`commission_amount` varchar(255) NOT NULL,
		`shipping` varchar(255) NOT NULL,
		`tax` varchar(255) NOT NULL,
		`is_trashed` varchar(10) NOT NULL,				
		`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,				
		PRIMARY KEY (`ID`),
		CONSTRAINT vendor_orders UNIQUE (order_id, vendor_id, commission_id, product_id)
		)$charset_collate;";

	$migs[] = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."wcmp_products_map` (
		`ID` bigint(20) NOT NULL AUTO_INCREMENT,
		`product_title` varchar(255) NOT NULL,
		`product_ids`text NOT NULL,						
		`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,				
		PRIMARY KEY (`ID`)
		)$charset_collate;";
		
		$needed_migration = count($migs);
	
    for ($i = 0; $i < $needed_migration; $i++) {
      $mig = $migs[$i];
      $wpdb->query($mig);
    }
    
    return;
	}
}
?>
