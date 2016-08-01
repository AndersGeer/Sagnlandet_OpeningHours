<?php
/**
 *	Opening Hours	– Holidays Widget
 */
 
class Opening_Hours_Holidays extends WP_Widget {
	
	/**
	 *	Constructor
	 */
	function __construct() {
		$widget_opts = array(
			'classname'		=> 'widget_op_holidays',
			'description'	=> op__('This Widget lists up all Holidays set up in the Opening Hours Section.')
		);	
		
		$this->WP_Widget('widget_op_holidays', op__('Opening Hours Holidays'), $widget_opts);
		$this->alt_option_name	= 'widget_op_holidays';
	}
	
	/**
	 *	Widget Function
	 */
	function widget ($args, $instance) {
		extract ($args);
		global $wp_opening_hours;
		
		echo $before_widget;
		
		/* Title Markup */
		if ($instance['title']) :
			echo $before_title;
			echo apply_filters('op_holidays_widget_title', $instance['title']);
			echo $after_title;
		endif;
		
		/* Sort Holidays */
		$holidays		= array();
		foreach ($wp_opening_hours->holidays as $holiday) :
			$holidays[ $holiday->start_ts ]		= $holiday;
		endforeach;
		
		ksort( $holidays );
		
		/* Body Markup */
		if (count($holidays)) :
		?>
			<table class="op-table op-holidays-table">
            <?php	foreach ($holidays as $holiday) : ?>
            	<tr class="op-holiday <?php if ($instance['highlighted'] and $holiday->isRunning()) echo 'highlighted' ?>">
                	<th>
                    	<?php echo apply_filters( 'op_holidays_widget_name', $holiday->name ) ?>
                    </th>
                    <td>
                    	<?php echo apply_filters( 'op_holidays_widget_date_string', dateString( array(
							'start'		=> $holiday->start_ts,
							'end'		=> $holiday->end_ts
						) ) )
						?>
                    </td>
                </tr>
            <?php	endforeach; ?>
            </table>
        <?php
        endif;
		
		echo $after_widget;
	}
	
	/**
	 *	Update Function
	 */
	function update ($new_instance, $old_instance) {
		return $new_instance;
	}
	
	/**
	 *	Form Function
	 */
	function form ($instance) {
	?>
		<p>
        	<label for="<?php echo $this->get_field_id('title') ?>">
            	<?php op_e('Title') ?>
            </label>
            <input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?>" value="<?php echo $instance['title'] ?>" class="widefat" />
        </p>
        
        <p>
        	<label for="<?php echo $this->get_field_id('highlighted') ?>">
            	<input type="checkbox" <?php echo ($instance['highlighted']) ? 'checked="checked"' : '' ?> name="<?php echo $this->get_field_name('highlighted') ?>" id="<?php echo $this->get_field_id('highlighted') ?>" />
                <?php op_e('Highlight currently running Holidays.') ?>
            </label>
        </p>
	<?php
	}
	
}
?>