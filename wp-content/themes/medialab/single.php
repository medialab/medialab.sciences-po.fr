<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>

<div class="grid_9 column_inside">
		
	<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
			
		<div class="column_display">
		
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<img src="<?php echo catch_that_image() ?>" alt="<?php the_permalink(); ?>" />
			<?php the_excerpt(); ?>
		
		</div>
      
	<?php endwhile; endif; ?>

	</div>
</div>

<?php get_footer(); ?>