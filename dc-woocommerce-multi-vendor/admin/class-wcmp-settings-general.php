<?php
class WCMp_Settings_Gneral {
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
                                                      "venor_approval_settings_section" => array("title" =>  '', // Section one
                                                                                         "fields" => array(                                                                                       	 								 
                                                                                         	 								 "enable_registration" => array('title' => __('New Vendor Registration', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'enable_registration', 'label_for' => 'enable_registration', 'desc' => __('Allow people to sign up as a vendor. Leave it unchecked if you want to keep your site an invite only marketpace.', $WCMp->text_domain), 'name' => 'enable_registration', 'value' => 'Enable'), // Checkbox
                                                                                                           "approve_vendor_manually" => array('title' => __('Approve Vendors Manually', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'approve_vendor_manually', 'label_for' => 'approve_vendor_manually', 'desc' => __('If left unchecked, every vendor applicant will be auto-approved, which is not a recommended setting.', $WCMp->text_domain), 'name' => 'approve_vendor_manually', 'value' => 'Enable'), // Checkbox
                                                                                                           "notify_configure_vendor_store" => array('title' => __('Add Vendor Notify Section', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'notify_configure_vendor_store', 'label_for' => 'notify_configure_vendor_store', 'desc' => __('Add a section in the vendor dashboard to notify vendors if they have not configured stores properly.', $WCMp->text_domain), 'name' => 'notify_configure_vendor_store', 'value' => 'Enable'), // Checkbox
                                                                                                           "is_university_on" => array('title' => __('Enable University', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_university_on', 'label_for' => 'is_university_on', 'name' => 'is_university_on', 'value' => 'Enable', 'desc' => __('Check this box to enable "University" section in the vendor dashboard.', $WCMp->text_domain)), // Checkbox
                                                                                                           ),
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
  public function wcmp_general_settings_sanitize( $input ) {
    global $WCMp;
    $new_input = array();
    
    $hasError = false;
    
    if( isset( $input['enable_registration'] ) )
      $new_input['enable_registration'] = sanitize_text_field( $input['enable_registration'] );
    
    if( isset( $input['notify_configure_vendor_store'] ) )
      $new_input['notify_configure_vendor_store'] = sanitize_text_field( $input['notify_configure_vendor_store'] );               
    
    if( isset( $input['approve_vendor_manually'] ) )
      $new_input['approve_vendor_manually'] = sanitize_text_field( $input['approve_vendor_manually'] );
    
		if( isset( $input['is_university_on'] ) )
			$new_input['is_university_on'] = sanitize_text_field( $input['is_university_on'] );
    
    if(!$hasError) {
			add_settings_error(
			 "wcmp_{$this->tab}_settings_name",
			 esc_attr( "wcmp_{$this->tab}_settings_admin_updated" ),
			 __('General Settings Updated', $WCMp->text_domain),
			 'updated'
			);
    }
    return apply_filters("settings_{$this->tab}_tab_new_input", $new_input , $input);
  }

   
  /** 
   * Print the Section text
   */
  public function venor_approval_settings_section_info() {
    global $WCMp;
  }
  
   /** 
   * Print the Section text
   */
  public function venor_frontend_settings_section_info() {
    global $WCMp;
    printf( __( 'These features are now available in the %sFrontend%s tab.', $WCMp->text_domain ), '<a target="_blank" href="?page=wcmp-setting-admin&tab=frontend">', '</a>' );
  }
  
}