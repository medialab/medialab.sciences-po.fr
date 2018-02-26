<?php
/**
 * Template Name: Tools Page
 *
 */

get_header(); ?>	
<div class="center">
	<h4>All our projects</h4>
	<div id="pin_like">
		
		<?php $loop = new WP_Query( array( 'post_type' => 'tools', 'posts_per_page' => 9, 'order' => 'DESC' ) );
		while ( $loop->have_posts() ) : $loop->the_post();?>
	
		<div class="column_display">
		
			<?php the_post_thumbnail('thumbnail'); ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php the_excerpt(); ?>
			
		</div>
		
		<?php endwhile; ?>
	</div>
	<div class="sidebar">
		<h4>Sort tools by projects</h4>
	</div>
</div>

<?php get_footer(); ?>