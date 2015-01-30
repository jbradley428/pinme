/**
 * Plugin Name: Page Management
 * Plugin URI: http://www.wpshowcase.net/plugins/page-management-plugin/ 
 * Description: Page Management is a great tool which allows you to do loads of useful things including to hide/expand subpages when viewing the page hierarchy, to display all pages/posts on the same page, to reorder pages using drag and drop, to show the current page's media files by default and to add a shortcode dropdown of all the site's shortcodes.
 * Version: 2.4
 * Author: WPShowCase
 * Author URI: http://www.wpshowcase.net
 * Text Domain: pagemanagementlang
 * Domain Path: /lang
 *
 * @package PageManagement
 * @version 2.4
 * @author WPShowCase <admin@wpshowcase.net>
 * @copyright Copyright (c) 2014, WPShowCase.net
 * @link http://www.wpshowcase.net/plugins/page-management-plugin/
 */
 
 jQuery(document).ready(function ($) {
	$('#wpcontent').ajaxStop(function() {
			$('.media-modal .media-frame .attachment-filters [value="uploaded"]').attr( 'selected', true ).parent().trigger('change');
	});
});