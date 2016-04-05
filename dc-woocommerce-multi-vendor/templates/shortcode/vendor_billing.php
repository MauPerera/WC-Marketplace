<?php
/**
 * The template for displaying vendor report
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_billing.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.3.0
 */
 
global $WCMp;
?>

<div class="wcmp_main_holder toside_fix">
	<div class="wcmp_headding1">
		<ul>
			<li><?php _e( 'Store Settings ', $WCMp->text_domain );?></li>
			<li class="next"> < </li>
			<li><?php _e( 'Billing', $WCMp->text_domain );?></li>
		</ul>
		<button class="wcmp_ass_btn edit_billing"><?php _e( 'Edit', $WCMp->text_domain );?></button>
		<div class="clear"></div>
	</div>
	<form method="post" name="shop_settings_form" class="wcmp_billing_form">
    <div class="wcmp_form1">
    	<div class="wcmp_headding2"><?php _e( 'Payment Mode', $WCMp->text_domain );?></div>
    	<div class="two_third_part">
				<div class="select_box no_input">
					<?php $vendor_payment_mode_select = apply_filters( 'wcmp_vendor_payment_mode', array('paypal_masspay' => __('PayPal Masspay', $WCMp->text_domain), 'direct_bank' => __('Direct Bank', $WCMp->text_domain)) ); ?>
					<select id="vendor_payment_mode" disabled name="vendor_payment_mode" class="user-profile-fields">
						<?php foreach( $vendor_payment_mode_select as $key => $value ) { ?>
							<option <?php if($vendor_payment_mode['value'] == $key) echo 'selected'?>  value="<?php echo $key; ?>"><?php echo $value; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="clear"></div>
			<div class="wcmp_headding2"><?php _e( 'Paypal', $WCMp->text_domain );?></div>
			<p><?php _e( 'Enter your Paypal ID', $WCMp->text_domain );?></p>
			<input  class="long no_input" readonly type="text" name="vendor_paypal_email" value="<?php echo isset($vendor_paypal_email['value']) ? $vendor_paypal_email['value']: ''; ?>"  placeholder="<?php _e( 'Enter your Paypal ID', $WCMp->text_domain );?>">
			<div class="wcmp_headding2"><?php _e( 'Bank Transfer', $WCMp->text_domain );?></div>
			<p><?php _e( 'Enter your Bank Details', $WCMp->text_domain );?></p>
			<div class="two_third_part">
				<div class="select_box no_input">
					<select id="vendor_bank_account_type" disabled name="vendor_bank_account_type" class="user-profile-fields">
						<option <?php if($vendor_bank_account_type['value'] == 'current') echo 'selected'?> value="current"><?php _e( 'Current', $WCMp->text_domain );?></option>
						<option <?php if($vendor_bank_account_type['value'] == 'savings') echo 'selected'?>  value="savings"><?php _e( 'Savings', $WCMp->text_domain );?></option>
					</select>
				</div>
			</div>
			<input class="long no_input" readonly type="text" id="vendor_bank_account_number" name="vendor_bank_account_number" class="user-profile-fields" value="<?php echo isset($vendor_bank_account_number['value']) ? $vendor_bank_account_number['value'] : ''; ?>" placeholder="<?php _e( 'Account Holder Name', $WCMp->text_domain );?>">
			<div class="half_part">
				<input class="long no_input" readonly type="text" id="vendor_bank_name" name="vendor_bank_name" class="user-profile-fields" value="<?php echo isset($vendor_bank_name['value']) ? $vendor_bank_name['value'] : ''; ?>" placeholder="<?php _e( 'Bank Name', $WCMp->text_domain );?>">
			</div>
			<div class="half_part">
				<input class="long no_input" readonly type="text" id="vendor_aba_routing_number" name="vendor_aba_routing_number" class="user-profile-fields" value="<?php echo isset($vendor_aba_routing_number['value']) ? $vendor_aba_routing_number['value'] : '';  ?>" placeholder="<?php _e( 'ABA Routing Number', $WCMp->text_domain );?>">
			</div>
			<div class="clear"></div>
			<textarea class="long no_input" readonly name="vendor_bank_address" cols="" rows="" placeholder="<?php _e( 'Bank Address', $WCMp->text_domain );?>"><?php echo isset($vendor_bank_address['value']) ? $vendor_bank_address['value'] : ''; ?></textarea>
			<div class="one_third_part">
				<input class="long no_input" readonly type="text" placeholder="<?php _e( 'Destination Currency', $WCMp->text_domain );?>" name="vendor_destination_currency" value="<?php echo isset($vendor_destination_currency['value']) ? $vendor_destination_currency['value'] : ''; ?>">
			</div>
			<div class="one_third_part">
				<input class="long no_input" readonly type="text" placeholder="<?php _e( 'IBAN', $WCMp->text_domain );?>"  name="vendor_iban" value="<?php echo isset($vendor_iban['value']) ? $vendor_iban['value'] : ''; ?>">
			</div>
			<div class="one_third_part">
				<input class="long no_input" readonly type="text" placeholder="<?php _e( 'Account Holder Name', $WCMp->text_domain );?>"  name="vendor_account_holder_name" value="<?php echo isset($vendor_account_holder_name['value']) ? $vendor_account_holder_name['value'] : ''; ?>">
			<div class="clear"></div>
		</div>
		<?php do_action('other_exta_field_dcmv'); ?>
    <div class="action_div_space"> </div>
		<div class="action_div">
			<?php
				if($is_billing_saved == 1) { ?>
					<div class="green_massenger"><i class="fa fa-check"></i> &nbsp; <?php _e( 'All Options Saved', $WCMp->text_domain );?></div>
				<?php } 
			?>
			<button class="wcmp_orange_btn" name="store_save_billing" ><?php _e( 'Save Options', $WCMp->text_domain );?></button>
			<div class="clear"></div>
		</div>
  </form>
</div>