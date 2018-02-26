<?php
/**
 * Template Name: Tools Page
 *
 */

get_header(); ?>

	<div class="grid_9 column_inside">
		<?php $loop = new WP_Query( array( 'post_type' => 'tools', 'posts_per_page' => 10 ) );
		while ( $loop->have_posts() ) : $loop->the_post();?>

		<div class="column_display">
		
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<img src="<?php echo catch_that_image() ?>" alt="<?php the_permalink(); ?>" />
			<?php the_excerpt(); ?>
			
		</div>
		
		<?php endwhile; ?>
	</div>
</div>

<?php get_footer(); ?>
