<?php
/**
 * The Template for displaying all Blog posts.
 *
 * @package WordPress
 * @subpackage Medialab 2.0
 * @since Medialab 1.0
 */

get_header(); ?>
<div class="container">
	<?php $locale = get_locale() ?>
	<?php $in_english = $locale == 'en_US' ?>
	<h2 class="row-main-title">
			<span class="title-text">
				<?php the_title(); ?>
			</span>
			<a href="/blog">
				<?php echo $in_english ? 'Go back to the news...' : 'Retour aux actualités' ?>
			</a>
	</h2>
	<div class="single-post">
	<?php  the_post(); ?>
		
		<div style="width:100%;clear:both;align:center;">
			<?php $video = get_post_meta($post->ID, 'vimeo_id', true);
				 if (!$video) the_post_thumbnail("s_post");
				 	else echo '<iframe  src="http://player.vimeo.com/video/'.$video.'" width="690px" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'; ?>
			</div>
			<div style="width:100%;clear:both;">
			<?php the_content(); ?>			
			</div>
		
	</div>
	<div class="sidebar">
		<div class="related-people">
			<h4>
				<?php echo $in_english ? 'Related people' : 'Personnes liées' ?>
			</h4>
			<div class="related-people-content"><?php the_terms($post->ID, "people", "", " "); ?></div>
		</div>
		<div class="related-tools">
			<h4>
				<?php echo $in_english ? 'Related tools' : 'Outils liés' ?>
			</h4>
			<div class="related-tools-content"><?php the_terms($post->ID, "tools", "", " "); ?></div>
		</div>
		<div class="related-projects">
		<h4>
				<?php echo $in_english ? 'Related projects' : 'Projets liés' ?>
		</h4>
			<div class="related-projects-content"><?php the_terms($post->ID, "projets", "", " "); ?></div>
		</div>
		<div class="related-publications">
		<h4>
				<?php echo $in_english ? 'Related publications' : 'Publications liées' ?>
		</h4>
			<div class="related-publications-content"><?php the_terms($post->ID, "publications", "", " "); ?></div>
		</div>
	</div>
	<div class="clear"></div>
   	<?php comments_template(); ?>
</div>
<?php get_footer(); ?>
