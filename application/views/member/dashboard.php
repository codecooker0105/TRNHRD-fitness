<div id="user_left">
	<!-- <div class="logged_in_name">
    	<div class="screenname"><?php echo $user->first_name; ?></div>
        <img src="/images/template/right_screenname.gif" />
    </div> -->
    
    <div id="user_photo_area">
    	<div id="photo">
        <?php if($user->photo != ""){ ?>
        	<a href="/member/edit_photo"><img src="/images/member_photos/<?php echo $user->photo; ?>" /></a>
        <?php }else{ ?>
        	<a href="/member/edit_photo"><img src="/images/template/no_photo.jpg" /></a>
        <?php } ?>
        </div>
        <div id="current_date" class="name-date">
            <p class="uname p-0"><?php echo $user->first_name; ?></p>
            <div class="day-date">
            <i class="far fa-calendar-alt"></i> &nbsp;
                <span id="month"><?php echo date("F"); ?></span>
                <span id="day"><?php echo date("j"); ?></span>
            </div>
        </div>
    </div>
    
    <div class="left_block">
    	<h2 class="library_header">Account Functions</h2>
        <div class="inner_block">
        	<ul>
                <li><a href="/member/edit_account">Edit Account Profile</a></li>
                <li><a href="/member/change_password">Change Password</a></li>
            </ul>
        </div>
    </div>
    
    <div class="left_block">
    	<div class="header"><span class="link-heading"><a href="/member/stats" class="popup_stats">My Stats <i class="fas fa-link"></i></a></span><a href="/member/edit_stats" class="small_edit edit-text-btn">Edit</a></div>
        <div class="inner_block">
        <?php if(count($stats) == 0){
			?><p>Would you like to track some stats for your own personal goals? <a href="/member/edit_stats">Add them here</a></p><?
		}else{
			foreach($stats as $stat){ ?>
                <h3><?=$stat['title']?> - <a href="#" class="add_current_stat" id="stat<?=$stat['id']?>">Update Current</a></h3>
                <p class="stat"><span class="current_stat"><?=$stat['current']?> <?=$stat['measurement_type']?></span> Current <?=$stat['title']?><br />
                <span class="starting_stat"><?=$stat['starting']?> <?=$stat['measurement_type']?></span> Starting <?=$stat['title']?></p>			
            <?php }
		}?>
        </div>
    </div>
    
    <!--<div class="left_block">
    	<h3 class="hf_header">Featured Product</h3>
        
    </div>-->
    
    <div class="left_block">
    	<h2 class="library_header">Featured Exercise</h2>
        <div class="inner_block">
        	<?php if($featured_exercise){ ?>
				<input type="hidden" name="featured_exercise_id" value="<?=$featured_exercise->id?>" />
                <h3><a href="/member/popup_video/<?=$featured_exercise->id?>" class="play-exercise"><?php echo $featured_exercise->title?></a></h3>
        		<a href="#" class="add_button" id="add_featured_exercise">add to existing workout</a><br />
				<a href="/member/popup_video/<?=$featured_exercise->id?>" class="play-exercise play_button">launch video</a><?
			} ?>
        </div>
    </div>
    <!--
    <div class="left_block">
    	<h3 class="hf_header">Featured Workout</h3>
        <div class="inner_block">
        	<?php if($featured_workout){ ?>
				<h3><a href="/member/popup_workout/"><?php echo $featured_workout->title?></a></h3>
        		<a href="#" class="add_button">use as a workout</a><?
			} ?>
        </div>
        
    </div>-->
    
    <!--<div class="left_block">
    	<h3 class="hf_header">News & Updates</h3>
        <h4 class="rss">Latest From HF Blog</h4>
        <ul>
        
        </ul>
    </div>-->
</div>
<?php //print_r($weathers); ?>
<div id="user_right">	
	<div id="weather">
    	<ul id="weather_tabs">
        	<?php $count = 0;
			if(count($weathers) > 0){ 
				foreach($weathers as $zip => $weather){ 
					if ($weather == 'error'){
						?><li><a href="#" id="tab<?=$zip?>"  class="tab_link<?php if($count == 0){ ?> on<?php } ?>" ><span>N/A</span></a></li><?
					}else{
						?><li><a href="#" id="tab<?=$zip?>"  class="tab_link<?php if($count == 0){ ?> on<?php } ?>" ><span><?=substr($weather->weather->forecast_information->city['data'],0,strripos($weather->weather->forecast_information->city['data'],','))?></span></a></li><?
					}
				$count++;
				}
			}?>
            <li><a href="#" id="add_weather"><span>Add</span></a></li>
        </ul>
        <div id="inner_weather">
			<?php $count = 0;
            if(count($weathers) > 0){
				foreach($weathers as $zip => $weather){ 
					?><div id="weather_tab<?=$zip?>"  class="tab<?php if($count == 0){ ?> on<?php } ?>" >
						<?php if ($weather == 'error'){
							?><strong>Weather not available at the moment</strong><?
						}else{ ?>
							<h4><?=$weather->weather->forecast_information->city['data']?> - <a class="confirmDeleteLink" href="<?=$zip?>">Remove</a></h4>
                            <table width="100%">
                            <tr>
                                <td><!--<img src="/assets/weather/icons/61x61/<?php //=$weather->cc->icon?>.png" />--><img src="http://www.google.com<?=$weather->weather->current_conditions->icon['data']?>" /></td>
                                <td valign="top"><span class="cc_temp"><?=$weather->cc->tmp?>&deg;F</span></td>
                                <td valign="top">Current: <?=$weather->weather->current_conditions->temp_f['data']?><br />
                                                <?=$weather->weather->current_conditions->wind_condition['data']?><br />
                                                <?=$weather->weather->current_conditions->humidity['data']?>%
                                </td>
                            </tr>
                            </table>
                            <table width="100%">
                            <tr>
                            <?php $day_count = 0;
                             foreach($weather->weather->forecast_conditions as $day){ 
                                $day_count++;
                                if($day_count < 5){ ?>
                                <td valign="top" align="center">
                                    <?=$day->day_of_week['data']?><br />
                                    <!--<img src="/assets/weather/icons/61x61/<?php //=$day->part[0]->icon?>.png" height="50" width="50" />-->
                                    <img src="http://www.google.com<?=$day->icon['data']?>" height="50" width="50" /><br />
                                    <?=$day->high['data']?>&deg; | <?=$day->low['data']?>&deg;
                                </td>
                            <?php }
                            } ?>
                            </tr>
                            </table>
                        <?php } ?>
					</div><?
				$count++;
				}
			}?>
            <div id="weather_logo">
            	<p align="center">Weather provided by<br /><a href="http://weather.com" target="_blank"><img src="/assets/weather/logos/TWClogo_61px.png" /></a></p>
            </div>
        </div>
    </div>
    
    <div class="right_block">
    	<div class="header"><h2 class="library_header">My Progress</h2><a href="/member/edit_progression_plan" class="med_view_edit edit-text-btn">View/Edit</a></div><br/>
        <h2 class="current-plan">Current Plan: <?php if(isset($current_progression_plan->title)){ ?><?=$current_progression_plan->title?><?php }else{ ?>None Selected<?php } ?></h2>
    </div>
    
    <div class="right_block">
    	<div class="header"><h2 class="library_header">Today's Workout Overview</h2><a href="edit_stats" class="med_view_edit edit-text-btn">View/Edit</a></div>
        <p>&nbsp;</p>
        <ul id="workout_tree">
		<?php if($todays_workout && $todays_workout['created'] == 'false'){
			?><p>You have a workout scheduled for today but it has not been generated yet. Please go through any previous workouts you have completed and enter your stats, so we can create a fully customized workout for you.</p><?
		}elseif($todays_workout){
			?><h2><?=$todays_workout['title']?></h2><?
			foreach($todays_workout['sections'] as $section){
				?><li><a href="#" class="section_title off"><?=$section['title']?></a>
                <?php if(isset($section['exercises'])){ ?>
                	<ul class="section">
                    <?php foreach($section['exercises'] as $exercise){
						?><li><a href="#" class="type_title off"><span><?=$exercise['type_title']?></span></a>
                			<ul class="type"><li class="exercise_type"><a href="/member/popup_video/<?=$exercise['id']?>" class="play-exercise"><?=$exercise['title']?></a></li></ul></li><?
					} ?>
                    </ul>
                <?php } ?>
                </li><?
			}
		} ?>
        </ul>
    </div>
    
    <?php if($member_group == 'trainer'){ ?>
    <div class="right_block">
    	<div class="header"><h2 class="library_header">Clients</h2><a href="/member/clients" class="med_view_edit  edit-text-btn">View/Edit</a></div>
        <p>&nbsp;</p>
        <table width="100%" class="listing tablesorter" id="client_tree">
		<thead>
        <tr>
            <th>Client</th>
            <th>Calendar</th>
            <th>Log Book</th>
        </tr>
        </thead><?php if($clients){
			foreach($clients as $client){
				?><tr>
					<td><?=$client->first_name . ' ' . $client->last_name?></td>
                    <td><a href="/member/client_calendar/<?=$client->user_id?>">Calendar</a></td>
                    <td><a href="/member/client_log_book/<?=$client->user_id?>">Log Book</a></td>
				</tr><?
			}
		} ?>
        </table>
    </div>
    <?php } ?>
    
    <?php if($member_group == 'member' && $trainer){ ?>
    <div class="right_block">
    	<div class="header"><h2 class="library_header">My Trainer</h2><a href="/member/edit_trainer" class="med_view_edit">View/Edit</a></div>
        <p>&nbsp;</p>
        <?php if($trainer->photo != ""){ ?>
        	<img src="/images/member_photos/<?php echo $trainer->photo; ?>" />
        <?php }else{ ?>
        	<img src="/images/template/no_photo.jpg" />
        <?php } ?>
        <?=$trainer->first_name?> <?=$trainer->last_name?>
    </div>
    <?php } ?>
</div>

<div id="dialog" title="Confirmation Required" style="display:none;">
  Are you sure you want to remove this location?
</div>

<div id="featured_exercise_dialog" title="Add Featured Exercise" style="display:none;">
  What workout would you like to add this featured exercise to?
  <select id="featured_exercise_upcoming_workouts">
  	<?php foreach($upcoming_workouts as $workout){ ?>
    	<option value="<?=$workout->id?>"><?=$workout->title?> (<?=date('n/d/y',strtotime($workout->workout_date))?>)</option>
    <?php } ?>
  </select>
  <div id="featured_exercise_choices">
  	<h3>Select an option</h3>
    <div id="replace_exercise_section">
    	<input type="radio" name="featured_option" id="replace_exercise" value="replace" checked="checked" /> Replace Exercise <select id="replace_exercise_id"></select><br />
    </div>
    <input type="radio" name="featured_option" id="add_to_section" value="add_to_section" /> Add to Section <select id="add_section_id"></select>
  </div>
  
</div>

<div id="add_weather_dialog" title="Add Weather Location" style="display:none;">
  <div id="add_weather_message"><?php echo $message;?></div>
  <p><strong>Zip Code:</strong> <input type="text" name="zip" id="zip" value="" /></p>
  <p><strong>Default?:</strong> <input type="checkbox" name="default" id="default" value="true" /></p>
</div>

<div id="add_current_stat_dialog" title="Add Personal Stat" style="display:none;">
  <div id="add_current_stat_message"><?php echo $message;?></div>
  <p><strong>Current Value:</strong> <input type="text" name="current" id="current" value="" /></p>
  <p><strong>Current Date:</strong> <input type="text" name="stat_date" id="stat_date" value="" /></p>
</div>