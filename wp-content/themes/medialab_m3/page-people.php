<?php
/**
 * Template Name: People Page
 *
 */

get_header(); ?>	
<div class="center">
	<h4>The medialab team</h4>
	<div id="pin_like">
		<?php $loop = new WP_Query( array( 'post_type' => 'people', 'posts_per_page' => 10, 'order' => 'ASC' ) );
		while ( $loop->have_posts() ) : $loop->the_post();?>
		
		
		<div class="column_display <?php get_object_terms("projets"); ?>">
	
			<?php the_post_thumbnail('thumbnail'); ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php the_excerpt(); ?>
			
		</div>
		
		<?php endwhile; ?>
	</div>
	<div class="sidebar">
		<h4>Sort people by projects</h4>
	</div>
</div>
<?php get_footer(); ?>