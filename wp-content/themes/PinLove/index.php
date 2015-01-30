<!--Head-->
<head>
<?php /* Template Name: pinHome */ 
get_header();
the_post();
?>

<!--Main Content Area-->
	<div class="mainContent-area">
		<!--Left Content-->
		<div class="contentLeft">
			<!--Title Area-->
			<div id="home-titleArea">
				<h1>How Pinterest Changed My Life</h1>
			<?php the_title(); ?><!--Pulls the title in from WP-->
			</div>
			<!--Title Area-->

			<!--Column One-->
			<div id="homeCol-1">

			<?php the_content(); ?><!--Pulls the content area in from WP-->
				
			</div>
			<!--Column One-->

			<!--Column Two-->
			<div id="homeCol-2">

			<?=get_field('second_column');?><!--Pulls the content from the custom field-->
				
			</div>
			<!--Column Two-->
		</div>
		<!--Left Content-->

		<!--Right Content-->
		<div class="contentRight">
			<!--About Me Section-->
			<div id="ME">
				<?=get_field('me_image');?>
				<?=get_field('me_blurb');?>
			</div>
			<!--About Me Section-->
	
		</div>
		<!--Right Content-->
	</div>

</div>
<!--Wrapper-->
	

<?php get_footer(); ?>

<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>