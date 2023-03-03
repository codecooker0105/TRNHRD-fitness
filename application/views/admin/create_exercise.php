<div class='mainInfo'>

	<h1>Edit Exercise</h1>
	
	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open_multipart("admin/create_exercise");?>
      <p>Title:<br />
      <?php echo form_input($title);?>
      </p>
      
      <p>Experience Level:<br />
      <? echo form_dropdown('experience_id',$experience_options,$experience_id,'class="required"'); ?>
      </p>
      
      <p>Default Set Type:<br />
      <? echo form_dropdown('type',array('sets_reps' => 'Sets x Reps','sets_time' => 'Sets x Time'),$type,'class="required"'); ?>
      </p>
      
      <p>Default Weight Type:<br />
      <? echo form_dropdown('weight_type',array('weighted' => 'Weights','bodyweight' => 'Body Weight Only'),$weight_type,'class="required"'); ?>
      </p>
      <table width="100%">
      <tr>
      	<td width="33%" valign="top">      
          <p>Exercise Muscles: (hold ctrl to select multiple)<br />
          <? echo form_multiselect('exercise_muscles[]',$muscle_options,$exercise_muscles,'style="width:200px;height:250px;"'); ?>
          </p>
       	</td>
        <td width="33%" valign="top">      
          <p>Exercise Categories: (hold ctrl to select multiple)<br />
          <? echo form_multiselect('exercise_types[]',$type_options,$exercise_types,'style="width:200px;height:250px;"'); ?>
          </p>
      	</td>
        <td width="33%" valign="top">
          <p>Exercise Equipment: (hold ctrl to select multiple)<br />
          <? echo form_multiselect('exercise_equipment[]',$equipment_options,$exercise_equipment,'style="width:200px;height:250px;"'); ?>
          </p>
        </td>
      </tr>
      </table>
      
      <p>Description:<br />
      <?php echo form_textarea($description);?>
      </p>
      
      <p>Website Video: <input type="file" name="video" size="20" /><br />  
		<? if($video != ''){ ?>
		<object width="400" height="300">
        <param name="allowfullscreen" value="true" />
        <param name="wmode" value="opaque" />
        <param name="allowscriptaccess" value="always" />
        <param name="movie" value="/flash/mediaplayer.swf?autostart=false&file=<?=$video?>&repeat=single" />
        <embed src="/flash/player.swf?autostart=false&file=<?=$video?>&repeat=single" type="application/x-shockwave-flash" allowfullscreen="true" wmode="opaque" allowscriptaccess="always" width="400" height="300"></embed>
        </object>
		<? } ?>		
        </p>
        
       <p>Mobile Video: <input type="file" name="mobile_video" size="20" /><br />  
		<? if($video != ''){ ?>
		<object width="400" height="300">
        <param name="allowfullscreen" value="true" />
        <param name="wmode" value="opaque" />
        <param name="allowscriptaccess" value="always" />
        <param name="movie" value="/flash/mediaplayer.swf?autostart=false&file=<?=$mobile_video?>&repeat=single" />
        <embed src="/flash/player.swf?autostart=false&file=<?=$mobile_video?>&repeat=single" type="application/x-shockwave-flash" allowfullscreen="true" wmode="opaque" allowscriptaccess="always" width="400" height="300"></embed>
        </object>
		<? } ?>		
        </p>
      
      <p><?php echo form_submit('submit', 'Submit');?></p>

      
    <?php echo form_close();?>

</div>
