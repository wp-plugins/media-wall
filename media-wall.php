<?php
/*
Plugin Name: Media Wall
Plugin URI: http://www.grabimo.com
Description: Enable yourself and your website visitors to post video, audio, photo, and text on your website. You manage which media to display on your site.
Version: 1.3.1
Author: Grabimo
Author URI: http://www.grabimo.com
License: GPLv2 or later
*/

/*  Copyright 2014 Grabimo  (email: admin@grabimo.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	The plugin reuses the simpe_side_tab wordpress plugin devloped by Scot Rumery.
*/

// Hook will fire upon activation - we are using it to set default option values
register_activation_hook( __FILE__, 'media_wall_activate_plugin' );

// Add options and populate default values on first load
function media_wall_activate_plugin() {	
	$media_wall_plugin_option_array = array(
		'business_alias'   => 'example',
		'font_family'      => 'Tahoma, sans-serif',
		'enable_post'      => '1'
	);
		
	// fill in the empty option or not existing
	update_option( 'media_wall_plugin_options', $media_wall_plugin_option_array );
}

// --- for admin hooks only -----------------------------------------
// Fire off hooks depending on if the admin settings page is used or the public website
if (is_admin()){ // admin actions and filters
	// Hook for adding admin menu
	add_action('admin_menu', 'media_wall_admin_menu');

	// Hook for registering plugin option settings
	add_action('admin_init', 'media_wall_settings_api_init');

	// Display the 'Settings' link in the plugin row on the installed plugins list page
	add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'media_wall_admin_plugin_actions', -10);
} 

// action function to add a new submenu under Settings
function media_wall_admin_menu() {
	// Add a new submenu under Settings
	add_options_page('Media Wall', '<img style="position:relative;top:4px" src="//www.grabimo.com/download/grabimo16x16.png"/>&nbsp;Media Wall', 'manage_options', 'media_wall', 'media_wall_options_page');
}

// Use Settings API to whitelist options
function media_wall_settings_api_init() {
	register_setting('media_wall_option_group', 'media_wall_plugin_options');
}

// Build array of links for rendering in installed plugins list
function media_wall_admin_plugin_actions($links) {
	$settings_link = '<img style="position:relative;top:4px" src="//www.grabimo.com/download/grabimo16x16.png"/>&nbsp;<a href="'. get_admin_url(null, 'options-general.php?page=media_wall') .'">Settings</a>';
	array_unshift($links, $settings_link);
	
	return $links;
}

// --- add javascrit and CSS file on webpage head ------------
function media_wall_css_js_files() {	
	// add Javscript
	wp_enqueue_script( 'multimedia-feedback-js-file', '//www.grabimo.com/download/mf.js' );	
	wp_enqueue_script( 'media-wall-iframe-resize-js-file', '//www.grabimo.com/download/iframeResizer.min.js'  );
	wp_enqueue_script( 'media-wall-js-file', plugins_url( 'media-wall.js', __FILE__ ), array(), '1.0.0', true );
	
	// CSS file
	wp_enqueue_style( 'multimedia-feedback-css-file', '//www.grabimo.com/download/mf.css' );	
}
add_action( 'wp_enqueue_scripts', 'media_wall_css_js_files');

// --- create the short code -------------------------------------------
function media_wall_short_code() {
	$media_wall_plugin_option_array	= get_option( 'media_wall_plugin_options' );
	$media_wall_business_alias 		= sanitize_text_field($media_wall_plugin_option_array[ 'business_alias' ]);
	$media_wall_font_family			= $media_wall_plugin_option_array[ 'font_family' ];
	$media_wall_enable_post			= $media_wall_plugin_option_array[ 'enable_post' ];	
	$html = '<div style="width:100%;position:relative">';
	$html = $html . '<iframe id="grabimo_media_wall" src="//www.grabimo.com/app/bizGigDoc.html?alias=' . $media_wall_business_alias;
	$html = $html . '&post=' . $media_wall_enable_post . '&font=' . rawurlencode($media_wall_font_family);
	$html = $html . '" scrolling="no" allowfullscreen width="100%" style="border: none;"></iframe>';
	if ($media_wall_enable_post) {
		$html = $html . '<div id="grabimo_media_wall_submit" style="position:absolute;top:20px;left:20px;width:280px;height:113px;cursor:pointer" onClick="showGrabimoCollectionMode(\'' . $media_wall_business_alias . '\',\'' . rawurlencode($media_wall_font_family) . '\')"></div>';
	}
	$html =  $html . '</div>';
	
	return $html;
}
add_shortcode('media-wall', 'media_wall_short_code');

// --- for admin page setting html UI ----------------------------------
// Display and fill the form fields for the plugin admin page
function media_wall_options_page() {
?>
	<div class="wrap">
	<?php screen_icon( 'plugins' ); ?>
	<form method="post" action="options.php">
<?php
	settings_fields( 'media_wall_option_group' );
	do_settings_sections( 'media_wall' );

	// get plugin option array and store in a variable
	$media_wall_plugin_option_array	= get_option( 'media_wall_plugin_options' );

	// fetch individual values from the plugin option variable array
	$media_wall_business_alias 		= $media_wall_plugin_option_array[ 'business_alias' ];		
	$media_wall_font_family			= $media_wall_plugin_option_array[ 'font_family' ];
	$media_wall_enable_post			= $media_wall_plugin_option_array[ 'enable_post' ];	
?>

	<table class="widefat">
		<tr valign="top">
		<td style="width:250px"><label for="media_wall_business_alias">Set business alias</label></td>
		<td><input maxlength="30" size="25" type="text" name="media_wall_plugin_options[business_alias]" value="<?php echo esc_html( $media_wall_business_alias ); ?>" />
			<p class="description">To create an Alias for your website, sign up at <a href="https://www.grabimo.com">https://www.grabimo.com</a></td>
		</tr>
		
		<tr valign="top">
		<td><label for="media_wall_tab_font">Text font family</label></td>
		<td><input maxlength="30" size="25" type="text" name="media_wall_plugin_options[font_family]" value="<?php echo esc_html( $media_wall_font_family ); ?>" /></td>
		</tr>
		
		<tr valign="top">
		<td><label for="media_wall_show_title">Allow customers to submit</label></td>
		<input name="media_wall_plugin_options[enable_post]" type="hidden" value="0" />
		<td><input name="media_wall_plugin_options[enable_post]" type="checkbox" value="1" <?php checked( '1', $media_wall_enable_post ); ?> /></td>
		</tr>					
	</table>
	<br/>

	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
	
	</form>
	</div>
<?php 
}
