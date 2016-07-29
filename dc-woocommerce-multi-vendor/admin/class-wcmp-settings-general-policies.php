<?php
class WCMp_Settings_Gneral_Policies {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;
  
  private $subsection;

  /**
   * Start up
   */
  public function __construct($tab,$subsection) {
    $this->tab = $tab;
    $this->subsection = $subsection;
    $this->options = get_option( "wcmp_{$this->tab}_{$this->subsection}_settings_name" );
    $this->settings_page_init();
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $WCMp;
    
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "subsection" => "{$this->subsection}",
                                  "sections" => array(
                                                      "wcmp_store_policies_settings_section" => array("title" =>  '', // Section one
                                                                                         "fields" => array( 
                                                                                         	 								 "is_policy_on" => array('title' => __('Enable Policies ', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_policy_on', 'label_for' => 'is_policy_on', 'name' => 'is_policy_on', 'value' => 'Enable'), // Checkbox
                                                                                         	 								 "is_cancellation_on" => array('title' => __('Cancellation/Return/Exchange Policy', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_cancellation_on', 'label_for' => 'is_cancellation_on', 'name' => 'is_cancellation_on', 'value' => 'Enable'), // Checkbox
                                                                                         	 								 "is_cancellation_product_level_on" => array('title' => __('Cancellation/Return/Exchange Policy Product Wise', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_cancellation_product_level_on', 'label_for' => 'is_cancellation_product_level_on', 'name' => 'is_cancellation_product_level_on', 'value' => 'Enable'), // Checkbox
                                                                                         	 								 "is_refund_on" => array('title' => __('Refund Policy', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_refund_on', 'label_for' => 'is_refund_on', 'name' => 'is_refund_on', 'value' => 'Enable'), // Checkbox
                                                                                         	 								 "is_refund_product_level_on" => array('title' => __('Refund Policy Product Wise', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_refund_product_level_on', 'label_for' => 'is_refund_product_level_on', 'name' => 'is_refund_product_level_on', 'value' => 'Enable'), // Checkbox
                                                                                         	 								 "is_shipping_on" => array('title' => __('Shipping Policy', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_shipping_on', 'label_for' => 'is_shipping_on', 'name' => 'is_shipping_on', 'value' => 'Enable'), // Checkbox
                                                                                         	 								 "is_shipping_product_level_on" => array('title' => __('Shipping Policy Product Wise', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_shipping_product_level_on', 'label_for' => 'is_shipping_product_level_on', 'name' => 'is_shipping_product_level_on', 'value' => 'Enable'), // Checkbox
                                                                                         	 								 
                                                                                                           ),
                                                                                         ),
                                                      "wcmp_store_policies_admin_details_section" => array("title" =>  'Policies Details for Admin', // Section two
                                                                                         "fields" => array( 
                                                                                         	 								 "policy_tab_title" => array('title' => __('Product Tab Title :', $WCMp->text_domain), 'type' => 'text', 'id' => 'policy_tab_title', 'label_for' => 'policy_tab_title', 'name' => 'policy_tab_title', 'desc' => __('Please Enter the Policies Tab Title .', $WCMp->text_domain)), // text	                                                                                                           
                                                                                                           "cancellation_policy_label" => array('title' => __('Cancellation/Return/Exchange Policy Label :', $WCMp->text_domain), 'type' => 'text', 'id' => 'cancellation_policy_label', 'label_for' => 'cancellation_policy_label', 'name' => 'cancellation_policy_label', 'cols'=> 50, 'rows' => 6,  'desc' => __('Please Enter the Cancellation Policy Custom Heading.', $WCMp->text_domain)), // text
																																																					 "cancellation_policy" => array('title' => __('Cancellation/Return/Exchange Policy :', $WCMp->text_domain), 'type' => 'wpeditor', 'id' => 'cancellation_policy', 'label_for' => 'cancellation_policy', 'name' => 'cancellation_policy', 'cols'=> 50, 'rows' => 6,  'desc' => __('Please Enter the Cancellation Policy .', $WCMp->text_domain)), // Textarea
																																																					 "refund_policy_label" => array('title' => __('Refund Policy Label:', $WCMp->text_domain), 'type' => 'text', 'id' => 'refund_policy_label', 'label_for' => 'refund_policy_label', 'name' => 'refund_policy_label',  'desc' => __('Please Enter the Refund Policy Label.', $WCMp->text_domain)), // text
                                                                                                           "refund_policy" => array('title' => __('Refund Policy :', $WCMp->text_domain), 'type' => 'wpeditor', 'id' => 'refund_policy', 'label_for' => 'refund_policy', 'name' => 'refund_policy', 'cols'=> 50, 'rows' => 6,  'desc' => __('Please Enter the Refund Policy .', $WCMp->text_domain)), // Textarea
                                                                                                           "shipping_policy_label" => array('title' => __('Shipping Policy Label :', $WCMp->text_domain), 'type' => 'text', 'id' => 'shipping_policy_label', 'label_for' => 'shipping_policy_label', 'name' => 'shipping_policy_label',  'desc' => __('Please Enter the Shipping Policy Label.', $WCMp->text_domain)), // text
                                                                                                           "shipping_policy" => array('title' => __('Shipping Policy :', $WCMp->text_domain), 'type' => 'wpeditor', 'id' => 'shipping_policy', 'label_for' => 'shipping_policy', 'name' => 'shipping_policy', 'cols'=> 50, 'rows' => 6,  'desc' => __('Please Enter the Shipping Policy .', $WCMp->text_domain)), // Textarea
                                                                                         	 								 
                                                                                                           ),
                                                                                         ),
                                                      ),
                                                     
                                  );
    
    $WCMp->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function wcmp_general_policies_settings_sanitize( $input ) {
    global $WCMp;
    $new_input = array();
    
    $hasError = false;
    
    if( isset( $input['is_policy_on'] ) )
      $new_input['is_policy_on'] = sanitize_text_field( $input['is_policy_on'] );
    if( isset( $input['is_cancellation_on'] ) )
      $new_input['is_cancellation_on'] = sanitize_text_field( $input['is_cancellation_on'] );
    if( isset( $input['is_cancellation_product_level_on'] ) )
      $new_input['is_cancellation_product_level_on'] = sanitize_text_field( $input['is_cancellation_product_level_on'] );
    if( isset( $input['is_refund_on'] ) )
      $new_input['is_refund_on'] = sanitize_text_field( $input['is_refund_on'] );
    if( isset( $input['is_refund_product_level_on'] ) )
      $new_input['is_refund_product_level_on'] = sanitize_text_field( $input['is_refund_product_level_on'] );
    if( isset( $input['is_shipping_on'] ) )
      $new_input['is_shipping_on'] = sanitize_text_field( $input['is_shipping_on'] );
    if( isset( $input['is_policy_on'] ) )
      $new_input['is_shipping_product_level_on'] = sanitize_text_field( $input['is_shipping_product_level_on'] );
    if( isset( $input['cancellation_policy'] ) )
     	 $new_input['cancellation_policy'] = $input['cancellation_policy'];    
    if( isset( $input['refund_policy'] ) )
    	$new_input['refund_policy'] = $input['refund_policy'];    
    if( isset( $input['shipping_policy'] ) )
    	$new_input['shipping_policy'] =  $input['shipping_policy'];    
    if( isset( $input['cancellation_policy_label'] ) )
    	$new_input['cancellation_policy_label'] = $input['cancellation_policy_label'];    
    if( isset( $input['refund_policy_label'] ) )
    	$new_input['refund_policy_label'] = $input['refund_policy_label'];    
    if( isset( $input['shipping_policy_label'] ) )
    	$new_input['shipping_policy_label'] =  $input['shipping_policy_label'];    
    if( isset( $input['policy_tab_title'] ) )
    	$new_input['policy_tab_title'] =  sanitize_text_field( $input['policy_tab_title'] );
    
    
    
    if(!$hasError) {
			add_settings_error(
			 "wcmp_{$this->tab}_{$this->subsection}_settings_name",
			 esc_attr( "wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated" ),
			 __('Policies Settings Updated', $WCMp->text_domain),
			 'updated'
			);
    }
    return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input , $input);
  }

   
  /** 
   * Print the Section text
   */
  public function wcmp_store_policies_settings_section_info() {
    global $WCMp;
    printf( __( 'Please configure the policies section.', $WCMp->text_domain ) );
  }
  
   /** 
   * Print the Section text
   */
  public function wcmp_store_policies_admin_details_section_info() {
    global $WCMp;
   
  }
  
}
