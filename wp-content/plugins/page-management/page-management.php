<?php



/**
 * Plugin Name: Page Management
 * Plugin URI: http://codecanyon.net/user/WPShowCase/portfolio?ref=WPShowCase
 * Description: Page Management is a great tool which allows you to do loads of useful things including to hide/expand subpages when viewing the page hierarchy, to display all pages/posts on the same page, to reorder pages using drag and drop, to show the current page's media files by default and to add a shortcode dropdown of all the site's shortcodes.
 * Version: 2.8
 * Author: Premium Plugins
 * Author URI: http://codecanyon.net/user/WPShowCase/portfolio?ref=WPShowCase
 * Text Domain: pagemanagementlang
 * Domain Path: /lang
 *
 */
if ( !defined('ABSPATH')) exit;

/**Activation*/
register_activation_hook(__FILE__, 'page_management_activate');
add_action('admin_init', 'page_management_redirect');

function page_management_activate() {
	add_option('page_management_redirect', true);
}

function page_management_redirect() {
	if (get_option('page_management_redirect', false)) {
		delete_option('page_management_redirect');
		if(!isset($_GET['activate-multi']))
			wp_redirect("admin.php?page=page_management_settings");
	}
}


class PageManagement {
	var $all_wp_pages = array();

	function __construct() {
		add_action('admin_init', array($this, 'page_management_scripts'));
		add_action('admin_menu', array($this, 'page_management_plugin_settings'));
		add_filter('post_class', array($this, 'page_management_post_classes'));
		add_filter('edit_posts_per_page', array($this, 'page_management_posts_per_page'));
		add_action('wp_ajax_update_post_position', array($this, 'ajax_update_post_position'));
		add_action('wp_ajax_reload_editphp_table', array($this, 'reloadEditPhpTable'));
		$plugin = plugin_basename(__FILE__); 		
        add_filter("plugin_action_links_$plugin", array($this,'page_management_link') );  
		load_plugin_textdomain( 'pagemanagementlang', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		if(get_option('page_management_media_this_page') == 'enabled')
			add_action('wp_enqueue_media', array($this, 'page_management_media_script'));
	}

	function page_management_media_script() {
		wp_enqueue_script('pagemanagementmediascript', plugins_url( '/page-management-media.js' , __FILE__ ), array('jquery'));
	}
	
	public function page_management_link($links) { 
		$settings = '<a href="admin.php?page=page_management_settings">Settings</a>'; 
		array_unshift($links, $settings); 
		return $links; 
    }
	
	function ajax_update_post_position() {
		define('WP_USE_THEMES', false); 
		require_once( ABSPATH  . 'wp-load.php' );
		global $wpdb;

		$newParent = intval($_POST['newParent']);
		$thisPost = intval($_POST['thisPost']);
		$postBefore = intval($_POST['postBefore']);
		
		if(!current_user_can('edit_page', $thisPost))
			wp_die(__( 'You are not allowed to edit this page.'));
			
		$postToUpdate = get_post($thisPost);
		$oldParent = $postToUpdate->post_parent;
		if($newParent>=0)
			$postToUpdate->post_parent = $newParent;
		if($postBefore>0) {
			$beforePost=get_post($postBefore);
			$newMenuOrder = $beforePost->menu_order + 1;
			$titleOfBeforeItem = $beforePost->post_title;
			$postToUpdate->menu_order = $newMenuOrder;
			$title = $postToUpdate->post_title;
			$wpdb->query( $wpdb->prepare(  
				"update {$wpdb->posts} set menu_order=menu_order+2 where
				post_parent=%d and post_type='page' and (menu_order>%d or (menu_order=%d and post_title>'%s'))",
				array($newParent, $newMenuOrder-1, $newMenuOrder-1, $titleOfBeforeItem)));
		}
		else {
			$postToUpdate->menu_order=0;
            $wpdb->query( 
                "update {$wpdb->posts} set menu_order=menu_order+1 where
                post_parent=0 and post_type='page'");
        }
		wp_update_post( $postToUpdate );

		$this->reloadEditPhpTable();
		die();
	}
	
	function reloadEditPhpTable() {
		require_once( ABSPATH  . 'wp-load.php' );
		global $wpdb;
		global $wp_query;

		global $wp_list_table;
		$args = wp_parse_args($_POST['query'], array('paged' => 1, 'orderby' => 'menu_order title', 'order' => 'ASC'));
		$args['posts_per_page'] = 10000;
		wp_reset_query();
		$wp_query = new WP_Query($args);
		
		global $post_type, $post_type_object;
		$post_type = 'page';
		$post_type_object = get_post_type_object( $post_type );

		if ( ! $post_type_object )
			wp_die( __( 'Invalid post type' ) );

		$wp_list_table = _get_list_table( 'WP_Posts_List_Table', array( 'screen' => 'edit-page' ));
		$wp_list_table->hierarchical_display = true;
		global $per_page;
		$per_page = $this->page_management_posts_per_page(20);
		$wp_list_table->page_num = $args['paged'];
		$wp_list_table->prepare_items();
		$wp_list_table->hierarchical_display = true;
		$wp_list_table->display_rows( $wp_query->get_posts(), 0 );
	}

	function get_new_classes($post, $newClasses) {
		$newClasses[$post->ID] = array();
		$newClasses[$post->ID] = $this->get_classes($post, $newClasses[$post->ID]);		
		$args = array(
			'numberposts' => -1, 'post_type' => 'page',
			'post_status' => 'any', 'post_parent' => $post->ID
		);
		$r = wp_parse_args( $args);
		$children = get_posts( $r );
		foreach($children as $child) {
			$newClasses = $this->get_new_classes($child, $newClasses);
		}
		return $newClasses;
	}
	
	/*Add CSS and Javascript*/
	public function page_management_scripts() {
		if(!isset($_GET['orderby'])) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-mouse');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-widget');
			wp_enqueue_script('jquery-ui-position');
			wp_enqueue_script('pagemanagementscript', plugins_url( '/page-management.js' , __FILE__ ), array('jquery', 'jquery-ui-tooltip', 'jquery-ui-sortable', 'jquery-ui-mouse', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position'));
			$expandpages = (get_option('page_management_expandpages') == 'enabled') ? 'true' : 'false';
			$localize_array = array( /*'some_string' => __( 'Some string to translate' ),*/ 'expandpages' => $expandpages );
			wp_localize_script( 'pagemanagementscript', 'page_management_settings', $localize_array );
			wp_register_style('pagemanagementcss', WP_PLUGIN_URL . '/page-management/page-management.css' );
			wp_enqueue_style('pagemanagementcss');
		}
	}

	/**
	* Adds relationships in css between the posts.
	*/
	public function page_management_post_classes($classes) {
		global $post;
		return $this->get_classes($post, $classes);
	}
	
	public function get_classes($post, $classes) {
		$ancestor = $post->post_parent;
		$classes[] = 'parent-is-'.$post->post_parent;
		$level = 0;
		while($ancestor>0) {
			$classes[] = 'descendent-'.$ancestor;
			$ancestor = get_post($ancestor)->post_parent;
			$level++;
		}
		if(count(get_page_children( $post->ID, $this->all_wp_pages ))>0)
			$classes[]='parent';
		$classes[]='level-'.$level;
		$classes[]='menu-order-'.$post->menu_order;
		return $classes;
	}
	
	function page_management_posts_per_page($per_page) {
		$wp_query_all_pages = new WP_Query();
		$this->all_wp_pages = $wp_query_all_pages->query(array('post_type' => 'page', 'posts_per_page' => 10000));
		if(get_option('page_management_showallpages')=='enabled')
			return 10000;
		else
			return $per_page;
	}

	function page_management_plugin_settings() {
		add_menu_page('Page Management', __('Page Settings', 'pagemanagementlang'), 'administrator', 'page_management_settings', array($this, 'page_management_settings'));
	}

	function page_management_settings() {
		$expandpages = (get_option('page_management_expandpages') == 'enabled') ? 'checked="checked"' : '';
		$showallpages  = (get_option('page_management_showallpages') == 'enabled') ? 'checked="checked"' : '' ;
		$mediathispage = (get_option('page_management_media_this_page') == 'enabled') ? 'checked="checked"' : '' ;
		$shortcodedropdown = (get_option('page_management_shortcode_dropdown') == 'enabled') ? 'checked="checked"' : '' ;
		print '</pre>
	<div class="wrap"><table width="100%" style="vertical-align:top;"><tr><td style="vertical-align:top;padding:20px">
	<form action="options.php" method="post" name="options">
	<h1>';
	_e('Choose Page Management Settings', 'pagemanagementlang');
	print '</h1>
	<p>';
	_e('Page Management allows you to show/hide the subpages on your site and allows you to drag and drop when sorting the pages. Here are options to improve even more the functionality of your site.', 'pagemanagementlang');
	print '</p>
	<table class="form-table" width="100%" cellpadding="10">
	<tbody>
	<tr><td colspan="2"><h2>';
	_e("Page and Post Settings", 'pagemanagementlang');
	print '</h2></td></tr>
	<tr>
	<td scope="row" align="left"><label>';
	_e("Expand pages by default (so that you can see the subpages). Tick here to show the subpages when viewing your site's pages. ", 'pagemanagementlang');
	print '</label><input '.$expandpages.' type="checkbox" name="page_management_expandpages" value="enabled" /></td>
	</tr>
	<tr>
	<td scope="row" align="left"><label>';
	_e('Remove pagination when editing pages and posts. Tick here to see all the pages on one page. ', 'pagemanagementlang');
	print '</label><input '.$showallpages.' type="checkbox" name="page_management_showallpages" value="enabled" /></td>
	</tr>
	<tr><td colspan="2"><h2>';
	_e("Images and Media Settings", 'pagemanagementlang');
	print '</h2></td></tr>
	<tr>
	<td scope="row" align="left"><label>';
	_e('If you would like to show the images files for each post by default (instead of all the image files), then tick here ', 'pagemanagementlang');
	print '</label><input '.$mediathispage.' type="checkbox" name="page_management_media_this_page" value="enabled" /></td>
	</tr>
	<tr><td colspan="2"><h2>';
	_e("Text Editor Settings", 'pagemanagementlang');
	print '</h2></td></tr>
	<tr>
	<td scope="row" align="left"><label>';
	_e("If you would like add a dropdown with your site's shortcodes to the rich text editor, then tick here. ", 'pagemanagementlang');
	print '</label><input '.$shortcodedropdown.' type="checkbox" name="page_management_shortcode_dropdown" value="enabled" /></td>
	</tr>
	<tr><td colspan="2">';
	 submit_button(); print '</td></tr>
	</tbody>
	</table>
	 <input type="hidden" name="action" value="update" />
	 <input type="hidden" name="page_options" value="page_management_expandpages,page_management_showallpages,page_management_showallposts,page_management_shortcode_dropdown,page_management_media_this_page" />
	 ' . wp_nonce_field('update-options') . '</form></td><td>
	 
	 <div style="border:1px solid #222;padding:20px;width:400px;">
		<h2>';
		_e('Support!', 'pagemanagementlang');
	print '</h2>';
print '<br />';
		_e('If you like this plugin and would like to support in other ways, please', 'pagemanagementlang');
	print ':
		<ol>
			<li><a href="http://wordpress.org/plugins/page-management/" target="_blank" >';
			_e('Rate Page Management as a fantastic plugin on wordpress', 'pagemanagementlang');
	print '</a>.</li>
			<li>';
			_e('Recommend this plugin to more people.', 'pagemanagementlang');
	print '</li>
		</ol>
<p>	<b>	'; _e('The more people using this plugin, the faster it will get developed - so you are helping yourself by encouraging more people to use this plugin!', 'pagemanagementlang');
	print '		</b></p>
	 </div>
	 
	 </td></tr></table></div>
	<pre>
	';
	}
}

$pageManagement = new PageManagement();


class ShortCodeDropDown2 {
	
	/**
	 * Add actions to load javascript/css in the constructor.
	 */
	function ShortCodeDropDown() {
		if(get_option('page_management_shortcode_dropdown') == 'enabled') {
			add_action('admin_enqueue_scripts', array($this, 'shortcode_dropdown_load_css') );
			add_action('admin_init', array( $this, 'add_dropdown' ) );
			add_action('admin_footer', array($this, 'get_shortcodes'));
		}
	}
	
	//Adds a css file
	function shortcode_dropdown_load_css() {
			wp_register_style( 'shortcode-dropdown.css', WP_PLUGIN_URL . '/shortcode-dropdown/shortcode-dropdown.css' );
			wp_enqueue_style( 'shortcode-dropdown.css');
	}
	
	/**
	 * Adds the dropdown in php and the javascript plugin which makes the dropdown work.
	 * Only rich text editor supports dropdowns!
	 */
	function add_dropdown() {
		if((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing') == 'true' ) {
			add_filter( 'mce_buttons', array( $this, 'add_dropdowntobuttons' ) );
			add_filter( 'mce_external_plugins', array( $this, 'load_js_plugin' ) );
		}
	}
	
	/**
	 * adds a separator between the previous button and this button then adds this button.
	 */
	function add_dropdowntobuttons( $buttons ) {
		array_push( $buttons, 'separator', 'shortcodedropdown' );
		return $buttons;
	}
	
	/**
	 * Loads the plugin (which does the selecting).
	 */
	function load_js_plugin( $plugin_array ) {
		$plugin_array['shortcodedropdown'] = WP_PLUGIN_URL . '/shortcode-dropdown/shortcode-dropdown.js';
		return $plugin_array;
	}

	/**
	* Gets all the site's shortcodes into javascript for the plugin to read.
	*/
	 function get_shortcodes() {
		global $shortcode_tags;
		$shortcodes = '';
		print '<script type="text/javascript">
		var shortcode_options_for_dropdown = new Array();
		';
		$cnt = 0;
		foreach($shortcode_tags as $tag => $function) {
			print "shortcode_options_for_dropdown[{$cnt}] = '{$tag}';
		";
			$cnt++;
		}
		print '
	</script>';
	}		
}

//Create the ShortCodeDropDown object.
global $ShortCodeDropDown;
$ShortCodeDropDown = new ShortCodeDropDown2();

?>