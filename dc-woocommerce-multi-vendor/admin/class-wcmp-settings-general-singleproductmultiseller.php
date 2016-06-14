<?php
class WCMp_Settings_Gneral_Singleproductmultiseller {
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
                                                      "singleproductmultiseller" => array("title" =>  '', // Section one
                                                                                         "fields" => array(
                                                                                         	 								 "is_singleproductmultiseller" => array('title' => __('Show Multiple Sellers in Single Product', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_singleproductmultiseller', 'label_for' => 'is_singleproductmultiseller', 'name' => 'is_singleproductmultiseller', 'value' => 'Enable', 'desc' => __('If checked, user can see the multiple vendors of same product in single product page if more then one vendors exits.',$WCMp->text_domain)), // Checkbox                                                                                                                                                                              
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
  public function wcmp_general_singleproductmultiseller_settings_sanitize( $input ) {
    global $WCMp;
    $new_input = array();
    
    $hasError = false;
    
    if( isset( $input['is_singleproductmultiseller'] ) )
			$new_input['is_singleproductmultiseller'] = sanitize_text_field( $input['is_singleproductmultiseller'] );
		
		
    
    if(!$hasError) {
			add_settings_error(
			 "wcmp_{$this->tab}_{$this->subsection}_settings_name",
			 esc_attr( "wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated" ),
			 __('Single Product Multi Sellers Settings Updated', $WCMp->text_domain),
			 'updated'
			);
    }
    return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input , $input);
  }

   
  /** 
   * Print the Section text
   */
  public function singleproductmultiseller_info() {
    global $WCMp;
    
  }  
  
}
