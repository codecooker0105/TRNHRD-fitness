(function ($) {
$(document).ready(function() {
	$("#dialog").hide();
	
	$("#add_stat_dialog").hide();
	
	$("a#add_stat").live('click',function(e) {
		e.preventDefault();
		$("#add_stat_dialog").dialog({
			autoOpen: false,
			modal: true,
			buttons : {
				"Yes, Please add this stat" : function() {
					var title = $("#title").val();
					var measurement_type = $("#measurement_type").val();
					var starting = $("#starting").val();
					if(title == ''){
						$("#stat_message").html("Title is required");
					}else{
						$.post("/member/add_stat",
						{ title: title,measurement_type: measurement_type,starting:starting},
							function(data){
								if(data.error == undefined){
									window.location.href = '/member/edit_stats';
									$("#add_stat_dialog").dialog("close");
								}else{
									$("#stat_message").html(data.error);
								}
								//$("#get_photo").attr("disabled","");
							},"json"
						 );
					}
				},
				"Cancel" : function() {
					$(this).dialog("close");
				}
			}
		});
		
		$("#add_stat_dialog").dialog("open");
	});
	
	$(".confirmDeleteLink").click(function(e) {
		e.preventDefault();
		var targetUrl = $(this).attr("href");
		
		$("#dialog").dialog({
			autoOpen: false,
			modal: true,
			buttons : {
				"Yes, Please remove this stat" : function() {
					window.location.href = targetUrl;
				},
				"Cancel" : function() {
					$(this).dialog("close");
				}
			}
		});
		
		$("#dialog").dialog("open");
	});
	
	
});

})(jQuery);
