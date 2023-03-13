<div class="create-trainer-page">

    <h1>New Trainer Group</h1>
    <p>Enter a title for the group and then select which clients will be a part off this group. Remember clients that
        are currently a part of another group will be removed that group if selected.</p>

    <div id="infoMessage">
        <?php echo $message; ?>
    </div>

    <?php echo form_open("member/create_trainer_group"); ?>
    <p>Title:<br />
        <?php echo form_input($title); ?>
    </p>

    <p>Experience Level:<br />
        <?php echo form_dropdown('exp_level_id', $experience_options, $experience_value, 'class="required smallsize"'); ?>
    </p>

    <p>Available Equipment:
        <?php foreach ($equipment as $id => $title) {
            ?><br />
            <?php echo form_checkbox($available_equipment[$id]); ?>
            <?= $title ?>
            <?php
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
            <tr class="<?= ($count % 2) ? 'odd' : 'even'; ?>">
                <td><input type="checkbox" name="clients[]" value="<?= $user->client_id ?>" /></td>
                <td>
                    <?php echo $user->last_name ?>,
                    <?php echo $user->first_name ?>
                </td>
                <td>
                    <?php echo $user->trainer_group_title; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p>
        <?php echo form_submit('submit', 'Create Group'); ?>
    </p>


    <?php echo form_close(); ?>

</div>