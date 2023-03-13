<div id="secpage_header" class="workout_page_heading" >
	<h1>Workout Generator</h1>
    <p class="p-0">By observing, Adam, our model you can learn proper technique, form and muscle intervention. You can utilize the training calculator to create goal specific workouts that either you have made for yourself or we have provided for you. We have professionally designed workouts for all levels and are available for purchase through our store.</p>
</div>

<form action="/member/process_workout_generator" id="workout_generator_form" method="post">
<input type="hidden" name="workout_id" id="workout_id" value="<?php if(isset($workout_id)){ ?><?=$workout_id?><?php } ?>" />
<input type="hidden" name="trainer_workout_id" id="trainer_workout_id" value="<?php if(isset($trainer_workout_id)){ ?><?=$trainer_workout_id?><?php } ?>" />
<input type="hidden" name="group_workout_id" id="group_workout_id" value="<?php if(isset($group_workout_id)){ ?><?=$group_workout_id?><?php } ?>" />
<input type="hidden" name="trainer_group_workout_id" id="trainer_group_workout_id" value="<?php if(isset($trainer_group_workout_id)){ ?><?=$trainer_group_workout_id?><?php } ?>" />

<div id="generator_left">
	
    <h2 class="library_header">Workout Generator</h2>
	<p>Select a date and plan your exercises.  Title custom workouts if you wish. Visit your CALENDAR to see past, present and future workouts.  View your LOG BOOK to see your progress!</p>
    <p>Workout Title (Optional) <input type="text" name="workout_title" value="" id="workout_title" /></p>
    <h2 class="library_header">Workout Dates</h2>
    <p>You can select either a specific date or a range of dates.<br />
    <input type="text" value="<?php if(isset($workout_date)){ ?><?=$workout_date?><?php } ?>" id="date" class="date" /></p>
    <div id="week_days">
    <p><strong>Days of Week:<br /></strong>
		<?php foreach($weekdays as $day => $checkbox){
			?> <?php echo form_checkbox($checkbox);?> <?=$weekday_title[$day]?> &nbsp;<?
		} ?>
    </p>
    </div>
    
    <hr class="divider" />
    <h2 class="library_header">Workout Autogenerator</h2>
    <p>If you would like a workout generated for this client based on their fitness level and available equipment. Select one of the predefined focus and workout type below. You can always modify the workout afterwards.<br />
    <table width="100%" class="exercise-checkbox">
    <tr class="d-flex">
    	<td valign="top" class="w-100">
    	<p><strong>Available Equipment.</strong><br />
		<?php foreach($equipment as $id => $title){    
            ?><?php echo form_checkbox($available_equipment[$id]);?> <?=$title?><br /><?
        } ?>
        </p>
        </td>
        <td valign="top" class="w-100">
            <strong>Select focus:</strong><br />
            <?php echo form_dropdown('progression_id',$progressions,$progression_id,'class="progression"'); ?>
            <br />
            <strong>Select workout type:</strong><br />
            <?php echo form_dropdown('skeleton_workout_id',$skeleton_workouts,$skeleton_workout_id,'class="skeleton"'); ?><br />
        </td>
    </tr>
    </table>
    <input type="button" name="generate" id="generate" value="Generate Workout" /></p>
</div>

<div id="generator_right">
    <div class="right_block">
    	<div class="header"><h2 class="library_header ">Clients/Groups</h2><a href="/member/clients" class="small_edit edit-text-btn">Edit</a></div>
        <div class="inner_block w-100">
        <select name="client" id="client" >
        <option value="">Select a Client/Group</option>
		<?php if($clients){
			?><option value=""><strong>Single Clients</strong></option><?
			foreach($clients as $client){
				?><option value="<?=$client->user_id?>" <?php if(isset($client_id) && $client_id == $client->user_id){ ?> selected="selected"<?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$client->first_name . ' ' . $client->last_name?></option><?
			}
		} ?>
        <?php if($trainer_groups){
			?><option value=""><strong>Client Groups</strong></option><?
			foreach($trainer_groups as $group){
				?><option value="group-<?=$group['id']?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$group['title']?></option><?
			}
		} ?>
        </select>
        </div>
    </div>
    <div id="user_photo_area">
    	<div id="photo">
        </div>
    </div>
</div>

<hr style="clear:both" class="divider-hr"/>

<div id="workout_left">  
    
    
    <h2 class="library_header">Current Workout</h2>
    <ul id="workout_list">
    </ul>
    <div class="add_section ui-widget ui-helper-clearfix ui-state-default ui-corner-all pointer"><span class="ui-icon ui-icon-plus pointer"></span>Add Section</div>

    <p>&nbsp;</p>
    <input type="submit" class="large_submit" value="<?php if(isset($workout_id)){ ?>Update Workout<?php }else{ ?>Save Workout<?php } ?>" />
    
</div>

<div id="workout_right">
    
    <h2 class="library_header" style="clear:both;">YOUR EXERCISE LIBRARY</h2>
    <!--<p class="smaller">Select an area of the body to view associated exercises</p>-->
    <ul id="exercise_library">
	<?php if($exercise_library){
        foreach($exercise_library['muscles'] as $muscle){
            if($muscle['no_exercises'] == 0){
				?><li><a href="#" class="muscle_title off"><?=$muscle['title']?></a>
				<?php if(isset($muscle['levels'])){ ?>
                    <ul class="levels">
                    <?php foreach($muscle['levels'] as $level){
						if($level['no_exercises'] == 0){
                        ?><li><a href="#" class="level_title off"><span><?=$level['title']?></span></a>
                            <?php if(isset($level['exercises'])){ ?>
                                <ul class="exercises" id="exercise-video">
                                <?php foreach($level['exercises'] as $exercise){
                                    ?><li class="exercise"><div class="ui-widget ui-helper-clearfix ui-state-default ui-corner-all move"><span class="ui-icon ui-icon-arrow-4 move "></span><span class="ex_title"><?=$exercise['title']?></span></div><a id="<?=$exercise['id']?>" href="/member/popup_video/<?=$exercise['id']?>" class="play-exercise"><div class="ui-widget ui-helper-clearfix ui-state-default ui-corner-all play"><span class="ui-icon ui-icon-play"></span></div></a></li><?
                                } ?>
                                </ul>
                            <?php } ?></li><?
						}
                    } ?>
                    </ul>
                <?php } ?>
                </li><?
			}
        }
    } ?>
    </ul>
</div>
<input type="hidden" id="current_exercise_edit" value="" />
</form>

    
