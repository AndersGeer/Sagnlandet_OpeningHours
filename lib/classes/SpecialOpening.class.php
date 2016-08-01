<?php
/**
 *	Special Opening Class
 */
 
class SpecialOpening {
	
	function __construct( $data = false ) {
		$this->setup( $data );
	}
	
	/**
	 *	Setup period instance
	 */
	function setup ( $data = false ) {
		if ($data === false or !is_array($data))	return;
				
		/* ex: $data = array(
			'name'		=> 'My Holiday',
			'date'		=> '10/16/2013'
			'start'		=> '08:00',
			'end'		=> '18:00'
		); */
		
		$this->data			= $data;
		
		if ($data['name'])
			$this->name		= $data['name'];
		
		/* Special Opening -> Date */	
		$this->date_str		= $data['date'];
		$this->date_arr		= explode('/', $data['date']);
		$this->date_assoc	= array(
			'day'		=> $this->date_arr[1],
			'month'		=> $this->date_arr[0],
			'year'		=> $this->date_arr[2]
		);
		
		/* Special Opening -> Time Start */
		$this->start_str	= $data['start'];	
		$this->start_arr	= explode(':', $data['start']);
		$this->start		= $this->start_arr[0]*100 + $this->start_arr[1];
		$this->start_ts		= mktime( 
			(int)$this->start_arr[0], (int)$this->start_arr[1], 0, 
			(int)$this->date_assoc['month'], (int)$this->date_assoc['day'], (int)$this->date_assoc['year'] 
		);
		
		/* Special Opening -> Time End */
		$this->end_str		= $data['end'];
		$this->end_arr		= explode(':', $data['end']);
		$this->end			= $this->end_arr[0]*100 + $this->end_arr[1];
		$this->end_ts		= mktime( 
			(int)$this->end_arr[0], (int)$this->end_arr[1], 0, 
			(int)$this->date_assoc['month'], (int)$this->date_assoc['day'], (int)$this->date_assoc['year'] 
		);
		
		$this->is_running	= $this->isRunning();
	}
	
	/**
	 *	Checks if Special Opening is currently running
	 */
	function isRunning() {
		$now		= current_time( 'timestamp' );
		return 		($now >= $this->start_ts and $now <= $this->end_ts);
	}
	
	/**
	 *	Checks if the Special Opening's date is today
	 */
	function isToday() {
		$now		= current_time( 'timestamp' );
		return		( date('m/d/Y', $this->start_ts) == date('m/d/Y', $now) );
	}

	function isRunningOn($now)
	{
		if (date('m/d/Y', $this->start_ts) == date('m/d/Y', $now)) {return true;}
		else return false;
	}

	function timestamps()
	{
		return array($this->start,$this->end);
	}
}
?>