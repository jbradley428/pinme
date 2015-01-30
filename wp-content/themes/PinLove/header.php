<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head>
<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
<meta http-equiv="content-type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, user-scalable=no">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />
<!--Add JS script here, though I feel like I've done this incorrectly...-->
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-1.6.2.min.js"></script>


<!--This information was taken from the Index template (home page)-->
<link rel="stylesheet" type="text/css" href="PinBlog.css">
<link href='http://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>
</head>
<!--Head-->
<!-- Home Content -->	
<!--Wrapper-->
<div class="wrapper">
	<!--Banner-->
	<div class="banner">
		<h2>Better Living through Pinterest</h2>
		<img src="file:///Users/jamiebradley/Desktop/honeyComb.jpg"/>
	</div>
	<!--Banner-->
<!--This information was taken from the Index template (home)-->


<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


<!-- Google Analytics -->

</head>

<body <?php body_class(); ?>>

	<div class="mainMenu">
		<?php wp_nav_menu(array('menu' => 'Primary Header Nav'));?>
	</div>
	
	<div class="mainContent-area">