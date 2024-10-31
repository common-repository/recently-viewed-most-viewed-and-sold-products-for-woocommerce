let rvpfw_modal = ( show = true ) => {
	if(show) {
		jQuery('#recently-viewed-products-for-woocommerce-modal').show();
	}
	else {
		jQuery('#recently-viewed-products-for-woocommerce-modal').hide();
	}
}

jQuery(function($){
	$('.recently-viewed-products-for-woocommerce-help-heading').click(function(e){
		var $this = $(this);
		var $target = $this.data('target');
		$('.recently-viewed-products-for-woocommerce-help-text:not('+$target+')').slideUp();
		if($($target).is(':hidden')){
			$($target).slideDown();
		}
		else {
			$($target).slideUp();
		}
	});

	$('#recently-viewed-products-for-woocommerce_report-copy').click(function(e) {
		e.preventDefault();
		$('#recently-viewed-products-for-woocommerce_tools-report').select();

		try {
			var successful = document.execCommand('copy');
			if( successful ){
				$(this).html('<span class="dashicons dashicons-saved"></span>');
			}
		} catch (err) {
			console.log('Oops, unable to copy!');
		}
	});
})