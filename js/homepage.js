(function ($) {
$(document).ready(function(){
	jwplayer("current_video").setup({
		// flashplayer: "/flash/player.swf",
		file: "/video/mobile_exercises/Back_1_InvertedRow.mp4",
		autostart: 1,
		height: 299,
		width: 449
	});	
});
})(jQuery);