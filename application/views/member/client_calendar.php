<h1>
    <?php if ($current_client) { ?>
        <?= $current_client->first_name ?>     <?= $current_client->last_name ?>
    <?php } else { ?>
        All Clients
    <?php } ?> Calendar
    <span style="float:right; font-size:14px;">
        Select a client: <select name="client" id="client">
            <!--<option value="">Select a Client</option>-->
            <?php if ($clients) {
                foreach ($clients as $client) {
                    ?>
                    <option value="<?= $client->user_id ?>" <?php if ($current_client->id == $client->user_id) { ?>
                            selected="selected" <?php } ?>><?= $client->first_name . ' ' . $client->last_name ?></option>
                    <?php
                }
            } ?>
        </select>
    </span>
</h1>



<div id="workout_calendar">

    <?php echo $this->calendar->generate($year, $month, $workouts); ?>

</div>