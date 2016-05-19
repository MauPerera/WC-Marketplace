<?php
/**
 * WCMp MassPay Cron Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		DualCube
 */
 
class WCMp_MassPay_Cron {

	public function __construct() {
		add_action('masspay_cron_start', array(&$this, 'do_mass_payment') );             
	}
        
	/**
	 * Calculate the amount and selete payment method.
	 *
	 *
	 */
	function do_mass_payment() {
		global $WCMp;
		$payment_admin_settings = get_option('wcmp_payment_settings_name');
		if(!isset($payment_admin_settings['wcmp_disbursal_mode_admin']))
			return;
		
		doProductVendorLOG("Cron Run Start for array creatation @ " . date('d/m/Y g:i:s A', time()));
		$commissions = $this->get_query_commission();		
		$commission_data = $commission_totals = $commissions_data = array();
		if($commissions) {
			$transaction_data = array();
			foreach($commissions as $commission) {				
				$WCMp_Commission = new WCMp_Commission();
				$commission_data = $WCMp_Commission->get_commission( $commission->ID );
				$commission_order_id = get_post_meta( $commission->ID, '_commission_order_id', true );
				$vendor_shipping = get_post_meta($commission->ID, '_shipping', true);
				$vendor_tax = get_post_meta($commission->ID, '_tax', true);				
				$order = new WC_Order ( $commission_order_id );
				$vendor = get_wcmp_vendor_by_term($commission_data->vendor->term_id);				
				$payment_type = get_user_meta($vendor->id, '_vendor_payment_mode', true);
					if(empty($payment_type)) {
						continue;
					}
				if(!preg_match('/masspay/', $payment_type)) continue;
				$due_vendor = $vendor->wcmp_get_vendor_part_from_order($order, $vendor->term_id);
					if(!empty($due_vendor)) {
						if(!$vendor_shipping) $vendor_shipping = $due_vendor['shipping'];
						if(!$vendor_tax) $vendor_tax = $due_vendor['tax'];
					}
				$vendor_due = 0;
				$vendor_due = (float)$commission_data->amount  + (float)$vendor_shipping + (float)$vendor_tax;				
				//check unpaid commission threshold
				$total_vendor_due = $vendor->wcmp_vendor_get_total_amount_due();
				$get_vendor_thresold = 0;
				if(isset($WCMp->vendor_caps->payment_cap['commission_threshold'])) $get_vendor_thresold = (float)$WCMp->vendor_caps->payment_cap['commission_threshold'];
				if($get_vendor_thresold > $total_vendor_due) continue;
				
				if(array_key_exists($commission_data->vendor->term_id, $transaction_data)) {
						$commission_totals[ $commission_data->vendor->term_id ]['amount'] += apply_filters( 'paypal_masspay_amount', $vendor_due, $commission_order_id, $commission_data->vendor->term_id);
				} else {							
						$commission_totals[ $commission_data->vendor->term_id ]['amount'] = apply_filters( 'paypal_masspay_amount', $vendor_due, $commission_order_id, $commission_data->vendor->term_id);
				}
				$transaction_data[$commission_data->vendor->term_id]['commission_detail'][$commission->ID] = $commission_order_id;
				$transaction_data[$commission_data->vendor->term_id]['amount'] = $commission_totals[ $commission_data->vendor->term_id ]['amount'];
				$transaction_data[$commission_data->vendor->term_id]['payment_mode'] = $payment_type;
			}			
			// Set info for all payouts
			$currency = get_woocommerce_currency();
			$payout_note = sprintf( __( 'Total commissions earned from %1$s as at %2$s on %3$s', $WCMp->text_domain ), get_bloginfo( 'name' ), date( 'H:i:s' ), date( 'd-m-Y' ) );			
			$commissions_data = array();
			$transactions_data = array();
			foreach( $commission_totals as $vendor_id => $total ) {
				if(!isset($total['amount'])) continue; //$total['amount'] = 0;
				if(isset($total['transaction_fee'])) $total_payable = $total['amount'] + $total['transaction_fee'];
				else $total_payable = $total['amount'];                                
				// Get vendor data
				$vendor_payment_mode = $transaction_data[$vendor_id]['payment_mode'];
				if(empty($vendor_payment_mode)) {
					continue;
				}
				$commissions_data[$vendor_payment_mode][] = array(
					'total' => $total_payable,
					'currency' => $currency,
					'vendor_id' =>$vendor_id,
					'payout_note' =>$payout_note
				);
				$transactions_data[$vendor_payment_mode][$vendor_id] = $transaction_data[$vendor_id];
			}			
			if(!empty($commissions_data)) {
				foreach($commissions_data as $payment_mode=>$payment_data) {
					// Call masspay api as vendor payment mode.
					$class = $payment_mode;
					$method = 'do_'.$payment_mode;
					if(!class_exists('WCMp_'.$class)) {
						$pro = explode('_', $payment_mode);
						if(!empty($pro)) {
							$provider = ucwords($pro[0]);
							$plug = 'WCMp_'.$provider.'_Gateway';
							$class = str_replace('_', '_Gateway_', $class);
							$plug = $plug.'_Masspay';							
							$WCMp_masspay_provider = new $plug();							
							$WCMp_masspay_provider->$method($payment_data, $transactions_data[$payment_mode]);
						}                                    
					} else {						
						$WCMp->$class->$method($payment_data, $transactions_data[$payment_mode]);
					}
				}
			}			
		}		
	}
        
	/**
	 * Get Commissions
	 *
	 * @return object $commissions
	 */
	public function get_query_commission() {
		$args = array(
			'post_type' => 'dc_commission',
			'post_status' => array( 'publish', 'private' ),
			'meta_key' => '_paid_status',
			'meta_value' => 'unpaid',
			'posts_per_page' => -1
		);
		$commissions = get_posts( $args );
		return $commissions;
	}
}
