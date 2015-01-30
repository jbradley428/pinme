<!--Head-->
<head>
<?php /* Template Name: pinBlog */ 
	
	$posts = new WP_Query(array(
		'post_type' => 'post',			
		'post_status' 	=> 'publish'
	));
?>

<?php get_header(); ?>


<link rel="stylesheet" type="text/css" href="PinBlog.css">
<link href='http://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>

</head>
<!--Head-->

<!-- Home Content -->	

<!--Wrapper-->
<div class="wrapper">
	
<!--Main Content Area-->
<div class="mainContent-area">

	<!--Left Content-->
	<div class="contentLeft">
		
		<?php the_title(); ?><!--Pulls the title in from WP-->

		<!--<?php the_content(); ?> Pulls the title in from WP-->
	
		<?php if( $posts->have_posts() ): ?>

        	<?php while( $posts->have_posts() ): $posts->the_post(); ?>

			<h2 ><?php the_title(); ?></h2>
			<p><?php the_content();?></p>

        	<?php endwhile; wp_reset_query(); endif; ?>
	</div>
	<!--Left Content-->

	<!--Right Content-->
		<div class="contentRight"></div>
	<!--Right Content-->

</div>
<!--Main Content Area-->

<!--Footer-->
<?php get_footer(); ?>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
<!--Footer-->




		
		

		
	