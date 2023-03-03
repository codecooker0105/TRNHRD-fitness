var select_item;
var clicked_item;
var section_item;
var exercise_count = 100;
(function ($) {
$(document).ready(function() {
	updateWorkoutListControls();
	
		
	$("#complete_dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 300,
		modal: true,
		buttons: {
			'Create New Workout': function() {	
				location.href = '/fit-calculator';
			},
			'Close': function() {	
				$(this).dialog('close');
			}
		},
		close: function() {
			//allFields.val('').removeClass('ui-state-error');
		}
	});
	
	$("#error_dialog").dialog({
			bgiframe: true,
			autoOpen: false,
			height: 300,
			modal: true,
			buttons: {
				'Return to Calculator': function() {
					$(this).dialog('close');
				}
			},
			close: function() {
				//allFields.val('').removeClass('ui-state-error');
			}
		});
		
	$("#dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 300,
		modal: true,
		buttons: {
			'Add New Section': function() {
				var bValid = true;
				//allFields.removeClass('ui-state-error');

				//bValid = bValid && checkLength(name,"username",3,16);
				//bValid = bValid && checkLength(email,"email",6,80);
				//bValid = bValid && checkLength(password,"password",5,16);
				
				if (bValid) {
					$('#workout_list').append('<li class="section"><span class="move ui-icon ui-icon-arrowthick-2-n-s"></span><span class="title">' + $('#section_dropdown').val() + '</span><span class="remove ui-icon ui-icon-circle-close"></span><ul class="categories"></ul></li>'); 
					updateWorkoutListControls();
					$(this).dialog('close');
				}
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			//allFields.val('').removeClass('ui-state-error');
		}
	});
	
	$("#exercise_dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 300,
		modal: true,
		buttons: {
			'Add New Exercise': function() {
				var bValid = true;
				
				if (bValid) {
					$.post('/member/get_admin_exercise_type',{id:$('#exercise_type_dropdown').val()},function(data){
						clicked_item.parent().children('ul').append(data);
						updateWorkoutListControls();
					},'html');
					
					
					
					$(this).dialog('close');
				}
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			//allFields.val('').removeClass('ui-state-error');
		}
	});
	
	
	setInterval('updateWorkoutList()',1000);
	
	function showResponse(responseText, statusText){
		$('#complete_dialog').dialog('open');
	}
	
	
	$("#exercise_library li.exercise").draggable({handle: 'span.ui-widget',
													revert:false,
													helper:'clone' });
		
		
	$('.add_section').click(function() {
		$('#section_dialog').dialog('open');
	});
	
	$("#section_dialog").dialog({
			bgiframe: true,
			autoOpen: false,
			height: 300,
			modal: true,
			buttons: {
				'Add New Section': function() {
					$.post('/member/get_section',{id:$('#section_dropdown').val()},function(data){
						$('#workout_list').append(data);
						updateWorkoutListControls();
					},'html');
					
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
				//allFields.val('').removeClass('ui-state-error');
			}
		});
		

});

function updateWorkoutList(){
	var workout_list = '';
	$('#workout_list li.section').each(function(index){
		if(workout_list != ''){
			workout_list += '|';
		}
		workout_list += 's|' + $(this).children('input.section_id').fieldValue();
		$(this).find('ul.workout_categories li').each(function(index2){
			workout_list += '|' + $(this).find('input.category_id').val();
		});
	});
	//alert(workout_list);
	$("#workout_list_value").val(workout_list);
}

function updateWorkoutListControls(){
	$('.add_exercise').unbind("click").click(function(){
		clicked_item = $(this);
		$('#exercise_dialog').dialog('open');
		return false;
	});
				
	$('a[class^="select_exercise"]').unbind("click").click(function() {
		clicked_item = $(this);
		$("input#current_exercise_edit").val($(this).attr('id').substring(9));
		$('#dialog_' + $(this).attr('class').substring(15)).dialog('open');
		return false;
	});	
	
	$("li.category").droppable({
		accept: ".exercise",
		activeClass: "ui-state-hover",
		hoverClass: "ui-state-active",
		drop: function( event, ui ) {
			$( this )
				.find( "a.play-exercise" )
					.html( ui.draggable.find('span.ex_title').html() );
			$( this )
				.find( "a.play-exercise" )
					.attr('href', ui.draggable.find('a').attr('href') );
		}
	});		

	$('.remove').unbind("click").click(function() {
		$(this).parent().fadeOut(function(){
			$(this).remove();
		});
	});
	
	$('.remove_exercise').unbind("click").click(function() {
		$(this).parents('li.category').first().fadeOut(function(){
			$(this).remove();
		});
		return false;
	});
	
	$('.add_set').unbind("click").click(function() {
		$(this).parents('table').first().children('tbody').first().append($(this).parents('table').first().children('tbody').first().children('tr.bottom').first().removeClass('bottom').clone().addClass('bottom'));
		$(this).parents('table').first().children('tbody').first().find('tr.bottom').find('span.set_number').html(parseInt($(this).parents('table').first().children('tbody').first().find('tr.bottom').find('span.set_number').html()) + 1);
		$(this).parents('table').first().children('tbody').first().find('tr.bottom').find('td.ex_options').remove();
		$(this).parents('table').first().children('tbody').first().children('tr').first().children('td').first().attr('rowspan',parseInt($(this).parents('table').first().children('tbody').first().children('tr').first().children('td').first().attr('rowspan')) + 1);
		return false;
	});
	
	$('.remove_set').unbind("click").click(function() {
		if($(this).parents('table').first().children('tbody').first().children('tr').length > 1){
			$(this).parents('table').first().children('tbody').first().children('tr.bottom').first().remove();
			$(this).parents('table').first().children('tbody').first().children('tr').last().addClass('bottom');
		}
		return false;
	});
	
	$('.remove_section').unbind("click").click(function() {
		$(this).parents('li').first().remove();
		return false;
	});
	
	//$('.workout_exercises').toggle();
	//$('.workout_categories').toggle();
	$('.section_title').unbind("click").click(function(){
		$(this).removeClass('off').addClass('on');
		$(this).parent().find('.workout_categories').toggle();
		return false;
	});
	
	$('.workout_category_title').unbind("click").click(function(){
		$(this).removeClass('off').addClass('on');
		$(this).parent().find('.workout_exercises').toggle();
		return false;
	});
	
	$('.edit_sets').unbind("click").click(function(){
		$("input#current_exercise_edit").val($(this).attr('id').substring(5));
		$('#sets_dropdown').val($(this).parent().parent().find('input.sets').val());
		$('#reps_dropdown').val($(this).parent().parent().find('input.reps').val());
		$('#sets_reps_dialog').dialog('open');
		return false;
	});
	
	$('.play-exercise').colorbox();
			
	
	$("#workout_list").sortable({ axis: 'y',handle: '.move' });
	$("#workout_list ul.workout_categories").sortable({axis: 'y',handle: '.move'});
	
	$('#workout_list select.set_type').each(function(){
		if($(this).val() == 'sets_reps'){
			$(this).parents('table').first().find('select.reps').show();
			$(this).parents('table').first().find('select.time').hide();
		}else if($(this).val() == 'sets_time'){
			$(this).parents('table').first().find('select.reps').hide();
			$(this).parents('table').first().find('select.time').show();
		}
	});
	
	$('#workout_list select.weight_option').each(function(){
		if($(this).val() == 'weighted'){
			$(this).parents('table').first().find('span.weight_input_box').show();
			$(this).parents('table').first().find('span.bodyweight').hide();
		}else if($(this).val() == 'bodyweight'){
			$(this).parents('table').first().find('span.weight_input_box').hide();
			$(this).parents('table').first().find('span.bodyweight').show();
		}
	});
	
	$('#workout_list select.set_type').change(function(){
		if($(this).val() == 'sets_reps'){
			$(this).parents('table').first().find('select.reps').show();
			$(this).parents('table').first().find('select.time').hide();
		}else if($(this).val() == 'sets_time'){
			$(this).parents('table').first().find('select.reps').hide();
			$(this).parents('table').first().find('select.time').show();
		}
	});
	
	$('#workout_list select.weight_option').change(function(){
		if($(this).val() == 'weighted'){
			$(this).parents('table').first().find('span.weight_input_box').show();
			$(this).parents('table').first().find('span.bodyweight').hide();
		}else if($(this).val() == 'bodyweight'){
			$(this).parents('table').first().find('span.weight_input_box').hide();
			$(this).parents('table').first().find('span.bodyweight').show();
		}
	});

}
})(jQuery);