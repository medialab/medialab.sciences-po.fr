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
	<div style="clear:both"></div>
	<div id="manifesto">
		
		<?php query_posts( array('post_type' => array( 'blog', 'projets', 'publications'), 'category_name' => '1st-level', 'showposts' => 1 ));
		while ( have_posts() ) : the_post(); ?>
		<div class="zone-1">
			<?php $link = '<a href="'.get_permalink().'" rel="bookmark" title="Permanent Link to '.get_the_title().'">'; ?>
			<h2><?php echo $link; the_title(); ?></a></h2>
			<div class="content">
				<?php $video = get_post_meta($post->ID, 'vimeo_id', true);
				 if (!$video) {
				 	echo $link;
				 	the_post_thumbnail("thumb-zone-1");
				 	echo "</a>";
				 } else echo '<iframe src="http://player.vimeo.com/video/'.$video.'" width="456px" height="343px" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'; ?>
			</div>
			<h3><?php echo_shorten_excerpt(get_the_excerpt(), 400); ?></h3>
		</div>
		<?php endwhile; ?>

		<?php wp_reset_query(); ?>
		<?php query_posts( array('post_type' => array( 'blog', 'projets', 'publications'), 'category_name' => '2nd-level', 'showposts' => 1 ));
		while ( have_posts() ) : the_post(); ?>
		<div class="zone-2">
			<?php $link = '<a href="'.get_permalink().'" rel="bookmark" title="Permanent Link to '.get_the_title().'">'; ?>
			<h2><?php echo $link; the_title(); ?></a></h2>
			<div class="content"><?php echo $link; the_post_thumbnail("thumb-zone-2"); ?></a></div>
			
			<h3><?php echo_shorten_excerpt(get_the_excerpt(), 200); ?></h3>
		</div>
		<?php endwhile; ?>


		<?php wp_reset_query(); ?>
		<!-- Level 3 zone -->
		<?php query_posts( array('post_type' => array( 'blog', 'projets', 'publications'), 'category_name' => '3rd-level', 'showposts' => 2 ));
		while ( have_posts() ) : the_post(); ?>
		<div class="zone-3">
			<?php $link = '<a href="'.get_permalink().'" rel="bookmark" title="Permanent Link to '.get_the_title().'">'; ?>
			<h2><?php echo $link; the_title(); ?></a></h2>
			<div class="content">
				<?php echo $link; the_post_thumbnail("thumb-zone-3-4"); ?></a>
			</div>
			<h3><?php echo_shorten_excerpt(get_the_excerpt(), 100); ?></h3>

			
		</div>
		<?php endwhile; ?>

		<div style="clear:both"></div>

	</div>
	
	<div id="zone-edito-old" style="display:none">
		<!-- Most recent/important zone -->
		<?php query_posts( array ( 'category_name' => '1st-level', 'posts_per_page' => 1 ) );?>
		<?php while ( have_posts() ) : the_post(); ?>
		<div class="zone-1">
			<?php the_post_thumbnail("thumb-zone-3-4"); ?>



			<?php echo get_post_meta($post->ID, 'post_video', true); ?>
			<div class="edito-title important">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3><?php echo get_post_meta($post->ID, 'subtitle', true); ?></h3>
			</div>
		</div>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
		<!-- Second zone -->
		<?php query_posts( array ( 'category_name' => '2nd-level', 'posts_per_page' => 1 ) );?>
		<?php while ( have_posts() ) : the_post(); ?>
		<div class="zone-2">
			<?php the_post_thumbnail(); ?>
			<div class="edito-title less-important">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3><?php echo get_post_meta($post->ID, 'subtitle', true); ?></h3>
			</div>
		</div>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
		<!-- Level 3 zone -->
		<?php query_posts( array ( 'category_name' => '3rd-level', 'posts_per_page' => 2 ) );?>
		<?php while ( have_posts() ) : the_post(); ?>
		<div class="zone-3">
			<?php the_post_thumbnail(); ?>
			<div class="edito-title less-important">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3><?php echo get_post_meta($post->ID, 'subtitle', true); ?></h3>
			</div>
		</div>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
	</div>
	<div class="columns-container">
		<!-- Projects column -->
		<?php 
		/// TRANSLATIONS !
		$columns = Array(
			"projets"=>"Projects",
			"blog"=>"Blog",
			"tools"=>"Tools",
			"publications"=>"Publications"
		);
		$n_elements_by_column = 5;
		foreach( $columns as $key=>$label ) : ?>
			<div class="column-content">
				<h2><a href="<?php echo get_site_url()."/".$key; ?>/"><?php echo $label ?></a></h2>
				<?php if ($key === "tools") :
					foreach (get_tools_short_list($n_elements_by_column) as $tool) : 
						$json = get_tool_metas($tool);
						if (!$json) continue; ?>
						<div class="column-element">
							<a href="http://lab.medialab.sciences-po.fr/#<?php echo $tool; ?>"><?php echo $json->name; ?></a>
						</div>
					<?php endforeach;
				elseif ($key === "publications") :
					$loop = new WP_Query( array( 'post_type' => 'publications', 'posts_per_page' => -1 ));
					$ct = 0;
					while ( $loop->have_posts() ) : $loop->the_post();
						if ($post->date_debut) $time = DateTime::createFromFormat("d/m/y", $post->date_debut)->getTimestamp();
						else $time = $post->id;
						$publis["$time-$ct"] = '<div class="column-element"><a href="'.get_permalink().'">'.get_the_title().'</a></div>';
						$ct++;
					endwhile;
					krsort($publis);
					$ct = 0;
					foreach ($publis as $time => $pub) {
						echo $pub;
						$ct++;
						if ($ct > 4) break;
					}
				else :
				$args = array( 'post_type' => $key, 'posts_per_page' => $n_elements_by_column );
				
				$loop = new WP_Query( $args );
				while ( $loop->have_posts() ) : $loop->the_post(); ?>

				<div class="column-element">
					<a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a>
					<?php
					// the_post_thumbnail();
					// the_excerpt(); 
					?>

				</div>
				<?php endwhile; endif;?>
			</div>
			<?php

		endforeach;


		?>


		
	</div>
<?php get_footer(); ?>
