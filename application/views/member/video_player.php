<!-- <object width="400" height="300">
<param name="allowfullscreen" value="true" />
<param name="wmode" value="opaque" />
<param name="allowscriptaccess" value="always" />
<param name="movie" value="/flash/mediaplayer.swf?autostart=true&file=<?= $exercise->video ?>&repeat=single" />
<embed src="/flash/player.swf?autostart=true&file=<?= $exercise->video ?>&repeat=single" type="application/x-shockwave-flash" allowfullscreen="true" wmode="opaque" allowscriptaccess="always" width="400" height="300"></embed>
</object> -->
<video width="480" height="320" controls="controls">
  <source src="<?= $exercise->mobile_video ?>" type="video/mp4">
</video>