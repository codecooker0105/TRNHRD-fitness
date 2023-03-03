(function ($) {
$(document).ready(function() {	
	$("#client").change(function(){		
		if($("#client").val() != ''){
			window.location.href = '/member/client_calendar/' + $("#client").val();
		}
	});
});
})(jQuery);