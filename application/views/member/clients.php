

<h1>Clients</h1>
<p>Below is the list of clients you are currently training. Status will indicate whether they have confirmed you as their trainer or not as well as if they have signed up at Hybrid Fitness.</p>

<p>If you would like to add a client simply use the <a id="request_client" href="<?php echo site_url('member/request_client');?>">request a client</a> form and an email will be sent will instructions on how to sign up at Hybrid Fitness and once signedup and they confirm you as their trainer they will be added to your workout system.</p>

<div id="infoMessage"><?php echo $message;?></div>

<table cellpadding="0" cellspacing="0" class="listing tablesorter" id="clients" width="100%">
    <thead>
    <tr>
        <th>Client</th>
        <th>Status</th>
        <th>Group</th>
        <th>Options</th>
    </tr>
    </thead>
    <?php foreach ($clients as $count => $user): ?>
        <tr class="<?=($count % 2) ? 'odd' : 'even';?>">
            <td><?php echo $user->last_name?>, <?php echo $user->first_name?></td>
            <td><?php echo $user->status;?></td>
            <td><?php echo $user->trainer_group_title;?></td>
            <td align="center"><a class="confirmDeleteLink" href="<?=site_url('member/remove_client/'.$user->client_id)?>">Remove Client</a></td>
        </tr>
    <?php endforeach;?>
</table>

<p>&nbsp;</p>
<h1>Client Groups</h1>
<p>Below is a list of your client groups. You can create groups of clients an use these groups to assign workouts to everyone in the group. A client can only be a part of one group but you can have as many groups as you like. <a href="<?php echo site_url('member/create_trainer_group');?>">Create a group</a></p>

<table cellpadding="0" cellspacing="0" class="listing tablesorter" id="client_groups" width="100%">
    <thead>
    <tr>
        <th>Group</th>
        <th>Options</th>
    </tr>
    </thead>
    <?php foreach ($trainer_groups as $count => $group): ?>
        <tr class="<?=($count % 2) ? 'odd' : 'even';?>">
            <td><?php echo $group['title']?></td>
            <td align="center"><a href="<?=site_url('member/edit_trainer_group/'.$group['id'])?>">Edit Group</a> | <a class="confirmDeleteGroupLink" href="<?=site_url('member/remove_group/'.$group['id'])?>">Remove Group</a></td>
        </tr>
    <?php endforeach;?>
</table>

<div id="dialog" title="Confirmation Required" style="display:none;">
  Are you sure you want to remove this client from your training? All information attached to training this client will be lost. You will be able to train this client again in the future but all current data will be removed along with any workouts you have scheduled for them.
</div>

<div id="dialog2" title="Confirmation Required" style="display:none;">
  Are you sure you want to remove this group? All clients who are in this group will be removed as well.
</div>
