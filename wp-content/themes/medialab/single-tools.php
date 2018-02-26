<?php
/**
 * The template to display one tool
 *
 */
get_header(); ?>

	<div class="container_12" id="main">
	
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div class="grid_9" id="inside_tool">
			<?php $metas = get_json_object("tool"); ?>
			<h1><?php get_object_name($metas); ?></h1>
			<img src="<?php get_object_image($metas); ?>" alt="<?php get_object_name($metas); ?>" />
			
		</div>
		<?php endwhile; ?>
	</div>
</div>
<?php get_footer(); ?>
