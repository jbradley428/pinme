<!--Head-->
<head>
<?php /* Template Name: pinMe */ 
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
					<h1>My Story</h1>
					<?php the_title(); ?><!--Pulls the title in from WP-->
				</div>
			<!--Title Area-->
		
			<!--Column One-->
			<div id="meCol-1">
				<?php the_content(); ?><!--Pulls the title in from WP-->
			</div>
			<!--Column One-->
			
			<!--Column Two-->
			<div id="meCol-2">
				<?=get_field('second_column');?><!--Pulls the content from the custom field-->
			</div>
			<!--Column Two-->

		</div>
		<!--Left Content-->

		<!--Right Content-->
		<div class="contentRight">
		<?php
				$diy = new WP_Query(array(
					'post_type' => 'diy',	
					'post_status' => 'publish',
					'orderby' => 'menu_order',	
					'order' => 'ASC',
					'posts_per_page' => 16,
				));

				$count = 0;
				while ($diy -> have_posts()) { 
					$diy->the_post();
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
		<!--Right Content-->

		
	</div>
<!--Footer-->
<div class="footer">
	<ul>
		<li>Designed by Jamie Bradley</li>
		<li>Copyright 2014</li>
	</ul>
	
	<?php get_footer(); ?>

	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
</div>
<!--Footer-->
</div>
<!--Wrapper-->
	