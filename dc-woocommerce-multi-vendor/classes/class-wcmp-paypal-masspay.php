<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class 		WCMp Paypal Masspay Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		DualCube
 */ 
class WCMp_Paypal_Masspay {
	
	public $is_masspay_enable;
	public $payment_schedule;
	public $api_username;
	public $api_pass;
	public $api_signature;	
	public $test_mode;
	
	public function __construct() {
		$masspay_admin_settings = get_option("wcmp_payment_settings_name");
		
		if($masspay_admin_settings  && array_key_exists('is_mass_pay', $masspay_admin_settings)) {
			$this->is_masspay_enable = true;
			$this->payment_schedule = $masspay_admin_settings['payment_schedule'];
			$this->api_username = $masspay_admin_settings['api_username'];
			$this->api_pass = $masspay_admin_settings['api_pass'];
			$this->api_signature = $masspay_admin_settings['api_signature'];
			if(array_key_exists('is_testmode', $masspay_admin_settings)) {
				$this->test_mode = true;
			}
		}								
	}
	
	/**
	 * Init payPal Mass pay api
	 */
	public function call_masspay_api($receiver_information) {
		global $WCMp;
		require_once($WCMp->plugin_path.'lib/paypal/CallerService.php');
		session_start();
		$emailSubject = urlencode('You have money!');
		$receiverType = urlencode('EmailAddress');
		$currency = urlencode(get_woocommerce_currency());
		$nvpstr = '';
		if($receiver_information) {
			$j = 0;
			foreach($receiver_information as $receiver) {				
				$receiverEmail = urlencode($receiver['recipient']);
				$amount = urlencode($receiver['total']);
				$uniqueID = urlencode($receiver['vendor_id']);
				$note = urlencode($receiver['payout_note']);
				$nvpstr.="&L_EMAIL$j=$receiverEmail&L_Amt$j=$amount&L_UNIQUEID$j=$uniqueID&L_NOTE$j=$note";
				$j++;
			}
			$nvpstr.="&EMAILSUBJECT=$emailSubject&RECEIVERTYPE=$receiverType&CURRENCYCODE=$currency" ;			
			doProductVendorLOG($nvpstr);			
			$resArray=hash_call("MassPay",$nvpstr);			
			$ack = strtoupper($resArray["ACK"]);
			if($ack == "SUCCESS" ||  $ack == "SuccessWithWarning" ){
				doProductVendorLOG(json_encode($resArray));
				return $resArray;
			} else {
				doProductVendorLOG(json_encode($resArray));
				return false;
			}
		}
		return false;
	}

	/**
	 * Process PayPal masspay 
	 */
	public function do_paypal_masspay() {
		global $WCMp;
		$commissions = $this->get_query_commission();
		$commission_data = $commission_totals = $commissions_data = array();
		if($commissions) {
			$transaction_data = array();			
			foreach($commissions as $commission) {
				//doProductVendorLOG("commision id ---".$commission->ID."---commision_id");
				$WCMp_Commission = new WCMp_Commission();
				$commission_data = $WCMp_Commission->get_commission( $commission->ID );
				$commission_order_id = get_post_meta( $commission->ID, '_commission_order_id', true );
				$vendor_shipping = get_post_meta($commission->ID, '_shipping', true);
				$vendor_tax = get_post_meta($commission->ID, '_tax', true);
				
				$order = new WC_Order ( $commission_order_id );
				$vendor = get_wcmp_vendor_by_term($commission_data->vendor->term_id);									
				$payment_type = get_user_meta($vendor->id, '_vendor_payment_mode', true);				
				if($payment_type == 'direct_bank') continue;				
				if(!is_object($vendor)) continue;				
				$due_vendor = $vendor->wcmp_get_vendor_part_from_order($order, $commission_data->vendor->term_id);
				if(!$vendor_shipping) $vendor_shipping = $due_vendor['shipping'];
				if(!$vendor_tax) $vendor_tax = $due_vendor['tax'];
				
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
			}
			// Set info for all payouts
			$currency = get_woocommerce_currency();
			$payout_note = sprintf( __( 'Total commissions earned from %1$s as at %2$s on %3$s', $WCMp->text_domain ), get_bloginfo( 'name' ), date( 'H:i:s' ), date( 'd-m-Y' ) );
			
			$commissions_data = array();
			foreach( $commission_totals as $vendor_id => $total ) {
				
				if(!isset($total['amount'])) $total['amount'] = 0;
				if(isset($total['transaction_fee'])) $total_payable = $total['amount'] + $total['transaction_fee'];
				else $total_payable = $total['amount'];
				
				// Get vendor data
				$vendor = get_wcmp_vendor_by_term($vendor_id);
				$vendor_paypal_email = get_user_meta($vendor->id, '_vendor_paypal_email', true);
				// Set vendor recipient field
				if( isset( $vendor_paypal_email ) && strlen( $vendor_paypal_email ) > 0 ) {
					$recipient = $vendor_paypal_email;
					$commissions_data[] = array( 
							'recipient' => $recipient,
							'total' => $total_payable,
							'currency' => $currency,
							'vendor_id' =>$vendor_id,
							'payout_note' =>$payout_note
					);
				}
			}		
			$result = $this->call_masspay_api($commissions_data);
			if($result) {
				// create a new transaction by vendor
				$WCMp->transaction->insert_new_transaction($transaction_data, 'wcmp_completed', 'paypal_masspay', $result);
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
?>