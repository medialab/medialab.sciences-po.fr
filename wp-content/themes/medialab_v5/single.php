<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>
<div class="container">
	<div class="single-post">
	<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<h2 class="row-main-title">
				<span class="title-text">
					<?php the_title(); ?>
				</span>
		</h2>
		
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php the_content(); ?>
      
	<?php endwhile; endif; ?>
	</div>
	<div class="clear"></div>
   	<?php comments_template(); ?>
</div>
<?php get_footer(); ?>
