<?php
/**
 * The template for displaying vendor report
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_report.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
 
global $WCMp;
?>
<div class="wcmp_main_holder toside_fix">
	<div class="wcmp_headding1">
		<ul>
			<li><?php _e( 'Stats & Reports', $WCMp->text_domain );?></li>
			<li class="next"> > </li>
			<li><?php _e( 'Overview', $WCMp->text_domain );?></li>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="wcmp_mixed_txt some_line"> <span><?php _e( 'Showing stats and reports for : ', $WCMp->text_domain );?></span> 
		<?php
			if(!isset($_GET['wcmp_stat_start_dt']) || !isset($_GET['wcmp_stat_end_dt'])) {
				echo date('F Y');
			} else {
				echo date('d, F Y', strtotime($_GET['wcmp_stat_start_dt'])) .' - '. date('d, F Y', strtotime($_GET['wcmp_stat_end_dt'])); 
			}
		?>
		<div class="clear"></div>
	</div>
	<form name="wcmp_vendor_dashboard_stat_report" method="get" >
		<div class="wcmp_form1 ">
			<p><?php _e( 'Select Date Range :', $WCMp->text_domain );?></p>
			<input type="text" name="wcmp_stat_start_dt" value="<?php echo isset($_GET['wcmp_stat_start_dt']) ? $_GET['wcmp_stat_start_dt'] : ''; ?>" class="pickdate gap1 wcmp_stat_start_dt">
			<input type="text" name="wcmp_stat_end_dt" value="<?php echo isset($_GET['wcmp_stat_end_dt']) ? $_GET['wcmp_stat_end_dt'] : ''; ?>" class="pickdate wcmp_stat_end_dt">
			<button name="submit_button" type="submit" value="Show" class="wcmp_black_btn "><?php _e( 'Show', $WCMp->text_domain );?></button>
		</div>
	</form>
	<div class="wcmp_ass_holder_box">
		<div class="wcmp_displaybox2">
			<h4><?php _e( 'Total Sales', $WCMp->text_domain );?></h4>
			<h3><sup><?php echo get_woocommerce_currency_symbol();?></sup><?php echo $total_vendor_sales; ?></h3>
		</div>
		<div class="wcmp_displaybox2">
			<h4><?php _e( 'My Earnings', $WCMp->text_domain );?></h4>
			<h3><sup><?php echo get_woocommerce_currency_symbol();?></sup><?php echo $total_vendor_earning; ?></h3>
		</div>
		<div class="clear"></div>
		<p>&nbsp; </p>
		<div class="wcmp_displaybox3"><?php _e( ' Total number of', $WCMp->text_domain );?><span><?php _e( 'Order placed', $WCMp->text_domain );?></span>
			<h3><?php echo $total_order_count; ?></h3>
		</div>
		<div class="wcmp_displaybox3"> <?php _e( 'Number of ', $WCMp->text_domain );?><span><?php _e( 'Purchased Products', $WCMp->text_domain );?></span>
			<h3><?php echo $total_purchased_products; ?></h3>
		</div>
		<div class="wcmp_displaybox3"><?php _e( ' Number of ', $WCMp->text_domain );?><span> <?php _e( ' Coupons used', $WCMp->text_domain );?></span>
			<h3><?php echo $total_coupon_used; ?></h3>
		</div>
		<div class="wcmp_displaybox3"><?php _e( ' Total ', $WCMp->text_domain );?><span><?php _e( ' Coupon Discount ', $WCMp->text_domain );?></span> <?php _e( ' value ', $WCMp->text_domain );?>
			<h3><?php echo get_woocommerce_currency_symbol();?><?php echo $total_coupon_discuont_value; ?></h3>
		</div>
		<div class="wcmp_displaybox3"><?php _e( '  Number of  ', $WCMp->text_domain );?><span><?php _e( '  Unique Customers  ', $WCMp->text_domain );?></span>
			<h3><?php echo count($total_customers); ?></h3>
		</div>
		<div class="clear"></div>
	</div>
	<?php
		$capabilities_settings = get_wcmp_vendor_settings('wcmp_capabilities_settings_name');
		if( isset($capabilities_settings['is_order_csv_export']) ) {
			if( $capabilities_settings['is_order_csv_export'] == 'Enable' ) {
				?>
				<div class="wcmp_mixed_txt" > <span><?php _e( 'Download CSV to get complete Stats & Reports', $WCMp->text_domain );?></span>
					<form name="wcmp_vendor_dashboard_stat_export" method="post" >
						<input type="hidden" name="wcmp_stat_export_submit" value="submit" />
						<button type="submit" class="wcmp_black_btn" name="wcmp_stat_export" value="export" style="float:right"><?php _e( 'Download CSV', $WCMp->text_domain );?></button>
						<div class="clear"></div>
					</form>
				</div>
				<?php
			}
		}
	?>
</div>