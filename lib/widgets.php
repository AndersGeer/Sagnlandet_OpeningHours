<?php
/**
 *	Opening Hours Widgets
 */
 
/**
 *	Require Widget Classes
 */
$widgets		= array(
	'OpeningHoursOverview',
	'OpeningHoursStatus',
	'OpeningHoursHolidays',
	'OpeningHoursSpecialOpenings',
);

foreach ( $widgets as $widget )
	require_once	op_basepath() . '/lib/widgets/'. $widget .'.widget.php';

/**
 *	Register Widgets
 */
function op_init_widgets() {
	
	register_widget ( 'Opening_Hours_Overview' 			);
	register_widget	( 'Opening_Hours_Status'			);
	register_widget	( 'Opening_Hours_Holidays' 			);
	register_widget ( 'Opening_Hours_Special_Openings' 	);
	
}
add_action ( 'widgets_init', 'op_init_widgets' );

?>