<?php
/**
 *	Template Tags
 */

/**
 *	is_open		– Returns wether the venue is open or not
 *	in	$return_type: bool, when set to true, function returns an array( $bool, $context )
 *	out array( $bool, $context ) or $bool
 */
function is_open ( $return_type 	= false ) {
	global $wp_opening_hours;
	
	if (!is_object($wp_opening_hours))	$wp_opening_hours	= new OpeningHours;
	
	$today		= strtolower( date('l', current_time('timestamp')) );

	/* Check for Special Openings */
	foreach ((array) $wp_opening_hours->specialOpenings as $specialOpening) :
		if ( $specialOpening->isToday() ) :											// Only take todays Special Opening
			if ( $specialOpening->isRunning() ) :									// Check if Special Opening is Running
				return ( $return_type ) ? array( true, 'special-opening' ) : true;
			else :
				return ( $return_type ) ? array( false, 'special-opening' ) : false;
			endif;
		endif;
	endforeach;

	/* Check for Holidays */
	foreach ((array) $wp_opening_hours->holidays as $holiday)
		if ($holiday->isRunning())	return ($return_type) ? array( false, 'holiday' ) : false;
	

	
	/* Check for regular Opening Periods */
	foreach ((array) $wp_opening_hours->$today as $period)
		if ($period->isRunning())	return ($return_type) ? array( true, 'regular' ) : true;
	
	return ($return_type) ? array( false, 'regular' ) : false;
}

function GetTimestamps()
{
	global $wp_opening_hours;
	if (!is_object($wp_opening_hours))	$wp_opening_hours	= new OpeningHours;

	$today		= strtolower( date('l', current_time('timestamp')) );

	foreach ((array) $wp_opening_hours->specialOpenings as $specialOpening) :
		if ( $specialOpening->isToday() ) :
			$timestamps = $specialOpening->timestamps($today);
		else:
			foreach ((array) $wp_opening_hours->$today as $period):
				$timestamps = $period->timestamps();
			endforeach;
		endif;
	endforeach;

	return @$timestamps;
}

function onVacation()
{
	global $wp_opening_hours;
	foreach ((array) $wp_opening_hours->holidays as $holiday)
		if ($holiday->isRunning())	return true;
	return false;
}

function isVacationRunningOn($ts)
{
	global $wp_opening_hours;
	foreach ((array) $wp_opening_hours->holidays as $holiday)
		if ($holiday->isRunningOn($ts))	return true;
	return false;
}

function SpecialOpeningBetween($monday,$sunday)
{
	global $wp_opening_hours;
	foreach ((array)$wp_opening_hours->specialOpenings as $specialOpening)
	{
		if ($specialOpening->start_ts >= $monday and $specialOpening->start_ts <= ($sunday + 86400))
		{
			return true;
		}

	}
	return false;
}

/**
 *	is_closed		– Returns the opposite of is_open
 */
function is_closed ( $return_type	= false ) {
	return !is_open( $return_type );
}
?>