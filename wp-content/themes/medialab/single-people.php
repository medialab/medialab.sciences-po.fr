<?php
/**
 * The template to display one person and his related projects
 *
 */
get_header(); ?>

		<div class="container_12" id="main">
		
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	
			<div class="grid_9" id="inside">
				
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_post_thumbnail('medium'); ?>
				<p><?php the_content(); ?></p>
					
			</div>
			<?php endwhile; ?>
		</div>
		
	</div>
<?php get_footer(); ?>
