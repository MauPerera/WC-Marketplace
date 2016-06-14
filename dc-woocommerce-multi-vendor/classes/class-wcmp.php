<?php
/**
 * WCMp Main Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		DualCube
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;

final class WCMp {

	public $plugin_url;

	public $plugin_path;

	public $version;

	public $token;
	
	public $text_domain;
	
	public $library;

	public $shortcode;

	public $admin;

	public $frontend;

	public $template;

	public $ajax;
	
	public $taxonomy;
	
	public $product;

	private $file;
	
	public $settings;
	
	public $wcmp_wp_fields;
	
	public $user;
	
	public $vendor_caps;
	
	public $vendor_dashboard;
	
	public $transaction;
	
	public $email;
	
	public $review_rating;

	public $more_product_array = array();
	
	public function __construct( $file ) {

		$this->file = $file;
		$this->plugin_url = trailingslashit( plugins_url( '', $plugin = $file ) );
		$this->plugin_path = trailingslashit( dirname( $file ) );
		$this->token = WCMp_PLUGIN_TOKEN;
		$this->text_domain = WCMp_TEXT_DOMAIN;
		$this->version = WCMp_PLUGIN_VERSION;
		$time_zone = get_option('timezone_string');
		if(!empty($time_zone)) {
			date_default_timezone_set($time_zone);	
		}
		// Intialize WCMp Widgets
		$this->init_custom_widgets();		
		// Intialize Crons
		$this->init_masspay_cron();		
		$dc_product_vendor_plugin_db_version = get_option('dc_product_vendor_plugin_db_version');
		if(!empty($dc_product_vendor_plugin_db_version)){
			if($dc_product_vendor_plugin_db_version <= '2.2.5') {
				delete_option('dc_product_vendor_plugin_db_version');
				delete_option('wcmp_vendor_orders_update');				
			}
		}
		
		// Intialize WCMp
		add_action( 'init', array( &$this, 'init' ) );
		
		// Intialize WCMp Emails
		add_filter( 'woocommerce_email_classes', array(&$this, 'wcmp_email_classes' ));		
	}
	
	function paypal_details() {
		$paypal_details = get_option('woocommerce_paypal_settings');		
	}
	
	/**
	 * Initialize plugin on WP init
	*/
	function init() {
		
		if(is_user_wcmp_pending_vendor(get_current_user_id()) || is_user_wcmp_rejected_vendor(get_current_user_id()) || is_user_wcmp_vendor(get_current_user_id())) show_admin_bar(false); 
		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		// Init library
		$this->load_class( 'library' );
		$this->library = new WCMp_Library();

		// Init main admin action class 
		$this->load_class( 'seller-review-rating' );
		$this->review_rating = new WCMp_Seller_Review_Rating();

		// Init ajax
		if( defined('DOING_AJAX') ) {
			$this->load_class( 'ajax' );
			$this->ajax = new  WCMp_Ajax();
		}
				
		// Init main admin action class 
		if ( is_admin() ) {
			$this->load_class( 'admin' );
			$this->admin = new WCMp_Admin();
		}
		
		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			// Init main frontend action class
			$this->load_class( 'frontend' );
			$this->frontend = new WCMp_Frontend();
			
			// Init main seller review and rating class
			
			// Init shortcode
			$this->load_class( 'shortcode' );
			$this->shortcode = new WCMp_Shortcode();

			// Init templates
			$this->load_class( 'template' );
			$this->template = new WCMp_Template();
			
			add_filter( 'template_include', array( $this, 'template_loader' ) );
		}
		
		// Init vendor action class
		$this->load_class( 'vendor-details' );
		
		// Init Calculate commission class
		$this->load_class( 'calculate-commission' );
		new WCMp_Calculate_Commission();
		
		
		
		// Init product vendor taxonomies
    $this->init_taxonomy();
		
		// Init product action class 
		$this->load_class('product');
		$this->product = new WCMp_Product();
		
		// Init email activity action class 
		$this->load_class('email');
		$this->email = new WCMp_Email();

		// WCMp Fields Lib
		$this->wcmp_wp_fields = $this->library->load_wp_fields();
		
		// Init custom capabilities
    $this->init_custom_capabilities();
		
		// Init user roles
    $this->init_user_roles();
    
    // Init product vendor custom post types
    $this->init_custom_post();
    
    // Init custom reports
    $this->init_custom_reports();
    
    // Init paypal masspay
    $this->init_paypal_masspay();
    
    // Init vendor dashboard
    $this->init_vendor_dashboard();
    
    // Init vendor coupon
    $this->init_vendor_coupon();
    
    // WCMp plugins loaded
    $this->wcmp_plugins_loaded();
    
    do_action( 'wcmp_init' );
    
    
	}
	
	
	function template_loader( $template ) {
	  global $WCMp;
	  if ( is_tax( 'dc_vendor_shop' )) {
	    $template = $this->template->locate_template('taxonomy-dc_vendor_shop.php');
	  }
	  return $template;
	}
	
	/**
   * Load Localisation files.
   *
   * Note: the first-loaded translation file overrides any following ones if the same translation is present
   *
   * @access public
   * @return void
   */
  public function load_plugin_textdomain() {
    $locale = apply_filters( 'plugin_locale', get_locale(), $this->token );
    load_textdomain( $this->text_domain, WP_LANG_DIR . "/dc-woocommerce-multi-vendor/wcmp-$locale.mo" );
    load_textdomain( $this->text_domain, $this->plugin_path . "/languages/wcmp-$locale.mo" );
  }

	public function load_class($class_name = '') {
		if ( '' != $class_name && '' != $this->token ) {
			require_once ( 'class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php' );
		} // End If Statement
	}// End load_class()
	
	/** Cache Helpers *********************************************************/

	/**
	 * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
	 *
	 * @access public
	 * @return void
	 */
	function nocache() {
		if (!defined('DONOTCACHEPAGE'))
			define("DONOTCACHEPAGE", "true");
		// WP Super Cache constant
	}
	
	/**
   * Init demo_plugin user capabilities.
   *
   * @access public
   * @return void
   */
  function init_user_roles() {
  	global $wpdb, $WCMp;
    $this->load_class( 'user' );
    $this->user = new WCMp_User();
    
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
  }

   /**
   * Init WCMp product vendor taxonomy.
   *
   * @access public
   * @return void
   */
  function init_taxonomy() {
    global $wpdb, $WCMp;
    
    $this->load_class( 'taxonomy' );
    $this->taxonomy = new WCMp_Taxonomy();
    
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
  }
  
  /**
   * Init WCMp product vendor post type.
   *
   * @access public
   * @return void
   */
  function init_custom_post() {
    global $wpdb, $WCMp;
    
    $this->load_class( 'post-commission' );
    new  WCMp_Commission();
    
    $this->load_class( 'post-transaction' );
    $this->transaction = new  WCMp_Transaction();
    
    $this->load_class( 'post-university' );
    new  WCMp_University();
    
    $this->load_class( 'post-notices' );
    new  WCMp_Notices();
    
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
  }
  
  /**
   * Init WCMp vendor reports.
   *
   * @access public
   * @return void
   */
  function init_custom_reports() {
  	global $wpdb, $WCMp;
  	
  	// Init custom report
    $this->load_class( 'report' );
		new WCMp_Report();
  	
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
  }
  
  /**
   * Init WCMp vendor widgets.
   *
   * @access public
   * @return void
   */
  function init_custom_widgets() {
  	global $wpdb, $WCMp;
  	
  	$this->load_class( 'widget-init' );
    new  WCMp_Widget_Init();
    
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
  }
  
   
  /**
   * Init WCMp vendor capabilities.
   *
   * @access public
   * @return void
   */
  function init_custom_capabilities() {
  	global $wpdb, $WCMp;
  	
  	$this->load_class('capabilities');
    $this->vendor_caps = new  WCMp_Capabilities();
    
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
  }
  
  /**
   * Init WCMp vendor MassPay.
   *
   * @access public
   * @return void
   */
  function init_paypal_masspay() {
  	global $wpdb, $WCMp;
  	
  	$this->load_class('paypal-masspay');
    $this->paypal_masspay = new  WCMp_Paypal_Masspay();
    
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
  }
  
  
  /**
	 * Init WCMp Dashboard Function
	 *
	 * @access public
	 * @return void
	*/	
  function init_vendor_dashboard() {
  	global $wpdb, $WCMp;
  	
  	$this->load_class('vendor-dashboard');
    $this->vendor_dashboard = new  WCMp_Admin_Dashboard();
    
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
  }
  
  
	/**
	 * Init Masspay Cron
	 *
	 * @access public
	 * @return void
	*/	
	function init_masspay_cron() {
		global $WCMp;
		add_filter( 'cron_schedules', array($this, 'cron_add_weekly') );
		$abc = wp_get_schedules();
  	$this->load_class('masspay-cron');
    $this->masspay_cron = new  WCMp_MassPay_Cron();
    
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
	}
	
	
	/**
	 * Init Vendor Coupon
	 *
	 * @access public
	 * @return void
	*/	
	function init_vendor_coupon() {
		global $wpdb, $WCMp;
  	
  	$this->load_class('coupon');
    new  WCMp_Coupon();
    
    register_activation_hook( __FILE__, 'flush_rewrite_rules' );
	}
	
	
	/**
	 * Add weekly and monthly corn schedule
	 *
	 * @access public
	 * @param schedules array
	 * @return schedules array
	*/	
	function cron_add_weekly($schedules) {
		$schedules['weekly'] = array(
		'interval' => 604800,
		'display' => __('Every 7 Days',$this->text_domain)
		);
		$schedules['monthly'] = array(
			'interval' => 2592000,
			'display' => __('Every 1 Month', $this->text_domain)
		);
		$schedules['fortnightly'] = array(
			'interval' => 1296000,
			'display' => __('Every 15 Days', $this->text_domain)
		);
		return $schedules;
	}
	
	/**
	 * Register all emails for vendor
	 *
	 * @access public
	 * @return array
	*/	
	function wcmp_email_classes($emails) {
		
		include( 'emails/class-wcmp-email-vendor-new-account.php' );
		include( 'emails/class-wcmp-email-admin-new-vendor-account.php' );
		include( 'emails/class-wcmp-email-approved-vendor-new-account.php' );
		include( 'emails/class-wcmp-email-rejected-vendor-new-account.php' );
		include( 'emails/class-wcmp-email-vendor-new-order.php' );
		include( 'emails/class-wcmp-email-vendor-notify-shipped.php' );
		include( 'emails/class-wcmp-email-vendor-new-product-added.php' );
		include( 'emails/class-wcmp-email-admin-added-new-product-to-vendor.php' );
		include( 'emails/class-wcmp-email-vendor-new-commission-transaction.php' );
		include( 'emails/class-wcmp-email-vendor-direct-bank.php' );
		include( 'emails/class-wcmp-email-admin-withdrawal-request.php' );
		
		$emails['WC_Email_Vendor_New_Account'] = new WC_Email_Vendor_New_Account();
		$emails['WC_Email_Admin_New_Vendor_Account'] = new WC_Email_Admin_New_Vendor_Account();
		$emails['WC_Email_Approved_New_Vendor_Account'] = new WC_Email_Approved_New_Vendor_Account();
		$emails['WC_Email_Rejected_New_Vendor_Account'] = new WC_Email_Rejected_New_Vendor_Account();
		$emails['WC_Email_Vendor_New_Order'] = new WC_Email_Vendor_New_Order();
		$emails['WC_Email_Notify_Shipped'] = new WC_Email_Notify_Shipped();
		$emails['WC_Email_Vendor_New_Product_Added'] = new WC_Email_Vendor_New_Product_Added();
		$emails['WC_Email_Admin_Added_New_Product_to_Vendor'] = new WC_Email_Admin_Added_New_Product_to_Vendor();
		$emails['WC_Email_Vendor_Commission_Transactions'] = new WC_Email_Vendor_Commission_Transactions();  
		$emails['WC_Email_Vendor_Direct_Bank'] = new WC_Email_Vendor_Direct_Bank();  
		$emails['WC_Email_Admin_Widthdrawal_Request'] = new WC_Email_Admin_Widthdrawal_Request();
		
		return $emails;
	}
	
	
	/**
   * On activation, include the installer and run it.
   *
   * @access public
   * @return void
   */
  function wcmp_plugins_loaded() {
  	global $WCMp, $wpdb;

  	if ( ! empty($wpdb->charset) )
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";      
	if ( ! empty($wpdb->collate) )
		$charset_collate .= " COLLATE $wpdb->collate";	
	$migs = array();
	
	// Create wcmp table
	
	$migs[] = "
		CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."wcmp_vendor_orders` (
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

	$migs[] = "
		CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."wcmp_products_map` (
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


  	$table_name = $wpdb->prefix.'wcmp_products_map';
  	$is_product_sync = get_option('is_wcmp_product_sync_with_multivendor');
  	if(empty($is_product_sync)) {
  		$args_multi_vendor = array (
  			'posts_per_page'   => -1,				
				'post_type'        => 'product',				
				'post_status'      => 'publish',
				'suppress_filters' => true   			
  		);
  		$post_array = get_posts($args_multi_vendor);
  		foreach( $post_array as $product_post ) {
  			$results = $wpdb->get_results("select * from {$table_name} where product_title = '{$product_post->post_title}' ");
  			if(is_array($results) && (count($results) > 0) ) {
					$id_of_similar = $results[0]->ID;
					$product_ids = $results[0]->product_ids;
					$product_ids_arr = explode(',',$product_ids);					
					if(is_array($product_ids_arr) && in_array($product_post->ID, $product_ids_arr) ) {
					
					}
					else {
						$product_ids = $product_ids.','.$product_post->ID;
						$wpdb->query("update {$table_name} set product_ids = '{$product_ids}' where ID = {$id_of_similar}");
					}											
				}
				else {
					$wpdb->query("insert into {$table_name} set product_title='{$product_post->post_title}', product_ids = '{$product_post->ID}' ");					
				}  			
  		}
			update_option('is_wcmp_product_sync_with_multivendor',1);  		
  	} 
  	//delete_option('dc_product_vendor_plugin_db_version');
  	$previous_plugin_version = get_option('dc_product_vendor_plugin_db_version');
  	if(!$previous_plugin_version || $previous_plugin_version < $WCMp->version) {
  		
  		$prev_general = get_option('dc_general_settings_name');
  		$prev_product = get_option('dc_product_settings_name');
  		$prev_capability = get_option('dc_capabilities_settings_name'); 
  		$prev_pages = get_option('dc_pages_settings_name');
  		$prev_payment = get_option('dc_payment_settings_name');
  		
  		$new_general = $new_product = $new_capability = $new_pages = $new_payment = $new_frontend = array();
  		
  		$new_payment = $prev_payment;
  		
  		if(!empty($prev_general)) {
  			
				if(isset($prev_general['enable_registration'])) {
					$new_general['enable_registration'] = 'Enable';
				} 
				
				if(isset($prev_general['approve_vendor_manually'])) {
					$new_general['approve_vendor_manually'] = 'Enable';
				} 
				
				if(isset($prev_general['notify_configure_vendor_store']))  {
					$new_general['notify_configure_vendor_store'] = $prev_general['notify_configure_vendor_store'];
				} 
				
				if(isset($prev_general['default_commission'])) {
					$new_payment['default_commission'] = $prev_general['default_commission'];
				} 
				
				if(isset($prev_general['commission_type'])) {
					$new_payment['commission_type'] = $prev_general['commission_type'];
				} 
				
				if(isset($prev_general['commission_include_coupon'])) {
					$new_payment['commission_include_coupon'] = $prev_general['commission_include_coupon'];
				} 
				
				if(isset($prev_general['sold_by_catalog'])) {
					$new_frontend['sold_by_catalog'] =  $prev_general['sold_by_catalog'];
				} 
				
				if(isset($prev_general['catalog_colorpicker'])) {
					$new_frontend['catalog_colorpicker'] =  $prev_general['catalog_colorpicker'];
				} 
				
				if(isset($prev_general['catalog_hover_colorpicker'])) {
					$new_frontend['catalog_hover_colorpicker'] =  $prev_general['catalog_hover_colorpicker'];
				}
				
				if(isset($prev_general['sold_by_cart_and_checkout'])) {
					$new_frontend['sold_by_cart_and_checkout'] =  $prev_general['sold_by_cart_and_checkout'];
				} 
				
				if(isset($prev_general['sold_by_text'])) {
					$new_frontend['sold_by_text'] =  $prev_general['sold_by_text'];
				} 
				
				if(isset($prev_general['block_vendor_desc'])) {
					$new_frontend['block_vendor_desc'] =  $prev_general['block_vendor_desc'];
				} 
  		}
  		
  		if(!empty($prev_capability)) {
  			
  			$new_capability = $prev_capability;  			
  			if(isset($new_capability['give_tax'])) {
  				$new_payment['give_tax'] = $new_capability['give_tax'];
  				unset($new_capability['give_tax']);
  			}  			
  			if(isset($new_capability['give_shipping'])) {
  				$new_payment['give_shipping'] = $new_capability['give_shipping'];
  				unset($new_capability['give_shipping']);
  			}
  			if($previous_plugin_version <= '2.3.3' ) {
  				$new_capability['is_hide_option_show'] = 'Enable'; 	
  			}
  		}
  		
  		if(!empty($prev_product)) {
  			update_option('wcmp_product_settings_name', $prev_product);
  		}
  		
  		if(!empty($prev_pages)) {
  			update_option('wcmp_pages_settings_name', $prev_pages);
  		}
  		
  		if(!empty($new_general)) {
  			update_option('wcmp_general_settings_name', $new_general);
  		}
  		
  		if(!empty($new_capability)) {
  			update_option('wcmp_capabilities_settings_name', $new_capability);
  		}
  		
  		if(!empty($new_payment)) {
  			update_option('wcmp_payment_settings_name', $new_payment);
  		}
  		
  		if(!empty($new_frontend)) {
  			update_option('wcmp_frontend_settings_name', $new_frontend);
  		}
  		
  		delete_option('dc_general_settings_name');
  		delete_option('dc_product_settings_name');
  		delete_option('dc_capabilities_settings_name');
  		delete_option('dc_payment_settings_name');
  		delete_option('dc_pages_settings_name');
  		
			$vendors = get_wcmp_vendors();
			
			if ( ! empty( $vendors ) ) {
				foreach( $vendors as $vendor ) {
					$vendorusers = new WP_User( $vendor->id );
					$vendorusers->remove_cap('manage_woocommerce');
					$vendor_products = $vendor->get_products();
					if(!empty($vendor_products)) {
						foreach($vendor_products as $vendor_product) {
							wp_update_post(array('ID' => $vendor_product->ID, 'post_author' => $vendor->id));
							$product_obj = wc_get_product( $vendor_product->ID );
							if ( $product_obj->is_type( 'variable' ) ) {
								$childrens = $product_obj->get_children();
								foreach ( $childrens as $child_id ) {
									wp_update_post(array('ID' => $child_id, 'post_author' => $vendor->id));
								}
							}
						}
					}  				
				}
			}
				
			$vendor_role = get_role( 'dc_vendor' );
			$vendor_role->remove_cap( 'manage_woocommerce' );
			$wcmp_pages = get_option('wcmp_pages_settings_name');
			
			$page_slug = 'wcmp_withdrawal_request';
			$page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$page_slug' LIMIT 1;");
			if (!$page_found ) {
				$page_data = array(
						'post_status' 		=> 'publish',
						'post_type' 		=> 'page',
						'post_author' 		=> 1,
						'post_name' 		=> $page_slug,
						'post_title' 		=> __('Withdrawal Request Status', $WCMp->text_domain),
						'post_content' 		=> '[transaction_thankyou]',
						'comment_status' 	=> 'closed'
				);
				$transaction_withdrawal_page_id = wp_insert_post( $page_data );
				update_option( 'wcmp_product_vendor_transaction_widthdrawal_page_id', $transaction_withdrawal_page_id );
				$wcmp_pages['vendor_transaction_thankyou'] = $transaction_withdrawal_page_id;
			}
			$page_slug = 'wcmp_transaction_details';
			$page_foundd = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$page_slug' LIMIT 1;");
			if (!$page_foundd ) {
				$page_data = array(
						'post_status' 		=> 'publish',
						'post_type' 		=> 'page',
						'post_author' 		=> 1,
						'post_name' 		=> $page_slug,
						'post_title' 		=> __('Transaction Details', $WCMp->text_domain),
						'post_content' 		=> '[transaction_details]',
						'comment_status' 	=> 'closed'
				);
				$transaction_details_page_id = wp_insert_post( $page_data );
				update_option( 'wcmp_product_vendor_transaction_details_page_id', $transaction_details_page_id );
				$wcmp_pages['vendor_transaction_detail'] = $transaction_details_page_id;
			}		
			
			$page_slug = 'wcmp_vendor_policies';
			$page_foundd = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$page_slug' LIMIT 1;");
			if (!$page_foundd ) {
				$page_data = array(
						'post_status' 		=> 'publish',
						'post_type' 		=> 'page',
						'post_author' 		=> 1,
						'post_name' 		=> $page_slug,
						'post_title' 		=> __('Vendor Policies', $WCMp->text_domain),
						'post_content' 		=> '[vendor_policies]',
						'comment_status' 	=> 'closed'
				);
				$policy_page_id = wp_insert_post( $page_data );
				update_option( 'wcmp_product_vendor_policies_page_id', $policy_page_id );
				$wcmp_pages['vendor_policies'] = $policy_page_id;
			}		
			
			$page_slug = 'wcmp_vendor_billing';
			$page_foundd = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$page_slug' LIMIT 1;");
			if (!$page_foundd ) {
				$page_data = array(
						'post_status' 		=> 'publish',
						'post_type' 		=> 'page',
						'post_author' 		=> 1,
						'post_name' 		=> $page_slug,
						'post_title' 		=> __('Vendor Billing', $WCMp->text_domain),
						'post_content' 		=> '[vendor_billing]',
						'comment_status' 	=> 'closed'
				);
				$vendor_billing_page_id = wp_insert_post( $page_data );
				update_option( 'wcmp_product_vendor_billing_page_id', $vendor_billing_page_id );
				$wcmp_pages['vendor_billing'] = $vendor_billing_page_id;
			}
			
			$page_slug = 'wcmp_vendor_shipping';
			$page_foundd = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$page_slug' LIMIT 1;");
			if (!$page_foundd ) {
				$page_data = array(
						'post_status' 		=> 'publish',
						'post_type' 		=> 'page',
						'post_author' 		=> 1,
						'post_name' 		=> $page_slug,
						'post_title' 		=> __('Vendor Shipping', $WCMp->text_domain),
						'post_content' 		=> '[vendor_shipping_settings]',
						'comment_status' 	=> 'closed'
				);
				$vendor_shipping_page_id = wp_insert_post( $page_data );
				update_option( 'wcmp_product_vendor_shipping_page_id', $vendor_shipping_page_id );
				$wcmp_pages['vendor_shipping'] = $vendor_shipping_page_id;
			}
			
			
			$page_slug = 'wcmp_vendor_report';
			$page_foundd = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$page_slug' LIMIT 1;");
			if (!$page_foundd ) {
				$page_data = array(
						'post_status' 		=> 'publish',
						'post_type' 		=> 'page',
						'post_author' 		=> 1,
						'post_name' 		=> $page_slug,
						'post_title' 		=> __('Vendor Report', $WCMp->text_domain),
						'post_content' 		=> '[vendor_report]',
						'comment_status' 	=> 'closed'
				);
				$vendor_report_page_id = wp_insert_post( $page_data );
				update_option( 'wcmp_product_vendor_report_page_id', $vendor_report_page_id );
				$wcmp_pages['vendor_report'] = $vendor_report_page_id;
			}
			
			$page_slug = 'wcmp_vendor_widthdrawals';
			$page_foundd = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$page_slug' LIMIT 1;");
			if (!$page_foundd ) {
				$page_data = array(
						'post_status' 		=> 'publish',
						'post_type' 		=> 'page',
						'post_author' 		=> 1,
						'post_name' 		=> $page_slug,
						'post_title' 		=> __('Vendor Widthdrawals', $WCMp->text_domain),
						'post_content' 		=> '[vendor_widthdrawals]',
						'comment_status' 	=> 'closed'
				);
				$vendor_widthdrawals_page_id = wp_insert_post( $page_data );
				update_option( 'wcmp_product_vendor_widthdrawals_page_id', $vendor_widthdrawals_page_id );
				$wcmp_pages['vendor_widthdrawals'] = $vendor_widthdrawals_page_id;
			}
			
			$page_slug = 'wcmp_vendor_university';
			$page_foundd = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$page_slug' LIMIT 1;");
			if (!$page_foundd ) {
				$page_data = array(
						'post_status' 		=> 'publish',
						'post_type' 		=> 'page',
						'post_author' 		=> 1,
						'post_name' 		=> $page_slug,
						'post_title' 		=> __('Vendor University', $WCMp->text_domain),
						'post_content' 		=> '[vendor_university]',
						'comment_status' 	=> 'closed'
				);
				$vendor_university_page_id = wp_insert_post( $page_data );
				update_option( 'wcmp_product_vendor_university_page_id', $vendor_university_page_id );
				$wcmp_pages['vendor_university'] = $vendor_university_page_id;
			}
			
			$page_slug = 'wcmp_vendor_announcements';
			$page_foundd = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'wcmp_vendor_messages' LIMIT 1;");
			$page_foundd2 = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'wcmp_vendor_announcements' LIMIT 1;");
			if (!$page_foundd && !$page_foundd2 ) {
				$page_data = array(
						'post_status' 		=> 'publish',
						'post_type' 		=> 'page',
						'post_author' 		=> 1,
						'post_name' 		=> $page_slug,
						'post_title' 		=> __('Vendor Announcements', $WCMp->text_domain),
						'post_content' 		=> '[vendor_announcements]',
						'comment_status' 	=> 'closed'
				);
				$vendor_announcements_page_id = wp_insert_post( $page_data );
				update_option( 'wcmp_product_vendor_announcements_page_id', $vendor_announcements_page_id );
				$wcmp_pages['vendor_announcements'] = $vendor_announcements_page_id;
			}
			if( $page_foundd && !$page_foundd2) {
				wp_update_post(array('ID' => $wcmp_pages['vendor_messages'], 'post_content' => '[vendor_announcements]', 'post_name'=> 'vendor_announcements', 'post_title' => 'Vendor Announcements' ));
				$wcmp_pages['vendor_announcements'] = $wcmp_pages['vendor_messages'];
				unset($wcmp_pages['vendor_messages']);				
			}			
			wp_update_post(array('ID' => $wcmp_pages['vendor_dashboard'], 'post_content' => '[vendor_dashboard]'));
			wp_update_post(array('ID' => $wcmp_pages['view_order'], 'post_content' => '[vendor_orders]'));
			
			update_option('wcmp_pages_settings_name', $wcmp_pages);		
			
			$WCMp_Calculate_Commission_obj = new WCMp_Calculate_Commission();	
			
			$vendors = get_wcmp_vendors();
			
			if ( ! empty( $vendors ) ) {
				$vendor_orders_array = array(); 
				foreach( $vendors as $vendor ) {
					$vendor_orders = $vendor->get_orders();					
					if(!empty($vendor_orders)) {
						foreach($vendor_orders as $commission_id => $order_id) {							
							$vendor_shipping_array = get_post_meta($order_id, 'dc_pv_shipped', true);
							$order = new WC_Order( $order_id );							
							$commission_array = array();
							$mark_ship = false;
							$items = $order->get_items( 'line_item' );							
							foreach( $items as $order_item_id => $item ) {								
								$comm_pro_id = $product_id = $order->get_item_meta( $order_item_id, '_product_id', true );								
								$variation_id = $order->get_item_meta( $order_item_id, '_variation_id', true );								
								if($variation_id) $comm_pro_id = $variation_id;								
								if( $product_id ) {									
									$product_vendors = get_wcmp_product_vendors($product_id);									
									if( $product_vendors ) {
										if(isset($product_vendors->id)) {
											if(isset($vendor_shipping_array) && !empty($vendor_shipping_array)) {
												if(in_array($product_vendors->id, $vendor_shipping_array)) {
													$mark_ship = true;
												}
												else {
													$mark_ship = 0;
												}
											}
											else {
												$mark_ship = 0;
											}
											$item_commission = $WCMp_Calculate_Commission_obj->get_item_commission( $comm_pro_id, $comm_pro_id, $item, $order_id, $order_item_id );
											$vendor_shipping_costs = $vendor->get_wcmp_vendor_shipping_total( $order_id, $item );
											$item_shipping = ($vendor_shipping_costs['shipping_amount'] + $vendor_shipping_costs['shipping_tax']);
											$item_tax = get_metadata( 'order_item', $order_item_id, '_line_tax', true );
											$commission_vendor_term_id = get_post_meta($commission_id,'_commission_vendor', true);											
											$vendor_term_id = get_user_meta($product_vendors->id,'_vendor_term_id', true);											
											if($commission_vendor_term_id == $vendor_term_id ) {
												$vendor_orders_array[] = array($order_id, $commission_id, $product_vendors->id, $mark_ship, $order_item_id, $comm_pro_id, $order->order_date, $item_commission, $item_shipping, $item_tax);
											}
										}
									}
								}
							}
						}
					}
				}
				if(!empty($vendor_orders_array)) {	
					usort($vendor_orders_array, function($a, $b) {	return $a[0] - $b[0];	});
					
					if(!get_option('wcmp_vendor_orders_update')) {
						foreach($vendor_orders_array as $vendor_orders) {							
							$insert_query = $wpdb->query($wpdb->prepare("INSERT INTO `{$wpdb->prefix}wcmp_vendor_orders` ( order_id, commission_id, vendor_id, shipping_status, order_item_id, product_id, created, commission_amount, shipping, tax )
														 VALUES
														 ( %d, %d, %d, %s, %d, %d, %s, %s, %s, %s )", $vendor_orders[0], $vendor_orders[1], $vendor_orders[2], $vendor_orders[3], $vendor_orders[4], $vendor_orders[5], $vendor_orders[6], $vendor_orders[7], $vendor_orders[8], $vendor_orders[9]));
							
						}
					}
					update_option('wcmp_vendor_orders_update', 1);
				}
			}
			update_option('dc_product_vendor_plugin_db_version', $WCMp->version );
  	}
	}
	
}
