<?

function secToMinute($seconds){
	if ($seconds > 0) {
		$mins = floor ($seconds / 60);
		$secs = $seconds % 60;
		if($secs == '0'){
			$secs = '00';
		}
		return $mins . ":". $secs;
	}else{
		return '0:00';
	}
}

?>