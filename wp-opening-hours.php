<?php
/*
Plugin Name: Opening Hours Fix
Description: This Plugin enables you to manage your opening hours in WordPress. It lets you set them up in a settings page and display them in two widgets.
Version: 1.2.1-alpha
Author: Jannik Portz (@janizde)
Author URI: http://jannikportz.de
*/

/*  Copyright 2013  Jannik Portz  (email : webmaster@jannikportz.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
*	Globals
*/
function op_textdomain() {
	return apply_filters( 'op_textdomain', 'opening_hours');
}

function op_basepath() {
	return plugin_dir_path ( __FILE__ );
}

function op_baseurl() {
	return plugins_url ( false, __FILE__ );
}

/**
*	Register Textdomain
*/
load_plugin_textdomain( op_textdomain() , false, apply_filters( 'op_language_path', basename( op_basepath() ) . '/lang/' ));

function op__ ( $msg ) {
	return __( $msg, op_textdomain() );
}

function op_e ( $msg ) {
	echo op__( $msg );
}

/**
*	Require Files
*/
include		op_basepath() . '/lib/classes/OpeningHours.class.php';
include		op_basepath() . '/lib/classes/OpeningPeriod.class.php';
include		op_basepath() . '/lib/classes/HolidayPeriod.class.php';
include		op_basepath() . '/lib/classes/SpecialOpening.class.php';

include		op_basepath() . '/lib/functions.php';
include		op_basepath() . '/lib/init.php';
include		op_basepath() . '/lib/widgets.php';
include		op_basepath() . '/lib/template-tags.php';
include		op_basepath() . '/lib/shortcodes.php';

/**
 *	Activation
 */
 
function op_activate () {
	
	/* default Settings */
	$default_settings	= array(
		'time-format'		=> 'H:i',
		'date-format'		=> 'd.m.Y'
	);
	
	/* unset default if setting already set */
	foreach ( $default_settings as $key => $value )
		if ( op_get_setting( $key ) )
			unset( $default_settings[ $key ] );
	
	/* apply remaining settings */
	$wp_opening_hours	= new OpeningHours;
	$wp_opening_hours->applySettings( $default_settings );
}

register_activation_hook( __FILE__, 'op_activate' );
?>