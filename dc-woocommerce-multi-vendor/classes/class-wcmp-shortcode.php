<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class 		WCMp Shortcode Class
 *
 * @version	  2.2.0
 * @package		WCMp
 * @author 		DualCube
 */
class WCMp_Shortcode {

	public $list_product;

	public function __construct() {
		// Vendor Dashboard
		add_shortcode('vendor_dashboard', array(&$this, 'vendor_dashboard_shortcode'));
		// Shop Settings
		add_shortcode('shop_settings', array(&$this, 'shop_settings_shortcode'));
		// Shop Settings
		add_shortcode('vendor_policies', array(&$this, 'vendor_policies_shortcode'));
		// Shop Settings
		add_shortcode('vendor_billing', array(&$this, 'vendor_billing_shortcode'));
		// Shop Settings
		add_shortcode('vendor_widthdrawals', array(&$this, 'vendor_widthdrawals_shortcode'));
		// Shop Settings
		add_shortcode('vendor_university', array(&$this, 'vendor_university_shortcode'));
		
		// Shop Settings
		add_shortcode('vendor_announcements', array(&$this, 'vendor_announcements_shortcode'));
		
		// Vendor Report
		add_shortcode('vendor_report', array(&$this, 'vendor_report_shortcode'));
		// Vendor Orders
		add_shortcode('vendor_orders', array(&$this, 'vendor_orders_shortcode'));
		// Vendor Order Detail
		add_shortcode('vendor_order_detail', array(&$this, 'vendor_order_detail_shortcode'));
		
		// Vendor Coupons
		add_shortcode('vendor_coupons', array(&$this, 'vendor_coupons_shortcode'));
		// Vendor Shiopping
		add_shortcode('vendor_shipping_settings', array(&$this, 'vendor_shipping_settings_shortcode'));
		
		add_shortcode('transaction_thankyou', array(&$this, 'vendor_transaction_thankyou'));
		add_shortcode('transaction_details', array(&$this, 'vendor_transaction_details'));
		
		
		// Recent Products 
		add_shortcode( 'dc_recent_products', array(&$this, 'wcmp_show_recent_products'));
		// Products by vendor
		add_shortcode( 'dc_products', array(&$this, 'wcmp_show_products'));
		// Featured products by vendor
		add_shortcode( 'dc_featured_products', array(&$this, 'wcmp_show_featured_products'));
		// Sale products by vendor
		add_shortcode( 'dc_sale_products', array(&$this, 'wcmp_show_sale_products'));
		// Top Rated products by vendor 
		add_shortcode( 'dc_top_rated_products', array(&$this, 'wcmp_show_top_rated_products'));
		// Best Selling product 
		add_shortcode( 'dc_best_selling_products', array(&$this, 'wcmp_show_best_selling_products'));
		// List products in a category shortcode
		add_shortcode( 'dc_product_category', array(&$this, 'wcmp_show_product_category'));
		// List of paginated vendors 
		add_shortcode( 'dc_vendorslist', array(&$this, 'wcmp_show_vendorslist' ) ); 
		
		
		
		// Recent Products 
		add_shortcode( 'wcmp_recent_products', array(&$this, 'wcmp_show_recent_products'));
		// Products by vendor
		add_shortcode( 'wcmp_products', array(&$this, 'wcmp_show_products'));
		//Featured products by vendor
		add_shortcode( 'wcmp_featured_products', array(&$this, 'wcmp_show_featured_products'));
		// Sale products by vendor
		add_shortcode( 'wcmp_sale_products', array(&$this, 'wcmp_show_sale_products'));
		// Top Rated products by vendor 
		add_shortcode( 'wcmp_top_rated_products', array(&$this, 'wcmp_show_top_rated_products'));
		// Best Selling product 
		add_shortcode( 'wcmp_best_selling_products', array(&$this, 'wcmp_show_best_selling_products'));
		// List products in a category shortcode
		add_shortcode( 'wcmp_product_category', array(&$this, 'wcmp_show_product_category'));
		// List of paginated vendors 
		add_shortcode( 'wcmp_vendorslist', array(&$this, 'wcmp_show_vendorslist' ) ); 
	}
	
	/**
	 * Vendor Shipping Settings
	 *
	 * @return void
	 */
	function 	vendor_announcements_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-announcements');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Announcements_Shortcode', 'output'));
	}
	
	
	/**
	 * Vendor Shipping Settings
	 *
	 * @return void
	 */
	function 	vendor_university_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-university');
		return $this->shortcode_wrapper(array('WCMp_Vendor_University_Shortcode', 'output'));
	}
	
	/**
	 * Vendor Shipping Settings
	 *
	 * @return void
	 */
	function vendor_widthdrawals_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-widthdrawal-settings');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Widthdrawal_Settings_Shortcode', 'output'));
	}
	
	/**
	 * Vendor Shipping Settings
	 *
	 * @return void
	 */
	public function vendor_policies_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-policy-settings');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Pollicy_Settings_Shortcode', 'output'));
	}
	
	/**
	 * Vendor Shipping Settings
	 *
	 * @return void
	 */
	public function vendor_billing_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-billing-settings');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Billing_Settings_Shortcode', 'output'));
	}
	
	/**
	 * Vendor Shipping Settings
	 *
	 * @return void
	 */
	public function vendor_shipping_settings_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-shipping-settings');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Shipping_Settings_Shortcode', 'output'));
	}

	/**
	 * Vendor Dashboard
	 *
	 * @return void
	 */
	public function vendor_dashboard_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-dashboard');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Dashboard_Shortcode', 'output'));
	}
	
	
	/**
	 * vendor shop settings
	 *
	 * @return void
	 */
	public function shop_settings_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-shop-settings');
		return $this->shortcode_wrapper(array('WCMp_Shop_Setting_Shortcode', 'output'));
	}

	/**
	 * vendor report
	 *
	 * @return void
	 */
	public function vendor_report_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-report');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Report_Shortcode', 'output'));
	}
	
	/**
	 * vendor orders
	 *
	 * @return void
	 */
	public function vendor_orders_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-orders');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Orders_Shortcode', 'output'));
	}
	
	/**
	 * vendor orer detail
	 *
	 * @return void
	 */
	public function vendor_order_detail_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-view-order-dtl');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Order_Detail_Shortcode', 'output'));
	}
	
	/**
	 * vendor orer detail
	 *
	 * @return void
	 */
	public function vendor_coupons_shortcode($attr) {
		global $WCMp;
		$this->load_class('vendor-used-coupon');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Coupon_Shortcode', 'output'));
	}
	
	/**
	 * Vendor Thank You
	 *
	 * @return void
	 */
	public function vendor_transaction_thankyou($attr) {
		global $WCMp;
		$this->load_class('vendor-withdrawal-request');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Withdrawal_Request_Shortcode', 'output'));
	}
	
	
	/**
	 * Vendor Transaction Details
	 *
	 * @return void
	 */
	public function vendor_transaction_details($attr) {
		global $WCMp;
		$this->load_class('vendor-transaction');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Transaction_Detail_Shortcode', 'output'));
	}
	
	
	/**
	 * Helper Functions
	 */

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode CLass Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	
	public function load_class($class_name = '') {
		global $WCMp;
		if ('' != $class_name && '' != $WCMp->token) {
			require_once ('shortcode/class-' . esc_attr($WCMp->token) . '-shortcode-' . esc_attr($class_name) . '.php');
		}
	}
	/**
	 * get vendor
	 *
	 * @return void
	 */
	public static function get_vendor ( $slug ) { 

		$vendor_id = get_user_by('slug', $slug); 

		if (!empty($vendor_id)) { 
			$author = $vendor_id->ID; 
		} else $author = '';

		return $author; 

	}
	/**
	 * list all recent products
	 *
	 * @return void
	 */
	public static function wcmp_show_recent_products( $atts ) {
			global $woocommerce_loop, $WCMp;
 
			extract( shortcode_atts( array(
				'per_page' 	=> '12',
				'vendor' 	=> '', 
				'columns' 	=> '4',
				'orderby' 	=> 'date',
				'order' 	=> 'desc'
			), $atts ) );
 
			$meta_query = WC()->query->get_meta_query();
			
			$args = array(
				'post_type'				=> 'product',
				'post_status'			=> 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' 		=> $per_page,
				'orderby' 				=> $orderby,
				'order' 				=> $order,
				'meta_query' 			=> $meta_query
			);
			if ( !empty( $vendor ) ) {
				$user = get_user_by ( 'login', $vendor );
			}
			
			if ( !empty( $vendor ) ) {
				$term_id = get_user_meta($user->ID, '_vendor_term_id', true);
				$args['tax_query'][] = array(
					'taxonomy' 		=> $WCMp->taxonomy->taxonomy_name,
					'field' => 'term_id',
					'terms' => $term_id
				);
			}
 
			ob_start();
 
			$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
 
			$woocommerce_loop['columns'] = $columns;
 
			if ( $products->have_posts() ) : ?>
 
				<?php woocommerce_product_loop_start(); ?>
 
					<?php while ( $products->have_posts() ) : $products->the_post(); ?>
 
						<?php wc_get_template_part( 'content', 'product' ); ?>
 
					<?php endwhile; // end of the loop. ?>
 
				<?php woocommerce_product_loop_end(); ?>
 
			<?php endif;
 
			wp_reset_postdata();
 
			return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}
	
	/**
	 * list all products
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function wcmp_show_products( $atts ) {
		global $woocommerce_loop, $WCMp;

		if ( empty( $atts ) ) return '';

		extract( shortcode_atts( array(
			'id' => '',
			'vendor' 	=> '',
			'columns' 	=> '4',
			'orderby'   => 'title',
			'order'     => 'asc'
		), $atts ) );

		if ( !empty( $vendor ) ) {
			$user = get_user_by ( 'login', $vendor );
		}

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'posts_per_page' 		=> -1,
			'meta_query' 			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array('catalog', 'visible'),
					'compare' 	=> 'IN'
				)
			)
		);
		
		if ( !empty( $vendor ) ) {
			$term_id = get_user_meta($user->ID, '_vendor_term_id', true);
			$args['tax_query'][] = array(
				'taxonomy' 		=> $WCMp->taxonomy->taxonomy_name,
				'field' => 'term_id',
				'terms' => $term_id
			);			
		} else if ( !empty( $id ) ) {
			$term_id = get_user_meta($id, '_vendor_term_id', true);
			$args['tax_query'][] = array(
				'taxonomy' 		=> $WCMp->taxonomy->taxonomy_name,
				'field' => 'term_id',
				'terms' => $term_id
			);
		}

		if ( isset( $atts['skus'] ) ) {
			$skus = explode( ',', $atts['skus'] );
			$skus = array_map( 'trim', $skus );
			$args['meta_query'][] = array(
				'key' 		=> '_sku',
				'value' 	=> $skus,
				'compare' 	=> 'IN'
			);
		}

		if ( isset( $atts['ids'] ) ) {
			$ids = explode( ',', $atts['ids'] );
			$ids = array_map( 'trim', $ids );
			$args['post__in'] = $ids;
		}
		
		
		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
		

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}


	/**
	 * list all featured products
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function wcmp_show_featured_products( $atts ) {
		global $woocommerce_loop, $WCMp;

		extract( shortcode_atts( array(
			'vendor' => '',
			'per_page' 	=> '12',
			'columns' 	=> '4',
			'orderby' 	=> 'date',
			'order' 	=> 'desc'
		), $atts ) );

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $per_page,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'meta_query'			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array('catalog', 'visible'),
					'compare'	=> 'IN'
				),
				array(
					'key' 		=> '_featured',
					'value' 	=> 'yes'
				)
			)
		);
		if ( !empty( $vendor ) ) {
			$user = get_user_by ( 'login', $vendor );
		}
		if ( !empty( $vendor ) ) {
			$term_id = get_user_meta($user->ID, '_vendor_term_id', true);
			$args['tax_query'][] = array(
				'taxonomy' 		=> $WCMp->taxonomy->taxonomy_name,
				'field' => 'term_id',
				'terms' => $term_id
			);			
		}

		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}
	
	/**
	 * List all products on sale
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function wcmp_show_sale_products( $atts ) {
		global $woocommerce_loop, $WCMp;

		extract( shortcode_atts( array(
			'vendor' 		=> '', 
			'per_page'      => '12',
			'columns'       => '4',
			'orderby'       => 'title',
			'order'         => 'asc'
		), $atts ) );

		// Get products on sale
		$product_ids_on_sale = wc_get_product_ids_on_sale();

		$meta_query   = array();
		$meta_query[] = WC()->query->visibility_meta_query();
		$meta_query[] = WC()->query->stock_status_meta_query();
		$meta_query   = array_filter( $meta_query );

		$args = array(
			'posts_per_page'	=> $per_page,
			'orderby' 			=> $orderby,
			'order' 			=> $order,
			'no_found_rows' 	=> 1,
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product',
			'meta_query' 		=> $meta_query,
			'post__in'			=> array_merge( array( 0 ), $product_ids_on_sale )
		);
		if ( !empty( $vendor ) ) {
			$user = get_user_by ( 'login', $vendor );
		}
		
		if ( !empty( $vendor ) ) {
			$term_id = get_user_meta($user->ID, '_vendor_term_id', true);
			$args['tax_query'][] = array(
				'taxonomy' 		=> $WCMp->taxonomy->taxonomy_name,
				'field' => 'term_id',
				'terms' => $term_id
			);			
		}
		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}

	/**
	 * List top rated products on sale by vendor
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function wcmp_show_top_rated_products( $atts ) {
		global $woocommerce_loop, $WCMp;

		extract( shortcode_atts( array(
			'vendor'		=> '', 
			'per_page'      => '12',
			'columns'       => '4',
			'orderby'       => 'title',
			'order'         => 'asc'
			), $atts ) );

		$args = array(
			'post_type' 			=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'   => 1,
			'orderby' 				=> $orderby,
			'order'					=> $order,
			'posts_per_page' 		=> $per_page,
			'meta_query' 			=> array(
				array(
					'key' 			=> '_visibility',
					'value' 		=> array('catalog', 'visible'),
					'compare' 		=> 'IN'
				)
			)
		);
		if ( !empty( $vendor ) ) {
			$user = get_user_by ( 'login', $vendor );
		}
		
		if ( !empty( $vendor ) ) {
			$term_id = get_user_meta($user->ID, '_vendor_term_id', true);
			$args['tax_query'][] = array(
				'taxonomy' 		=> $WCMp->taxonomy->taxonomy_name,
				'field' => 'term_id',
				'terms' => $term_id
			);			
		}	

		ob_start();

		add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}

	/**
	 * List best selling products on sale per vendor
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function wcmp_show_best_selling_products( $atts ) {
		global $woocommerce_loop, $WCMp;

		extract( shortcode_atts( array(
			'vendor'		=> '', 
			'per_page'      => '12',
			'columns'       => '4'
		), $atts ) );

		$args = array(
			'post_type' 			=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'   => 1,
			'posts_per_page'		=> $per_page,
			'meta_key' 		 		=> 'total_sales',
			'orderby' 		 		=> 'meta_value_num',
			'meta_query' 			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array( 'catalog', 'visible' ),
					'compare' 	=> 'IN'
				)
			)
		);
		
		if ( !empty( $vendor ) ) {
			$user = get_user_by ( 'login', $vendor );
		}
		
		if ( !empty( $vendor ) ) {
			$term_id = get_user_meta($user->ID, '_vendor_term_id', true);
			$args['tax_query'][] = array(
				'taxonomy' 		=> $WCMp->taxonomy->taxonomy_name,
				'field' => 'term_id',
				'terms' => $term_id
			);			
		}
		
		
		
		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}

	/**
	 * List products in a category shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function wcmp_show_product_category( $atts ) {
		global $woocommerce_loop, $WCMp;

		extract( shortcode_atts( array(
			'vendor'   => '', 
			'per_page' => '12',
			'columns'  => '4',
			'orderby'  => 'title',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'operator' => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
		), $atts ) );

		if ( ! $category ) {
			return '';
		}

		// Default ordering args
		$ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $ordering_args['orderby'],
			'order' 				=> $ordering_args['order'],
			'posts_per_page' 		=> $per_page,
			'meta_query' 			=> array(
				array(
					'key' 			=> '_visibility',
					'value' 		=> array('catalog', 'visible'),
					'compare' 		=> 'IN'
				)
			),
			'tax_query' 			=> array(
				array(
					'taxonomy' 		=> 'product_cat',
					'terms' 		=> array_map( 'sanitize_title', explode( ',', $category ) ),
					'field' 		=> 'slug',
					'operator' 		=> $operator
				)
			)
		);
		
		if ( !empty( $vendor ) ) {
			$user = get_user_by ( 'login', $vendor );
		}
		
		if ( !empty( $vendor ) ) {
			$term_id = get_user_meta($user->ID, '_vendor_term_id', true);
			$args['tax_query'][] = array(
				'taxonomy' 		=> $WCMp->taxonomy->taxonomy_name,
				'field' => 'term_id',
				'terms' => $term_id
			);			
		}
		
		
		
		if ( isset( $ordering_args['meta_key'] ) ) {
			$args['meta_key'] = $ordering_args['meta_key'];
		}

		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		woocommerce_reset_loop();
		wp_reset_postdata();

		$return = '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';

		// Remove ordering query arguments
		WC()->query->remove_ordering_args();

		return $return;
	}

	/**
	  * 	list of vendors 
	  * 
	  * 	@param $atts shortcode attributs 
	*/
	public function wcmp_show_vendorslist( $atts ) {
		global $WCMp;
		$vendors_available = false;
		$get_all_vendors = array();
		$select_html = '';
		
		extract( shortcode_atts( array(
			'orderby'  => 'registered',
			'order'    => 'ASC',
		), $atts ) );
		
    $vendors = '';
    $vendor_sort_type = '';
    if(isset($_GET['vendor_sort_type'])) {
			if($_GET['vendor_sort_type'] == 'category') {
				$vendors_ids = array();
				$vendor_sort_type = $_GET['vendor_sort_type'];
				$selected_category = $_GET['vendor_sort_category'];
				
				$args = array('post_type' => 'product',
											'tax_query' => array(
												array(
													'taxonomy' => 'product_cat',
													'field' => 'term_id',
													'terms' => $selected_category
												)
											)
				);
				
				$wp_obj = new WP_Query($args);
				$sorted_products = $wp_obj->posts;
				
				if( isset($sorted_products) && !empty($sorted_products) ) {
					foreach( $sorted_products as $sorted_product ) {
						$vendor_obj = get_wcmp_product_vendors($sorted_product->ID);
						if( isset($vendor_obj) && !empty($vendor_obj) ) {
							if( !in_array($vendor_obj->id, $vendors_ids) ) {
								$vendors_ids[] = $vendor_obj->id;
								$get_all_vendors[] = new WCMp_Vendor($vendor_obj->id);
								$vendors_available = true;
							}
						}
					}
				}
				
			} else {
				$vendor_sort_type = $_GET['vendor_sort_type'];
				$orderby = $vendor_sort_type;
				$order = 'ASC';
			}
		}
		
		if( !$vendors_available ) {
			$get_all_vendors = get_wcmp_vendors(array('orderby' => $orderby, 'order' => $order));
		}
		
		
		
		$vendors .= '<div class="vendor_list">';
		$vendors .= '<form name="vendor_sort" method="get" ><div class="vendor_sort">';
		$vendors .= '<select class="select short" id="vendor_sort_type" name="vendor_sort_type">';
		if($vendor_sort_type) {
			if($vendor_sort_type == 'registered') {
				$option = '<option selected value="registered">' . __( "By date", $WCMp->text_domain ) . '</option><option value="name">' . __( "By Alphabetically", $WCMp->text_domain ) . '</option><option value="category">' . __( "By Category", $WCMp->text_domain ) . '</option>';
			} else if($vendor_sort_type == 'name') {
				$option = '<option value="registered">' . __( "By date", $WCMp->text_domain ) . '</option><option selected value="name">' . __( "By Alphabetically", $WCMp->text_domain ) . '</option><option value="category">' . __( "By Category", $WCMp->text_domain ) . '</option>';
			} else if($vendor_sort_type == 'category') {
				$option = '<option value="registered">' . __( "By date", $WCMp->text_domain ) . '</option><option value="name">' . __( "By Alphabetically", $WCMp->text_domain ) . '</option><option selected value="category">' . __( "By Category", $WCMp->text_domain ) . '</option>';
			} else {
				$option = '<option value="registered">' . __( "By date", $WCMp->text_domain ) . '</option><option value="name">' . __( "By Alphabetically", $WCMp->text_domain ) . '</option><option value="category">' . __( "By Category", $WCMp->text_domain ) . '</option>';
			}
		} else {
			if($orderby == 'registered') {
				$option = '<option selected value="registered">' . __( "By date", $WCMp->text_domain ) . '</option><option value="name">' . __( "By Alphabetically", $WCMp->text_domain ) . '</option><option value="category">' . __( "By Category", $WCMp->text_domain ) . '</option>';
			} else if($orderby == 'name') {
				$option = '<option  value="registered">' . __( "By date", $WCMp->text_domain ) . '</option><option selected value="name">' . __( "By Alphabetically", $WCMp->text_domain ) . '</option><option value="category">' . __( "By Category", $WCMp->text_domain ) . '</option>';
			} else if($vendor_sort_type == 'category') {
				$option = '<option value="registered">' . __( "By date", $WCMp->text_domain ) . '</option><option value="name">' . __( "By Alphabetically", $WCMp->text_domain ) . '</option><option value="category">' . __( "By Category", $WCMp->text_domain ) . '</option>';
			}
		}
		
		if(isset($_GET['vendor_sort_type'])) {
			if($_GET['vendor_sort_type'] == 'category') {
				$category_terms = get_terms('product_cat');
				$select_html = '&nbsp&nbsp&nbsp<select class="select" id="vendor_sort_category" name="vendor_sort_category">';
				foreach( $category_terms as $terms ) {
					if( isset( $_GET['vendor_sort_category'] ) ) {
						if( $_GET['vendor_sort_category'] == $terms->term_id ) {
							$select_html .= '<option selected value="' . $terms->term_id . '">' . $terms->name . '</option>';
						} else {
							$select_html .= '<option value="' . $terms->term_id . '">' . $terms->name . '</option>';
						}
					}
				}
				$select_html .= '</select>';
			}
		}
		
		$vendors .= $option.'</select>'.$select_html;
		$vendors .= '&nbsp;&nbsp;&nbsp;<input type="submit" value="' . __( "Sort", $WCMp->text_domain ) . '" />';
		$vendors .= '</div>';
		$vendors .= '</form>';
		
		
		$get_blocked = wcmp_get_all_blocked_vendors();
		$get_block_array = array();
		if(!empty($get_blocked)) {
			foreach($get_blocked as $get_block) {
				$get_block_array[] = (int)$get_block->id;
			}
		}
		if( isset($get_all_vendors) && !empty($get_all_vendors) ) {
			foreach ( $get_all_vendors as $get_vendor ) {
				if(in_array($get_vendor->id, $get_block_array)) continue;
				if(!$get_vendor->image) $get_vendor->image = $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
				$vendors .= '<div class="sorted_vendors" style="display:inline-block; margin-right:10%;">
											 <center>
													<a href="'.$get_vendor->permalink.'"><img width="125" class="vendor_img" src="'. $get_vendor->image .'" id="vendor_image_display"></a><br />
													<a href="'.$get_vendor->permalink.'" class="button">'.$get_vendor->user_data->display_name.'</a>
													<br /><br />
											 </center>
										 </div>';
			}
			$vendors .= '</div>';
		}
		return $vendors;
	}
}
?>