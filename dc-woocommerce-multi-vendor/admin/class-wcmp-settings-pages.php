<?php
class WCMp_Settings_Pages {
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
    $pages = get_pages(); 
    $woocommerce_pages = array ( woocommerce_get_page_id('shop'), woocommerce_get_page_id('cart'), woocommerce_get_page_id('checkout'), woocommerce_get_page_id('myaccount'));
    foreach ( $pages as $page ) {
    	if(!in_array($page->ID, $woocommerce_pages)) {
    		$pages_array[$page->ID] = $page->post_title;
    	}
    }
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
                                                      "default_settings_section" => array("title" =>  '', // Section one
                                                                                         "fields" => array(
                                                                                                           "vendor_dashboard" => array('title' => __('Vendor Dashboard', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_dashboard', 'label_for' => 'vendor_dashboard', 'name' => 'vendor_dashboard', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor dashboard.', $WCMp->text_domain)), // Select
                                                                                                           "shop_settings" => array('title' => __('Shop Settings', $WCMp->text_domain), 'type' => 'select', 'id' => 'shop_settings', 'label_for' => 'shop_settings', 'name' => 'shop_settings', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor shop settings', $WCMp->text_domain)), // Select
                                                                                                           "view_order" => array('title' => __('View Vendor Orders', $WCMp->text_domain), 'type' => 'select', 'id' => 'view_order', 'label_for' => 'view_order', 'name' => 'view_order', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor view order', $WCMp->text_domain)), // Select
                                                                                                           "vendor_order_detail" => array('title' => __('Vendor Order Detail Page', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_order_detail', 'label_for' => 'vendor_order_detail', 'name' => 'vendor_order_detail', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor order details', $WCMp->text_domain)), // Select
                                                                                                           "vendor_transaction_thankyou" => array('title' => __('Withdrawal Request Status Page', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_transaction_thankyou', 'label_for' => 'vendor_transaction_thankyou', 'name' => 'vendor_transaction_thankyou', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor transaction thankyou', $WCMp->text_domain)), // Select
                                                                                                           "vendor_transaction_detail" => array('title' => __('Transaction Details', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_transaction_detail', 'label_for' => 'vendor_transaction_detail', 'name' => 'vendor_transaction_detail', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor transactions details', $WCMp->text_domain)), // Select
                                                                                                           "vendor_policies" => array('title' => __('Vendor Policies', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_policies', 'label_for' => 'vendor_policies', 'name' => 'vendor_policies', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor policies', $WCMp->text_domain)), // Select
                                                                                                           "vendor_billing" => array('title' => __('Vendor Billing', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_billing', 'label_for' => 'vendor_billing', 'name' => 'vendor_billing', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor transactions details', $WCMp->text_domain)), // Select
                                                                                                           "vendor_report" => array('title' => __('Vendor Report', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_report', 'label_for' => 'vendor_report', 'name' => 'vendor_report', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor report', $WCMp->text_domain)), // Select
                                                                                                           "vendor_widthdrawals" => array('title' => __('Vendor Withdrawals', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_widthdrawals', 'label_for' => 'vendor_widthdrawals', 'name' => 'vendor_widthdrawals', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor report', $WCMp->text_domain)), // Select
                                                                                                           "vendor_university" => array('title' => __('Vendor University', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_university', 'label_for' => 'vendor_university', 'name' => 'vendor_university', 'options' => $pages_array, 'hints' => __('Choose your preferred page for university', $WCMp->text_domain)), // Select
                                                                                                           "vendor_announcements" => array('title' => __('Vendor Announcements', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_announcements', 'label_for' => 'vendor_announcements', 'name' => 'vendor_announcements', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor announcements', $WCMp->text_domain)), // Select
                                                                                                           "vendor_shipping" => array('title' => __('Vendor Shipping', $WCMp->text_domain), 'type' => 'select', 'id' => 'vendor_shipping', 'label_for' => 'vendor_shipping', 'name' => 'vendor_shipping', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor shipping', $WCMp->text_domain)), // Select
                                                                                                           
                                                                                         ), 
                                                                                         )
                                                      
                                                      )
                                  );
    
    $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function wcmp_pages_settings_sanitize( $input ) {
    global $WCMp;
    $new_input = array();
    
    $hasError = false;
    
   
    if( isset( $input['vendor_dashboard'] ) )
      $new_input['vendor_dashboard'] = sanitize_text_field( $input['vendor_dashboard'] );
    
    if( isset( $input['vendor_transaction_thankyou'] ) )
      $new_input['vendor_transaction_thankyou'] = sanitize_text_field( $input['vendor_transaction_thankyou'] );
    
    if( isset( $input['shop_settings'] ) )
      $new_input['shop_settings'] = sanitize_text_field( $input['shop_settings'] );
    
		if( isset( $input['view_order'] ) )
		$new_input['view_order'] = sanitize_text_field( $input['view_order'] );
    
    if( isset( $input['vendor_order_detail'] ) )
      $new_input['vendor_order_detail'] = sanitize_text_field( $input['vendor_order_detail'] );
    
    if( isset( $input['vendor_transaction_detail'] ) )
      $new_input['vendor_transaction_detail'] = sanitize_text_field( $input['vendor_transaction_detail'] );
    
    if( isset( $input['vendor_policies'] ) )
      $new_input['vendor_policies'] = sanitize_text_field( $input['vendor_policies'] );
    
    if( isset( $input['vendor_billing'] ) )
      $new_input['vendor_billing'] = sanitize_text_field( $input['vendor_billing'] );  
    
    if( isset( $input['vendor_report'] ) )
      $new_input['vendor_report'] = sanitize_text_field( $input['vendor_report'] ); 
    
    if( isset( $input['vendor_widthdrawals'] ) )
      $new_input['vendor_widthdrawals'] = sanitize_text_field( $input['vendor_widthdrawals'] );   
    
    if( isset( $input['vendor_university'] ) )
      $new_input['vendor_university'] = sanitize_text_field( $input['vendor_university'] );
    
    if( isset( $input['vendor_announcements'] ) )
      $new_input['vendor_announcements'] = sanitize_text_field( $input['vendor_announcements'] );
    if( isset( $input['vendor_shipping'] ) )
      $new_input['vendor_shipping'] = sanitize_text_field( $input['vendor_shipping'] );
    
    
    
    if(!$hasError) {
			add_settings_error(
			 "wcmp_{$this->tab}_settings_name",
			 esc_attr( "wcmp_{$this->tab}_settings_admin_updated" ),
			 __('Page Settings Updated', $WCMp->text_domain),
			 'updated'
			);
    }
    return apply_filters("settings_{$this->tab}_tab_new_input", $new_input, $input);
  }

  /** 
   * Print the Section text
   */
  public function default_settings_section_info() {
    global $WCMp;
  }
 
  
}