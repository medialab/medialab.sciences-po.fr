<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Medialab
 */

get_header(); ?>	

	<div class="center">
		<!-- Highlighted posts -->
		<div id="zone-edito">
			<!-- Most recent/important zone -->
			<?php query_posts( array ( 'category_name' => '1st-level', 'posts_per_page' => 1 ) );?>
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="zone-1">
				<?php the_post_thumbnail(); ?>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3><?php echo get_post_meta($post->ID, 'subtitle', true); ?></h3>
			</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
			<!-- Second zone -->
			<?php query_posts( array ( 'category_name' => '2nd-level', 'posts_per_page' => 1 ) );?>
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="zone-2">
				<?php the_post_thumbnail(); ?>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3><?php echo get_post_meta($post->ID, 'subtitle', true); ?></h3>
			</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
			<!-- Level 3 zone -->
			<?php query_posts( array ( 'category_name' => '3rd-level', 'posts_per_page' => 2 ) );?>
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="zone-3">
				<?php the_post_thumbnail(); ?>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3><?php echo get_post_meta($post->ID, 'subtitle', true); ?></h3>
			</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
		</div>
	</div>
<div class="bg-container">
	<!-- Columns titles -->
	<div class="center">
		<div class="column-title">
			<h5>Projects</h5>
		</div>
		<div class="column-title">
			<h5>Research &amp; Developement</h5>
		</div>
		<div class="column-title">
			<h5>Tools</h5>
		</div>
		<div class="column-title">
			<h5>People</h5>
		</div>
	</div>
</div>
	<div class="center" id="columns">
		<!-- Projects column -->
		<div class="column-content">
			<?php $args = array( 'post_type' => 'projets', 'posts_per_page' => 4 );
			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) : $loop->the_post(); ?>
		
			<div class="column-element">
				<h3><?php the_title(); ?></h3>
				<p><?php the_excerpt(); ?></p>
				<a href="<?php the_permalink(); ?>">(More..)</a>
			</div>
			<?php endwhile;?>
		</div>
		<div class="column-content">
			<!-- R&D column -->
			<div class="column-element">
				<h3>A pre-publication</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>What are we working on?</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>Check out what we found!</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>A pre-publication</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>What are we working on?</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>Check out what we found!</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
		</div>
		<div class="column-content">
			<!-- Tools column -->
			<div class="column-element">
				<h3>ANTA</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>Feel free to use this tool</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>I want hue</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>ANTA</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>Feel free to use this tool</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
			<div class="column-element">
				<h3>I want hue</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Suspendisse tincidunt, eros nec euismod vehicula</p>
				<a href="#">(More...)</a>
			</div>
		</div>
		<div class="column-content">
			<!-- People column -->
			<?php $args = array( 'post_type' => 'people', 'posts_per_page' => 3 , 'order' => 'ASC');
			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) : $loop->the_post(); ?>
		
			<div class="column-element">
				<h3><?php the_title(); ?></h3>
				<?php the_post_thumbnail(); ?>
				<p><?php the_excerpt(); ?></p>
				<a href="<?php the_permalink(); ?>">(More...)</a>
			</div>
			<?php endwhile;?>
		</div>
	</div>
<?php get_footer(); ?>