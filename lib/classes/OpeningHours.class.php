<?php
/**
 *	Opening Hours Class
 */
 
class OpeningHours {
	
	function __construct() {
		$this->setup();
		$this->getSettings();
		$this->getHolidays();
		$this->getSpecialOpenings();
	}
	
	/**
	 *	Setup – intializes opening hours
	 */
	function setup() {
		if (get_option('wp_opening_hours')) :
			$this->data		= json_decode( get_option('wp_opening_hours'), true );
		else :
			$this->data		= array();
		endif;
		
		$this->weekdays		= array(
			'monday'	=> op__('Monday'), 
			'tuesday'	=> op__('Tuesday'),
			'wednesday'	=> op__('Wednesday'),
			'thursday'	=> op__('Thursday'),
			'friday'	=> op__('Friday'), 
			'saturday'	=> op__('Saturday'), 
			'sunday'	=> op__('Sunday')
		);
				
		foreach ( $this->data as $key => $values ) :
			$periods	= array();
			
			foreach ( $this->data[ $key ][ 'times' ] as $period ) :
				$periods[]	= new OpeningPeriod( array(
					'day'	=> $key,
					'times'	=> $period
				) );
			endforeach;
			
			$this->$key	= $periods;
		endforeach;		
	}
	
	/**
	 *	Save – Saves opening hours to wp_option
	 */
	function save() {
		$data		= array();
		foreach ($this->weekdays as $key => $caption) :
			if (is_array($this->$key))
				$periods	= $this->$key;
			
			foreach ($periods as $period) :
				
				if ($period->correctValues()) :
					$data[ $key ]['times'][]	= $period->times;
				else :
					unset( $period );
				endif;
				
			endforeach;
		endforeach;

		$success	= update_option( 'wp_opening_hours', json_encode( $data ) );
		$this->setup();
		return $success;
	}
			
	/**
	 *	Add Dummy Sets – adds an empty set for unset days
	 */
	function addDummySets() {
		foreach ($this->weekdays as $key => $caption) :
			if (!is_array( $this->$key ) or !count( $this->$key ))
				$this->$key		= array(
					new OpeningPeriod( array(
						'day'		=> $key,
						'times'		=> array(0, 0, 0, 0)
					) )
				);
		endforeach;
	}
	
	/**
	 *	Returns an array with all periods
	 */
	function allPeriods() {
		$periods		= array();
		
		foreach ($this->weekdays as $key => $caption) :
						
			if (is_array( $this->$key ))
				foreach ($this->$key as $period)
					$periods[]		= $period;
		endforeach;
		
		return $periods;
	}
	
	/**
	 *	Returns number of all periods
	 */
	 function numberPeriods() {
		return count( $this->allPeriods() ); 
	 }
	 
	 /**
	  *	Returns an array with all days
	  */
	 function allDays() {
		 $days		= array();
		 foreach ($this->weekdays as $key => $caption) :
		 	$days[ $key ]	= (is_array( $this->$key )) ? $this->$key : array();
		 endforeach;
		 return $days;
	 }
			
	/**
	 *	Settings
	 */
	function getSettings() {
		$this->settings		= json_decode( get_option('wp_opening_hours_settings'), true );
	}
	
	function saveSettings() {
		update_option( 'wp_opening_hours_settings', json_encode( $this->settings ) );
	}
	
	function applySettings( $args = array() ) {
		// Format of $args:		array( $setting_key => $value )
		foreach ( $args as $key => $value ) :
			$this->settings[ $key ]			= $value;
		endforeach;
		
		$this->saveSettings();
	}
	
	/**
	 *	Get Holidays – Sets up the Holidays array from WP Option
	 */
	function getHolidays() {
		$holidays		= json_decode( get_option('wp_opening_hours_holidays'), true );
		
		$this->holidays	= array();
		foreach ((array) $holidays as $holiday) :
		
			$this->holidays[]	= new HolidayPeriod( $holiday );
		
		endforeach;
	}
	
	/**
	 *	Get Special Openings – Sets up the Special Openings array from WP Option
	 */
	function getSpecialOpenings() {
		$specialOpenings		= json_decode( get_option('wp_opening_hours_special_openings'), true );
				
		$this->specialOpenings	= array();
		foreach ((array) $specialOpenings as $special_opening) :
		
			$this->specialOpenings[]	= new SpecialOpening( $special_opening );
		
		endforeach;
	}

	
	/**
	 *	Saves the Holidays array to WP Option
	 */
	function saveHolidays() {
		$holidays	= array();
		
		foreach ((array) $this->holidays as $holiday) :
			$holidays[]		= $holiday->data;
		endforeach;
		
		update_option('wp_opening_hours_holidays', json_encode( $holidays ));
	}
	
	/**
	 *	Saves Special Openings Array to WP Option
	 */
	function saveSpecialOpenings() {
		$specialOpenings	= array();
		
		foreach ((array) $this->specialOpenings as $special_opening) :
			$specialOpenings[]	= $special_opening->data;
		endforeach;
		
		update_option( 'wp_opening_hours_special_openings', json_encode( $specialOpenings ) );
	}
	
	/**
	 *	Add Holiday Dummy – Adds an empty Holiday to the array of Holidays
	 */
	function addHolidayDummy() {
		if (!count($this->holidays))
			$this->holidays		= array(
				new HolidayPeriod
			);
	}
	
	/**
	 *	Add Special Opening Dummy – Adds an empty Special Opening to the array of Special Openings
	 */
	function addSpecialOpeningDummy() {
		if (!count($this->specialOpenings))
			$this->specialOpenings	= array(
				new SpecialOpening
			);
	}
	
	/**
	 *	HELPERS
	 */
	 
	function getSets( $key ) {
		return $this->data[ $key ][ 'times' ];
	}
	
	function numberSets( $key ) {
		return count($this->getSets( $key ));
	}
	
	function timeString( $key = false, $set = false, $edge = false ) {
		if (!$key or $set === false)		return;
		$string			= '';

		$setdata		= $this->data[ (string)$key ][ 'times' ][ (string)$set ];
		if ($edge == 'start') :
			return twoDigits($setdata[0]).':'.twoDigits($setdata[1]);
		elseif ($edge == 'end') :
			return twoDigits($setdata[2]).':'.twoDigits($setdata[3]);
		else :
			return twoDigits($setdata[0]).':'.twoDigits($setdata[1]).' – '.twoDigits($setdata[2]).':'.twoDigits($setdata[3]);
		endif;
	}
	
	function isRunning ( $key = false, $set = false ) {
		if (!$key or $set === false)		return;
		
		if ($key != strtolower( date('l', time()) ))	return false;

		$setdata		= $this->data[ (string)$key ][ 'times' ][ (string)$set ];
		$currentTime	= date('H', current_time('timestamp'))*100 + date('i', current_time('timestamp'));
		return	( $currentTime >= $setdata[0]*100 + $setdata[1] and $currentTime <= $setdata[2]*100 + $setdata[3] );
	}

	
// End of Class
}
?>