<?php
/**
 * The template to display one tool
 *
 */
get_header(); ?>
<div class="container">
	<div class="single-post">
	
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<?php $metas = get_json_object("tool"); ?>
			
			<h2><a href="#"><?php get_object_name($metas); ?></a></h2>
			<img src="<?php get_object_image($metas); ?>" alt="<?php get_object_name($metas); ?>" />
			<?php the_excerpt();?>
		<?php endwhile; ?>
	</div>
	<div class="sidebar">
		<div class="related-projects">
		<h4>Related projects</h4>
			<div class="related-projects-content"><?php the_terms($post->ID, "projets", "", " "); ?></div>
		</div>
		<div class="related-people">
		<h4>Related people</h4>
			<div class="related-people-content"></div>
		</div>
	</div>
</div>
<?php get_footer(); ?>
