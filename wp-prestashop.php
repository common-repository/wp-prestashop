<?php
/*
Plugin Name: WP PrestaShop
Plugin URI: http://blog.joelgaujard.info/realisations/wp-prestashop
Description: Include a <a href="http://www.prestashop.com">PrestaShop</a> ecommerce website to your blog. Include the header and footer of your ecommerce website on your blog. You need to install the <a href="http://prestashop.joelgaujard.info/product.php?id_product=17">WordPress module for PrestaShop</a> on your ecommerce website to use this plugin.
Version: 1.2.1
Author: Joel Gaujard
Author URI: http://www.joelgaujard.info/
*/

/*  Copyright 2009  Joel Gaujard  (email : contact@joelgaujard.info)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( defined('PRESTASHOP_URL') )
	$prestashop_url = constant('PRESTASHOP_URL');
else
	$prestashop_url = '';

$module_url = 'http://prestashop.joelgaujard.info/product.php?id_product=17';

function prestashop_init() {
	global $prestashop_url;

	if ( $prestashop_url )
		$prestashop_url .= 'modules/wordpress/blog.php';
	else
		$prestashop_url = get_option('prestashop_url').'modules/wordpress/blog.php';

	add_action('admin_menu', 'prestashop_config_page');
	prestashop_admin_warnings();
}
add_action('init', 'prestashop_init');

function prestashop_config_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('options-general.php', __('PrestaShop Configuration'), __('PrestaShop Configuration'), 'manage_options', 'prestashop-url-config', 'prestashop_conf');
}

function prestashop_conf() {
	global $prestashop_url, $module_url;

	if ( isset($_POST['register']) ) {
		check_admin_referer();
		$url = $_POST['url'];
		$theme = $_POST['theme'];

		if (empty($url)) {
			delete_option('prestashop_url');
		} else {
			update_option('prestashop_url', $url);
		}
		if (empty($theme)) {
			delete_option('prestashop_theme');
		} else {
			update_option('prestashop_theme', $theme);
		}
	}
?>
<?php if ( !empty($_POST['register'] ) ) : ?>
<div id="message" class="updated fade">
	<p><strong><?php _e('Options saved.') ?></strong></p>
</div>
<?php endif; ?>
<div class="wrap">
	<h2><?php _e('PrestaShop Configuration'); ?></h2>
	<div class="narrow">
		<form action="" method="post" id="prestashop-conf" style="margin: auto; width: 80%; ">
			<p><?php printf(__('To use this feature, you need to have installed <b>WordPress module for <a href="%1$s">PrestaShop</a></b> on your ecommerce website.'), 'http://www.prestashop.com/'); ?></p>
			<p>
				<?php printf(__('You can buy it here : <a href="%1$s">%1$s</a>.'), $module_url); ?>
			</p>
			<h3><label for="url"><?php _e('PrestaShop URL'); ?></label></h3>
			<p>
				<input id="url" name="url" type="text" size="50" value="<?php echo get_option('prestashop_url'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.2em;" />
			</p>
			<h3><label for="theme"><?php _e('PrestaShop theme name'); ?></label></h3>
			<p>
				<input id="theme" name="theme" type="text" size="30" value="<?php echo get_option('prestashop_theme'); ?>" />
				<br/><i>optional : if you fill this field stylesheet, javascript, etc. will be included in your blog.</i>
			</p>
			<p class="submit"><input type="submit" name="register" value="<?php _e('Save'); ?>" /></p>
		</form>
	</div>
</div>
<?php
	echo prestashop_installation();
}

function prestashop_installation() {
?>
	<br/>
	<h2><?php _e('Installation of this plugin in your theme'); ?></h2>
	<h4><?php _e('Add 2 spaces for widgets :'); ?></h4>
	<p>
		Add this to your functions.php file
<pre>if ( function_exists('register_sidebar') )
  register_sidebar(array(
    'name' => 'PrestaShop Header iframe',
    'id' => 'header_iframe'
  ));

if ( function_exists('register_sidebar') )
  register_sidebar(array(
    'name' => 'PrestaShop Footer iframe',
    'id' => 'footer_iframe'
  ));</pre>
	</p>
	<h4><?php _e('Call this 2 spaces in your theme :'); ?></h4>
	<p>
		You can display the header iframe like this:
<pre><?php echo htmlentities("<?php if ( get_header('prestashop') ) get_header('prestashop'); ?>") ?></pre>
		<i>NB: Mostly in your header.php file</i>
	</p>
	<p>
		You can display the footer iframe like this:
<pre><?php echo htmlentities("<?php if ( get_footer('prestashop') ) get_footer('prestashop'); ?>") ?></pre>
		<i>NB: Mostly in your footer.php file</i>
	</p>
	<h4><?php _e('2 files execute this 2 spaces :'); ?></h4>
	<p>
		Copy the two files header-prestashop.php and footer-prestashop.php of plugin folder in your theme directory.
	</p>
<?php
}

function prestashop_get_url() {
	global $prestashop_url;
	if ( !empty($prestashop_url) )
		return $prestashop_url;
	return get_option('prestashop_url');
}

function prestashop_admin_warnings() {
	global $prestashop_url;
	if ( !get_option('prestashop_url') && !$prestashop_url && !isset($_POST['register']) ) {
		function prestashop_warning() {
			echo "
			<div id='prestashop-warning' class='updated fade'><p><strong>".__('PrestaShop is almost ready.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your URL</a> for it to work.'), "options-general.php?page=prestashop-url-config")."</p></div>
			";
		}
		add_action('admin_notices', 'prestashop_warning');
		return;
	}
}


function widget_ps_header_register() {

	if ( function_exists('register_sidebar_widget') ) :

	function widget_ps_header($args) {
		extract($args);
		$url = prestashop_get_url() . '?position=header';
		$width = get_option('headeriframe_width');
		$height = get_option('headeriframe_height');

		$output = '
		<iframe width="'.$width.'" height="'.$height.'" id="iframe_header" name="iframe_header" src="'.$url.'" scrolling="no" frameborder="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" noresize>
			[Your user agent does not support frames or is currently configured not to display frames.]
		</iframe>';
		//$output .= $before_widget.$before_title.$url.$after_title.$after_widget;
		echo $output;
	}

	function ps_html_header() {
		$url = get_option('prestashop_url');
		$theme = get_option('prestashop_theme');
		$output = '
			<link rel="icon" type="image/vnd.microsoft.icon" href="'.$url.'img/favicon.ico" />
			<link rel="shortcut icon" type="image/x-icon" href="'.$url.'img/favicon.ico" />
		';
		if ($theme)
			$output .= '<link rel="stylesheet" href="'.$url.'/themes/'.$theme.'/css/global.css" type="text/css" media="all" />';
		echo $output;
	}

	function widget_ps_header_control() {
		if (isset($_POST['ps_header_submit'])) { 
			update_option('headeriframe_width', strip_tags(stripslashes($_POST['headeriframe_width'])));
			update_option('headeriframe_height', strip_tags(stripslashes($_POST['headeriframe_height'])));
		}

		$width = @get_option('headeriframe_width');
		$height = @get_option('headeriframe_height');
		$url = @get_option('prestashop_url');

		$form = '
			<p>'.__('URL:').' '.$url.'</p>
			<label for="headeriframe_width">'.__('Width:').'
				<input id="headeriframe_width" name="headeriframe_width" type="text" value="'.$width.'" />
			</label>
			<label for="headeriframe_height">'.__('Height:').'
				<input id="headeriframe_height" name="headeriframe_height" type="text" value="'.$height.'" />
			</label>
			<input type="hidden" id="ps_header_submit" name="ps_header_submit" value="1" />
		';
		echo $form;
	}

	register_sidebar_widget('PrestaShop Header', 'widget_ps_header');
	register_widget_control('PrestaShop Header', 'widget_ps_header_control');
	if ( is_active_widget('widget_ps_header') )
		add_action('wp_head', 'ps_html_header');

	endif;

}
add_action('init', 'widget_ps_header_register');


function widget_ps_footer_register() {

	if ( function_exists('register_sidebar_widget') ) :

	function widget_ps_footer($args) {
		extract($args);
		$url = prestashop_get_url() . '?position=footer';
		$width = get_option('footeriframe_width');
		$height = get_option('footeriframe_height');

		$output = '
		<iframe width="'.$width.'" height="'.$height.'" id="iframe_footer" name="iframe_footer" src="'.$url.'" scrolling="no" frameborder="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" noresize>
			[Your user agent does not support frames or is currently configured not to display frames.]
		</iframe>';
		//$output .= $before_widget.$before_title.$url.$after_title.$after_widget;
		echo $output;
	}

	function widget_ps_footer_control() {
		if (isset($_POST['ps_footer_submit'])) { 
			update_option('footeriframe_width', strip_tags(stripslashes($_POST['footeriframe_width'])));
			update_option('footeriframe_height', strip_tags(stripslashes($_POST['footeriframe_height'])));
		}

		$width = @get_option('footeriframe_width');
		$height = @get_option('footeriframe_height');
		$url = @get_option('prestashop_url');

		$form = '
			<p>'.__('URL:').' '.$url.'</p>
			<label for="footeriframe_width">'.__('Width:').'
				<input id="footeriframe_width" name="footeriframe_width" type="text" value="'.$width.'" />
			</label>
			<label for="footeriframe_height">'.__('Height:').'
				<input id="footeriframe_height" name="footeriframe_height" type="text" value="'.$height.'" />
			</label>
			<input type="hidden" id="ps_footer_submit" name="ps_footer_submit" value="1" />
		';
		echo $form;
	}

	register_sidebar_widget('PrestaShop Footer', 'widget_ps_footer');
	register_widget_control('PrestaShop Footer', 'widget_ps_footer_control');

	endif;

}
add_action('init', 'widget_ps_footer_register');

function widget_ps_left_register() {
	if ( function_exists('register_sidebar_widget') ) :
		function widget_ps_left($args) {
			extract($args);
			$url = prestashop_get_url() . '?position=left_column';
			$width = get_option('ps_leftcolumn_width');
			$height = get_option('ps_leftcolumn_height');
			$output = '
			<iframe width="'.$width.'" height="'.$height.'" id="iframe_left" name="iframe_left" src="'.$url.'" scrolling="no" frameborder="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" noresize>
				[Your user agent does not support frames or is currently configured not to display frames.]
			</iframe>';
			//$output .= $before_widget.$before_title.$url.$after_title.$after_widget;
			echo $output;
		}
		function widget_ps_left_control() {
			if (isset($_POST['ps_left_submit'])) { 
				update_option('ps_leftcolumn_width', strip_tags(stripslashes($_POST['ps_leftcolumn_width'])));
				update_option('ps_leftcolumn_height', strip_tags(stripslashes($_POST['ps_leftcolumn_height'])));
			}
			$width = @get_option('ps_leftcolumn_width');
			$height = @get_option('ps_leftcolumn_height');
			$url = @get_option('prestashop_url');
			$form = '
				<p>'.__('URL:').' '.$url.'</p>
				<label for="ps_leftcolumn_width">'.__('Width:').'
					<input id="ps_leftcolumn_width" name="ps_leftcolumn_width" type="text" value="'.$width.'" />
				</label>
				<label for="ps_leftcolumn_height">'.__('Height:').'
					<input id="ps_leftcolumn_height" name="ps_leftcolumn_height" type="text" value="'.$height.'" />
				</label>
				<input type="hidden" id="ps_left_submit" name="ps_left_submit" value="1" />
			';
			echo $form;
		}
		register_sidebar_widget('PrestaShop Left column', 'widget_ps_left');
		register_widget_control('PrestaShop Left column', 'widget_ps_left_control');
	endif;
}
add_action('init', 'widget_ps_left_register');

function widget_ps_right_register() {
	if ( function_exists('register_sidebar_widget') ) :
		function widget_ps_right($args) {
			extract($args);
			$url = prestashop_get_url() . '?position=right_column';
			$width = get_option('ps_rightcolumn_width');
			$height = get_option('ps_rightcolumn_height');
			$output = '
			<iframe width="'.$width.'" height="'.$height.'" id="iframe_right" name="iframe_right" src="'.$url.'" scrolling="no" frameborder="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" noresize>
				[Your user agent does not support frames or is currently configured not to display frames.]
			</iframe>';
			//$output .= $before_widget.$before_title.$url.$after_title.$after_widget;
			echo $output;
		}
		function widget_ps_right_control() {
			if (isset($_POST['ps_right_submit'])) { 
				update_option('ps_rightcolumn_width', strip_tags(stripslashes($_POST['ps_rightcolumn_width'])));
				update_option('ps_rightcolumn_height', strip_tags(stripslashes($_POST['ps_rightcolumn_height'])));
			}
			$width = @get_option('ps_rightcolumn_width');
			$height = @get_option('ps_rightcolumn_height');
			$url = @get_option('prestashop_url');
			$form = '
				<p>'.__('URL:').' '.$url.'</p>
				<label for="ps_rightcolumn_width">'.__('Width:').'
					<input id="ps_rightcolumn_width" name="ps_rightcolumn_width" type="text" value="'.$width.'" />
				</label>
				<label for="ps_rightcolumn_height">'.__('Height:').'
					<input id="ps_rightcolumn_height" name="ps_rightcolumn_height" type="text" value="'.$height.'" />
				</label>
				<input type="hidden" id="ps_right_submit" name="ps_right_submit" value="1" />
			';
			echo $form;
		}
		register_sidebar_widget('PrestaShop Right column', 'widget_ps_right');
		register_widget_control('PrestaShop Right column', 'widget_ps_right_control');
	endif;
}
add_action('init', 'widget_ps_right_register');
