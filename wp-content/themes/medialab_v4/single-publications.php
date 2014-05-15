<?php

/**
 * The template to display one project
 *
 */

get_header(); ?>

<div class="container">

	<div class="single-post">

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

			<h2><?php the_title(); ?></h2>
			<p><em><?php echo get_the_excerpt(); ?></em></p>
			<?php the_content(); ?>			

		<?php endwhile; ?>

	</div>

	<div class="sidebar">
		<div class="related-people">
			<h4>Author<?php if (count(get_the_terms( $post->ID, 'people')) > 1) echo "s"; ?></h4>
				<div class="related-people-content"><?php the_terms( $post->ID, 'people','',' ',''); ?></div>
		</div>
		<div class="related-tools">
			<h4>Related tools</h4>
			<div class="related-tools-content"></div>
		</div>
	</div>

</div>

<?php get_footer(); ?>
