

<h1>Personal Stats</h1>
<p>Below is the list of stats you are currently tracking besides the workout progression that Hybrid Fitness tracks for you. You can add any kind of statistic here and we will keeptrack of it. Examples of stats might be your weight, max lift of bench press or body fat percentage.</p>
<p><a href="#" id="add_stat">Add Stat</a></p>
<div id="infoMessage"><?php echo $message;?></div>

<table cellpadding="0" cellspacing="0" class="listing tablesorter" id="stats" width="100%">
    <thead>
    <tr>
        <th>Stat</th>
        <th>Options</th>
    </tr>
    </thead>
    <?php foreach ($stats as $count => $stat): ?>
        <tr id="stat<?=$stat->id?>" class="<?=($count % 2) ? 'odd' : 'even';?>">
            <td><?php echo $stat->title?></td>
            <td align="center"><a class="confirmDeleteLink" href="<?=site_url('member/remove_stat/'.$stat->id)?>">Remove Stat</a></td>
        </tr>
    <?php endforeach;?>
</table>


<div id="dialog" title="Confirmation Required" style="display:none;">
  Are you sure you want to remove this stat? All information attached to stat will be lost.
</div>

<div id="add_stat_dialog" title="Add Personal Stat" style="display:none;">
  <div id="add_stat_message"><?php echo $message;?></div>
  <p><strong>Stat Title:</strong> <input type="text" name="title" id="title" value="" /></p>
  <p><strong>Statistic Measurement?:</strong> <select name="measurement_type" id="measurement_type">
  												<option value="lbs">Pounds</option>
                                                <option value="percentage">Percentage</option>
                                                <option value="inches">Inches</option>
                                                <option value="other">Other</option>
                                                </select></p>
  <p><strong>Current/Starting Value:</strong> <input type="text" name="starting" id="starting" value="" /></p>
</div>
