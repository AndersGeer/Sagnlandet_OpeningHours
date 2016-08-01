<?php

/**
 *	Opening Hours		– Overview Widget
 */

class Opening_Hours_Overview extends WP_Widget {

    private $daysAdded;
	/**
	 *	Constructor
	 */
    function __construct() {
		$widget_ops = array(
			'classname' 	=> 'widget_op_overview', 
			'description' 	=> op__('This Widgets displays an overview of your Opening Hours in the corresponding Sidebar')
		);
	
		$this->WP_Widget('widget_op_overview', op__('Opening Hours Overview'), $widget_ops);
		$this->alt_option_name = 'widget_op_overview';
		
		$this->defaults = array(
			'title'				=> apply_filters( 'op_overview_widget_default_title', op__('Opening Hours') ),
			'caption-closed'	=> apply_filters( 'op_overview_widget_default_closed', op__('Closed') ),
            'caption-vacation'  => apply_filters( 'op_overview_widget_default_vacation', "Lukket - Uden for sæson")
		);
	}


	/**
	 *	Widget Function
	 */
	function widget ($args, $instance) {
		extract ( $args );
		
		/* Set default Values */
		$instance	= self::setup_defaults( $instance );
		
		/* Initialize $wp_opening_hours */
		global $wp_opening_hours;
		if (!is_object($wp_opening_hours))	$wp_opening_hours	= new OpeningHours;
		
		echo $before_widget;
		
		/* Title Markup */
		echo $before_title;
		echo apply_filters( 'op_overview_widget_title', $instance['title'] );
		echo $after_title;

		/* Body Markup */

        if ($wp_opening_hours->numberPeriods() or $instance['show-closed'])         // If there are any Periods or if the "Show Closed" option is activated
        {
            ?>
            <table class="op-table op-overview-table">
                <?php
                global $daysAdded;
                foreach ($wp_opening_hours->allDays() as $key => $periods)        // Each day
                {
                    if (count($periods) or $instance['show-closed']) {
                        ?>
                        <tr class="op-overview-row <?php echo ($instance['highlight'] == 'day' and $key ==
                            strtolower(date('l', current_time('timestamp')))) ? 'highlighted' : '' ?>">
                            <th class="op-overview-title">
                                <?php echo apply_filters('op_overview_widget_weekday',
                                    $wp_opening_hours->weekdays[$key]) ?>
                            </th>

                            <td class="op-overview-times">
                                <?php

                                $givenYear = date('Y');
                                $givenWeek = date('W');
                                $monday = strtotime("{$givenYear}-W{$givenWeek}");
                                $sunday = strtotime("{$givenYear}-W{$givenWeek}-7");


                                $firstDateTs = $monday;
                                $thisDateTs = $firstDateTs + ($daysAdded++ * 86400);
                                if ($daysAdded == 7) $daysAdded = 0;

                                if (is_array($periods) and count($periods))
                                {
                                    foreach ($periods as $period) {
                                        ?>
                                        <div  <?php echo ($instance['highlight'] == 'period' and
                                            $period->isRunning()) ? 'highlighted' : '' ?>">
                                            <?php


                                            if (SpecialOpeningBetween($monday,$sunday))
                                            {
                                                SpecialOpening($wp_opening_hours, $thisDateTs, $instance['caption-vacation'], $period, $periods,$instance['caption-closed']);
                                            }
                                            else
                                            {
                                                NormalRoutine($thisDateTs, $instance['caption-vacation'], $period, $periods, $instance['caption-closed']);
                                            }
                                            ?>
                                        </div>
                                        <?php


                                    }
                                }
                                else
                                {
                                    if (SpecialOpeningBetween($monday, $sunday))
                                    {
                                        SpecialOpening($wp_opening_hours, $thisDateTs, $instance['caption-vacation'], $period, $periods, $instance['caption-closed']);
                                    }
                                    else
                                    {
                                        NormalRoutine($thisDateTs, $instance['caption-vacation'], $period, $periods, $instance['caption-closed']);
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
            <?php
        }


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
            <input type="text" value="<?php echo $instance['title'] ?>" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?>" placeholder="<?php echo $this->defaults['title'] ?>" class="widefat" />
        </p>
                
        <p>
        	<label for="<?php echo $this->get_field_id('show-closed') ?>">
            	<input type="checkbox" <?php echo ($instance['show-closed']) ? 'checked="checked"' : '' ?> name="<?php echo $this->get_field_name('show-closed') ?>" id="<?php echo $this->get_field_id('show-closed') ?>" />
                <?php op_e('Also show closed days') ?>
            </label>
        </p>
        
    	<p id="op-overview-caption-closed">
        	<label for="<?php echo $this->get_field_id('caption-closed') ?>">
            	<?php op_e('Caption for "closed"-label') ?>
            </label>
            <input type="text" value="<?php echo $instance['caption-closed'] ?>" name="<?php echo $this->get_field_name('caption-closed') ?>" id="<?php echo $this->get_field_id('caption-closed') ?>" placeholder="<?php echo $this->defaults['caption-closed'] ?>" class="widefat" />
        </p>
        
        <p id="op-overview-caption-vacation">
            <label for="<?php echo $this->get_field_id('caption-vacation') ?>">
                <?php op_e('Caption for "vacation"-label') ?>
            </label>
            <input type="text" value="<?php echo $instance['caption-vacation'] ?>" name="<?php echo $this->get_field_name('caption-vacation') ?>" id="<?php echo $this->get_field_id('caption-vacation') ?>" placeholder="<?php echo $this->defaults['caption-vacation'] ?>" class="widefat" />
        </p>
        
        
        <p>
        	<label for="<?php echo $this->get_field_id('highlight') ?>">
            	<?php op_e('Highlight:') ?>
            </label>
            
            <select class="widefat" name="<?php echo $this->get_field_name('highlight') ?>" id="<?php echo $this->get_field_id('highlight') ?>">
            <?php
				$highlight_opts		= array(
					'nothing'		=> op__('nothing'),
					'period'		=> op__('running period'),
					'day'			=> op__('current day')
				);
				
				foreach ($highlight_opts as $slug => $caption) :
				?>
                	<option value="<?php echo $slug ?>" <?php echo ($instance['highlight'] == $slug) ? 'selected="selected"' : '' ?>>
                    	<?php echo $caption ?>
                    </option>
                <?php
				endforeach;
			?>
            </select>
        </p>
    <?php
	}
	
	/**
	 *	Helper:	Set default values if not set by user
	 */
	function setup_defaults( $instance = array() ) {
		foreach ($this->defaults as $key => $caption) :
			if (empty($instance[ $key ]))	$instance[ $key ]	= $caption;
		endforeach;
		
		return $instance;
	}
	
}


/**
 * @param $wp_opening_hours
 * @param $thisDateTs
 * @param $vacationText
 * @param $period
 */


?>