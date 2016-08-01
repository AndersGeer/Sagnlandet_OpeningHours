<?php
/**
 *	Functions
 */



function twoDigits( $num = false ) {
	if ($num === false)		return;
	
	if ($num === 0)			return '00';
	return		(strlen($num) == 1) ? '0'.$num : $num;
}

function timeFormat( $hour = 0, $minute = 0 ) {
	return $hour * 100 + $minute;
}

function getDifference( $data ) {
	$hours_diff		= $data[2] - $data[0];
	$min_diff		= $data[3] - $data[1];
	
	return	$hours_diff * 60 + $min_diff;	// return [minutes]
}

function timeString( $data, $timeFormat = false ) {
	if (!$timeFormat)
		$timeFormat		= (op_get_setting( 'time-format' ) != '') ? op_get_setting( 'time-format' ) : 'H:i';
	
	return	dateString( $data, $timeFormat );
}


function dateString( $data, $dateFormat = false ) {
	if (!$dateFormat)
		$dateFormat		= (op_get_setting( 'date-format' ) != '') ? op_get_setting( 'date-format' ) : 'd.m.Y';
	
	return	date( $dateFormat, $data['start'] ).' – '.date( $dateFormat, $data['end'] );
}

function timeExtension ( $hour ) {
	return ($hour < 12) ? 'am' : 'pm';
}

function op_get_setting( $key ) {
	global $wp_opening_hours;
		
	return $wp_opening_hours->settings[ $key ];
}

function op_assets_path( $url = false ) {
	$assets_directory		= wp_upload_dir();
	return	( $url ) 
		? $assets_directory['baseurl'].'/opening-hours' 
		: $assets_directory['basedir'].'/opening-hours';
}



?>