<?php
/**
 *	Opening Hours Shortcodes
 */

global $wp_opening_hours;
$daysAdded;
if (!is_object($wp_opening_hours))	$wp_opening_hours	= new OpeningHours;
/**
 *	Is Open Shortcode
 */
function op_shortcode_is_open ($atts) {



	/* Extract Attributes */
	extract(shortcode_atts( array(
		'caption_open'		=> apply_filters( 'op_status_shortcode_default_open', op__('We\'re currently open') ),
		'caption_closed'	=> apply_filters( 'op_status_shortcode_default_closed', op__('We\'re currently closed') )
	), $atts, apply_filters( 'op_status_shortcode_key', 'is-open' )));
	
	/* Return right string */
	return	apply_filters( 
		'op_status_shortcode_output', 
		(is_open())	
			? apply_filters( 'op_status_shortcode_open', $caption_open ) 
			: apply_filters( 'op_status_shortcode_closed', $caption_closed ) 
		);
}

function shortcode($atts, $content)
{
    $a = shortcode_atts( array(
        'closed' => 'Sorry we\'re closed!',
        'vacation' => 'Sorry we\'re on holidays',
    ), $atts );

    $closedText = "{$a['closed']}";
    $vacationText = "{$a['vacation']}";


    //Change to open
    if (is_open())
    {
        if ( empty( $content ) )
            return $content;

        //get opening hours
        $timestamps = GetTimestamps();
        $open = str_split($timestamps[0],2);
        if ($open[1] === "0" || $open[1] === "5")
        {
            $open = FixEarlyTimes($open);
        }
        $opentime = $open[0].'.'.$open[1];
        $close = str_split($timestamps[1],2);
        if ($close[1] === "0" || $close[1] === "5")
        {
            $close = FixEarlyTimes($close);
        }
        $closetime = $close[0].'.'.$close[1];

        $content = str_replace( "{{TodayOpen}}", $opentime, $content );
        $content = str_replace( "{{TodayClose}}", $closetime, $content );

    }
    else
    {

        if (onVacation())
        {
            //Change to vacation
            $content = $vacationText;
        }
        else
        {
            //Change to close
            $content = $closedText;
        }
    }
    return $content;
}

/**
 * @param $inputArray
 * @return mixed
 */
function FixEarlyTimes($inputArray)
{
    $split = str_split($inputArray[0], 1);
    $inputArray[0] = $split[0];
    $inputArray[1] = $split[1] . $inputArray[1];
    return $inputArray;
}

function resetWeekSatus()
{
    global $daysAdded;
    $daysAdded = 0;
}


function weekMarkercorrectPlace($atts)
{
    ob_start();
    shortcode_week($atts);
    $output_string=ob_get_contents();;
    ob_end_clean();
    return $output_string;
}
function shortcode_week($atts)
{
    $a = shortcode_atts(array(
        'closed' => 'Sorry we\'re closed!',
        'vacation' => 'Sorry we\'re on holidays',
        'highlight' => 'day'
    ), $atts);

    $closedText = "{$a['closed']}";
    $vacationText = "{$a['vacation']}";
    $highlightType = "{$a['highlight']}";


    global $wp_locale;
    global $wp_opening_hours;
    if (!is_object($wp_opening_hours)) $wp_opening_hours = new OpeningHours;

    $weekdays = $wp_locale->weekday;

    //Changes so Monday is first and sunday last
    $first = array_slice($weekdays, 0, 1, true);
    $second = array_slice($weekdays, 1, count($weekdays), true);
    $weekdays = $second + $first;


    /* Body Markup */
    if ($wp_opening_hours->numberPeriods() or $closedText)         // If there are any Periods or if the "Show Closed" option is activated
    {
        ?>
        <table class="op-table op-overview-table">
            <?php
            global $daysAdded;
            foreach ($wp_opening_hours->allDays() as $key => $periods)        // Each day
            {
                if (count($periods) or $closedText) {
                    ?>
                    <tr class="op-overview-row <?php echo ($highlightType == 'day' and $key ==
                        strtolower(date('l', current_time('timestamp')))) ? 'highlighted' : '' ?>">
                        <th class="op-overview-title">
                            <?php echo apply_filters('op_overview_widget_weekday',
                                $wp_opening_hours->weekdays[$key]) ?>
                        </th>
                        <th class="op-overview-date">
                            <?php

                            $week_start = new DateTime();
                            $week_start->setISODate(date("Y"),date("W"));
                            $weekDates = array();
                            $weekDates = getWeekDate($week_start, $weekDates, $daysAdded, $key);
                            echo apply_filters('op_overview_widget_weekday',$weekDates[$key]->format('d-m-Y'));


                            ?>
                        </th>
                        <td class="op-overview-times">
                            <?php
                            $givenYear = date('Y');
                            $givenWeek = date('W');
                            $monday = strtotime("{$givenYear}-W{$givenWeek}");
                            $sunday = strtotime("{$givenYear}-W{$givenWeek}-7");

                            global $daysAdded;
                            $firstDateTs = $monday;
                            $thisDateTs = $firstDateTs + ((-1 + $daysAdded) * 86400); //Negative one, due to i already being increased by one earlier - monday is 0, sunday is 6
                            if ($daysAdded == 7) $daysAdded = 0;

                            if (is_array($periods) and count($periods))
                            {
                                foreach ($periods as $period) {
                                    ?>
                                    <div class="op-overview-set <?php echo ($highlightType == 'period' and
                                        $period->isRunning()) ? 'highlighted' : '' ?>">
                                        <?php


                                        if (SpecialOpeningBetween($monday,$sunday))
                                        {
                                            SpecialOpening($wp_opening_hours, $thisDateTs, $vacationText, $period, $periods,$closedText);
                                        }
                                        else
                                        {
                                            NormalRoutine($thisDateTs, $vacationText, $period, $periods, $closedText);

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
                                    SpecialOpening($wp_opening_hours, $thisDateTs, $vacationText, $period, $periods, $closedText);
                                }
                                else
                                {
                                    NormalRoutine($thisDateTs, $vacationText, $period, $periods, $closedText);
                                }
                            } ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <?php
    }
}

/**
 * @param $wp_opening_hours
 * @param $thisDateTs
 * @param $vacationText
 * @param $period
 */
function SpecialOpening($wp_opening_hours, $thisDateTs, $vacationText, $period, $periods, $closedText)
{
//timeString(GetOpeningTimes($period))
    //Special openings overrule vacations
    $echoDone = false;
    foreach ((array)$wp_opening_hours->specialOpenings as $specialOpening) {
        /*
         * If special opening is between monday and sunday
         * Is special opening today
         * Set times.
        */

        if ($specialOpening->isRunningOn($thisDateTs)) {
            echo apply_filters('op_overview_widget_time_string', timeString(
                array(
                    'start' => $specialOpening->start_ts,
                    'end' => $specialOpening->end_ts
                )));
            $echoDone = true;
            break;
        }
    }
    if (!$echoDone)
    {
        NormalRoutine($thisDateTs, $vacationText, $period, $periods,$closedText);
    }
}

/**
 * @param $thisDateTs
 * @param $vacationText
 * @param $period
 * @param $periods
 * @param $closedText
 */
function NormalRoutine($thisDateTs, $vacationText, $period, $periods, $closedText)
{
// Vacations overrule normal openings

    if (isVacationRunningOn($thisDateTs))
    {
        echo apply_filters('op_overview_widget_closed',
            $vacationText);
    }
    else if (is_array($periods) and count($periods))
    {
        //Normal openings
        echo apply_filters('op_overview_widget_time_string', timeString(
            GetOpeningTimes($period)));
    }
    else
    {
        echo apply_filters('op_overview_widget_closed',
            $closedText);
    }
}

/**
 * @param $period
 * @return array
 */
function GetOpeningTimes($period)
{
    global $wp_opening_hours;
    return array(
        'start' => $period->start_ts,
        'end' => $period->end_ts
    );
}

/**
 * @param $week_start
 * @param $weekDates
 * @param $daysAdded
 * @param $day
 * @return mixed
 */
function getWeekDate($week_start, $weekDates, $daysAdded, $day)
{
    global $daysAdded;
    $epoch = time();
    switch ($daysAdded) {
        case 0:
            $weekDates[$day] = $week_start;
            $daysAdded++;
            break;
        case 1:
            $weekDates[$day] = $week_start->add(new DateInterval('P1D'));
            $daysAdded++;
            break;
        case 2:
            $weekDates[$day] = $week_start->add(new DateInterval('P2D'));
            $daysAdded++;
            break;
        case 3:
            $weekDates[$day] = $week_start->add(new DateInterval('P3D'));
            $daysAdded++;
            break;
        case 4:
            $weekDates[$day] = $week_start->add(new DateInterval('P4D'));
            $daysAdded++;
            break;
        case 5:
            $weekDates[$day] = $week_start->add(new DateInterval('P5D'));
            $daysAdded++;
            break;
        case 6:
            $weekDates[$day] = $week_start->add(new DateInterval('P6D'));
            $daysAdded ++;
            break;
    }

    return $weekDates;
}


/**
 *	Register Shortcode
 */
add_shortcode (apply_filters( 'op_status_shortcode_key', 'is-open' ), 'op_shortcode_is_open');
add_shortcode( 'businesshours', 'shortcode');
add_shortcode( 'businesshoursweek','weekMarkercorrectPlace');





?>