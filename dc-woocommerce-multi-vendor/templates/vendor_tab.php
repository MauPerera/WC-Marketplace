<?php
/**
 * The template for displaying single product page vendor tab 
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor_tab.php
 *
 * @author 		dualcube
 * @package 	dc-product-vendor/Templates
 * @version   2.2.0
 */
 
global $WCMp, $product;
$review_settings = get_option('wcmp_general_sellerreview_settings_name');
	$html = '';
	$vendor = get_wcmp_product_vendors( $product->id );
	if( $vendor ) {
		$html .= '<div class="product-vendor">';
		$html .= apply_filters('wcmp_before_seller_info_tab', ''); 
		$html .= '<h2>' . $vendor->user_data->display_name . '</h2>';
		echo $html;
		$term_vendor = wp_get_post_terms( $product->id, 'dc_vendor_shop' );	
		if( !is_wp_error($term_vendor) ) {
			$rating_result_array = wcmp_get_vendor_review_info($term_vendor[0]->term_id);
			if(isset($review_settings['is_sellerreview'])) {
				$term_link = get_term_link($term_vendor[0]);
				$rating_result_array['shop_link'] = $term_link;
				echo '<div style="text-align:left; float:left;">';				
				$WCMp->template->get_template( 'review/rating-vendor-tab.php', array('rating_val_array' => $rating_result_array));
				echo "</div>";
				echo '<div style="clear:both; width:100%;"></div>';
			}
		}
		$html = '';
		if( '' != $vendor->description ) {
			$html .= '<p>' . $vendor->description . '</p>';
		}
		$html .= '<p><a href="' . $vendor->permalink . '">' . sprintf( __( 'More Products from %1$s', $WCMp->text_domain ), $vendor->user_data->display_name ) . '</a></p>';
		$html .= apply_filters('wcmp_after_seller_info_tab', ''); 
		$html .= '</div>';
		echo $html;		
	}
	
	
	
?>