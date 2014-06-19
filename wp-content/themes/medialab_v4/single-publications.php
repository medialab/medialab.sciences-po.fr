<?php

/**
 * The template to display one project
 *
 */

get_header(); ?>

<div class="container">

	<div class="single-post">

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

            <?php
            	$remote_url = array_pop(get_post_meta($post->ID, 'remote_url'));
            ?>

			<h2><?php the_title(); ?></h2>
			<?php the_post_thumbnail("s_post"); ?>
			<p><em><?php echo get_the_excerpt(); ?></em></p>
			<p><?php 

			echo ($remote_url?'<a href="'.$remote_url.'">'.$remote_url.'</a>':'') ?>

			</p>

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
