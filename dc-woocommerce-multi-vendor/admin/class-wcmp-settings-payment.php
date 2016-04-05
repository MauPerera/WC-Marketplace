<?php
class WCMp_Settings_Payment {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;  
  private $tab;
  private $paypal_api_username;
  private $paypal_api_password;
  private $paypal_api_signature;

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $this->options = get_option( "wcmp_{$this->tab}_settings_name" );
    $paypal_details = get_option('woocommerce_paypal_settings');
    if(isset($paypal_details['api_username']) && !empty($paypal_details['api_username'])){
    	$this->paypal_api_username = $paypal_details['api_username'];
    }
    if(isset($paypal_details['api_password']) && !empty($paypal_details['api_password'])){
    	$this->paypal_api_password = $paypal_details['api_password'];
    }
    if(isset($paypal_details['api_signature']) && !empty($paypal_details['api_signature'])){
    	$this->paypal_api_signature = $paypal_details['api_signature'];
    }
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
                                  										"revenue_sharing_mode_section" => array("title" =>  __('Revenue Sharing Mode', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                         	 								"revenue_sharing_mode" => array('title' => __('Mode ', $WCMp->text_domain), 'type' => 'radio', 'id' => 'revenue_sharing_mode', 'label_for' => 'revenue_sharing_mode', 'name' => 'revenue_sharing_mode', 'dfvalue' => 'vendor', 'options' => array('admin' => __('Admin fees', $WCMp->text_domain), 'vendor' => __('Vendor Commissions', $WCMp->text_domain)), 'desc' => sprintf(__('To know more about these two modes, please visit [%s]', $WCMp->text_domain), '<a target="_blank" href="http://wc-marketplace.com/knowledgebase/setting-up-commission-and-other-payments-for-wcmp-v-2-1-1/">View</a>')), // Radio
                                                                                         	 								),
                                                                                    ),
                                                      "what_to_pay_section" => array("title" =>  __('What to Pay', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                         	 								"commission_type" => array('title' => __('Commission Type', $WCMp->text_domain), 'type' => 'select', 'id' => 'commission_typee', 'label_for' => 'commission_typee', 'name' => 'commission_type', 'options' => array('' => __('Choose Commission Type', $WCMp->text_domain), 'fixed' => __('Fixed Amount', $WCMp->text_domain), 'percent' => __('Percentage', $WCMp->text_domain), 'fixed_with_percentage' => __('%age + Fixed (per transaction)', $WCMp->text_domain), 'fixed_with_percentage_qty' => __('%age + Fixed (per unit)', $WCMp->text_domain)), 'desc' => __('Choose your preferred commission type. It will affect all commission calculations.', $WCMp->text_domain)), // Select
                                                                                         	 								"default_commission" => array('title' => __('Commission value', $WCMp->text_domain), 'type' => 'text', 'id' => 'default_commissionn', 'label_for' => 'default_commissionn', 'name' => 'default_commission', 'desc' => __('This will be the default commission(in percentage or fixed) paid to vendors if product and vendor specific commission is not set. ', $WCMp->text_domain)), // Text
                                                                                         	 								"default_percentage" => array('title' => __('Commission Percentage', $WCMp->text_domain), 'type' => 'text', 'id' => 'default_percentage', 'label_for' => 'default_percentage', 'name' => 'default_percentage', 'desc' => __('This will be the default percentage paid to vendors if product and vendor specific commission is not set. ', $WCMp->text_domain)), // Text
                                                                                         	 								"fixed_with_percentage" => array('title' => __('Fixed Amount', $WCMp->text_domain), 'type' => 'text', 'id' => 'fixed_with_percentage', 'label_for' => 'fixed_with_percentage', 'name' => 'fixed_with_percentage', 'desc' => __('Fixed (per transaction)', $WCMp->text_domain)), // Text
                                                                                         	 								"fixed_with_percentage_qty" => array('title' => __('Fixed Amount', $WCMp->text_domain), 'type' => 'text', 'id' => 'fixed_with_percentage_qty', 'label_for' => 'fixed_with_percentage_qty', 'name' => 'fixed_with_percentage_qty', 'desc' => __('Fixed (per unit)', $WCMp->text_domain)), // Text
                                                                                         	 								"commission_include_coupon" => array('title' => __('Share Coupon Discount', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'commission_include_couponn', 'label_for' => 'commission_include_couponn', 'desc' => __('If checked, vendors commission will be calculated AFTER the discount. If left unchecked, the site owner will bear the cost of the coupon.  ', $WCMp->text_domain), 'name' => 'commission_include_coupon', 'value' => 'Enable'), // Checkbox
                                                                                         	 								"give_tax" => array('title' => __('Tax', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'give_taxx', 'label_for' => 'give_taxx', 'name' => 'give_tax',  'desc' => __('Transfer the tax collected (per product) to the vendor. ', $WCMp->text_domain),  'value' => 'Enable'), // Checkbox
                                                                                         	 								"give_shipping" => array('title' => __('Shipping', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'give_shippingg', 'label_for' => 'give_shippingg', 'name' => 'give_shipping', 'desc' => __('Transfer shipping charges collected (per product) to the vendor.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                                                                                         	 								"commission_threshold" => array('title' => __('Minimum Threshold', $WCMp->text_domain), 'type' => 'text', 'id' => 'fixed_with_percentage_qty', 'label_for' => 'commission_threshold', 'name' => 'commission_threshold', 'desc' => __('Threshold amount required to disburse commission.', $WCMp->text_domain)), // Text
                                                                                         	 								),
                                                                                         ),
                                  										"default_settings_section" => array("title" =>  __('How/When to Pay ', $WCMp->text_domain), // Section one
                                                                                         "fields" => array("choose_payment_mode" => array('title' => __('Disbursal Mode', $WCMp->text_domain), 'type' => 'select', 'id' => 'choose_payment_mode', 'label_for' => 'choose_payment_mode', 'name' => 'choose_payment_mode', 'options' => apply_filters( 'wcmp_disbursal_mode', array('admin' => __('Automatic disbursal', $WCMp->text_domain), 'vendor' => __('Withdrawal by request', $WCMp->text_domain)) ), 'desc' => __('Choose your preferred payment mode.', $WCMp->text_domain)), // Select
																																																					"commission_transfer" => array('title' => __('Withdrawal Charges', $WCMp->text_domain), 'type' => 'text', 'id' => 'commission_transfer', 'label_for' => 'commission_transfer', 'name' => 'commission_transfer', 'desc' => __('Vendor will be charged this amount per withdrawal after the quota of free withdrawals is over.', $WCMp->text_domain)), // Text
																																																					"no_of_orders" => array('title' => __('Number of Free Withdrawals', $WCMp->text_domain), 'type' => 'text', 'id' => 'no_of_orders', 'label_for' => 'no_of_orders', 'name' => 'no_of_orders', 'desc' => __('Number of Free Withdrawal Requests.', $WCMp->text_domain)), // Text
																																																					"is_mass_pay" => array('title' => __('Activate MassPay', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_mass_pay', 'label_for' => 'is_mass_pay', 'name' => 'is_mass_pay', 'value' => 'Enable'), // Checkbox
																																																					"payment_schedule" => array('title' => __('Set Schedule', $WCMp->text_domain), 'type' => 'radio', 'id' => 'payment_schedule', 'label_for' => 'payment_schedule', 'name' => 'payment_schedule', 'dfvalue' => 'daily', 'options' => array('weekly' => __('Weekly', $WCMp->text_domain), 'daily' => __('Daily', $WCMp->text_domain), 'monthly' => __('Monthly', $WCMp->text_domain), 'fortnightly' => __('Fortnightly',$WCMp->text_domain), 'hourly' => __('Hourly', $WCMp->text_domain)), 'desc' => __('Choose your preferred schedule for PayaPal MassPay.', $WCMp->text_domain)), // Radio
																																																					"api_username" => array('title' => __('API Username', $WCMp->text_domain), 'type' => 'text', 'id' => 'api_username', 'label_for' => 'api_username', 'dfvalue'=>$this->paypal_api_username, 'name' => 'api_username', 'desc' => __('Give your PayPal API Username.', $WCMp->text_domain)),
																																																					"api_pass" => array('title' => __('API Password', $WCMp->text_domain), 'type' => 'text', 'id' => 'api_pass', 'label_for' => 'api_pass', 'name' => 'api_pass', 'dfvalue'=>$this->paypal_api_password, 'desc' => __('Give your PayPal API Password.', $WCMp->text_domain)),
																																																					"api_signature" => array('title' => __('API Signature', $WCMp->text_domain), 'type' => 'text', 'id' => 'api_signature', 'label_for' => 'api_signature', 'name' => 'api_signature', 'dfvalue'=>$this->paypal_api_signature,  'desc' => __('Give your PayPal API Signature.', $WCMp->text_domain)),
																																																					"is_testmode" => array('title' => __('Enable Test Mode', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_testmode', 'label_for' => 'is_testmode', 'name' => 'is_testmode', 'value' => 'Enable'), // Checkbox
                                                                                                          ),							
                                                      																	 )
                                                      ),
                                  );
    
    $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function wcmp_payment_settings_sanitize( $input ) {
    global $WCMp;
    $new_input = array();
    
    $hasError = false;
    
    if( isset( $input['revenue_sharing_mode'] ) )
    	$new_input['revenue_sharing_mode'] = sanitize_text_field( $input['revenue_sharing_mode'] );
    
		if( isset( $input['is_mass_pay'] ) )
			$new_input['is_mass_pay'] = sanitize_text_field( $input['is_mass_pay'] );
		
		if( isset( $input['default_commission'] ) )
			$new_input['default_commission'] = sanitize_text_field( $input['default_commission'] );
		
		
		if( isset( $input['default_percentage'] ) )
			$new_input['default_percentage'] = sanitize_text_field( $input['default_percentage'] );
		
		if( isset( $input['fixed_with_percentage_qty'] ) )
			$new_input['fixed_with_percentage_qty'] = sanitize_text_field( $input['fixed_with_percentage_qty'] );
		
		if( isset( $input['fixed_with_percentage'] ) )
			$new_input['fixed_with_percentage'] = sanitize_text_field( $input['fixed_with_percentage'] );
		
		
		if( isset( $input['commission_threshold'] ) )
			$new_input['commission_threshold'] = sanitize_text_field( $input['commission_threshold'] );
		
		if( isset( $input['commission_transfer'] ) )
			$new_input['commission_transfer'] = sanitize_text_field( $input['commission_transfer'] );
		
		if( isset( $input['no_of_orders'] ) )
			$new_input['no_of_orders'] = sanitize_text_field( $input['no_of_orders'] );
		
		
		
		if( isset( $input['commission_type'] ) )
			$new_input['commission_type'] = sanitize_text_field( $input['commission_type'] );
		if( isset( $input['commission_include_coupon'] ) )
			$new_input['commission_include_coupon'] = sanitize_text_field( $input['commission_include_coupon'] );
		if( isset( $input['give_tax'] ) )
			$new_input['give_tax'] = sanitize_text_field( $input['give_tax'] );
		if( isset( $input['give_shipping'] ) )
			$new_input['give_shipping'] = sanitize_text_field( $input['give_shipping'] );
		
		if( isset( $input['is_testmode'] ) )
			$new_input['is_testmode'] = sanitize_text_field( $input['is_testmode'] );
		
		if( isset( $input['payment_schedule'] ) )
			$new_input['payment_schedule'] = $input['payment_schedule'];
		
		if( isset( $input['is_mass_pay']) ) {
			$schedule = wp_get_schedule( 'paypal_masspay_cron_start' );
			if($schedule != $input['payment_schedule']) {
				if(wp_next_scheduled( 'paypal_masspay_cron_start' ) ) {
					$timestamp = wp_next_scheduled( 'paypal_masspay_cron_start' );
					wp_unschedule_event($timestamp, 'paypal_masspay_cron_start' );
				}
				if( $input['choose_payment_mode'] == 'admin' ) {
					wp_schedule_event( time(), $input['payment_schedule'], 'paypal_masspay_cron_start');
				}
			} else {
				//wp_schedule_event( time(), $input['payment_schedule'], 'paypal_masspay_cron_start');
			}
		} else {
    	if(wp_next_scheduled( 'paypal_masspay_cron_start' ) ) {
				$timestamp = wp_next_scheduled( 'paypal_masspay_cron_start' );
				wp_unschedule_event($timestamp, 'paypal_masspay_cron_start' );
			}
    }
    
    if(isset( $input['choose_payment_mode'] )) {
    	$new_input['choose_payment_mode'] = sanitize_text_field( $input['choose_payment_mode'] );	
    } 
    if( $input['choose_payment_mode'] == 'vendor') {
    	if(wp_next_scheduled( 'paypal_masspay_cron_start' ) ) {
				$timestamp = wp_next_scheduled( 'paypal_masspay_cron_start' );
				wp_unschedule_event($timestamp, 'paypal_masspay_cron_start' );
			}
    }
		
    if( isset( $input['api_username'] ) ) {
      $new_input['api_username'] = trim($input['api_username']);
    } else {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_error" ),
        __('Set API Username', $WCMp->text_domain),
        'error'
      );
      $hasError = true;
    }
    
    if( isset( $input['api_pass'] ) ) {
      $new_input['api_pass'] = trim($input['api_pass']);
    } else {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_error" ),
        __('Set API Password', $WCMp->text_domain),
        'error'
      );
      $hasError = true;
    }
      
    if( isset( $input['api_signature'] ) ) {
      $new_input['api_signature'] = trim($input['api_signature']);
    } else {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_error" ),
        __('Set API Signature', $WCMp->text_domain),
        'error'
      );
      $hasError = true;
    }
    
    if(!$hasError) {
			add_settings_error(
			 "wcmp_{$this->tab}_settings_name",
			 esc_attr( "wcmp_{$this->tab}_settings_admin_updated" ),
			 __('Payment Settings Updated', $WCMp->text_domain),
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
   	_e('Payment can be done only if vendors have valid PayPal Email Id in their profile. You can add from [Users->Edit Users->PayPal Email]', $WCMp->text_domain);
   	echo '<br>';
   	$pages = get_option('wcmp_pages_settings_name');
		$vendor_transaction_detail = $pages['vendor_transaction_detail'];
   	echo '<a href="'.get_permalink($vendor_transaction_detail).'" target="_blank" >' . __( "View All Transaction Details", $WCMp->text_domain ) . '</a>';
  }
  
  /** 
   * Print the Section text
  */
  public function  revenue_sharing_mode_section_info() {
  	 global $WCMp;
  }
  
  /** 
   * Print the Section text
  */
  public function what_to_pay_section_info() {
    global $WCMp;
  }
  
   /** 
   * Print the Section text
  */
  public function commiossion_tax_sextion_info() {
    global $WCMp;
  }
  
   /** 
   * Print the Section text
  */
  public function commiossion_shipping_sextion_info() {
    global $WCMp;
  }
  
}