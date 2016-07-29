<?php
if (!defined('ABSPATH'))
    exit;

/**
 * @class 		WCMp Vendor Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		DualCube
 */
class WCMp_Vendor {

    public $id;
    public $taxonomy;
    public $term;
    public $user_data;

    /**
     * Get the vendor if UserID is passed, otherwise the vendor is new and empty.
     *
     * @access public
     * @param string $id (default: '')
     * @return void
     */
    public function __construct($id = '') {

        $this->taxonomy = 'dc_vendor_shop';

        $this->term = false;

        if ($id > 0) {
            $this->get_vendor($id);
        }
    }

    public function get_reviews_and_rating($offset = 0) {
        global $WCMp, $wpdb;
        $vendor_id = $this->id;
        $posts_per_page = get_option('posts_per_page');
        if (empty($vendor_id) || $vendor_id == '' || $vendor_id == 0) {
            return 0;
        } else {
            $args_default = array(
                'status' => 'approve',
                'type' => 'wcmp_vendor_rating',
                'count' => false,
                'number' => $posts_per_page,
                'offset' => $offset,
                'meta_key' => 'vendor_rating_id',
                'meta_value' => $vendor_id,
            );
            $args = apply_filters('wcmp_vendor_review_rating_args_to_fetch', $args_default);
            return get_comments($args);
        }
    }

    public function get_review_count() {
        global $WCMp, $wpdb;
        $vendor_id = $this->id;
        if (empty($vendor_id) || $vendor_id == '' || $vendor_id == 0) {
            return 0;
        } else {
            $args_default = array(
                'status' => 'approve',
                'type' => 'wcmp_vendor_rating',
                'count' => true,
                'meta_key' => 'vendor_rating_id',
                'meta_value' => $vendor_id,
            );
            $args = apply_filters('wcmp_vendor_review_rating_args_to_fetch', $args_default);
            return get_comments($args);
        }
    }

    /**
     * Gets an Vendor User from the database.
     *
     * @access public
     * @param int $id (default: 0)
     * @return bool
     */
    public function get_vendor($id = 0) {
        if (!$id) {
            return false;
        }

        if (!is_user_wcmp_vendor($id)) {
            return false;
        }

        if ($result = get_userdata($id)) {
            $this->populate($result);
            return true;
        }
        return false;
    }

    /**
     * Populates an Vendor from the loaded user data.
     *
     * @access public
     * @param mixed $result
     * @return void
     */
    public function populate($result) {

        $this->id = $result->ID;
        $this->user_data = $result;
    }

    /**
     * __isset function.
     *
     * @access public
     * @param mixed $key
     * @return bool
     */
    public function __isset($key) {
        global $WCMp;

        if (!$this->id) {
            return false;
        }

        if (in_array($key, array('term_id', 'page_title', 'page_slug', 'link'))) {
            if ($term_id = get_user_meta($this->id, '_vendor_term_id', true)) {
                return term_exists(absint($term_id), $WCMp->taxonomy->taxonomy_name);
            } else {
                return false;
            }
        }

        return metadata_exists('user', $this->id, '_' . $key);
    }

    /**
     * __get function.
     *
     * @access public
     * @param mixed $key
     * @return mixed
     */
    public function __get($key) {
        if (!$this->id) {
            return false;
        }

        if ($key == 'page_title') {

            $value = $this->get_page_title();
        } elseif ($key == 'page_slug') {

            $value = $this->get_page_slug();
        } elseif ($key == 'permalink') {

            $value = $this->get_permalink();
        } else {
            // Get values or default if not set
            $value = get_user_meta($this->id, '_vendor_' . $key, true);
        }

        return $value;
    }

    /**
     * generate_term function
     * @access public
     * @return void
     */
    public function generate_term() {
        global $WCMp;

        if (!isset($this->term_id)) {
            $term = wp_insert_term($this->user_data->user_login, $WCMp->taxonomy->taxonomy_name);
            if (!is_wp_error($term)) {
                update_user_meta($this->id, '_vendor_term_id', $term['term_id']);
                update_woocommerce_term_meta($term['term_id'], '_vendor_user_id', $this->id);
            }
        }
    }

    /**
     * update_page_title function
     * @access public
     * @param $title
     * @return boolean
     */
    public function update_page_title($title = '') {
        global $WCMp;

        if (!empty($title) && isset($this->term_id)) {
            if (!is_wp_error(wp_update_term($this->term_id, $WCMp->taxonomy->taxonomy_name, array('name' => $title)))) {
                return true;
            }
        }
        return false;
    }

    /**
     * update_page_slug function
     * @access public
     * @param $slug
     * @return boolean
     */
    public function update_page_slug($slug = '') {
        global $WCMp;

        if (!empty($slug) && isset($this->term_id)) {
            if (!is_wp_error(wp_update_term($this->term_id, $WCMp->taxonomy->taxonomy_name, array('slug' => $slug)))) {
                return true;
            }
        }
        return false;
    }

    /**
     * set_term_data function
     * @access public
     * @return void
     */
    public function set_term_data() {
        global $WCMp;
        //return if term is already set
        if ($this->term)
            return;

        if (isset($this->term_id)) {
            $term = get_term($this->term_id, $WCMp->taxonomy->taxonomy_name);
            if (!is_wp_error($term)) {
                $this->term = $term;
            }
        }
    }

    /**
     * get_page_title function
     * @access public
     * @return string
     */
    public function get_page_title() {
        $this->set_term_data();
        if ($this->term) {
            return $this->term->name;
        } else {
            return '';
        }
    }

    /**
     * get_page_slug function
     * @access public
     * @return string
     */
    public function get_page_slug() {
        $this->set_term_data();
        if ($this->term) {
            return $this->term->slug;
        } else {
            return '';
        }
    }

    /**
     * get_permalink function
     * @access public
     * @return string
     */
    public function get_permalink() {
        global $WCMp;

        $link = '';
        if (isset($this->term_id)) {
            $link = get_term_link(absint($this->term_id), $WCMp->taxonomy->taxonomy_name);
        }

        return $link;
    }

    /**
     * Get all products belonging to vendor
     * @param  $args (default=array())
     * @return arr Array of product post objects
     */
    public function get_products($args = array()) {
        global $WCMp;
        $products = false;

        $default = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                    'field' => 'id',
                    'terms' => absint($this->term_id)
                )
            )
        );

        $args = wp_parse_args($args, $default);

        $products = get_posts($args);

        return $products;
    }

    /**
     * get_orders function
     * @access public
     * @return array with order id
     */
    public function get_orders($no_of = false, $offset = false, $more_args = false) {
        if (!$no_of)
            $no_of = -1;
        $vendor_id = $this->term_id;
        $commissions = false;
        $order_id = null;
        if ($vendor_id > 0) {
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => (int) $no_of,
                'meta_query' => array(
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($vendor_id),
                        'compare' => '='
                    )
                )
            );
            if ($offset)
                $args['offset'] = $offset;
            if ($more_args)
                $args = wp_parse_args($more_args, $args);
            $commissions = get_posts($args);
        }

        if ($commissions) {
            $order_id = array();
            foreach ($commissions as $commission) {
                $order_id[$commission->ID] = get_post_meta($commission->ID, '_commission_order_id', true);
            }
        }
        return $order_id;
    }

    /**
     * get_vendor_items_from_order function get items of a order belongs to a vendor
     * @access public
     * @param order_id , vendor term id 
     * @return array with order item detail
     */
    public function get_vendor_items_from_order($order_id, $term_id) {
        $item_dtl = array();
        $order = new WC_Order($order_id);
        if ($order) {
            $items = $order->get_items('line_item');
            if ($items) {
                foreach ($items as $item_id => $item) {
                    $product_id = $order->get_item_meta($item_id, '_product_id', true);

                    if ($product_id) {
                        if ($term_id > 0) {
                            $product_vendors = get_wcmp_product_vendors($product_id);
                            if (!empty($product_vendors) && $product_vendors->term_id == $term_id) {
                                $item_dtl[$item_id] = $item;
                            }
                        }
                    }
                }
            }
        }
        return $item_dtl;
    }

    /**
     * get_vendor_items_from_order function get items of a order belongs to a vendor
     * @access public
     * @param order_id , vendor term id 
     * @return array with order item detail
     */
    public function get_vendor_shipping_from_order($order_id, $term_id) {
        $item_dtl = array();
        $order = new WC_Order($order_id);
        if ($order) {
            $items = $order->get_items('shipping');
            /* if( $items ) {
              foreach( $items as $item_id => $item ) {
              $product_id = $order->get_item_meta( $item_id, '_product_id', true );

              if( $product_id ) {
              if( $term_id > 0 ) {
              $product_vendors = get_wcmp_product_vendors($product_id);
              if(!empty($product_vendors) && $product_vendors->term_id == $term_id) {
              $item_dtl[$item_id] = $item;
              }
              }
              }
              }
              } */
        }
        return $items;
    }

    /**
     * get_vendor_orders_by_product function to get orders belongs to a vendor and a product
     * @access public
     * @param product id , vendor term id 
     * @return array with order id
     */
    public function get_vendor_orders_by_product($vendor_term_id, $product_id) {
        $order_dtl = array();
        if ($product_id && $vendor_term_id) {
            $commissions = false;
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'order' => 'asc',
                'meta_query' => array(
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($vendor_term_id),
                        'compare' => '='
                    ),
                    array(
                        'key' => '_commission_product',
                        'value' => absint($product_id),
                        'compare' => 'LIKE'
                    ),
                ),
            );
            $commissions = get_posts($args);
            if (!empty($commissions)) {
                foreach ($commissions as $commission) {
                    $order_dtl[] = get_post_meta($commission->ID, '_commission_order_id', true);
                }
            }
        }
        return $order_dtl;
    }

    /**
     * get_vendor_commissions_by_product function to get orders belongs to a vendor and a product
     * @access public
     * @param product id , vendor term id 
     * @return array with order id
     */
    public function get_vendor_commissions_by_product($order_id, $product_id) {
        $order_dtl = array();
        if ($product_id && $order_id) {
            $commissions = false;
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'order' => 'asc',
                'meta_query' => array(
                    array(
                        'key' => '_commission_order_id',
                        'value' => absint($order_id),
                        'compare' => '='
                    ),
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($this->term_id),
                        'compare' => '='
                    ),
                ),
            );
            $commissions = get_posts($args);

            if (!empty($commissions)) {
                foreach ($commissions as $commission) {
                    $order_dtl[] = $commission->ID;
                }
            }
        }
        return $order_dtl;
    }

    /**
     * vendor_order_item_table function to get the html of item table of a vendor.
     * @access public
     * @param order id , vendor term id 
     */
    public function vendor_order_item_table($order, $vendor_id, $is_ship = false) {
        global $WCMp;
        require_once ( 'class-wcmp-calculate-commission.php' );
        $commission_obj = new WCMp_Calculate_Commission();
        $vendor_items = $this->get_vendor_items_from_order($order->id, $vendor_id);
        foreach ($vendor_items as $item_id => $item) {
            $_product = apply_filters('wcmp_woocommerce_order_item_product', $order->get_product_from_item($item), $item);
            $item_meta = new WC_Order_Item_Meta($item['item_meta'], $_product);
            ?>
            <tr class="">
                <td scope="col" style="text-align:left; border: 1px solid #eee;" class="product-name">
                    <?php
                    if ($_product && !$_product->is_visible())
                        echo apply_filters('wcmp_woocommerce_order_item_name', $item['name'], $item);
                    else
                        echo apply_filters('woocommerce_order_item_name', sprintf('<a href="%s">%s</a>', get_permalink($item['product_id']), $item['name']), $item);
                    $item_meta->display();
                    ?>
                </td>
                <td scope="col" style="text-align:left; border: 1px solid #eee;">	
                    <?php
                    echo $item['qty'];
                    ?>
                </td>
                <td scope="col" style="text-align:left; border: 1px solid #eee;">
                    <?php
                    $variation_id = '';
                    if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                        $variation_id = $item['variation_id'];
                    }
                    $product_id = $item['product_id'];
                    if ($is_ship)
                        echo $order->get_formatted_line_subtotal($item);
                    else
                        echo $commission_obj->get_item_commission($product_id, $variation_id, $item, $order->id, $item_id);
                    ?>
                </td>
            </tr>
            <?php
        }
    }

    /**
     * plain_vendor_order_item_table function to get the plain html of item table of a vendor.
     * @access public
     * @param order id , vendor term id 
     */
    public function plain_vendor_order_item_table($order, $vendor_id, $is_ship = false) {
        global $WCMp;
        require_once ( 'class-wcmp-calculate-commission.php' );
        $commission_obj = new WCMp_Calculate_Commission();
        $vendor_items = $this->get_vendor_items_from_order($order->id, $vendor_id);
        foreach ($vendor_items as $item_id => $item) {
            $_product = apply_filters('woocommerce_order_item_product', $order->get_product_from_item($item), $item);
            $item_meta = new WC_Order_Item_Meta($item['item_meta'], $_product);

            // Title
            echo apply_filters('woocommerce_order_item_name', $item['name'], $item);


            // Variation
            echo $item_meta->meta ? "\n" . $item_meta->display(true, true) : '';

            // Quantity
            echo "\n" . sprintf(__('Quantity: %s', $WCMp->text_domain), $item['qty']);
            if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                $variation_id = $item['variation_id'];
            }
            $product_id = $item['product_id'];

            if ($is_ship)
                echo "\n" . sprintf(__('Total: %s', $WCMp->text_domain), $order->get_formatted_line_subtotal($item));
            else
                echo "\n" . sprintf(__('Commission: %s', $WCMp->text_domain), $commission_obj->get_item_commission($product_id, $variation_id, $item, $order->id, $item_id));

            echo "\n\n";
        }
    }

    /**
     * wcmp_get_vendor_part_from_order function to get vendor due from an order.
     * @access public
     * @param order , vendor term id 
     */
    public function wcmp_get_vendor_part_from_order($order, $vendor_term_id) {
        global $WCMp;
        require_once ( 'class-wcmp-calculate-commission.php' );
        $commission_obj = new WCMp_Calculate_Commission();
        $vendor_items = $this->get_vendor_items_from_order($order->id, $vendor_term_id);
        $vendor = get_wcmp_vendor_by_term($vendor_term_id);
        $commission_amt = 0;
        $vendor_due = array();
        $product_value_total = 0;
        $flag = false;
        $line_items = $order->get_items('line_item');
        $shipping = $order->get_items('shipping');
        $line_tax = 0;
        $shipping_amount_per_vendor = 0;
        if (!empty($line_items)) {
            foreach ($line_items as $item_id => $item) {
                $give_tax_to_vendor = $order->get_item_meta($item_id, '_give_tax_to_vendor', true);
                if (!empty($give_tax_to_vendor)) {
                    $flag = true;
                }
//                if ($give_tax_to_vendor == 1) {
//                    $line_tax += $order->get_item_meta($item_id, '_line_tax', true);
//                }
            }
        }
        if (!empty($shipping)) {
            foreach ($shipping as $shipping_id => $value) {
                $give_shipping_to_vendor = $order->get_item_meta($shipping_id, '_give_shipping_to_vendor', true);
                if (!empty($give_shipping_to_vendor)) {
                    $flag = true;
                }
                if ($give_shipping_to_vendor) {
                    $flat_shipping_per_vendor = wc_get_order_item_meta($shipping_id, 'vendor_cost_' . $vendor->id, true);
                    $shipping_amount_per_vendor += $flat_shipping_per_vendor;
                    $vendor_shipping_tax_array = $order->get_item_meta($shipping_id,'vendor_tax_'.$vendor->id,true);
                    if(!empty($vendor_shipping_tax_array) && is_array($vendor_shipping_tax_array)){
                        foreach ($vendor_shipping_tax_array as $shipping_tax){
                            $line_tax += (float)$shipping_tax;
                        }
                    }
                }
            }
        }
        foreach ($vendor_items as $item_id => $item) {
            if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                $variation_id = $item['variation_id'];
            } else {
                $variation_id = 0;
            }
            $product_id = $item['product_id'];
            if ($variation_id == 0) {
                $product_id_for_value = $product_id;
            } else {
                $product_id_for_value = $variation_id;
            }
            $product_value = get_post_meta($product_id_for_value, '_price', true);
            if (empty($product_value)) {
                $product_value = 0;
            }
            $product_value_total += ($product_value * $item['qty']);
            $commission_amt = (float) $commission_amt + (float) $commission_obj->get_item_commission($product_id, $variation_id, $item, $order->id, $item_id);

            $vendor_due['commission'] = $commission_amt;
            if ($vendor_due['commission'] > $product_value_total) {
                $vendor_due['commission'] = $product_value_total;
            }

            if ($flag) {
                $vendor_due['tax'] = 0;
                if ($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
                    $give_tax_to_vendor = $order->get_item_meta($item_id, '_give_tax_to_vendor', true);
                    if ($give_tax_to_vendor == 1) {
                        $line_tax += $order->get_item_meta($item_id, '_line_tax', true);
                    }
                    $vendor_due['tax'] += $line_tax;
                } else {
                    $vendor_due['tax'] += 0;
                }
                $vendor_due['shipping'] = 0;
                if ($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
                    $vendor_due['shipping'] += $shipping_amount_per_vendor;
                } else {
                    $vendor_due['shipping'] += 0;
                }
            } else {
                $shipping_tax_total = $this->get_vendor_total_tax_and_shipping($order, $vendor_term_id, $item, $commission_obj);

                if (!isset($vendor_due['tax']))
                    $vendor_due['tax'] = 0;
                if ($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
                    $vendor_due['tax'] += $shipping_tax_total['tax_subtotal'];
                } else {
                    $vendor_due['tax'] += 0;
                }

                if (!isset($vendor_due['shipping']))
                    $vendor_due['shipping'] = 0;
                if ($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
                    $vendor_due['shipping'] += $shipping_tax_total['shipping_subtotal'];
                } else {
                    $vendor_due['shipping'] += 0;
                }
            }
            $vendor_due['total'] = (float) $commission_amt + (float) $vendor_due['shipping'] + (float) $vendor_due['tax'];
        }

        return apply_filters('vendor_due_per_order', $vendor_due, $order, $vendor_term_id);
    }

    /**
     * wcmp_vendor_get_total_amount_due function to get vendor due from an order.
     * @access public
     * @param order , vendor term id 
     */
    public function wcmp_vendor_get_total_amount_due() {
        global $WCMp;
        $commissions = array();
        $total_due = 0;
        $vendor = get_wcmp_vendor_by_term($this->term_id);
        if ($this->term_id > 0) {
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($this->term_id),
                        'compare' => '='
                    ),
                    array(
                        'key' => '_paid_status',
                        'value' => 'unpaid',
                        'compare' => '='
                    ),
                )
            );
            $commissions = get_posts($args);
        }

        if (!empty($commissions)) {
            foreach ($commissions as $commission) {
                $shipping = $tax = 0;
                $commission_amount = (float) get_post_meta($commission->ID, '_commission_amount', true);

                if ($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
                    if (!get_user_meta($vendor->id, '_vendor_give_shipping', true)) {
                        $shipping = (float) get_post_meta($commission->ID, '_shipping', true);
                    }
                }

                if ($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
                    if (!get_user_meta($vendor->id, '_vendor_give_tax', true)) {
                        $tax = (float) get_post_meta($commission->ID, '_tax', true);
                    }
                }
                $total_due += $commission_amount + $shipping + $tax;
            }
            return (float) $total_due;
        }
    }

    /**
     * wcmp_get_vendor_part_from_order function to get vendor due from an order.
     * @access public
     * @param order , vendor term id 
     */
    public function wcmp_vendor_transaction() {
        global $WCMp;
        $transactions = $paid_array = array();
        $vendor = get_wcmp_vendor_by_term($this->term_id);
        if ($this->term_id > 0) {
            $args = array(
                'post_type' => 'wcmp_transaction',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'post_author' => $vendor->id
            );
            $transactions = get_posts($args);
        }

        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                $paid_array[] = $transaction->ID;
            }
        }
        return $paid_array;
    }

    /**
     * wcmp_vendor_get_order_item_totals function to get order item table of a vendor.
     * @access public
     * @param order id , vendor term id 
     */
    public function wcmp_vendor_get_order_item_totals($order, $vendor_id) {
        global $WCMp;

        $vendor_totals = $this->wcmp_get_vendor_part_from_order($order, $vendor_id);
        $return = array();

        if (!isset($vendor_totals['commission']))
            $vendor_totals['commission'] = 0;
        if (!isset($vendor_totals['tax']))
            $vendor_totals['tax'] = 0;
        if (!isset($vendor_totals['shipping']))
            $vendor_totals['shipping'] = 0;

        $return['commission_subtotal'] = array('label' => __('Commission Subtotal:', $WCMp->text_domain), 'value' => $vendor_totals['commission']);
        if ($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
            $return['tax_subtotal'] = array('label' => '', 'value' => '');
            $return['tax_subtotal']['label'] = __('Tax Subtotal:', $WCMp->text_domain);
            $return['tax_subtotal']['value'] = woocommerce_price($vendor_totals['tax']);
        }
        if ($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
            $return['shipping_subtotal'] = array('label' => '', 'value' => '');
            $return['shipping_subtotal']['label'] = __('Shipping Subtotal:', $WCMp->text_domain);
            $return['shipping_subtotal']['value'] = woocommerce_price($vendor_totals['shipping']);
        }
        $return['total']['label'] = __('Total:', $WCMp->text_domain);
        $return['total']['value'] = woocommerce_price($vendor_totals['commission'] + $vendor_totals['shipping'] + $vendor_totals['tax']);
        return $return;
    }

    public function get_vendor_total_tax_and_shipping($order, $vendor_id, $product, $commission_obj) {
        $tax_amt = 0;
        $give_tax = false;
        $give_shipping = false;
        $vendor_items = $this->get_vendor_items_from_order($order->id, $vendor_id);
        $shipping_given = 0;
        $tax_given = 0;
        if (!empty($product)) {
            $product_id = !empty($product['variation_id']) ? $product['variation_id'] : $product['product_id'];
            $vendor_user = get_wcmp_vendor_by_term($vendor_id);
            $give_tax_override = get_user_meta($vendor_user->id, '_vendor_give_tax', true);
            $give_shipping_override = get_user_meta($vendor_user->id, '_vendor_give_shipping', true);
            $tax = !empty($product['line_tax']) ? (float) $product['line_tax'] : 0;

            // Check if shipping is enabled
            if (get_option('woocommerce_calc_shipping') === 'no') {
                $shipping = 0;
                $shipping_tax = 0;
            } else {
                $shipping_costs = $this->get_wcmp_vendor_shipping_total($order->id, $product);
                $shipping = $shipping_costs['shipping_amount'];
                $shipping_tax = $shipping_costs['shipping_tax'];
            }


            // Add line item tax and shipping taxes together 
            $total_tax = (float) $tax + (float) $shipping_tax;

            // Tax override on a per vendor basis
            if (!$give_tax_override)
                $give_tax = true;

            // Shipping override 
            if (!$give_shipping_override)
                $give_shipping = true;

            $shipping_given += $give_shipping ? $shipping : 0;
            $tax_given += $give_tax ? $total_tax : 0;

            return array('shipping_subtotal' => $shipping_given, 'tax_subtotal' => $tax_given);
        }
        return array('shipping_subtotal' => 0, 'tax_subtotal' => 0);
    }

    /**
     * Get Vendor Shipping commission total. Supports Flat Rate, International Delivery and Local Delivery
     *
     * @param int $order_id
     * @param object $product
     */
    public function get_wcmp_vendor_shipping_total($order_id, $product) {
        global $WCMp, $woocommerce;

        $vendor_shipping_costs = array('shipping_amount' => 0, 'shipping_tax' => 0);
        $method = '';
        $_product = get_product($product['product_id']);
        $order = wc_get_order($order_id);

        if ($_product && $_product->needs_shipping() && !$_product->is_downloadable()) {

            $shipping_methods = $order->get_shipping_methods();

            foreach ($shipping_methods as $shipping_method) {
                $method = $shipping_method['method_id'];
                break;
            }

            if (version_compare(WC_VERSION, '2.6.0', '>=')) {
                $methodArr = explode(':', $method);
                if (count($methodArr) >= 2) {
                    $method_id = $methodArr[0];
                    $instance_id = $methodArr[1];
                    if ($method_id == 'flat_rate') {
                        $woocommerce_shipping_method_settings = get_option('woocommerce_' . $method_id . '_' . $instance_id . '_settings');
                        if ($woocommerce_shipping_method_settings['type'] == 'class') {
                            $vendor_shipping_costs['shipping_amount'] = isset($product['flat_shipping_per_item']) ? $product['flat_shipping_per_item'] : '';
                        }
                    } else {
                        do_action('wcmp_other_shipping_methods', $order_id, $product, $method, $order);
                    }
                } else { // Deprecated Shipping method
                    // Flat Rate
                    if ($method == 'legacy_flat_rate') {
                        $woocommerce_flat_rate_settings = get_option('woocommerce_flat_rate_settings');

                        if ($woocommerce_flat_rate_settings['type'] == 'class') {
                            $vendor_shipping_costs['shipping_amount'] = isset($product['flat_shipping_per_item']) ? $product['flat_shipping_per_item'] : '';
                        }
                    }// Local Delivery	
                    else if ($method == 'legacy_local_delivery') {
                        $local_delivery = get_option('woocommerce_local_delivery_settings');

                        if ($local_delivery['type'] == 'product') {
                            $vendor_shipping_costs['shipping_amount'] = $product['qty'] * $local_delivery['fee'];
                            $vendor_shipping_costs['shipping_tax'] = $this->calculate_shipping_tax($vendor_shipping_costs['shipping_amount'], $order);
                        }
                    }// International Delivery 
                    else if ($method == 'legacy_international_delivery') {

                        $wc_international_delivery = get_option('woocommerce_international_delivery_settings');

                        if ($wc_international_delivery['type'] == 'class') {
                            $vendor_shipping_costs['shipping_amount'] = isset($product['international_flat_shipping_per_item']) ? $product['international_flat_shipping_per_item'] : '';
                        }
                    }
                }
            } else { // WC version < 2.6
                // Flat Rate
                if ($method == 'flat_rate') {
                    $woocommerce_flat_rate_settings = get_option('woocommerce_flat_rate_settings');
                    if (version_compare(WC_VERSION, '2.4.0', '>')) {
                        if ($woocommerce_flat_rate_settings['type'] == 'class') {
                            $vendor_shipping_costs['shipping_amount'] = isset($product['flat_shipping_per_item']) ? $product['flat_shipping_per_item'] : '';
                        }
                    } else {
                        if ($woocommerce_flat_rate_settings['type'] == 'item') {
                            $vendor_shipping_costs['shipping_amount'] = $product['flat_shipping_per_item'];
                        }
                    }
                }// Local Delivery	
                else if ($method == 'local_delivery') {
                    $local_delivery = get_option('woocommerce_local_delivery_settings');

                    if ($local_delivery['type'] == 'product') {
                        $vendor_shipping_costs['shipping_amount'] = $product['qty'] * $local_delivery['fee'];
                        $vendor_shipping_costs['shipping_tax'] = $this->calculate_shipping_tax($vendor_shipping_costs['shipping_amount'], $order);
                    }
                }// International Delivery 
                else if ($method == 'international_delivery') {

                    $wc_international_delivery = get_option('woocommerce_international_delivery_settings');

                    if (version_compare(WC_VERSION, '2.4.0', '>')) {
                        if ($wc_international_delivery['type'] == 'class') {
                            $vendor_shipping_costs['shipping_amount'] = isset($product['international_flat_shipping_per_item']) ? $product['international_flat_shipping_per_item'] : '';
                        }
                    } else {
                        if ($wc_international_delivery['type'] == 'item') {
                            $WC_Shipping_International_Delivery = new WC_Shipping_International_Delivery();
                            $fee = $WC_Shipping_International_Delivery->get_fee($int_delivery['fee'], $_product->get_price());
                            $vendor_shipping_costs['shipping_amount'] = ( $int_delivery['cost'] + $fee ) * $product['qty'];
                            $vendor_shipping_costs['shipping_tax'] = ( 'taxable' === $int_delivery['tax_status'] ) ? $this->calculate_shipping_tax($vendor_shipping_costs['shipping_amount'], $order) : 0;
                        }
                    }
                } else {
                    do_action('wcmp_other_shipping_methods', $order_id, $product, $method, $order);
                }
            }
        }
        $vendor_shipping_costs = apply_filters('wcmp_vendors_shipping_amount', $vendor_shipping_costs, $order_id, $product);

        return $vendor_shipping_costs;
    }

    /**
     * Calculate wcmp vendor shipping tax
     *
     * @param double $shipping_amount
     * @param object $order
     */
    public function calculate_shipping_tax($shipping_amount, $order) {
        global $WCMp, $woocommerce;

        $wc_tax_enabled = get_option('woocommerce_calc_taxes');
        if ('no' === $wc_tax_enabled)
            return 0;

        $tax_based_on = get_option('woocommerce_tax_based_on');

        $WC_Tax = new WC_Tax();

        if ('base' === $tax_based_on) {
            $default = wc_get_base_location();
            $country = $default['country'];
            $state = $default['state'];
            $postcode = '';
            $city = '';
        } elseif ('billing' === $tax_based_on) {
            $country = $order->billing_country;
            $state = $order->billing_state;
            $postcode = $order->billing_postcode;
            $city = $order->billing_city;
        } else {
            $country = $order->shipping_country;
            $state = $order->shipping_state;
            $postcode = $order->shipping_postcode;
            $city = $order->shipping_city;
        }

        $matched_tax_rates = array();

        $tax_rates = $WC_Tax->find_rates(array(
            'country' => $country,
            'state' => $state,
            'postcode' => $postcode,
            'city' => $city,
            'tax_class' => ''
        ));


        if ($tax_rates) {
            foreach ($tax_rates as $key => $rate) {
                if (isset($rate['shipping']) && 'yes' === $rate['shipping']) {
                    $matched_tax_rates[$key] = $rate;
                }
            }
        }

        $vendor_shipping_taxes = $WC_Tax->calc_shipping_tax($shipping_amount, $matched_tax_rates);
        $vendor_shipping_tax_total = $WC_Tax->round(array_sum($vendor_shipping_taxes));

        return $vendor_shipping_tax_total;
    }

    /**
     * format_order_details function
     * @access public
     * @param order id , product_id
     * @return array of order details
     */
    public function format_order_details($orders, $product_id) {
        $body = $items = array();
        $product = get_product($product_id)->get_title();
        foreach (array_unique($orders) as $order) {
            $i = $order;
            $order = new WC_Order($i);
            $body[$i] = array(
                'order_number' => $order->get_order_number(),
                'product' => $product,
                'name' => $order->shipping_first_name . ' ' . $order->shipping_last_name,
                'address' => $order->shipping_address_1,
                'city' => $order->shipping_city,
                'state' => $order->shipping_state,
                'zip' => $order->shipping_postcode,
                'email' => $order->billing_email,
                'date' => $order->order_date,
                'comments' => wptexturize($order->customer_note),
            );

            $items[$i]['total_qty'] = 0;
            foreach ($order->get_items() as $line_id => $item) {

                if ($item['product_id'] != $product_id && $item['variation_id'] != $product_id)
                    continue;

                $items[$i]['items'][] = $item;
                $items[$i]['total_qty'] += $item['qty'];
            }
        }

        return array('body' => $body, 'items' => $items, 'product_id' => $product_id);
    }

}
?>