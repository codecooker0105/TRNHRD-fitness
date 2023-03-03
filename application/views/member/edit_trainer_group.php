

<h1>Edit Trainer Group</h1>
<p>Enter a title for the group and then select which clients will be a part off this group. Remember clients that are currently a part of another group will be removed that group if selected.</p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("member/edit_trainer_group/".$this->uri->segment(3));?>

<p>Title:<br />
<?php echo form_input($title);?>
</p>

<p>Experience Level:<br />
	<? echo form_dropdown('exp_level_id',$experience_options,$experience_id,'class="required smallsize"'); ?></p>
    
<p>Available Equipment:
    <? foreach($equipment as $id => $title){
        ?><br /><?php echo form_checkbox($available_equipment[$id]);?> <?=$title?><?
    } ?>
</p>

<table cellpadding="0" cellspacing="0" class="listing tablesorter" id="clients" width="100%">
    <thead>
    <tr>
        <th>Add to Group?</th>
        <th>Name</th>
        <th>Group</th>
    </tr>
    </thead>
    <?php foreach ($clients as $count => $user): ?>
        <tr class="<?=($count % 2) ? 'odd' : 'even';?>">
        	<td><input type="checkbox" name="clients[]" value="<?=$user->client_id?>" <? if(in_array($user->client_id,$group_clients)){ ?> checked="checked" <? } ?> /></td>
            <td><?php echo $user->last_name?>, <?php echo $user->first_name?></td>
            <td><?php echo $user->trainer_group_title;?></td>
        </tr>
    <?php endforeach;?>
</table>
<?php echo form_input($group_id);?>
<p><?php echo form_submit('submit', 'Update Group');?></p>


<?php echo form_close();?>
