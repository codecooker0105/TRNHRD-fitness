<rss version="2.0" xmlns:jwplayer="http://developer.longtailvideo.com/">
	<channel>
		<title>
			<?= $title ?>:
			<?php if ($workout) { ?>
				<?= $workout['title'] ?>
			<?php } ?>
		</title>
		<?
		foreach ($workout['sections'] as $section) {
			if (isset($section['exercises'])) {
				foreach ($section['exercises'] as $exercise) {
					?>
					<item>
						<title>
							<?= $exercise['title'] ?>
						</title>
						<enclosure url="<?= $exercise['video'] ?>" type="video/x-flv" />

						<jwplayer:provider>http</jwplayer:provider>
						<jwplayer:http.startparam>start</jwplayer:http.startparam>
					</item>
					<?
				}
			}
		} ?>
	</channel>
</rss>