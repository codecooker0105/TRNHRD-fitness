<ul id="weather_tabs">
    <?php $count = 0;
    foreach ($weathers as $zip => $weather) { ?>
        <li><a href="#" id="tab<?= $zip ?>"
                class="tab_link<?php if ($count == 0) { ?> on<?php } ?>"><span><?= substr($weather->loc->dnam, 0, strripos($weather->loc->dnam, ',')) ?></span></a>
        </li>
        <?php $count++;
    } ?>
    <li><a href="#" id="add_weather"><span>Add</span></a></li>
</ul>
<div id="inner_weather">
    <?php $count = 0;
    foreach ($weathers as $zip => $weather) {
        ?>
        <div id="weather_tab<?= $zip ?>" class="tab<?php if ($count == 0) { ?> on<?php } ?>">
            <h4><?= $weather->loc->dnam ?> - <a class="confirmDeleteLink"
                    href="<?= site_url('member/remove_weather/' . $zip) ?>">Remove</a></h4>
            <table width="100%">
                <tr>
                    <td><img src="/assets/weather/icons/61x61/<?= $weather->cc->icon ?>.png" /></td>
                    <td valign="top"><span class="cc_temp"><?= $weather->cc->tmp ?>&deg;F</span></td>
                    <td valign="top">Current: <?= $weather->cc->t ?><br />
                        Wind: <?= $weather->cc->wind->t ?> at <?= $weather->cc->wind->s ?> mph<br />
                        Humidity: <?= $weather->cc->hmid ?>%
                    </td>
                </tr>
            </table>
            <table width="100%">
                <tr>
                    <?php foreach ($weather->dayf->day as $day) {
                        if ($day->attributes()->d < 4) { ?>
                            <td valign="top" align="center">
                                <?= substr($day->attributes()->t, 0, 3) ?><br />
                                <img src="/assets/weather/icons/61x61/<?= $day->part[0]->icon ?>.png" height="50" width="50" /><br />
                                <?= $day->hi ?>&deg; | <?= $day->low ?>&deg;
                            </td>
                        <?php }
                    } ?>
                </tr>
            </table>
        </div>
        <?php
        $count++;
    } ?>
    <div id="weather_logo">
        <p align="center">Weather provided by<br /><a href="http://weather.com" target="_blank"><img
                    src="/assets/weather/logos/TWClogo_61px.png" /></a></p>
        <p><a href="/member/edit_weather">Edit Locations</a></p>
    </div>
</div>