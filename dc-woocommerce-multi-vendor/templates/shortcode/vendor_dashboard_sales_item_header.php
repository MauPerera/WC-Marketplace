<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_dashboard_sales_item_header.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.3.0
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;
?>
<tr>
	<td align="center" >ID</td>
	<td  align="center" >SKU</td>
	<td class="no_display"  align="center" >Sales</td>
	<td class="no_display" align="center" >Discount</td>
	<td align="center" >My Earnings</td>
</tr>