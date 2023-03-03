$(document).ready(function() {
	
	
	$("a.delete_member").click(function(e) {
		e.preventDefault();
		var member_id = $(this).attr('id').replace('member','');
		var member_row = $(this).parent().parent();
		$("#delete_member_dialog").dialog({
			autoOpen: false,
			modal: true,
			buttons : {
				"Yes, Please delete this member" : function() {
					$.post("/admin/delete_member",
						{ user_id: member_id},
							function(data){
								if(data.error == undefined){
									$("#delete_member_dialog").dialog("close");
									member_row.remove();
								}else{
									alert(data.error);
								}
							},"json"
						 );
					}
				},
				"Cancel" : function() {
					$(this).dialog("close");
				}
		});
		
		$("#delete_member_dialog").dialog("open");
	});
	
	
});