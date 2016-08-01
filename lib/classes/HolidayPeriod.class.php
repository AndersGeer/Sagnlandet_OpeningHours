<?php
/**
 *	Holiday Period Class
 */
 
class HolidayPeriod {
	
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
			'start'		=> '10/16/2013',
			'end'		=> '10/19/2013'
		); */
		
		$this->data			= $data;
		
		if ($data['name'])
			$this->name		= $data['name'];
			
		$this->start		= $data['start'];
		$this->start_arr	= explode('/', $data['start']);
		$this->start_num	= $this->start_arr[2].$this->start_arr[0].$this->start_arr[1];
		$this->start_ts		= mktime( 0, 0, 0, $this->start_arr[0], $this->start_arr[1], $this->start_arr[2] );

		$this->end			= $data['end'];
		$this->end_arr		= explode('/', $data['end']);
		$this->end_num		= $this->end_arr[2].$this->end_arr[0].$this->end_arr[1];
		$this->end_ts		= mktime( 0, 0, 0, $this->end_arr[0], $this->end_arr[1], $this->end_arr[2] );
	}
	
	function isRunning() {
		$now		= time();
		return 		($now >= $this->start_ts and $now <= $this->end_ts);
	}
	function isRunningOn($now)
	{
		return 		($now >= $this->start_ts and $now <= $this->end_ts);
	}


}
?>