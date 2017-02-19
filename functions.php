<?php
function byte_to_mb($size, $precision = 2) {
	$base = log($size, 1024);
	$suffixes = array('', 'K', 'MB', 'G', 'T');   

	return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}

function sec_to_time($seconds){
	$hours = floor($seconds / 3600);
	$mins = floor($seconds / 60 % 60);
	$secs = floor($seconds % 60);


	$timeFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
	return $timeFormat;
}

function getHost($Address){
		$parseUrl = parse_url(trim($Address));

		if(!empty($parseUrl['host'])){
			$host = $parseUrl['host'];
		} else {
			$host = explode("/", $parseUrl['path'])[0];
		}
		$c = count(explode(".", $host));

		if($c == 2){
			$domain = explode(".", $host)[0];
		} else if($c == 3){
			$domain = explode(".", $host)[1];
		} else {
			$domain = false;
		}

		return $domain;
	}