<?php
	if ($_POST['action'] == 'save') :
		
		$holidays		= array();
		for ($i = 0; $i <= count($_POST['op-holiday-name']) - 1; $i++) :
			if (!empty($_POST['op-holiday-name'][$i]) and !empty($_POST['op-holiday-start'][$i]) and !empty($_POST['op-holiday-end'][$i]))
				$holidays[]		= new HolidayPeriod( array(
					'name'		=> $_POST['op-holiday-name'][$i],
					'start'		=> $_POST['op-holiday-start'][$i],
					'end'		=> $_POST['op-holiday-end'][$i]
				) );
		endfor;
		
		global $wp_opening_hours;
		$wp_opening_hours->holidays		= $holidays;
		$wp_opening_hours->saveHolidays();
		
	endif;
?>

<div class="wrap">

	<?php screen_icon('options-general') ?>
    
    <h2>
    	<?php op_e('Holidays') ?>
        <a class="add-new-h2 add-holiday" style="cursor: pointer">
        	<?php op_e('Add Holiday') ?>
        </a>
    </h2>
    
    <p>
    	<?php op_e('Give your Holiday a name and set start and end date.') ?>
    </p>
    
	<form method="post">
    	<table class="op-form-table" id="op-holidays-form">
        	<thead>
            	<th><?php op_e('Name') ?>		</th>
                <th><?php op_e('Start Date') ?></th>
                <th><?php op_e('End Date') ?>	</th>
                <th><!-- options -->		</th>
            </thead>
                        
            <?php 
			$wp_opening_hours->addHolidayDummy();
			foreach ($wp_opening_hours->holidays as $holiday) : 
			?>
            <tr class="op-holiday">
                <td><input type="text" class="widefat" 			name="op-holiday-name[]" 	value="<?php echo $holiday->name ?>"	/></td>
                <td><input type="text" class="widefat start date-input" 	name="op-holiday-start[]" 	value="<?php echo $holiday->start ?>" 	/></td>
                <td><input type="text" class="widefat end 	date-input" 	name="op-holiday-end[]" 	value="<?php echo $holiday->end ?>"/></td>
                <td><a class="op-label red remove-holiday <?php if (empty($holiday->name)) echo 'hidden' ?>"><?php op_e('Remove') ?></a></td>
            </tr>
            <?php endforeach ?>

        </table>
		
        <input type="hidden" name="action" value="save" />
        <?php
			submit_button()
		?>

    </form>
</div>

<script type="text/javascript">	

	function reBind() {	
		jQuery('.op-holiday').unbind();
	
		jQuery('.op-holiday').each(function(index, element) {
			
			inputStart		= jQuery(this).find('.start');
			inputEnd		= jQuery(this).find('.end');
						
			inputStart.datepicker({
				showAnim:	'drop'
			});
			
			inputEnd.datepicker({
				showAnim:	'drop'
			});
		});
		
		jQuery('.remove-holiday').unbind();
		
		jQuery('.remove-holiday').click(function(e) {
			jQuery(this).parents('.op-holiday').remove();
		});
	}
	
	reBind();
	
	jQuery('.add-holiday').click( function(e) {
		newRow	= jQuery('tr.op-holiday').last().clone();
		newRow.find('.start, .end').removeAttr('id').removeClass('hasDatepicker');
		newRow.find('input').val('');
		newRow.appendTo('#op-holidays-form');
		newRow.find('.remove-holiday').removeClass('hidden');

		reBind();
	} );
</script>