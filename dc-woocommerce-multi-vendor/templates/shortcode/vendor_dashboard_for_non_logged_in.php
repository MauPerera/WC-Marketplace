<?php
/**
 * The template for displaying vendor dashboard non-loggedin
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_dashboard_for_non_logged_in.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;
?>
<div class="wcmp_main_holder toside_fix">
	<div class="wcmp_headding2">General</div>
	<div class="wcmp_form1">
		<div class="vendor_apply">
			<form method="post">
				<table class="vendor_apply" >
					<tbody>
						<tr><?php _e('Your vendor account is not approved yet!',  $WCMp->text_domain ) ?></tr>
						<tr> 
							<?php echo $WCMp->user->wcmp_woocommerce_register_form(); ?>
						</tr>
						<tr><input type="submit" name="vendor_apply" value="Save"></tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>