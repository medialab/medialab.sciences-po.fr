<?php
/**
 * Template Name: Single Page
 *
 */

get_header(); ?>	
<div class="container">
	<h2 class="row-main-title">
			<span class="title-text">
				<?php the_title(); ?>
			</span>
	</h2>
	<div class="page-container">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div>
			<div class="page-content">
				<p><?php the_content(); ?></p>
			</div>
		<?php endwhile; else: ?>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>