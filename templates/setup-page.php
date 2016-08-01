<?php
/**
 *	Backend Options Page: Setup
 */

$weekdays		= array(
	'monday'		=> op__('Monday'),
	'tuesday'		=> op__('Tuesday'),
	'wednesday'		=> op__('Wednesday'),
	'thursday'		=> op__('Thursday'),
	'friday'		=> op__('Friday'),
	'saturday'		=> op__('Saturday'),
	'sunday'		=> op__('Sunday')
);

// Process Data
if ($_POST['action']	== 'save') :
	if (!is_object($wp_opening_hours))	$wp_opening_hours	= new OpeningHours;
	
	foreach ($weekdays as $key => $caption) :
		$periods	= array();
		$i			= 0;
		
		foreach ($_POST['op-' . $key . '-start'] as $start) :
			$data		= array(
				'day'		=> $key,
				'times'		=> array_merge(
						explode(':', $_POST['op-'. $key .'-start'][$i]),
						explode(':', $_POST['op-'. $key .'-end'][$i])
				)
			);
						
			$periods[]	= new OpeningPeriod( $data );
			$i++;
		endforeach;
		
		$wp_opening_hours->$key		= $periods;
	endforeach;
		
	$wp_opening_hours->save();	// save data in wp_option
	
endif;

$wp_opening_hours->addDummySets();

?>
<div class="wrap">
	
    <?php screen_icon('options-general') ?>

    <h2>
        <?php op_e('Opening Hours') ?>
    </h2>
    
    <p>
        <?php op_e('Set up your Opening Hours in this form. You can add several periods per day.') ?>
    </p>
    
    <!-- Opening Hours Form -->
    <form method="post">
    	<table class="form-table" id="op-options-times">
        <?php			
			$hours			= range( 0, 24 );
			$minutes		= range( 0, 55, 5 );
			
			foreach ( $weekdays as $key => $caption ) :
			?>
            	<tr id="op-row-<?php echo $key ?>">
                	<th class="op-day-heading">
                    	<label for="toggle-<?php echo $key ?>">
								<?php echo $caption ?>
                        </label>
                    </th>
                    <td class="op-day-options">
                        <div class="op-times-container" id="op-times-container-<?php echo $key ?>">
                        	<?php foreach ($wp_opening_hours->$key as $i => $period) : ?>
                            <div class="op-time-group">
                            	<div class="op-time-set">
                                    <input 
                                    	type="text" 
                                        class="op-label time-input" 
                                        name="op-<?php echo $key ?>-start[]" 
                                        value="<?php echo twoDigits($period->val('start', 'hour')).':'.twoDigits($period->val('start', 'minute')) ?>"
                                        onfocus="this.blur()" />
                                </div>
                                &nbsp;–&nbsp;
                            	<div class="op-time-set">
                                    <input 
                                    	type="text" 
                                        class="op-label time-input" 
                                        name="op-<?php echo $key ?>-end[]" 
                                        value="<?php echo twoDigits($period->val('end', 'hour')).':'.twoDigits($period->val('end', 'minute')) ?>"
                                        onfocus="this.blur()" />
                                </div>
                                <?php if ($i == 0) : ?>
                                    <a class="op-label green op-add-period" data-key="<?php echo $key ?>">+ <?php op_e('Add Period') ?></a>
                                <?php else : ?>
                                	<a class="op-label red op-remove-period" data-key="<?php echo $key ?>"><?php op_e('Remove') ?></a>
                                <?php endif ?>
                            </div>
                            <?php endforeach ?>
                        </div>
                    </td>
                </tr>
            <?php
			endforeach;
		?>
        </table>
        
        <input type="hidden" name="action" value="save" />
        <?php
			submit_button()
		?>
    </form>
</div>

<script type="text/javascript">
	
	jQuery('input.time-input').timepicker();
	
	function initActions() {
		jQuery('.op-add-period, .op-remove-period').unbind();
		
		jQuery('.op-add-period').click(function(e) {
			e.preventDefault();
			addPeriod (jQuery(this));
		});
		
		jQuery('.op-remove-period').click(function(e) {
			e.preventDefault();
			removePeriod (jQuery(this));
		});
	}
		
	initActions();

	function addPeriod ( element ) {
		key			= element.attr('data-key');
		container	= jQuery('#op-times-container-'+key);
		newGroup	= container.find('.op-time-group').first().clone();
		newGroup.find('.time-input').removeClass('hasTimepicker').removeAttr('id').timepicker();
		option		= newGroup.find('.op-add-period');
		option.removeClass('green').removeClass('op-add-period').addClass('red').addClass('op-remove-period').html('<?php op_e('Remove') ?>');
		newGroup.appendTo( container );
		initActions();
	}
	
	function removePeriod ( element ) {
		element.parent('.op-time-group').remove();
		initActions();
	}
</script>