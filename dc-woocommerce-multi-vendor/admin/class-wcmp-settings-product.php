<?php
class WCMp_Settings_Product {
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
                                                      "default_settings_section_left_pnl" => array("title" =>  __('Left Side Panel ', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                                           "is_inventory" => array('title' => __('Inventory', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_inventory', 'label_for' => 'is_inventory', 'name' => 'inventory', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_shipping" => array('title' => __('Shipping', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_shipping', 'label_for' => 'is_shipping', 'name' => 'shipping', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_linked_products" => array('title' => __('Linked Products', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_linked_products', 'label_for' => 'is_linked_products', 'name' => 'linked_products', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_attribute" => array('title' => __('Attributes', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_attribute', 'label_for' => 'is_attribute', 'name' => 'attribute', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_advanced" => array('title' => __('Advanced', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_advanced', 'label_for' => 'is_advanced', 'name' => 'advanced', 'value' => 'Enable'), // Checkbox
                                                                                                           )
                                                                                         ), 
                                                      "default_settings_section_types" => array("title" =>  __('Product Types ' , $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                                           "is_simple" => array('title' => __('Simple', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_simple', 'label_for' => 'is_simple', 'name' => 'simple', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_variable" => array('title' => __('Variable', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_variable', 'label_for' => 'is_variable', 'name' => 'variable', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_grouped" => array('title' => __('Grouped', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_grouped', 'label_for' => 'is_grouped', 'name' => 'grouped', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_external" => array('title' => __('External / Affiliate', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_external', 'label_for' => 'is_external', 'name' => 'external', 'value' => 'Enable'), // Checkbox
                                                                                                           )
                                                                                         ), 
                                                      "default_settings_section_type_option" => array("title" =>  __('Type Options ', $WCMp->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                                           "is_virtual" => array('title' => __('Virtual', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_virtual', 'label_for' => 'is_virtual', 'name' => 'virtual', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_downloadable" => array('title' => __('Downloadable', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_downloadable', 'label_for' => 'is_downloadable', 'name' => 'downloadable', 'value' => 'Enable'), // Checkbox
                                                                                                           )
                                                                                         ), 
                                                      "default_settings_section_miscellaneous" => array("title" =>  __('Miscellaneous ', $WCMp->text_domain), // Section one sku
                                                                                         "fields" => array(
                                                                                         	 								 "is_sku" => array('title' => __('SKU', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_sku', 'label_for' => 'is_sku', 'name' => 'sku', 'value' => 'Enable'), // Checkbox	
                                                                                                           "is_taxes" => array('title' => __('Taxes', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_taxes', 'label_for' => 'is_taxes', 'name' => 'taxes', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_add_comment" => array('title' => __('Add Comment', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_add_comment', 'label_for' => 'is_add_comment', 'name' => 'add_comment', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_comment_box" => array('title' => __('Comment Box', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_comment_box', 'label_for' => 'is_comment_box', 'name' => 'comment_box', 'value' => 'Enable'), // Checkbox                                                                                                           
                                                                                                           "stylesheet" => array('title' => __('Stylesheet', $WCMp->text_domain), 'type' => 'textarea', 'id' => 'stylesheet', 'label_for' => 'stylesheet', 'name' => 'stylesheet', 'cols'=> 50, 'rows' => 6,  'desc' => __('You can add CSS in the text area that will be loaded on the product page.', $WCMp->text_domain)), // Textarea
                                                                                                           )
                                                                                         ),
                                                                                           
                                                      
                                                      )
                                  );
    
    $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function wcmp_product_settings_sanitize( $input ) {
    global $WCMp;
    $new_input = array();
    
    $hasError = false;
    
    if( isset( $input['is_policy_on'] ) )
    	$new_input['is_policy_on'] = sanitize_text_field( $input['is_policy_on'] );
    
    if( isset( $input['policies_can_override_by_vendor'] ) )
    	$new_input['policies_can_override_by_vendor'] = sanitize_text_field( $input['policies_can_override_by_vendor'] );
    
    if( isset( $input['product_level_policies_on'] ) )
    	$new_input['product_level_policies_on'] = sanitize_text_field( $input['product_level_policies_on'] );
    
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
    
    if( isset( $input['stylesheet'] ) )
      $new_input['stylesheet'] = sanitize_text_field( $input['stylesheet'] );
    
    if( isset( $input['inventory'] ) )
      $new_input['inventory'] = sanitize_text_field( $input['inventory'] );
    
    if( isset( $input['shipping'] ) )
      $new_input['shipping'] = sanitize_text_field( $input['shipping'] ); 
    
    if( isset( $input['linked_products'] ) )
      $new_input['linked_products'] = sanitize_text_field( $input['linked_products'] );
    
    if( isset( $input['attribute'] ) )
      $new_input['attribute'] = sanitize_text_field( $input['attribute'] );
    
    if( isset( $input['advanced'] ) )
      $new_input['advanced'] = sanitize_text_field( $input['advanced'] ); 
    
    if( isset( $input['simple'] ) )
      $new_input['simple'] = sanitize_text_field( $input['simple'] );
    
		if( isset( $input['variable'] ) )
			$new_input['variable'] = sanitize_text_field( $input['variable'] );
    
    if( isset( $input['grouped'] ) )
      $new_input['grouped'] = sanitize_text_field( $input['grouped'] );
    
    if( isset( $input['virtual'] ) )
      $new_input['virtual'] = sanitize_text_field( $input['virtual'] );
    
    if( isset( $input['external'] ) )
      $new_input['external'] = sanitize_text_field( $input['external'] );
    
    if( isset( $input['downloadable'] ) )
      $new_input['downloadable'] = sanitize_text_field( $input['downloadable'] );
    
    if( isset( $input['taxes'] ) )
      $new_input['taxes'] = sanitize_text_field( $input['taxes'] );
    
    if( isset( $input['add_comment'] ) )
      $new_input['add_comment'] = sanitize_text_field( $input['add_comment'] );
    
    if( isset( $input['comment_box'] ) )
      $new_input['comment_box'] = sanitize_text_field( $input['comment_box'] );
    
    if( isset( $input['sku'] ) )
      $new_input['sku'] = sanitize_text_field( $input['sku'] );
    
    if(!$hasError) {
			add_settings_error(
			 "wcmp_{$this->tab}_settings_name",
			 esc_attr( "wcmp_{$this->tab}_settings_admin_updated" ),
			 __('Product Settings Updated', $WCMp->text_domain),
			 'updated'
			);
    }
    
    return apply_filters("settings_{$this->tab}_tab_new_input", $new_input, $input);
  }

  /** 
   * Print the Section text
   */
  public function default_settings_section_left_pnl_info() {
    global $WCMp;
  }
  
  /** 
   * Print the Section text
   */
  public function default_settings_section_types_info() {
    global $WCMp;
  }
  /** 
   * Print the Section text
   */
  public function default_settings_section_type_option_info() {
    global $WCMp;
  }
  /** 
   * Print the Section text
   */
  public function default_settings_section_miscellaneous_info() {
    global $WCMp;
  }
  public function default_settings_section_policies_info() {
    global $WCMp;
  }
  public function default_settings_section_policiessettings_info() {
  	global $WCMp;
  }
 
}