<?php
/**
 *	Opening Period Class
 */
 
class OpeningPeriod {
	
	function __construct( $data = array() ) {
		$this->setup( $data );
		
		$this->timestamp	= current_time('timestamp');
		$this->currentTime	= timeFormat( date('H', $this->timestamp), date('i', $this->timestamp) );
	}
	
	/**
	 *	Setup period instance
	 */
	function setup ( $data = false ) {
		if ($data === false or !is_array($data))	return;
				
		if ($data['day'])
			$this->day		= $data['day'];
		
		$this->times		= $data['times'];
				
		$this->start		= timeFormat( $this->times[0], $this->times[1] );
		$this->end			= timeFormat( $this->times[2], $this->times[3] );
		
		$this->start_ts		= mktime( $this->times[0], $this->times[1], 0, 0, 0, 0 );
		$this->end_ts		= mktime( $this->times[2], $this->times[3], 0, 0, 0, 0 );
						
		$this->duration		= getDifference( $this->times );
								
		if ($this->isRunning()) :
			$this->stillRunning		= getDifference( array(
				date('H', $this->timestamp), date('i', $this->timestamp), 	// now
				$this->times[2], $this->times[3]							// end
		) );
		endif;
		
		$this->is_running	= $this->isRunning();
	}
	
	/**
	 *	Is this period currently running?
	 */
	function isRunning() {		
		if ( strtolower( date('l', current_time('timestamp')) ) != $this->day )	
			return false;
			
		return ( $this->start <= $this->currentTime and $this->end >= $this->currentTime );
	}
		
	/**
	 *	Returns Time Value
	 */
	function val( $edge	= 'start', $type = 'hour' ) {
		$position		= ($edge	== 'start') ? 0 : 2;
		if ($type	== 'minute')	$position++;
		if (!is_array($this->times))	return false;
		return	$this->times[ $position ];
	}
	function timestamps()
	{
		return array($this->start,$this->end);

	}
	/**
	 *	Removes nonsense-entries
	 */
	function correctValues() {
		return ( $this->start < $this->end );
	}
}
?>