<div id="generator_left">
	<h1><?=$title?>: <?php if($workout){ ?><?=$workout['title']?><?php } ?></h1>

    <?php //print_r($workout); ?>
	<?php if($workout){ ?>
    <?php if($workout['created'] == 'true'){ ?>
    	<ul id="workout_logbook"><?
        foreach($workout['sections'] as $section){
            ?><li><?=$section['title']?><?php if($section['section_rest'] != '' && $section['section_rest'] != 0){ ?> - <?=secToMinute($section['section_rest'])?><?php } ?>
            <?php if(isset($section['exercises'])){ ?>
                <ul class="section">
                <?php foreach($section['exercises'] as $exercise){ ?>
					       	<li class="exercise_type">
                            	<table width="100%" cellspacing="0" cellpadding="0" border="1">
                                <thead>
                                <tr>
                                	<th class="left"><?=$exercise['title']?></th>
                                    <th colspan="3">Recommended</th>
                                    <th colspan="2">Completed</th>
                                </tr>
                                <tr>
                                    <th>Set</th>
                                    <th><?php if($exercise['set_type'] == 'sets_reps'){ ?>Reps<?php }elseif($exercise['set_type'] == 'sets_time'){ ?>Time<?php } ?></th>
                                    <th>Weight</th>
                                    <th>Rest</th>
                                    <th><?php if($exercise['set_type'] == 'sets_reps'){ ?>Reps<?php }elseif($exercise['set_type'] == 'sets_time'){ ?>Time<?php } ?></th>
                                    <th class="right">Weight</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
								$ex_sets = explode('|',$exercise['sets']);
								$ex_reps = explode('|',$exercise['reps']);
								$ex_rest = explode('|',$exercise['rest']);
								$ex_weight = explode('|',$exercise['weight']);
								$ex_time = explode('|',$exercise['time']);
								foreach($ex_sets as $index => $set){ 
								$save_stats = mysql_query("SELECT * FROM user_workout_stats WHERE uwe_id = '" . $exercise['uwe_id'] . "' AND user_workout_stats.set = '" . $set . "'");
								if($previous_stats = mysql_fetch_assoc($save_stats)){
									$previous = true;
									$reps = $previous_stats['reps'];
									$weight = $previous_stats['weight'];
									$time = $previous_stats['time'];
									$difficulty = $previous_stats['difficulty'];
								}else{
									$previous = false;
									$reps = $ex_reps[$index];
									if(isset($ex_weight[$index])){
										$weight = $ex_weight[$index];
									}
									if(isset($ex_time[$index])){
										$time = $ex_time[$index];
									}
									$difficulty = 3;
								}
								$rest = $ex_rest[$index]; ?>                                
                                <tr <?php if($set == count($ex_sets)){ ?> class="bottom" <?php } ?>>
                                	<td align="center"><?=$set?></td>
                                    <td align="center"><?php if($exercise['set_type'] == 'sets_reps'){ ?><?=$ex_reps[$index]?><?php }else{ ?><?=secToMinute($ex_time[$index])?><?php } ?></td>
                                    <td align="center">
										<?php if($exercise['weight_option'] == 'weighted'){ ?>
                                        	<?php if($weight == 0){ ?>N/A<?php }else{ ?><?=$weight?> lbs.<?php } ?>
                                        <?php }elseif($exercise['weight_option'] == 'bodyweight'){ ?>
                                        	Body Weight
                                        <?php } ?>
                                        </td>
                                    <td align="center"><?=$ex_rest[$index]?></td>
                                    <td>&nbsp;</td>
                                    <td><?php if($exercise['weight_option'] == 'bodyweight'){ ?>
                                        	Body Weight
                                        <?php } ?></td>
                                </tr>
                                <?php } ?>
                                <tbody class="footer">
                                <tr>
                                	<td colspan="7" class="left">
                                    	<strong>COMMENTS (circle one)</strong>
                                        easy | moderate | difficult | impossible
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            </li><?php
                } ?>
                </ul>
            <?php } ?>
            </li><?php
        }
		?></ul><?php
	}
    }else{ ?>
    	<p><strong>You had no workout today</strong></p>
    <?php } ?>
</div>