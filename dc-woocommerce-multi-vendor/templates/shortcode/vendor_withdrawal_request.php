<?php
/**
 * The template for displaying vendor withdrawal content
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_transaction_thankyou.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
global $WCMp;
$transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : false;
if($transaction_id) { 
	$transaction = get_post($transaction_id);
	$vendor = get_wcmp_vendor_by_term($transaction->post_author);
?>
<p><?php echo apply_filters( 'wcmp_thankyou_transaction_received_text', sprintf(__( 'Hello,<br>We have received a new withdrawal request for $%s from you and Your request is being processed.The order details are as follow:', $WCMp->text_domain), get_post_meta($transaction_id, 'amount', true)), $transaction_id ); ?></p>
<table cellspacing="0" cellpadding="6"  border="1" >
	<thead>
		<?php $commission_details  = $WCMp->transaction->get_transaction_item_details($transaction_id); 
		?>
		<tr>
			<?php
			if(!empty($commission_details['header'])) { ?>
				<tr>
					<?php
						foreach ( $commission_details['header'] as $header_val ) {	?>
							<th class="td" scope="col"><?php echo $header_val; ?></th><?php
						}
					?>
				</tr>	<?php
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
			if(!empty($commission_details['body'])) {
				foreach ( $commission_details['body'] as $commission_detail ) {	?>
					<tr>
						<?php
							foreach($commission_detail as $details) {
								foreach($details as $detail_key => $detail) {
									?>
									<td class="td" scope="col"><?php echo $detail; ?></td><?php
								}
							}
						?>
					</tr><?php
				}
			}
			if ( $totals =  $WCMp->transaction->get_transaction_item_totals($transaction_id, $vendor) ) {
				foreach ( $totals as $total ) {
					?><tr>
						<td class="td" scope="col" colspan="2" ><?php echo $total['label']; ?></td>
						<td class="td" scope="col" ><?php echo $total['value']; ?></td>
					</tr><?php
				}
			}
		?>
	</tbody>
</table>
<?php } else { ?>
	<p><?php printf(__( 'Hello,<br>Unfortunately your request for withdrawal amount could not be completed. You may try again later, or check you PayPal settings in your account page, or contact the admin at <b>%s</b>', $WCMp->text_domain), get_option( 'admin_email' ));?></p>
<?php } ?>