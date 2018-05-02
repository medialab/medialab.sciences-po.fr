<?php

/**
 * The template to display one project
 *
 */

get_header(); ?>

<div class="container">

	<?php $locale = get_locale() ?>
	<?php $in_english = $locale == 'en_US' ?>
	<h2 class="row-main-title">
			<span class="title-text">
				<?php the_title(); ?>
			</span>
			<a href="/publication">
				<?php echo $in_english ? 'Go back to the publications...' : 'Retour aux publications' ?>
			</a>
	</h2>

	<div class="single-post">

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

            <?php
            	$remote_url = array_pop(get_post_meta($post->ID, 'remote_url'));
            ?>

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
			<h4>
				<?php echo $in_english ? 'Authors' : 'Auteurs' ?>
				<?php if (count(get_the_terms( $post->ID, 'people')) > 1) echo "s"; ?></h4>
				<div class="related-people-content"><?php the_terms( $post->ID, 'people','',' ',''); ?></div>
		</div>
		<div class="related-tools">
			<h4>
				<?php echo $in_english ? 'Related tools' : 'Outils liés' ?>
			</h4>
			<div class="related-tools-content"><?php echo_the_tools($pid); ?></div>
		</div>
	</div>

</div>

<?php get_footer(); ?>
