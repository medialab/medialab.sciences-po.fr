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
		
		<div class="container_12" id="main">
			<?php query_posts( array ( 'category_name' => '1st-level', 'posts_per_page' => 1 ) );?>
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="grid_6" id="edito_zone_1">
				<?php the_post_thumbnail(); ?>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3><?php echo get_post_meta($post->ID, 'subtitle', true); ?></h3>
			</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
			<?php query_posts( array ( 'category_name' => '2nd-level', 'posts_per_page' => 1 ) );?>
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="grid_6" id="edito_zone_2">
				<?php the_post_thumbnail(); ?>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3><?php echo get_post_meta($post->ID, 'subtitle', true); ?></h3>
			</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
			<?php query_posts( array ( 'category_name' => '3rd-level', 'posts_per_page' => 2 ) );?>
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="grid_3" id="edito_zone_3">
				<?php the_post_thumbnail(); ?>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3><?php echo get_post_meta($post->ID, 'subtitle', true); ?></h3>
			</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
		</div>
		<div class="container_12" id="columns">
			<div class="grid_3" id="tools">
				<h4>Tools</h4>
				<?php $loop = new WP_Query( array( 'post_type' => 'tools', 'posts_per_page' => 4 ) );
				while ( $loop->have_posts() ) : $loop->the_post();?>
				<div class="column_element">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
					<a href="<?php the_permalink(); ?>">More about this</a>
				</div>
				<?php endwhile; ?>
			</div>
			<div class="grid_3" id="projects">
				<h4>Projects</h4>
				<?php $loop = new WP_Query( array( 'post_type' => 'projets', 'posts_per_page' => 4 ) );
				while ( $loop->have_posts() ) : $loop->the_post();?>
				<div class="column_element">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
					<a href="<?php the_permalink(); ?>">More about this</a>
				</div>
				<?php endwhile; ?>
			</div>
			<div class="grid_3" id="people">
				<h4>People</h4>
				<?php $args = array( 'post_type' => 'people', 'posts_per_page' => 4 );
				$loop = new WP_Query( $args );
				while ( $loop->have_posts() ) : $loop->the_post(); ?>
		
				<div class="column_element">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_post_thumbnail(); ?>
					<p><?php the_excerpt(); ?></p>
					<a href="<?php the_permalink(); ?>">More about this</a>
				</div>
				
				<?php endwhile;?>
			</div>
			<div class="grid_3" id="news">
				<h4>News</h4>
				<div class="column_element">
					<h2><a href="<?php the_permalink(); ?>">The title of a news object</a></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
					nec placerat erat. Fusce condimentum erat augue, non tempor 
					massa.</p>
					<a href="#">More about this</a>
				</div>
				<div class="column_element">
					<h2><a href="<?php the_permalink(); ?>">The title of some other news object</a></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
					nec placerat erat. Fusce condimentum erat augue, non tempor 
					massa.</p>
					<a href="#">More about this</a>
				</div>
				<div class="column_element">
					<h2><a href="<?php the_permalink(); ?>">The title of some other news object</a></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
					nec placerat erat. Fusce condimentum erat augue, non tempor 
					massa.</p>
					<a href="#">More about this</a>
				</div>
			</div>
		</div>
	</div>
<?php get_footer(); ?>