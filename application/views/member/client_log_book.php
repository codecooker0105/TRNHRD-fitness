<div id="generator_left">
    <h1>
        <?php if ($current_client) { ?>
            <?= $current_client->first_name ?>     <?= $current_client->last_name ?>
        <?php } else { ?>
            All Clients
        <?php } ?> Log Book
    </h1>


    <div id="logbook_container">
        <h1><span id="log_date">
                <?= $title ?>
            </span>:
            <?php if ($workout) { ?>
                <?= $workout['title'] ?>
            <?php } ?>
        </h1>

        <?php //print_r($workout); ?>
        <?php if ($workout) { ?>
            <?php if ($workout['trainer_id'] == $user->id) {
                ?>
                <ul>
                    <li><strong>Individual Options</strong>
                        <ul>
                            <li><a href="/member/workout_generator/workout/<?= $workout['workout_id'] ?>">Edit Today's Workout</a>
                            </li>
                            <?php if ($workout['end_date'] != '') { ?>
                                <li><a href="/member/workout_generator/trainer_workout/<?= $workout['trainer_workout_id'] ?>">Edit All
                                        Occurences of this workout</a></li>
                            <?php } ?>
                            <li><a href="<?= $workout['workout_id'] ?>" class="confirmWorkoutDeleteLink">Delete Today's
                                    Workout</a></li>
                            <?php if ($workout['end_date'] != '') { ?>
                                <li><a href="<?= $workout['user_id'] ?>-<?= $workout['trainer_workout_id'] ?>"
                                        class="confirmTrainerWorkoutDeleteLink">Delete Today's Workout and all occurences of this
                                        workout</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php if ($workout['trainer_group_id'] != '') { ?>
                        <li><strong>Group Options</strong>
                            <ul>
                                <li><a href="/member/workout_generator/group_workout/<?= $workout['workout_id'] ?>">Edit Today's
                                        Workout for entire group</a></li>
                                <?php if ($workout['end_date'] != '') { ?>
                                    <li><a href="/member/workout_generator/trainer_group_workout/<?= $workout['trainer_workout_id'] ?>">Edit
                                            All Occurences of this workout for entire group</a></li>
                                <?php } ?>
                                <li><a href="<?= $workout['workout_id'] ?>" class="confirmGroupWorkoutDeleteLink">Delete Today's
                                        Workout for entire group</a></li>
                                <?php if ($workout['end_date'] != '') { ?>
                                    <li><a href="<?= $workout['trainer_workout_id'] ?>"
                                            class="confirmTrainerGroupWorkoutDeleteLink">Delete Today's Workout and all occurences of
                                            this workout for entire group</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
                <?php
            }

            if ($workout['created'] == 'false') { ?>
                <p>The client has a scheduled workout on this day but it has not yet been generated.</p>
            <?php } ?>
            <?php if ($workout['created'] == 'true') { ?>
                <p><a target="_blank"
                        href="/member/print_log_book<?php if ($this->uri->segment(3) != '') { ?>/<?= $this->uri->segment(3) ?>/<?= $this->uri->segment(4) ?>/<?= $this->uri->segment(5) ?><?php } ?>">Printer
                        Friendly Version</a></p>
                <ul id="workout_logbook">
                    <?php
                    foreach ($workout['sections'] as $section) {
                        ?>
                        <li><a href="#"
                                class="<?php if ($section['title'] == 'Rest') { ?>section_rest<?php } else { ?>section_title<?php } ?> off"><?= $section['title'] ?><?php if ($section['section_rest'] != '' && $section['section_rest'] != 0) { ?> -
                                    <?= secToMinute($section['section_rest']) ?>            <?php } ?></a>
                            <?php if (isset($section['exercises'])) { ?>
                                <ul class="section">
                                    <?php foreach ($section['exercises'] as $exercise) {
                                        ?>
                                        <li><a href="#" class="type_title off"><span>
                                                    <?= $exercise['type_title'] ?>
                                                </span></a>
                                            <ul class="type">
                                                <li class="exercise_type">
                                                    <form action="/member/save_log_stats" method="post">
                                                        <input type="hidden" name="exercise_id" value="<?= $exercise['id'] ?>" />
                                                        <input type="hidden" name="uw_id" value="<?= $workout['workout_id'] ?>" />
                                                        <input type="hidden" name="uwe_id" value="<?= $exercise['uwe_id'] ?>" />
                                                        <input type="hidden" name="workout_date" value="<?= $workout['workout_date'] ?>" />

                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="left"><a href="/member/popup_video/<?= $exercise['id'] ?>"
                                                                            class="play-exercise"><?= $exercise['title'] ?></a></th>
                                                                    <th>Set</th>
                                                                    <th>
                                                                        <?php if ($exercise['set_type'] == 'sets_reps') { ?>Reps
                                                                        <?php } elseif ($exercise['set_type'] == 'sets_time') { ?>Time
                                                                        <?php } ?>
                                                                    </th>
                                                                    <th>Rest</th>
                                                                    <th>
                                                                        <?php if ($exercise['set_type'] == 'sets_reps') { ?>Reps
                                                                        <?php } elseif ($exercise['set_type'] == 'sets_time') { ?>Time
                                                                        <?php } ?>
                                                                    </th>
                                                                    <th class="right">Weight</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $ex_sets = explode('|', $exercise['sets']);
                                                                $ex_reps = explode('|', $exercise['reps']);
                                                                $ex_rest = explode('|', $exercise['rest']);
                                                                $ex_weight = explode('|', $exercise['weight']);
                                                                $ex_time = explode('|', $exercise['time']);
                                                                foreach ($ex_sets as $index => $set) {
                                                                    $save_stats = mysql_query("SELECT * FROM user_workout_stats WHERE uwe_id = '" . $exercise['uwe_id'] . "' AND user_workout_stats.set = '" . $set . "'");
                                                                    if ($previous_stats = mysql_fetch_assoc($save_stats)) {
                                                                        $previous = true;
                                                                        $reps = $previous_stats['reps'];
                                                                        $weight = $previous_stats['weight'];
                                                                        $time = $previous_stats['time'];
                                                                        $difficulty = $previous_stats['difficulty'];
                                                                    } else {
                                                                        $previous = false;
                                                                        $reps = $ex_reps[$index];
                                                                        if (isset($ex_weight[$index])) {
                                                                            $weight = $ex_weight[$index];
                                                                        }
                                                                        if (isset($ex_time[$index])) {
                                                                            $time = $ex_time[$index];
                                                                        }
                                                                        $difficulty = 3;
                                                                    }
                                                                    $rest = $ex_rest[$index]; ?>
                                                                    <tr <?php if ($set == count($ex_sets)) { ?> class="bottom" <?php } ?>>
                                                                        <?php if ($set == 1) { ?><td class="left bottom"
                                                                                rowspan="<?= count($ex_sets) ?>">&nbsp;</td>
                                                                        <?php } ?>
                                                                        <td>
                                                                            <?= $set ?><input name="sets[]" type="hidden" value="<?= $set ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <?php if ($exercise['set_type'] == 'sets_reps') { ?>
                                                                                <?= $ex_reps[$index] ?>
                                                                            <?php } else { ?>
                                                                                <?= secToMinute($ex_time[$index]) ?>
                                                                            <?php } ?>
                                                                        </td>
                                                                        <td class="right">
                                                                            <?= $ex_rest[$index] ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php if ($exercise['set_type'] == 'sets_reps') { ?>
                                                                                <select name="reps[<?= $set ?>]">
                                                                                    <?php for ($x = 1; $x <= 30; $x++) { ?>
                                                                                        <option value="<?= $x ?>" <?php if ($exercise['reps'] == $x) { ?>
                                                                                                selected="selected" <?php } ?>><?= $x ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            <?php } elseif ($exercise['set_type'] == 'sets_time') { ?>
                                                                                <select name="time[<?= $set ?>]" class="time">
                                                                                    <?php for ($x = 15; $x <= 300; $x += 15) { ?>
                                                                                        <option value="<?= $x ?>" <?php if ($exercise['time'] == $x) { ?>
                                                                                                selected="selected" <?php } ?>><?= secToMinute($x) ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            <?php } ?>
                                                                        </td>
                                                                        <td class="right">
                                                                            <?php if ($exercise['weight_option'] == 'weighted') { ?>
                                                                                <input type="text" name="weight[<?= $set ?>]" class="weight"
                                                                                    value="<?= $weight ?>" /> lbs.
                                                                            <?php } elseif ($exercise['weight_option'] == 'bodyweight') { ?>
                                                                                Body Weight
                                                                            <?php } ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            <tbody class="spacer">
                                                                <tr>
                                                                    <td colspan="6">&nbsp;</td>
                                                                </tr>
                                                            </tbody>
                                                            <tbody class="footer">
                                                                <tr>
                                                                    <td colspan="4" class="left">
                                                                        <strong>COMMENTS</strong>
                                                                        <input type="radio" name="difficulty" value="1" <?php if ($difficulty == 1) { ?> checked="checked" <?php } ?> /> easy
                                                                        <input type="radio" name="difficulty" value="2" <?php if ($difficulty == 2) { ?> checked="checked" <?php } ?> /> moderate
                                                                        <input type="radio" name="difficulty" value="3" <?php if ($difficulty == 3) { ?> checked="checked" <?php } ?> /> difficult
                                                                        <input type="radio" name="difficulty" value="4" <?php if ($difficulty == 4) { ?> checked="checked" <?php } ?> />
                                                                        impossible
                                                                    </td>
                                                                    <td colspan="2" class="right">
                                                                        <!--<strong>SHARE</strong>--> <input type="submit" name="submit"
                                                                            value="<?php if ($previous) { ?>UPDATE STATS<?php } else { ?>SAVE STATS<?php } ?>" />
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </form>
                                                </li>
                                            </ul>
                                        </li>
                                        <?
                                    } ?>
                                </ul>
                            <?php } ?>
                        </li>
                        <?
                    }
                    ?>
                </ul>
                <?
            }
        } else { ?>
            <p><strong>The client has no workout on this day.</strong></p>
        <?php } ?>
    </div>
</div>

<div id="generator_right">
    <h1>Current Client</h1>
    <?php if ($current_client) { ?>
        <?php if ($current_client->photo != "") { ?>
            <img src="/images/member_photos/<?php echo $current_client->photo; ?>" />
        <?php } else { ?>
            <img src="/images/template/no_photo.jpg" />
        <?php } ?>
        <h2><?= $current_client->first_name ?>     <?= $current_client->last_name ?></h2>
        <p>Select client:
            <select name="client" id="client">
                <!--<option value="">Select a Client</option>-->
                <?php if ($clients) {
                    foreach ($clients as $client) {
                        ?>
                        <option value="<?= $client->user_id ?>" <?php if ($current_client->id == $client->user_id) { ?>
                                selected="selected" <?php } ?>><?= $client->first_name . ' ' . $client->last_name ?></option>
                        <?
                    }
                } ?>
            </select>
        </p>
    <?php } ?>
    <h2 class="library_header">ARCHIVED WORKOUTS</h2>
    <p class="smaller">Select a month to view past workouts and comments.</p>
    <ul id="archive_logbook">
        <?php if ($past_workouts) {
            $current_month = '';
            $current_year = '';
            foreach ($past_workouts as $workout) {
                if ($current_month != date('F', strtotime($workout->workout_date)) || $current_year != date('Y', strtotime($workout->workout_date))) {
                    if ($current_month != '') { ?>
                    </ul>
                    </li>
                <?php }
                    $current_month = date('F', strtotime($workout->workout_date));
                    $current_year = date('Y', strtotime($workout->workout_date));
                    ?>
                <li><a href="#" class="month_title off">
                        <?= $current_month ?>
                        <?= $current_year ?>
                    </a>
                    <ul class="month">
                        <?
                }
                ?>
                    <li><a class="day"
                            href="/member/client_log_book/<?= $current_client->id ?>/<?= date('Y/m/d', strtotime($workout->workout_date)) ?>"><?= date('l jS', strtotime($workout->workout_date)) ?></a></li>
                    <?
            }
        } ?>
        </ul>

</div>

<div id="single_dialog" title="Confirmation Required" style="display:none;">
    Are you sure you want to remove this workout? This can not be undone.
</div>

<div id="all_dialog" title="Confirmation Required" style="display:none;">
    Are you sure you want to remove this workout and all occurrences of this workout? This can not be undone.
</div>

<div id="group_single_dialog" title="Confirmation Required" style="display:none;">
    Are you sure you want to remove this workout for today for the entire workout group? This will delete today's
    workout for everyone in this workout group. This can not be undone.
</div>

<div id="group_all_dialog" title="Confirmation Required" style="display:none;">
    Are you sure you want to remove this workout and all occurrences of this workout for the entire group? This will
    delete all occurences of this workout for everyone in this workout group. This can not be undone.
</div>