<?php
/**
 *	Backend Special Openings Page Template
 */
 
/**
 *	Process data
 */
if ($_POST['action'] == 'save') :
	$i					= 0;
	$specialOpenings	= array();
	
	foreach ((array)$_POST['op-name'] as $name) :
		$specialOpenings[]	= new SpecialOpening( array(
			'name'		=> $_POST['op-name'][ $i ],
			'date'		=> $_POST['op-date'][ $i ],
			'start'		=> $_POST['op-start-time'][ $i ],
			'end'		=> $_POST['op-end-time'][ $i ]
		) );
		
		$i++;
	endforeach;
		
	global $wp_opening_hours;
	if (!is_object( $wp_opening_hours ))
		$wp_opening_hours		= new OpeningHours();
	$wp_opening_hours->specialOpenings	= $specialOpenings;	
	$wp_opening_hours->saveSpecialOpenings();
	
endif;

/**
 *	Template
 */
?>

<div class="wrap">
	
    <h2>
        <?php op_e('Special Opening Hours') ?>
        <a class="add-new-h2 add-item">
        	<?php op_e('Add Special Opening') ?>
        </a>
    </h2>
    
    <p>
        <?php op_e('Special Openings overwrite the Opening Hours of a certain day.') ?>
    </p>
    
    <?php $wp_opening_hours->addSpecialOpeningDummy(); ?>
    
    <form method="post">
    	<table class="op-form-table" id="op-special-openings-form">
        	<thead>
            	<th width="30%"><?php op_e('Name')	?>			</th>
                <th width="30%"><?php op_e('Date') ?>			</th>
                <th><?php op_e('Start Time') ?>				</th>
                <th><?php op_e('End Time') ?>					</th>
                <th width="15%"><!-- options -->			</th>
            </thead>
            
            <tbody id="op-special-openings">
            	<?php foreach ((array)$wp_opening_hours->specialOpenings	as $special_opening) : ?>
            	<tr class="op-item">
                	<td><input type="text" class="widefat" 		value="<?php echo $special_opening->name ?>" 		name="op-name[]" />								</td>
                    <td><input type="text" class="date-input"	value="<?php echo $special_opening->date_str ?>"	name="op-date[]" 		 /></td>
                    <td><input type="text" class="time-input"	value="<?php echo $special_opening->start_str ?>"	name="op-start-time[]"	onfocus="this.blur()" /></td>
                    <td><input type="text" class="time-input"	value="<?php echo $special_opening->end_str ?>"	name="op-end-time[]" 	onfocus="this.blur()" /></td>
                    <td><a class="op-label red remove-item <?php if (empty($special_opening->name)) echo 'hidden' ?>"><?php op_e('Remove') ?></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    
    <input type="hidden" name="action" value="save" />
    <?php
		submit_button()
	?>
    </form>
    
</div>

<?php
	/**
	 *	JavaScript
	 */
?>

<script type="text/javascript">
	/* Bind elements when loaded */
	reBind();
	
	/**
	 *	Add Item
	 */
	jQuery('.add-item').click( function(e) {
		newRow	= jQuery('tr.op-item').last().clone();
		newRow.find('.time-input, .date-input').removeAttr('id').removeClass('hasDatepicker').removeClass('hasTimepicker');
		newRow.find('input').val('');
		newRow.appendTo('#op-special-openings');
		newRow.find('.remove-item').removeClass('hidden');

		reBind();
	} );
	
	/**
	 *	ReBind Datepicker, Timepicker and Remove-Button
	 */
	function reBind() {
		jQuery('.remove-item').unbind().click( function(e) {
			jQuery( this ).parents( '.op-item' ).remove();
		} );
		
		jQuery('.time-input').unbind().removeClass('hasTimepicker').timepicker();
		
		jQuery('.date-input').unbind().removeClass('hasDatepicker').datepicker();
	}
</script>