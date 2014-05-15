<?php
/**
 * Template Name: Projects Page
 *
 */

get_header(); ?>	
<div class="container">
	<div id="content">
		
		<?php 
		$projects_slugs=array();
		$loop = new WP_Query( array( 'post_type' => 'projets', 'orderby' => 'rand', 'order' => 'DESC' ) );
		while ( $loop->have_posts() ) : 
			$loop->the_post();
			array_push($projects_slugs,$post->post_name);
			$actif = true;
			if (get_post_meta($post->ID, "date_fin", TRUE) != "")
				$actif = (DateTime::createFromFormat("d/m/y", $post->date_fin)->getTimestamp() > time()); 
		?>
	
		<div class="column_display projets <?php 
		echo get_objects_for_slug('people','projets',$post->post_name);
		echo ' '.get_objects_for_slug('publications','projets',$post->post_name); 
		echo"  ";
		if (!$actif) echo 'in'; echo 'actif'; ?>"<?php if (!$actif) echo ' style="display: none;"'; ?>>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
			<p><?php echo_shorten_excerpt(get_the_excerpt(), 100); ?></p>
			
		</div>
		
		<?php endwhile; ?>
	</div>
	<div class="sidebar">
		<h4>Active members</h4>
		<a class="active-p active-p-enabled">Currently in médialab</a>
		<a class="retired-p">Previously in médialab</a>
		<h4>Related people</h4>
		<h5>Click on a person to see who is working on what</h5>
		<div class="sidebar-inside"><?php
			$loop = new WP_Query( array( 
				'post_type' => 'people',
				'tax_query' => array(
					array('taxonomy' => 'projets','field' => 'slug','terms' => $projects_slugs,'operator' => 'IN')
				),
				 'orderby' => 'name', 'order' => 'ASC' ) );
			while ( $loop->have_posts() ) : $loop->the_post();?>
			<a id="<?php echo basename(get_permalink()); ?>" class="facet"><?php the_title(); ?></a>
			<?php endwhile; ?>
		</div>
		
	</div>
</div>

<?php get_footer(); ?>
