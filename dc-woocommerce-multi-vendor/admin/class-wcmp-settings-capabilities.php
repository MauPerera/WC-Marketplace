<?php
class WCMp_Settings_Capabilities {
   /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $this->options = get_option( "wcmp_{$this->tab}_settings_name" );
    $this->settings_page_init();
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $WCMp;
    
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
                                  										"products_settings_section" => array("title" =>  __('Uploading Product Data ', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                                           "is_submit_product" => array('title' => __('Submit Products', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_submit_product', 'label_for' => 'is_submit_product', 'desc' => __('Allow vendors to submit products for approval/publishing.', $WCMp->text_domain), 'name' => 'is_submit_product', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_published_product" => array('title' => __('Publish Products', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_published_product', 'label_for' => 'is_published_product', 'name' => 'is_published_product', 'desc' => __('If checked, products uploaded by vendors will be directly published without admin approval.',  $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                                                                                                           )
                                                                                         ), 
                                                      "vendor_order_export" => array("title" =>  __('Order Export Data / Report Export Data', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                                           "is_order_csv_export" => array('title' => __('Allow vendors to export orders.', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_order_csv_export', 'label_for' => 'is_order_csv_export', 'name' => 'is_order_csv_export', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_order_show_email" => array('title' => __('Customer Name', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_show_email', 'label_for' => 'is_show_email', 'name' => 'is_show_email', 'value' => 'Enable'), // Checkbox
                                                                                                           "show_customer_dtl" => array('title' => __('E-mail and Phone Number', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'show_customer_dtl', 'label_for' => 'show_customer_dtl', 'name' => 'show_customer_dtl', 'value' => 'Enable'), // Checkbox
                                                                                                           "show_customer_billing" => array('title' => __('Billing Address', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'show_customer_billing', 'label_for' => 'show_customer_billing', 'name' => 'show_customer_billing', 'value' => 'Enable'), // Checkbox
                                                                                                           "show_customer_shipping" => array('title' => __('Shipping Address', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'show_customer_shipping', 'label_for' => 'show_customer_shipping', 'name' => 'show_customer_shipping', 'value' => 'Enable'), // Checkbox
                                                                                                           )
                                                                                         ), 
                                                      
                                                      "vendor_email_settings" => array("title" =>  __('Order Email Settings for Vendor', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                                           "show_cust_name" => array('title' => __('Name, Phone no. and Email', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'show_cust_add', 'label_for' => 'show_cust_add', 'name' => 'show_cust_add', 'value' => 'Enable'), // Checkbox
                                                                                                           "show_cust_billing_add" => array('title' => __('Billing Address', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'show_cust_billing_add', 'label_for' => 'show_cust_billing_add', 'name' => 'show_cust_billing_add', 'value' => 'Enable'), // Checkbox
                                                                                                           "show_cust_shipping_add" => array('title' => __('Shipping Address', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'show_cust_shipping_add', 'label_for' => 'show_cust_shipping_add', 'name' => 'show_cust_shipping_add', 'value' => 'Enable'), // Checkbox
                                                                                                           "show_cust_order_calulations" => array('title' => __('Order Calculations', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'show_cust_order_calulations', 'label_for' => 'show_cust_order_calulations', 'name' => 'show_cust_order_calulations', 'value' => 'Enable'), // Checkbox
                                                                                                           )
                                                                                         ),
                                                                            
                                                      
                                                      "vendor_miscellaneous" => array("title" =>  __('Miscellaneous', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                                           "is_upload_files" => array('title' => __('Upload Media Files', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_upload_files', 'label_for' => 'is_upload_files', 'name' => 'is_upload_files', 'desc' => __('Allow vendors to upload media files.',  $WCMp->text_domain),  'value' => 'Enable'), // Checkbox
                                                                                                           "is_submit_coupon" => array('title' => __('Submit Coupons', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_submit_coupon', 'label_for' => 'is_submit_coupon', 'name' => 'is_submit_coupon', 'desc' => __('Allow vendors to create coupons.', $WCMp->text_domain),  'value' => 'Enable'), // Checkbox
                                                                                                           "is_published_coupon" => array('title' => __('Publish Coupons', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_published_coupon', 'label_for' => 'is_published_coupon', 'name' => 'is_published_coupon', 'desc' => __('If checked, coupons added by vendors will be directly published without admin approval.',  $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                                                                                                           "is_vendor_view_comment" => array('title' => __('View Comment', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_vendor_view_comment', 'label_for' => 'is_vendor_view_comment', 'name' => 'is_vendor_view_comment',  'desc' => __('Vendor can see order notes.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                                                                                                           "is_vendor_submit_comment" => array('title' => __('Submit Comment', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_vendor_submit_comment', 'label_for' => 'is_vendor_submit_comment', 'name' => 'is_vendor_submit_comment', 'desc' => __('Vendor can add order notes.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                                                                                                           "is_vendor_add_external_url" => array('title' => __('Enable store url', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_vendor_add_external_url', 'label_for' => 'is_vendor_add_external_url', 'name' => 'is_vendor_add_external_url', 'desc' => __('Vendor can add external store url.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                                                                                                           "is_hide_option_show" => array('title' => __('Enable hide option for vendor', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_hide_option_show', 'label_for' => 'is_hide_option_show', 'name' => 'is_hide_option_show', 'desc' => __('Vendor can hide some details from shop.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                                                                                                           )
                                                                                         ),
                                                      "vendor_messages" => array("title" =>  __('Messages ', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                                           "can_vendor_add_message_on_email_and_thankyou_page" => array('title' => __('Message to buyer', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'can_vendor_add_message_on_email_and_thankyou_page', 'label_for' => 'can_vendor_add_message_on_email_and_thankyou_page', 'name' => 'can_vendor_add_message_on_email_and_thankyou_page', 'value' => 'Enable', 'desc' => __('Allow vendors to add vendor shop specific message in "Thank you" page and order mail.',$WCMp->text_domain)), // Checkbox
                                                                                                           
                                                                                                           )
                                                                                         ),
                                                      "vendor_customer_support" => array("title" =>  __('Customer Support Settings ', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(                                                                                         	 								 
                                                                                                           "can_vendor_add_customer_support_details" => array('title' => __('Vendor Shop Support', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'can_vendor_add_customer_support_details', 'label_for' => 'can_vendor_add_customer_support_details', 'name' => 'can_vendor_add_customer_support_details', 'value' => 'Enable', 'desc' => __('Allow vendors to add vendor shop specific customer support details. If left blank by the vendor, the site wide customer support details would be on display.',$WCMp->text_domain)), // Checkbox                                                                                                        
                                                                                                                                                                                                                                                                     
                                                                                                           
                                                                                                           )
                                                                                         ),
                                                      "policies_capabilities_section" => array("title" =>  __('Policies Settings ', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                         	 								 "can_vendor_edit_policy_tab_label" => array('title' => __('Can Vendor Edit Policy Tab Title', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'can_vendor_edit_policy_tab_label', 'label_for' => 'can_vendor_edit_policy_tab_label', 'name' => 'can_vendor_edit_policy_tab_label', 'value' => 'Enable', 'desc' => __('Allow vendors to edit the Policy Tab Label.',$WCMp->text_domain)), // Checkbox	
                                                                                                           "can_vendor_edit_cancellation_policy" => array('title' => __('Can Vendor Edit Cancellation/Return/Exchange Policy', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'can_vendor_edit_cancellation_policy', 'label_for' => 'can_vendor_edit_cancellation_policy', 'name' => 'can_vendor_edit_cancellation_policy', 'value' => 'Enable', 'desc' => __('Allow vendors to edit the Cancellation/Return/Exchange Policy.',$WCMp->text_domain)), // Checkbox
                                                                                                           "can_vendor_edit_refund_policy" => array('title' => __('Can Vendor Edit Refund Policy', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'can_vendor_edit_refund_policy', 'label_for' => 'can_vendor_edit_refund_policy', 'name' => 'can_vendor_edit_refund_policy', 'value' => 'Enable', 'desc' => __('Allow vendors to edit the Refund Policy.',$WCMp->text_domain)), // Checkbox
                                                                                                           "can_vendor_edit_shipping_policy" => array('title' => __('Can Vendor Edit Shipping Policy', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'can_vendor_edit_shipping_policy', 'label_for' => 'can_vendor_edit_shipping_policy', 'name' => 'can_vendor_edit_shipping_policy', 'value' => 'Enable', 'desc' => __('Allow vendors to edit the Shipping Policy.',$WCMp->text_domain)), // Checkbox
                                                                                                                                                                                                                                                                     
                                                                                                           
                                                                                                           )
                                                                                         ),
                                                      
                                                      
                                                      ),
                                  );
    
    $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function wcmp_capabilities_settings_sanitize( $input ) {
    global $WCMp;
    $new_input = array();
    
    $hasError = false;
    
    if( isset( $input['is_upload_files'] ) ) {
      $new_input['is_upload_files'] = sanitize_text_field( $input['is_upload_files'] );  
      add_cap_existing_users('upload_files');
    } else {
    	 change_cap_existing_users('is_upload_files');
    }
    
    if( isset( $input['is_published_product'] ) ) {
      $new_input['is_published_product'] = sanitize_text_field( $input['is_published_product'] );
      add_cap_existing_users('publish_products');
    } else {
    	change_cap_existing_users('is_published_product');
    }
    
    if( isset( $input['is_submit_product'] ) ) {
      $new_input['is_submit_product'] = sanitize_text_field( $input['is_submit_product'] );
      add_cap_existing_users('is_submit_product');
    } else {
    	 change_cap_existing_users('is_submit_product');
    	 if( isset( $input['is_published_product'] ) ) {
    	 	 unset( $new_input['is_published_product'] );
    	 }
    }
    
    if( isset( $input['is_published_coupon'] ) ) {
      $new_input['is_published_coupon'] = sanitize_text_field( $input['is_published_coupon'] );
      add_cap_existing_users('publish_shop_coupons');
    } else {
    	change_cap_existing_users('is_published_coupon');
    }
    
    if( isset( $input['is_submit_coupon'] ) ) {
      $new_input['is_submit_coupon'] = sanitize_text_field( $input['is_submit_coupon'] );
      add_cap_existing_users('is_submit_coupon');
    } else {
    	 change_cap_existing_users('is_submit_coupon');
    	 if( isset( $input['is_published_coupon'] ) ) {
    	 	 unset( $new_input['is_published_coupon'] );
    	 }
    }
    
    if( isset( $input['give_tax'] ) )
      $new_input['give_tax'] = sanitize_text_field( $input['give_tax'] );  
    
    if( isset( $input['give_shipping'] ) )
      $new_input['give_shipping'] = sanitize_text_field( $input['give_shipping'] );  
    
    
    if( isset( $input['is_order_csv_export'] ) )
      $new_input['is_order_csv_export'] = sanitize_text_field( $input['is_order_csv_export'] );      
    
    if( isset( $input['is_show_email'] ) )
      $new_input['is_show_email'] = sanitize_text_field( $input['is_show_email'] );    
    
    if( isset( $input['is_vendor_submit_comment'] ) ) 
      $new_input['is_vendor_submit_comment'] = sanitize_text_field( $input['is_vendor_submit_comment'] );   
    
    if( isset( $input['is_vendor_add_external_url'] ) ) 
      $new_input['is_vendor_add_external_url'] = sanitize_text_field( $input['is_vendor_add_external_url'] );
    
    if( isset( $input['is_vendor_view_comment'] ) ) {
      $new_input['is_vendor_view_comment'] = sanitize_text_field( $input['is_vendor_view_comment'] );      
    } else if( isset( $input['is_vendor_submit_comment'] ) ) {
    	unset( $new_input['is_vendor_submit_comment'] );
    }
    
    if( isset( $input['is_order_email'] ) )
      $new_input['is_order_email'] = sanitize_text_field( $input['is_order_email'] );       
    
    if( isset( $input['show_cust_billing_add'] ) )
      $new_input['show_cust_billing_add'] = sanitize_text_field( $input['show_cust_billing_add'] );
    
    if( isset( $input['show_cust_shipping_add'] ) )
      $new_input['show_cust_shipping_add'] = sanitize_text_field( $input['show_cust_shipping_add'] );
    
    if( isset( $input['show_cust_order_calulations'] ) )
      $new_input['show_cust_order_calulations'] = sanitize_text_field( $input['show_cust_order_calulations'] );
    
    if( isset( $input['show_customer_dtl'] ) )
      $new_input['show_customer_dtl'] = sanitize_text_field( $input['show_customer_dtl'] );
    
    if( isset( $input['show_customer_billing'] ) )
      $new_input['show_customer_billing'] = sanitize_text_field( $input['show_customer_billing'] );
    
    if( isset( $input['show_customer_shipping'] ) )
      $new_input['show_customer_shipping'] = sanitize_text_field( $input['show_customer_shipping'] );
    
		if( isset( $input['show_cust_add'] ) )
			$new_input['show_cust_add'] = sanitize_text_field( $input['show_cust_add'] );
		
		if( isset( $input['can_vendor_add_message_on_email_and_thankyou_page'] ) )
			$new_input['can_vendor_add_message_on_email_and_thankyou_page'] = sanitize_text_field( $input['can_vendor_add_message_on_email_and_thankyou_page'] );
		
		if( isset( $input['is_customer_support_details'] ) )
			$new_input['is_customer_support_details'] = sanitize_text_field( $input['is_customer_support_details'] );
		
		if( isset( $input['can_vendor_add_customer_support_details'] ) )
			$new_input['can_vendor_add_customer_support_details'] = sanitize_text_field( $input['can_vendor_add_customer_support_details'] );		
		
		if( isset( $input['csd_email'] ) )
			$new_input['csd_email'] = sanitize_text_field( $input['csd_email'] );
		
		if( isset( $input['csd_phone'] ) )
			$new_input['csd_phone'] = sanitize_text_field( $input['csd_phone'] );
		
		if( isset( $input['csd_return_address_1'] ) )
			$new_input['csd_return_address_1'] = sanitize_text_field( $input['csd_return_address_1'] );
		
		if( isset( $input['csd_return_address_2'] ) )
			$new_input['csd_return_address_2'] = sanitize_text_field( $input['csd_return_address_2'] );
		
		if( isset( $input['csd_return_state'] ) )
			$new_input['csd_return_state'] = sanitize_text_field( $input['csd_return_state'] );
		
		if( isset( $input['csd_return_city'] ) )
			$new_input['csd_return_city'] = sanitize_text_field( $input['csd_return_city'] );
		
		if( isset( $input['csd_return_country'] ) )
			$new_input['csd_return_country'] = sanitize_text_field( $input['csd_return_country'] );
		
		if( isset( $input['is_hide_option_show'] ) )
			$new_input['is_hide_option_show'] = sanitize_text_field( $input['is_hide_option_show'] );
		
		if( isset( $input['can_vendor_edit_cancellation_policy'] ) )
			$new_input['can_vendor_edit_cancellation_policy'] = sanitize_text_field( $input['can_vendor_edit_cancellation_policy'] );
		
		if( isset( $input['can_vendor_edit_refund_policy'] ) )
			$new_input['can_vendor_edit_refund_policy'] = sanitize_text_field( $input['can_vendor_edit_refund_policy'] );
		
		if( isset( $input['can_vendor_edit_shipping_policy'] ) )
			$new_input['can_vendor_edit_shipping_policy'] = sanitize_text_field( $input['can_vendor_edit_shipping_policy'] );
		
		if( isset( $input['can_vendor_edit_policy_tab_label'] ) )
			$new_input['can_vendor_edit_policy_tab_label'] = sanitize_text_field( $input['can_vendor_edit_policy_tab_label'] );
			
		       
			
		if(!$hasError) {
			add_settings_error(
			 "wcmp_{$this->tab}_settings_name",
			 esc_attr( "wcmp_{$this->tab}_settings_admin_updated" ),
			 __('Capability Settings Updated', $WCMp->text_domain),
			 'updated'
			);
    }
    return apply_filters("settings_{$this->tab}_tab_new_input", $new_input , $input);
  }

  /** 
   * Print the Section text
   */
  public function products_settings_section_info() {
    global $WCMp;
  }
  
  /** 
   * Print the Section text
   */
  public function vendor_email_settings_info() {
    global $WCMp;
    stripslashes(_e('Choose customer details you want to show in "New order e-mail" to Vendors.', $WCMp->text_domain));
  }
  
  /** 
   * Print the Section text
   */
  public function view_vendor_order_info() {
    global $WCMp;
  }
 
  /** 
   * Print the Section text
   */
  public function vendor_order_dtl_info() {
    global $WCMp;
    stripslashes(_e('Choose customer details you want to show in "Order Details" in Vendor dashboard.', $WCMp->text_domain));
  }
  
  /** 
   * Print the Section text
   */
  public function vendor_order_export_info() {
    global $WCMp;
  }
  
  
  /** 
   * Print the Section text
   */
  public function vendor_miscellaneous_info() {
    global $WCMp;
  }
  
   /** 
   * Print the Section text
   */
  public function vendor_messages_info() {
    global $WCMp;
    
  }
  /** 
   * Print the Section text
   */
  public function vendor_customer_support_info() {
    global $WCMp;
    
  }
  
  public function policies_capabilities_section_info() {
  	global $WCMp;
  	
  }
  
   /** 
   * Print the Section text
   */
  public function vendor_return_address_info() {
    global $WCMp;
    _e('Please Enter the customer supports details and returns address of adminstrator if admin want to care of support and returns insteed of vendors .', $WCMp->text_domain);
  }
  
}