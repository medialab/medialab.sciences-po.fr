<?php
/**
 * The template to display one tool
 *
 */
get_header(); ?>
<div class="center">
	<div class="single-post">
	
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<?php $metas = get_json_object("tool"); ?>
			
			<h2><a href="#"><?php get_object_name($metas); ?></a></h2>
			<img src="<?php get_object_image($metas); ?>" alt="<?php get_object_name($metas); ?>" />
			
		<?php endwhile; ?>
	</div>
</div>
<?php get_footer(); ?>
