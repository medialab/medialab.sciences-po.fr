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
	<div class="single-post">
	<?php  the_post(); ?>
		
		<h2><?php the_title(); ?></h2>
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
			<h4>Related people</h4>
			<div class="related-people-content"><?php the_terms($post->ID, "people", "", " "); ?></div>
		</div>
		<div class="related-tools">
			<h4>Related tools</h4>
			<div class="related-tools-content"><?php the_terms($post->ID, "tools", "", " "); ?></div>
		</div>
		<div class="related-projects">
		<h4>Related projects</h4>
			<div class="related-projects-content"><?php the_terms($post->ID, "projets", "", " "); ?></div>
		</div>
	</div>
	<div class="clear"></div>
   	<?php comments_template(); ?>
</div>
<?php get_footer(); ?>
