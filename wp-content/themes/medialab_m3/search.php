<?php
/*
Template Name: Search Page
*/
?>
<?php get_header(); ?>
<div class="center">
	<h4>Search Results:</h4>
	<div id="pin_like_searched">
		<?php if(have_posts()): ?>
		<?php while (have_posts()) : the_post(); ?>
		<div class="column_display">
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" 
			title="Permanent Link to <?php the_title_attribute(); ?>">
			<?php the_title(); ?></a></h2>
			<?php the_post_thumbnail(); ?>
			<p><?php the_excerpt(); ?></p>
			<a href="<?php the_permalink() ?>">Read the full post</a>
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