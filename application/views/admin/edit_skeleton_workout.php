<div class='mainInfo'>

    <h1>Edit Skeleton Workout</h1>

    <div id="infoMessage">
        <?php echo $message; ?>
    </div>

    <?php echo form_open("admin/edit_skeleton_workout/" . $this->uri->segment(3), 'id="admin_skeleton_form"'); ?>
    <p>Title:<br />
        <?php echo form_input($title); ?>
    </p>

    <p>Workout Progressions: (hold ctrl to select multiple)<br />
        <?php echo form_multiselect('workout_progressions[]', $progression_options, $workout_progressions, 'style="width:250px;height:200px;"'); ?>
    </p>


    <?php echo form_input($workout_id); ?>

    <h2>Skeleton Workout</h2>
    <ul id="workout_list">
        <?php foreach ($workout_sections as $section) { ?>
            <li class="section">
                <div class="ui-widget ui-helper-clearfix ui-state-default ui-corner-all move"><span
                        class="ui-icon ui-icon-arrow-4 move"></span></div>
                <input type="hidden" value="<?= $section['section_type_id'] ?>" name="section_id" class="section_id" />
                <?php if ($section['type'] == 'rest' || $section['type'] == 'active-rest') { ?>
                    <span class="rest">
                        <?= $section['title'] ?>
                    </span>
                <?php } else { ?>
                    <a href="#" class="section_title off">
                        <?= $section['title'] ?>
                    </a>
                <?php } ?>
                <div class="remove_section ui-widget ui-helper-clearfix ui-state-default ui-corner-all remove"><span
                        class="ui-icon ui-icon-closethick remove"></span>Remove Section</div>
                <div class="add_exercise ui-widget ui-helper-clearfix ui-state-default ui-corner-all pointer"><span
                        class="ui-icon ui-icon-plus pointer"></span>Add Exercise Type</div>
                <ul class="workout_categories">
                    <?php foreach ($section['exercises'] as $exercise) { ?>
                        <li class="category">
                            <div class="ui-state-default ui-corner-all move"><span class="ui-icon ui-icon-arrow-4 move"></span>
                            </div><input type="hidden" value="<?= $exercise['exercise_type_id'] ?>" name="category_id"
                                class="category_id" /> <strong>
                                <?= $exercise['title'] ?>
                            </strong> - <a href="#" class="remove_exercise">Remove Exercise</a>
                        <?php } ?>
                </ul>
            <?php } ?>
    </ul>
    <div class="add_section ui-widget ui-helper-clearfix ui-state-default ui-corner-all pointer"><span
            class="ui-icon ui-icon-plus pointer"></span>Add Section</div>

    <p>&nbsp;</p>
    <input type="hidden" name="workout_list" id="workout_list_value" value="" />
    <p>
        <?php echo form_submit('submit2', 'Submit', 'id="submit"'); ?>
    </p>
    <?php echo form_close(); ?>

</div>

<div id="section_dialog" title="Create new section">
    <form>
        <label for="name">Section Type</label>
        <select name="section" id="section_dropdown" class="ui-widget-content ui-corner-all">
            <?php $result = mysql_query("SELECT * FROM skeleton_section_types ORDER BY title");
            while ($row = mysql_fetch_assoc($result)) {
                ?>
                <option value="<?= $row['id'] ?>"><?= $row['title'] ?></option>
                <?
            } ?>
        </select>
    </form>
</div>

<div id="exercise_dialog" title="Create new exercise">
    <form>
        <label for="name">Exercise Type</label>
        <input type="hidden" id="exercise_type_title" value="" />
        <select name="type" id="exercise_type_dropdown" class="ui-widget-content ui-corner-all">
            <option value="">Select Exercise Type</option>
            <?php $result = mysql_query("SELECT * FROM exercise_types ORDER BY title");
            while ($row = mysql_fetch_assoc($result)) {
                ?>
                <option value="<?= $row['id'] ?>"><?= $row['title'] ?></option>
                <?
            } ?>
        </select>
    </form>
</div>