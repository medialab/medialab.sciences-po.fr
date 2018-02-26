<?php
/**
 * The template to display one project
 *
 */
get_header(); ?>

	<div class="container_12" id="main">
	
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div class="grid_9" id="inside_tool">

			<h1><?php the_title(); ?></h1>
			<?php the_post_thumbnail(); ?>
			<?php the_content(); ?>
			
		</div>
		<?php endwhile; ?>
	</div>
</div>
<?php get_footer(); ?>
