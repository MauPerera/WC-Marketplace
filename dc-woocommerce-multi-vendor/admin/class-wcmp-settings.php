<?php
class WCMp_Settings {
  
  private $tabs = array();
  
  private $options;
  
  private $tabsection_general = array();
  
  /**
   * Start up
   */
  public function __construct() {
    // Admin menu
    add_action( 'admin_menu', array( $this, 'add_settings_page' ), 100 );
    add_action( 'admin_init', array( $this, 'settings_page_init' ) );
    
    // Settings tabs
    add_action('settings_page_general_tab_init', array(&$this, 'general_tab_init'), 10, 1);
    add_action('settings_page_product_tab_init', array(&$this, 'product_tab_init'), 10, 1);
    add_action('settings_page_capabilities_tab_init', array(&$this, 'capabilites_tab_init'), 10, 1);
    add_action('settings_page_pages_tab_init', array(&$this, 'pages_tab_init'), 10, 1);
    add_action('settings_page_payment_tab_init', array(&$this, 'payment_tab_init'), 10, 1);
    add_action('settings_page_frontend_tab_init', array(&$this, 'frontend_tab_init'), 10, 1);
    add_action('settings_page_to_do_list_tab_init', array(&$this, 'to_do_list_tab_init'), 10, 1);
    add_action('settings_page_notices_tab_init', array(&$this, 'notices_tab_init'), 10, 1);    
    add_action('settings_page_general_policies_tab_init', array(&$this, 'general_policies_tab_init'), 10, 2);
    add_action('settings_page_general_customer_support_details_tab_init', array(&$this, 'general_customer_support_details_tab_init'), 10, 2);
    add_action('settings_page_general_singleproductmultiseller_tab_init', array(&$this, 'general_singleproductmultiseller_tab_init'), 10, 2);
		add_action('settings_page_general_sellerreview_tab_init', array(&$this, 'general_sellerreview_tab_init'), 10, 2);    
  }
  
  public function delete_dc_product_vendor_plugin_db_version() {
  	delete_option('dc_product_vendor_plugin_db_version');  	
  }
  
  /**
   * Add options page   
   */
  public function add_settings_page() {
    global $WCMp;
    
    add_submenu_page(
        'woocommerce', 
        __('WCMp', $WCMp->text_domain), 
        __('WCMp', $WCMp->text_domain), 
        'manage_woocommerce', 
        'wcmp-setting-admin', 
        array( $this, 'create_wcmp_settings' ),
        $WCMp->plugin_url . 'assets/images/dualcube.png'
    );
    
    $this->tabs = $this->get_wcmp_settings_tabs();
    $this->tabsection_general = $this->get_wcmp_settings_tabsections_general();
  }
  
  function get_wcmp_settings_tabs() {
    global $WCMp;
    $tabs = apply_filters('wcmp_tabs', array(
        'general' => __('General', $WCMp->text_domain),
        'product' =>  __('Products', $WCMp->text_domain),
        'frontend' =>  __('Frontend', $WCMp->text_domain),
        'payment' =>  __('Payment', $WCMp->text_domain),
        'capabilities' =>  __('Capabilities', $WCMp->text_domain),
        'to_do_list' =>  __('To-do List', $WCMp->text_domain),
        'pages' =>  __('Pages', $WCMp->text_domain),
        
                
    ));
    return $tabs;
  }
  
  function get_wcmp_settings_tabsections_general() {
    global $WCMp;
    $tabsection_general = apply_filters('wcmp_tabsection_general', array(
        'general' => __('General', $WCMp->text_domain),
        'policies' =>  __('Policies', $WCMp->text_domain),
        'customer_support_details' =>  __('Customer Support', $WCMp->text_domain),
        'university' =>  __('University', $WCMp->text_domain),
        'vendor_notices' =>  __('Announcements', $WCMp->text_domain),
        'commission' =>  __('WCMp Commission', $WCMp->text_domain),
        'singleproductmultiseller' =>  __('Single Product Multiple Seller', $WCMp->text_domain),
        'sellerreview' =>  __('Vendor Review and Rating', $WCMp->text_domain),
       
    ));
    return $tabsection_general;
  }
  
  function get_saettings_tab_desc() {
  	global $WCMp;
    $tab_desc = apply_filters('wcmp_tabs_desc', array(
        'product' =>  __('Configure the "Product Add" page for vendors. Choose the features you want to show to your vendors.', $WCMp->text_domain),
        'frontend' =>  __('Configure which vendor details you want to reveal to your users', $WCMp->text_domain),
        'payment' =>  __('Manage everything about payments to vendors in this page - what to pay, how to pay and when to pay.', $WCMp->text_domain),
        'capabilities' =>  __('These are general sets of permissions for vendors. Note that these are global settings, and you may override these settings for an individual vendor from the vendor profile page. ', $WCMp->text_domain),
    ));
    return $tab_desc;
  }
  
  function wcmp_settings_tabs( $current = 'general' ) {
  	 global $WCMp;  
  	 $admin_url = get_admin_url();
  	 
    if ( isset ( $_GET['tab'] ) ) :
      $current = $_GET['tab'];
    else:
      $current = 'general';
    endif;
    if($current == 'general' ) {
			if( isset ($_GET['tab_section'] ) ) {
				$current_section = $_GET['tab_section'];
			}
			else {
				$current_section = 'general';
			}
			$sublinks = array();
			foreach( $this->tabsection_general as $tabsection => $sectionname ) :
				if($tabsection == 'university' || $tabsection == 'vendor_notices' || $tabsection == 'commission' ) {
					$admin_url = trailingslashit(get_admin_url());
					if( $tabsection == 'university' ) {
						$link_url = $admin_url.'edit.php?post_type=wcmp_university';
					}
					elseif( $tabsection == 'vendor_notices' ) {
						$link_url = $admin_url.'edit.php?post_type=wcmp_vendor_notice';						
					}
					elseif( $tabsection == 'commission' ) {
						$link_url = $admin_url.'edit.php?post_type=dc_commission';						
					}					
					$sublinks[] = "<li><a class='wcmp_sub_sction' href='$link_url'>$sectionname</a>  </li>"; 
				}
				else {
					if ( $tabsection == $current_section ) :      	       	
						 $sublinks[] = "<li><a class='current wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";         
					else :      	
						 $sublinks[] = "<li><a class='wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";         
					endif;
				}
			endforeach;
		}
    $links = array();
    foreach( $this->tabs as $tab => $name ) :
      if ( $tab == $current ) :      	       	
				 $links[] = "<a class='nav-tab nav-tab-active' href='?page=wcmp-setting-admin&tab=$tab'>$name</a>";         
      else :      	
				 $links[] = "<a class='nav-tab' href='?page=wcmp-setting-admin&tab=$tab'>$name</a>";         
      endif;
    endforeach;
    
    echo '<div class="icon32" id="dualcube_menu_ico"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $links as $link )
      echo $link;
    echo '</h2>';		
    
		foreach( $this->tabs as $tab => $name ) :
			if ( $tab == $current ) :
				printf( __( "<h2>%s Settings</h2>", $WCMp->text_domain) , $name);
			endif;
		endforeach;
    
    
    if($current == 'general' ) {
    	echo '<ul class="subsubsub wcmpsubtabadmin">';
    		foreach ( $sublinks as $sublink ){
					echo $sublink;
				}				
    	echo '</ul>';
			echo '<div style="width:100%; clear:both;">&nbsp;</div>';    	
    }
    
    $tab_desc = $this->get_saettings_tab_desc();
    foreach( $this->tabs as $tabd => $named ) :
      if ( $tabd == $current && !empty($tab_desc[$tabd]) ) :
        printf( __( "<h4 style=\'border-bottom: 1px solid rgb(215, 211, 211);padding-bottom: 21px;\'>%s</h4>", $WCMp->text_domain) , $tab_desc[$tabd]);
      endif;
    endforeach;
  }

  /**
   * Options page callback
   */
  public function create_wcmp_settings() {
    global $WCMp;
    ?>
    <div class="wrap">
      <?php $this->wcmp_settings_tabs(); ?>
      <?php
      $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'general' );
      if( $tab =='general' && isset($_GET['tab_section']) && $_GET['tab_section']!='general') {
      	$tab_section = $_GET['tab_section'];
      	$this->options = get_option( "wcmp_{$tab}_{$tab_section}_settings_name" );
      }
      else {
      	$this->options = get_option( "wcmp_{$tab}_settings_name" );
      }
      
      
      // This prints out all hidden setting errors
      if( $tab =='general' && isset($_GET['tab_section']) && $_GET['tab_section']!='general') {
      	settings_errors("wcmp_{$tab}_{$tab_section}_settings_name");
      }
      else {
      	settings_errors("wcmp_{$tab}_settings_name");
      }
      ?>
      <form class='wcmp_vendors_settings' method="post" action="options.php">
      <?php
        // This prints out all hidden setting fields
        if( $tab =='general' && isset($_GET['tab_section']) && $_GET['tab_section']!='general') {
        	
        	settings_fields( "wcmp_{$tab}_{$tab_section}_settings_group" );        
					do_action("wcmp_{$tab}_{$tab_section}_settings_before_submit");        
					do_settings_sections( "wcmp-{$tab}-{$tab_section}-settings-admin" );
					submit_button(); 
        }
        else {        	
					settings_fields( "wcmp_{$tab}_settings_group" );        
					do_action("wcmp_{$tab}_settings_before_submit");        
					do_settings_sections( "wcmp-{$tab}-settings-admin" );					
					if($tab == 'to_do_list' ) do_action("settings_page_{$tab}_tab_init", $tab);					
					if(isset($_GET['tab']) && $_GET['tab'] == 'to_do_list') { 
						 wp_enqueue_style('wcmp_admin_todo_list',  $WCMp->plugin_url.'assets/admin/css/admin-to_do_list.css', array(), $WCMp->version);
					} else {
						submit_button(); 
					}
        }
      ?>
      </form>
      <?php
      if( isset($_GET['tab']) && $_GET['tab'] == 'payment') {
      	if(wp_next_scheduled( 'paypal_masspay_cron_start' )) {
      		_e('<br><b>MassPay Sync</b><br>', $WCMp->text_domain);
					printf( __('Next MassPay cron @ %s', $WCMp->text_domain),  date('d/m/Y g:i:s A', wp_next_scheduled( 'paypal_masspay_cron_start' ))) ;
					printf( __('<br>Now the time is %s', $WCMp->text_domain), date('d/m/Y g:i:s A', time()));
				}
			} 
			?>
    </div>
    <?php
    do_action('dualcube_admin_footer');
  }

  /**
   * Register and add settings
   */
  public function settings_page_init() { 
    do_action('befor_settings_page_init');    
    // Register each tab settings
    foreach( $this->tabs as $tab => $name ) :    	
    	if($tab == 'to_do_list' ) continue;
      do_action("settings_page_{$tab}_tab_init", $tab);
      if($tab == 'general' ) {
    		foreach( $this->tabsection_general as $tabsection => $sectionname ) {
    			if($tabsection == 'general' || $tabsection == 'university' || $tabsection == 'vendor_notices' || $tabsection == 'commission' ) {
    				
    			}
					else {
						do_action("settings_page_{$tab}_{$tabsection}_tab_init", $tab, $tabsection);						
					}
    		}    		
    	}
    endforeach;    
    do_action('after_settings_page_init');
  }
  
  /**
   * Register and add settings fields
   */
  public function settings_field_init($tab_options) {
    global $WCMp;
    
    if(!empty($tab_options) && isset($tab_options['tab']) && isset($tab_options['ref']) && isset($tab_options['sections'])) {
      // Register tab options
      register_setting(
        "wcmp_{$tab_options['tab']}_settings_group", // Option group
        "wcmp_{$tab_options['tab']}_settings_name", // Option name
        array( $tab_options['ref'], "wcmp_{$tab_options['tab']}_settings_sanitize" ) // Sanitize
      );
      
      foreach($tab_options['sections'] as $sectionID => $section) {
          // Register section
        if(method_exists( $tab_options['ref'], "{$sectionID}_info" )) {
					add_settings_section(
							$sectionID, // ID
							$section['title'], // Title
							array( $tab_options['ref'], "{$sectionID}_info" ), // Callback
							"wcmp-{$tab_options['tab']}-settings-admin" // Page
					);
				} else {
					add_settings_section(
							$sectionID, // ID
							$section['title'], // Title
							array( $section['ref'], "{$sectionID}_info" ), // Callback
							"wcmp-{$tab_options['tab']}-settings-admin" // Page
					);
				}
        
        // Register fields
        if(isset($section['fields'])) {
          foreach($section['fields'] as $fieldID => $field) {
            if(isset($field['type'])) {
              $field['tab'] = $tab_options['tab'];
              $callbak = $this->get_field_callback_type($field['type']);
              if(!empty($callbak)) {
                add_settings_field(
                  $fieldID,
                  $field['title'],
                  array( $this, $callbak ),
                  "wcmp-{$tab_options['tab']}-settings-admin",
                  $sectionID,
                  $this->process_fields_args($field, $fieldID)
                );
              }
            }
          }
        }
      }
    }
  }
  
  
   /**
   * Register and add settings fields
   */
  public function settings_field_withsubtab_init($tab_options) {
    global $WCMp;
    
    
    
    if(!empty($tab_options) && isset($tab_options['tab']) && isset($tab_options['ref']) && isset($tab_options['sections']) && isset($tab_options['subsection'])) {    	
      // Register tab options
      register_setting(
        "wcmp_{$tab_options['tab']}_{$tab_options['subsection']}_settings_group", // Option group
        "wcmp_{$tab_options['tab']}_{$tab_options['subsection']}_settings_name", // Option name
        array( $tab_options['ref'], "wcmp_{$tab_options['tab']}_{$tab_options['subsection']}_settings_sanitize" ) // Sanitize
      );
      
      foreach($tab_options['sections'] as $sectionID => $section) {
          // Register section
        if(method_exists( $tab_options['ref'], "{$sectionID}_info" )) {
					add_settings_section(
							$sectionID, // ID
							$section['title'], // Title
							array( $tab_options['ref'], "{$sectionID}_info" ), // Callback
							"wcmp-{$tab_options['tab']}-{$tab_options['subsection']}-settings-admin" // Page
					);
				} else {
					add_settings_section(
							$sectionID, // ID
							$section['title'], // Title
							array( $section['ref'], "{$sectionID}_info" ), // Callback
							"wcmp-{$tab_options['tab']}-{$tab_options['subsection']}-settings-admin" // Page
					);
				}
        
        // Register fields
        if(isset($section['fields'])) {
          foreach($section['fields'] as $fieldID => $field) {
            if(isset($field['type'])) {
              $field['tab'] = $tab_options['tab'].'_'.$tab_options['subsection'];
              $callbak = $this->get_field_callback_type($field['type']);
              if(!empty($callbak)) {
                add_settings_field(
                  $fieldID,
                  $field['title'],
                  array( $this, $callbak ),
                  "wcmp-{$tab_options['tab']}-{$tab_options['subsection']}-settings-admin",
                  $sectionID,
                  $this->process_fields_args($field, $fieldID)
                );
              }
            }
          }
        }
      }
    }
  }
  
  

	/**
   * function process_fields_args
   * @param $fields
   * @param $fieldId
   * @return Array
   */
  function process_fields_args( $field, $fieldID ) {

    if( !isset($field['id'] ) ) {
      $field['id'] = $fieldID;
    }

    if( !isset($field['label_for'] ) ) {
      $field['label_for'] = $fieldID;
    }

    if( !isset($field['name'] ) ) {
      $field['name'] = $fieldID;
    }

    return $field;
  }
  
  function general_tab_init($tab) {
    global $WCMp;
    $WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
    new WCMp_Settings_Gneral($tab);
  }
  
  function general_policies_tab_init($tab,$subsection) {
    global $WCMp;
    $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
    new WCMp_Settings_Gneral_Policies($tab, $subsection);
  }
  
  function general_customer_support_details_tab_init($tab,$subsection) {
    global $WCMp;
    $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
    new WCMp_Settings_Gneral_Customer_support_Details($tab, $subsection);
  }

  function general_singleproductmultiseller_tab_init($tab,$subsection) {
    global $WCMp;
    $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
    new WCMp_Settings_Gneral_Singleproductmultiseller($tab, $subsection);
  }
  
  function general_sellerreview_tab_init($tab,$subsection) {
    global $WCMp;
    $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
    new WCMp_Settings_Gneral_Sellerreview($tab, $subsection);
  }  
  
  function product_tab_init($tab) {
  	global $WCMp;
    $WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
    new WCMp_Settings_Product($tab);
  }
  
	function capabilites_tab_init($tab) {
		global $WCMp;
		$WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
		new WCMp_Settings_Capabilities($tab);
	}
	
	function pages_tab_init($tab) {
		global $WCMp;
		$WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
		new WCMp_Settings_Pages($tab);
	}
	
	function notices_tab_init($tab) {
		global $WCMp;
		$WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
		new WCMp_Settings_Notices($tab);
	}	
	
	function payment_tab_init($tab) {
		global $WCMp;
		$WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
		new WCMp_Settings_Payment($tab);
	}
	
	function frontend_tab_init($tab) {
		global $WCMp;
		$WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
		new WCMp_Settings_Frontend($tab);
	}
	
	function to_do_list_tab_init($tab) {
		global $WCMp;
		$WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
		new WCMp_Settings_To_Do_List($tab);		
	}
	
  
  function get_field_callback_type($fieldType) {
    $callBack = '';
    switch($fieldType) {
      case 'input':
      case 'text':
      case 'email':
      case 'url':
        $callBack = 'text_field_callback';
        break;
        
      case 'hidden':
        $callBack = 'hidden_field_callback';
        break;
        
      case 'textarea':
        $callBack = 'textarea_field_callback';
        break;
        
      case 'wpeditor':
        $callBack = 'wpeditor_field_callback';
        break;
        
      case 'checkbox':
        $callBack = 'checkbox_field_callback';
        break;
        
      case 'radio':
        $callBack = 'radio_field_callback';
        break;
        
      case 'select':
        $callBack = 'select_field_callback';
        break;
        
      case 'upload':
        $callBack = 'upload_field_callback';
        break;
        
      case 'colorpicker':
        $callBack = 'colorpicker_field_callback';
        break;
        
      case 'datepicker':
        $callBack = 'datepicker_field_callback';
        break;
        
      case 'multiinput':
        $callBack = 'multiinput_callback';
        break;
        
      default:
        $callBack = '';
        break;
    }
    
    return $callBack;
  }
  
  /** 
   * Get the hidden field display
   */
  public function hidden_field_callback($field) {
    global $WCMp;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->hidden_input($field);
  }
  
  /** 
   * Get the text field display
   */
  public function text_field_callback($field) {
    global $WCMp;
    $field['dfvalue'] = isset( $field['dfvalue'] ) ? esc_attr( $field['dfvalue'] ) : '';
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : $field['dfvalue'];
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->text_input($field);
    
  }
  
  /** 
   * Get the text area display
   */
  public function textarea_field_callback($field) {
    global $WCMp;
    $field['value'] = isset( $field['value'] ) ? esc_textarea( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_textarea( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->textarea_input($field);
  }
  
  /** 
   * Get the wpeditor display
   */
  public function wpeditor_field_callback($field) {
    global $WCMp;
    $field['value'] = isset( $field['value'] ) ? ( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? ( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->wpeditor_input($field);
  }
  
  /** 
   * Get the checkbox field display
   */
  public function checkbox_field_callback($field) {
    global $WCMp;    
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['dfvalue'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : '';
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->checkbox_input($field);
  }
  
  /** 
   * Get the checkbox field display
   */
  public function radio_field_callback($field) {
    global $WCMp;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->radio_input($field);
  }
  
  /** 
   * Get the select field display
   */
  public function select_field_callback($field) {
    global $WCMp;
    $field['value'] = isset( $field['value'] ) ? esc_textarea( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_textarea( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->select_input($field);
  }
  
  /** 
   * Get the upload field display
   */
  public function upload_field_callback($field) {
    global $WCMp;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->upload_input($field);
  }
  
  /** 
   * Get the multiinput field display
   */
  public function multiinput_callback($field) {
    global $WCMp;
    $field['value'] = isset( $field['value'] ) ? $field['value'] : array();
    $field['value'] = isset( $this->options[$field['name']] ) ? $this->options[$field['name']] : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->multi_input($field);
  }
  
  /** 
   * Get the colorpicker field display
   */
  public function colorpicker_field_callback($field) {
    global $WCMp;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->colorpicker_input($field);
  }
  
  /** 
   * Get the datepicker field display
   */
  public function datepicker_field_callback($field) {
    global $WCMp;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
    $WCMp->wcmp_wp_fields->datepicker_input($field);
  }
  
}
