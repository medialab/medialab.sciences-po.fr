<?php
/**
 * Template Name: People Page
 *
 */

get_header(); ?>		
	<div class="grid_9 column_inside">
		<?php $loop = new WP_Query( array( 'post_type' => 'people', 'posts_per_page' => 10 ) );
		while ( $loop->have_posts() ) : $loop->the_post();?>

		<div class="column_display">
		
			<?php the_post_thumbnail('thumbnail'); ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php the_excerpt(); ?>
			
		</div>
		
		<?php endwhile; ?>
	</div>
</div>
<?php get_footer(); ?>