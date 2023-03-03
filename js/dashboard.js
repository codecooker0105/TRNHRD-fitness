(function ($) {
$(document).ready(function() {
	$('.play-exercise').colorbox();
	$('.popup_stats').colorbox({iframe:true,innerWidth:850,innerHeight:600});
	$('.exercise_type').toggle();
	$('.section').toggle();
	$('.section_title').click(function(){
		$(this).removeClass('off').addClass('on');
		$(this).parent().find('.section').toggle();
		return false;
	});
	
	$('.type_title').click(function(){
		$(this).removeClass('off').addClass('on');
		$(this).parent().find('.exercise_type').toggle();
		return false;
	});
	
	$("#weather_tabs a.tab_link").live('click',function(){
		$("a.on").removeClass('on');
		$(this).addClass('on');
		$("div.on").removeClass('on');
		$("#weather_" + $(this).attr('id')).addClass('on');
		
		return false;
	});
	
	$("#stat_date").datepicker();
	
	$("a.add_current_stat").live('click',function(e) {
		e.preventDefault();
		var stat_id = $(this).attr('id').replace('stat','');
		var currentTime = new Date();
		var month = currentTime.getMonth() + 1;
		var day = currentTime.getDate();
		var year = currentTime.getFullYear();
		$("#stat_date").val(month + "/" + day + "/" + year);
		$("#add_current_stat_dialog").dialog({
			autoOpen: false,
			modal: true,
			buttons : {
				"Yes, Please update this stat" : function() {
					var current = $("#current").val();
					var date = $("#stat_date").val();
					if(current == '' || date == ''){
						$("#stat_message").html("You must enter a value and selct a date");
					}else{
						$.post("/member/add_current_stat",
						{ current_value: current,id: stat_id,date_taken:date},
							function(data){
								if(data.error == undefined){
									$("#add_current_stat_dialog").dialog("close");
								}else{
									$("#stat_message").html(data.error);
								}
							},"json"
						 );
					}
				},
				"Cancel" : function() {
					$(this).dialog("close");
				}
			}
		});
		
		$("#add_current_stat_dialog").dialog("open");
	});
	
	$("a#add_featured_exercise").live('click',function(e) {
		e.preventDefault();
		$("#featured_exercise_upcoming_workouts").removeAttr('disabled');
		$("#featured_exercise_dialog").dialog('destroy');
		$("#featured_exercise_choices").hide();
		var exercise_id = $("input[name=featured_exercise_id]").val();
		$("#featured_exercise_dialog").dialog({
			width:400,
			autoOpen: false,
			modal: true,
			buttons : {
				"Select this workout" : function() {
					var current_workout = $("#featured_exercise_upcoming_workouts").val();
					$("#featured_exercise_upcoming_workouts").attr('disabled','disabled');
					$.post("/member/get_similiar_workout_exercises",
						{ exercise: exercise_id,workout_id: current_workout},
							function(data){
								if(data.error == undefined){
									if(data.exercises != 'none'){
										$("#replace_exercise_section").show();
										$("input#replace_exercise").attr('checked','checked');
										$("input#add_to_section").removeAttr('checked');
										$.each(data.exercises,function(i,exercise){
											$("select#replace_exercise_id")
												.append($("<option></option>")
											 	.attr("value",exercise.id)
											 	.text(exercise.section_title + ' - ' + exercise.exercise_title));
										});
									}else{
										$("#replace_exercise_section").hide();
										$("input#replace_exercise").removeAttr('checked');
										$("input#add_to_section").attr('checked','checked');
									}
									
									if(data.sections != 'none'){
										$.each(data.sections,function(i,exercise){											
											$("select#add_section_id")
												.append($("<option></option>")
											 	.attr("value",exercise.id)
											 	.text(exercise.section_title));
										});
									}
									
									$("#featured_exercise_choices").show();
									$("#featured_exercise_dialog").dialog("option",{
										buttons : {
											"Add Exercise to Workout" : function() {
												var uwe_id = $("#replace_exercise_id").val();
												var uws_id = $("#add_section_id").val();
												var featured_choice = $("input[name=featured_option]:checked").val();
												$.post("/member/add_featured_exercise_to_workout",
													{ exercise: exercise_id,workout_id: current_workout,choice:featured_choice,uwe:uwe_id,uws:uws_id},
														function(data){
															if(data.error == undefined){
																$("#featured_exercise_dialog").dialog('close');
															}
														}
													);
											},
											"Cancel" : function() {
												$(this).dialog("close");
											}
										}
									});
								}else{
									$("#stat_message").html(data.error);
								}
							},"json"
						 );
				},
				"Cancel" : function() {
					$(this).dialog("close");
				}
			}
		});
		
		$("#featured_exercise_dialog").dialog("open");
	});
	
	$("#dialog").hide();
	
	$("#add_weather_dialog").hide();
	
	$("a#add_weather").live('click',function(e) {
		e.preventDefault();
		$("#add_weather_dialog").dialog({
			autoOpen: false,
			modal: true,
			buttons : {
				"Yes, Please add this location" : function() {
					var zip = $("#zip").val();
					if(zip.length != 5){
						$("#weather_message").html("Zip code must be 5 numbers");
					}else{
						$.post("/member/add_weather",
						{ zip: zip},
							function(data){
								if(data.error == undefined){
									$.get('/member/get_weather_ajax', function(data) {
										$('#weather').html(data);
									});
									$("#add_weather_dialog").dialog("close")
								}else{
									$("#weather_message").html(data.error);
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
		
		$("#add_weather_dialog").dialog("open");
	});
	
	$(".confirmDeleteLink").live('click',function(e) {
		e.preventDefault();
		var targetUrl = $(this).attr("href");
		
		$("#dialog").dialog({
			autoOpen: false,
			modal: true,
			buttons : {
				"Yes, Please remove this location" : function() {
					$.post("/member/remove_weather",
						{ zip: targetUrl},
							function(data){
								if(data.success == 'true'){
									$("a#tab" + targetUrl).parent().remove();
									$("#weather_tab" + targetUrl).remove();
									$("#dialog").dialog("close");
									$("#weather_tabs a.tab_link:first").trigger('click');
									
								}
								//$("#get_photo").attr("disabled","");
							},"json"
						 );
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