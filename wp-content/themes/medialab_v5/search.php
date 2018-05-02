<?php
/*
Template Name: Search Page
*/
?>
<?php get_header(); ?>
<div class="container">
	<div id="pin_like_searched">
		<?php if(have_posts()): ?>
		<?php while (have_posts()) : the_post(); ?>
		<div class="column_display <?php echo get_post_type(); ?>">
			<?php 
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' ); 
			if ($image) echo '<img src="'.$image[0].'" alt="" />';
			 ?>
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" 
			title="Permanent Link to <?php the_title_attribute(); ?>">
			<?php the_title(); ?></a></h2>
			<p><?php echo_shorten_excerpt(get_the_excerpt(), 100); ?>
			<a href="<?php the_permalink() ?>">Read the full post</a></p>
		</div>
		<?php endwhile; ?>
		<?php else: ?>
		<div class="nothing">
			<p>Sorry, but no posts matched your criteria</p>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>
