jQuery(document).ready(function($) {
	$('#report_abuse').click(function(e){
		e.preventDefault();
		$('#report_abuse_form').simplePopup();
	});
	
	$('.submit-report-abuse').on('click' , function(e) {
		 e.preventDefault();
		 var data = {
				action : 'send_report_abuse',
				product_id : $('.report_abuse_product_id').val(),
				name : $('.report_abuse_name').val(),
				email : $('.report_abuse_email').val(),
				msg : $('.report_abuse_msg').val(),
		 }	
		 $.post(woocommerce_params.ajax_url, data, function(responsee) {
		 	$('.simplePopupClose').click();
		 });		
	});
	
	$('#vendor_sort_type').change(function() {
		selected_type = $('#vendor_sort_type').val();
	
		if(selected_type == 'category') {
			
			var category_data = {
				action: 'vendor_list_by_category',
			}
			
			$.post(woocommerce_params.ajax_url, category_data, function(response) {
				$('#vendor_sort_type').after(response);
			
			});
		} else {
			$('#vendor_sort_category').remove();
		}
		
	});
});