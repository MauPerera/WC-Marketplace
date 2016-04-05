<?php
/**
 * The template for displaying vendor dashboard for non-vendors
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/non_vendor_dashboard.php
 *
 * @author 		dualcube
 * @package 	WCMm/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;
$user = wp_get_current_user();
if($user && !in_array( 'dc_pending_vendor', $user->roles ) && !in_array( 'administrator', $user->roles )) {
?>
<div class="wcmp_main_holder toside_fix">
  <div class="wcmp_headding2">General</div>
	<div class="vendor_apply">
		<form method="post">
			<table class="vendor_apply" >
				<tbody>
					<tr><?php _e('Your vendor account is not approved yet!',  $WCMp->text_domain ) ?></tr>
					<?php 
						echo $WCMp->user->wcmp_woocommerce_add_vendor_form(); 
					?>
				</tbody>
			</table>
		</form>
	</div>
</div>
<?php } 

if($user &&  in_array( 'administrator', $user->roles )) { ?>
  <div class="vendor_apply">
  	<p>
  		<?php _e('You have logged in as Administrator. Please log out and then view this page.' , $WCMp->text_domain); ?>
  	</p>
  </div>
<?php
 }
 if($user &&  in_array( 'dc_pending_vendor', $user->roles )) { ?>
	<div class="vendor_apply">
		<p>
			<?php _e('Congratulations! You have successfully applied as a Vendor. Please wait for further notifications from the admin.' , $WCMp->text_domain); ?>
		</p>
  </div>
<?php
}
?>