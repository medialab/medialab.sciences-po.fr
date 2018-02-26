<?php
/**
 * The template to display one project
 *
 */
get_header(); ?>
<div class="center">
	<div class="single-post">
	
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			
			<h2><?php the_title(); ?></h2>
			<?php the_post_thumbnail(); ?>
			<?php the_content(); ?>
			
		<?php endwhile; ?>
	</div>
</div>
<?php get_footer(); ?>
