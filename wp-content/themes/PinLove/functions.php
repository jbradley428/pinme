<?php /* Functions File */
ini_set('display_errors', '1');
ini_set('error_reporting', E_ERROR);
add_theme_support( 'post-thumbnails' ); 

/*Menu Functionality*/
// THIS THEME USES wp_nav_menu() IN TWO LOCATIONS FOR CUSTOM MENU.
if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus(
		array()
	);
}
/* allow upload flash */
add_filter('upload_mimes', 'pixert_upload_swf');
function pixert_upload_swf($existing_mimes){
$existing_mimes['swf'] = 'text/swf'; //allow swf files
return $existing_mimes;
}
/* allow upload flash */

add_filter('upload_mimes', 'custom_upload_xml');
 
function custom_upload_xml($mimes) {
    $mimes = array_merge($mimes, array('xml' => 'application/xml'));
    return $mimes;
}

/*=======DIY Custom Post Type=========*/
add_action('init', 'cptui_register_my_cpt_diy');
function cptui_register_my_cpt_diy() {
register_post_type('diy', array(
'label' => 'diy',
'description' => 'Do It Yourself Projects',
'public' => true,
'show_ui' => true,
'show_in_menu' => true,
'capability_type' => 'post',
'map_meta_cap' => true,
'hierarchical' => false,
'rewrite' => array('slug' => 'diy', 'with_front' => true),
'query_var' => true,
'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes','post-formats'),
'labels' => array (
  'name' => 'diy',
  'singular_name' => 'diys',
  'menu_name' => 'diy',
  'add_new' => 'Add diys',
  'add_new_item' => 'Add New diys',
  'edit' => 'Edit',
  'edit_item' => 'Edit diys',
  'new_item' => 'New diys',
  'view' => 'View diys',
  'view_item' => 'View diys',
  'search_items' => 'Search diy',
  'not_found' => 'No diy Found',
  'not_found_in_trash' => 'No diy Found in Trash',
  'parent' => 'Parent diys',
)
) ); }
/*=======DIY Custom Post Type=========*/

/*=======Pin Custom Post Type=========*/
add_action('init', 'cptui_register_my_cpt_pin');
function cptui_register_my_cpt_pin() {
register_post_type('pin', array(
'label' => 'Pins',
'description' => 'Use this post type to display your favorite pins in the pin gallery.',
'public' => true,
'show_ui' => true,
'show_in_menu' => true,
'capability_type' => 'post',
'map_meta_cap' => true,
'hierarchical' => false,
'rewrite' => array('slug' => 'pin', 'with_front' => true),
'query_var' => true,
'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes','post-formats'),
'labels' => array (
  'name' => 'Pins',
  'singular_name' => 'Pin',
  'menu_name' => 'Pins',
  'add_new' => 'Add Pin',
  'add_new_item' => 'Add New Pin',
  'edit' => 'Edit',
  'edit_item' => 'Edit Pin',
  'new_item' => 'New Pin',
  'view' => 'View Pin',
  'view_item' => 'View Pin',
  'search_items' => 'Search Pins',
  'not_found' => 'No Pins Found',
  'not_found_in_trash' => 'No Pins Found in Trash',
  'parent' => 'Parent Pin',
)
) ); }
/*==============Pin Custom Post Type=======================*/