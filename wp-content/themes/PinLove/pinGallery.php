<!--Head-->
<head>
<?php /* Template Name: pinGallery */ 

get_header();
the_post();
?>

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
		
		<!--Title Area-->
		<div id="home-titleArea">
			<h1>My Pins</h1>
		</div>
		<!--Title Area-->
			
		<!--My Favorite Pins-->
		<!--<?php the_post_thumbnail(‘full’); ?> -->
			<div class="pressGallery">
				<?php
				$pin = new WP_Query(array(
					'post_type' => 'pin',	
					'post_status' => 'publish',
					'orderby' => 'menu_order',	
					'order' => 'ASC',
					'posts_per_page' => 16,
				));

				$count = 0;
				while ($pin -> have_posts()) { 
					$pin->the_post();
					$content = get_the_content();
			
			
					if ($content != ''){
						echo '<a href="';
						the_permalink();
						echo '">';
					}
			
					the_post_thumbnail(‘full’); 
			
					if ($content != ''){
						echo "</a>";
					}
					$count++;
					if ($count == 6){
						echo '<br>';
						$count = 0;
					}
				} ?>
				<?php wp_reset_query();?>
			</div>
	</div>
	<!--Left Content-->

	
	<!--Right Content-->
	<div class="contentRight">
			
		<!--This is where the about me section should go, until I have galleries of different categories.-->
	</div>
	<!--Right Content-->
	
</div>
<!--Main Content Area-->

<!--Footer--Commented out
<div class="footer">
	<ul>
	<li>Designed by Jamie Bradley</li>
	<li>Copyright 2014</li>
	</ul>
</div>-->

<?php get_footer(); ?>
<!--Script that makes html5 execute?-->
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>

</div>
<!--Wrapper-->
	

<?php get_footer(); ?>
<!--Script that makes html5 execute?-->
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>