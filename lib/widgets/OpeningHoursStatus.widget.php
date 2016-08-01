<?php
/**
 *	Opening Hours 	– Status Widget
 */
 
class Opening_Hours_Status extends WP_Widget {

	/**
	 *	Constructor
	 */
	function __construct() {
		$widget_ops = array(
			'classname' 	=> 'widget_op_status', 
			'description' 	=> op__('This Widgets displays information in your sidebar, wether your venue is closed or open.')
		);
	
		$this->WP_Widget('widget_op_status', op__('Opening Hours Status'), $widget_ops);
		$this->alt_option_name = 'widget_op_status';
		
		$this->defaults = array(
			'caption-open'				=> apply_filters( 'op_status_widget_default_open', op__('We\'re currently open.') ),
			'caption-closed'			=> apply_filters( 'op_status_widget_default_closed', op__('We\'re currently closed.') ),
			'caption-closed-holiday'	=> apply_filters( 'op_status_widget_default_closed_holiday', op__('We\'re currently on holiday.') )
		);
	}
	
	/**
	 *	Widget Function
	 */
	function widget ($args, $instance) {
		extract ($args);
		
		/* Set default values */		
		$instance	= self::setup_defaults( $instance );
		
		echo $before_widget;
		
		/* Title Markup */
		if ($instance['title'])	:
			echo $before_title;
			echo apply_filters( 'op_status_widget_title', $instance['title'] );
			echo $after_title;
		endif;
		
		/* Body Markup */
		$is_open		= is_open( true ); 
		?>
        	<div class="op-status-label <?php echo ($is_open[0]) ? 'open' : 'closed' ?> <?php if (!$is_open[0]) echo 'closed-' . $is_open[1] ?>">
            	<?php
				
				if ($is_open[0]) :
					$message	= apply_filters( 'op_status_widget_open', $instance['caption-open'] );
				else :
					$message	= ($is_open[1] == 'holiday') 
						? apply_filters( 'op_status_widget_closed_holiday', $instance['caption-closed-holiday'] ) 
						: apply_filters( 'op_status_widget_closed', $instance['caption-closed'] );
				endif;
				
				echo apply_filters (
					'op_status_widget_output',
					$message
				)
				?>
            </div>
        <?php
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
            <input type="text" class="widefat" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?>" value="<?php echo $instance['title'] ?>" />
        </p>
        
        <p>
        	<label for="<?php echo $this->get_field_id('caption-open') ?>">
            	<?php op_e('Custom open-message') ?>
            </label>
            <input type="text" class="widefat" name="<?php echo $this->get_field_name('caption-open') ?>" id="<?php echo $this->get_field_id('caption-open') ?>" value="<?php echo $instance['caption-open'] ?>" placeholder="<?php echo $this->defaults['caption-open'] ?>" />
        </p>
        
        <p>
        	<label for="<?php echo $this->get_field_id('caption-closed') ?>">
            	<?php op_e('Custom closed-message') ?>
            </label>
            <input type="text" class="widefat" name="<?php echo $this->get_field_name('caption-closed') ?>" id="<?php echo $this->get_field_id('caption-closed') ?>" value="<?php echo $instance['caption-closed'] ?>" placeholder="<?php echo $this->defaults['caption-closed'] ?>" />
        </p>
        
        <p>
        	<label for="<?php echo $this->get_field_id('caption-closed-holiday') ?>">
            	<?php op_e('Custom closed-holiday-message') ?>
            </label>
            <input type="text" class="widefat" name="<?php echo $this->get_field_name('caption-closed-holiday') ?>" id="<?php echo $this->get_field_id('caption-closed-holiday') ?>" value="<?php echo $instance['caption-closed-holiday'] ?>" placeholder="<?php echo $this->defaults['caption-closed-holiday'] ?>" />
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
?>