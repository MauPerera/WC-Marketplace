<?php
class WCMp_Settings_Gneral_Sellerreview {
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
                                                      "sellerreview" => array("title" =>  '', // Section one
                                                                                         "fields" => array(
                                                                                         	 								 "is_sellerreview" => array('title' => __('Enable Review ( for Vendor )', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_sellerreview', 'label_for' => 'is_sellerreview', 'name' => 'is_sellerreview', 'value' => 'Enable', 'desc' => __('If checked, user can rate vendor(s).',$WCMp->text_domain)), // Checkbox  
                                                                                         	 								 "is_sellerreview_varified" => array('title' => __('Enable Review ( by verified customers ).', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_sellerreview_varified', 'label_for' => 'is_sellerreview_varified', 'name' => 'is_sellerreview_varified', 'value' => 'Enable', 'desc' => __('If checked, only customers who have purchased from the vendor can rate them.',$WCMp->text_domain)), // Checkbox 
                                                                                                           )
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
  public function wcmp_general_sellerreview_settings_sanitize( $input ) {
    global $WCMp;
    $new_input = array();
    
    $hasError = false;
    
    if( isset( $input['is_sellerreview'] ) )
			$new_input['is_sellerreview'] = sanitize_text_field( $input['is_sellerreview'] );
		
		if( isset( $input['is_sellerreview_varified'] ) )
			$new_input['is_sellerreview_varified'] = sanitize_text_field( $input['is_sellerreview_varified'] );
		
		
    
    if(!$hasError) {
			add_settings_error(
			 "wcmp_{$this->tab}_{$this->subsection}_settings_name",
			 esc_attr( "wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated" ),
			 __('Vendor Review Rating Settings Updated', $WCMp->text_domain),
			 'updated'
			);
    }
    return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input , $input);
  }

   
  /** 
   * Print the Section text
   */
  public function sellerreview_info() {
    global $WCMp;
    
  }  
  
}
