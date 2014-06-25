<?php
/**
 * Template Name: Publications Page
 *
 */

get_header(); ?>	
<div class="container">
	<div id="content">
		<?php
		$people_slugs = $tools_slugs = $projects_slugs = $publication_type_slugs = array();
		
		/*$args : an array that traduce an SQL request  */
		
		$args = array( 
			'post_type' => 'publications', 
			'orderby' => 'date', 
			'nopaging'=>true,
			'tax_query' => array( 'relation' => 'OR',

				array(
					'taxonomy' => 'publications_types',
					'field' => 'slug',
					'terms' => array( 'journal-articles', 'conferences-proceedings','books', 'thesis','working-papers', 'maps-viz', 'datascapes', 'websites' )
				),
			)
		);

		/*request execution. $loop = all of the post filter by terms */	

		$loop = new WP_Query( $args );
	
		/* creation of two empty arrays that are going to be fill by the while */
		$left = '';
		$right = '';


		while ( $loop->have_posts() ) : $loop->the_post();

			/* split the publications_types separate by an blank space, and return it in an array */

			$terms = explode(" ", get_object_terms('publications_types'));

			$remote_url = array_pop(get_post_meta($post->ID, 'remote_url'));

			/* Add publication_slugs/projets_slugs/people/slugs to the global slugs */
			$projects_slugs=array_merge($projects_slugs,explode(" ",get_object_terms('projets')));
			$people_slugs=array_merge($people_slugs,explode(" ",get_object_terms('people')));
			$publication_type_slugs=array_merge($publication_type_slugs,explode(" ",get_object_terms('publications_types')));

			/* Built the html div that will be display on the page */
			$el = '<div class="column_display publications '.get_object_terms('projets').' '.get_object_terms('people').' '.get_object_terms('publications_types').'"><h2><a href="'.get_permalink($post->id).'">'.$post->post_title.'</a></h2><h3 class="facet do-not-enlighten" data-target="'.get_object_terms('publications_types').'"><a href="#" title="">'.implode($terms, ',').'</a></h3><div class="img_publi"><a href=" '.get_permalink($post->id).'">'.get_the_post_thumbnail().'</a></div><p>'.get_shorten_excerpt(get_the_excerpt(), 140).'</p>
				'.($remote_url?'<a href="'.$remote_url.'">'.$remote_url.'</a>':'').'

			</div>';
		

			if(in_array('datascapes', $terms)) {
				$right.= $el;
			} 

			if(in_array('maps-viz', $terms)) {
				$right.= $el;
			} 

			if(in_array('websites', $terms)) {
				$right.= $el;
			} 

			if (in_array('journal-articles', $terms)) {
			 	$left.= $el;
			 }

			if (in_array('conferences-proceedings', $terms)) {
			 	$left.= $el;
			 }

			if (in_array('books', $terms)) {
			 	$left.= $el;
			 }

			if (in_array('thesis', $terms)) {
			 	$left.= $el;
			 }

			if (in_array('working-papers', $terms)) {
			 	$left.= $el;
			 };

		endwhile; 
		?>
		<div id="content_left" data-columns="1">
		<?php echo $left ?>
		</div>

		<div id="content_right"  data-columns="1">
		<?php echo $right ?>
		</div>

	</div>



	<div class="sidebar" id="sidebar_publi">
		<h4>Publications types</h4>
		<h5>filter by publication types</h5>
		<div class="sidebar-inside">
			<?php 
				$publication_type_slugs=array_unique($publication_type_slugs);
				
				foreach ($publication_type_slugs as $publication_type) :
					//echo $publication_type;
					$pt=get_term_by("slug",$publication_type,"publications_types");
			?>
					<a id="<?php echo $pt->slug; ?>" class="facet"><?php echo $pt->name; ?></a>
			<?php
			endforeach; ?>
		</div>
		<h4>Authors</h4>
		<h5>filter by authors</h5>
		<div class="sidebar-inside">
			<?php 
				$people_slugs=array_unique($people_slugs);
				
				$loop = new WP_Query( array( 
				'post_type' => 'people',
				'orderby' => 'name', 'order' => 'ASC' ) );
			while ( $loop->have_posts() ) : 
				$loop->the_post();
				if(in_array($post->post_name,$people_slugs)):
			?>
					<a id="<?php echo basename(get_permalink()); ?>" class="facet"><?php the_title(); ?></a>
			<?php
			endif;
			endwhile; ?>
		</div>
		<h4>Related projects</h4>
		<h5>filter by projects</h5>
		<div class="sidebar-inside">
			<?php 
				$projects_slugs=array_unique($projects_slugs);
				$loop = new WP_Query( array( 
				'post_type' => 'projets',
				'orderby' => 'name', 'order' => 'ASC' ) );
			while ( $loop->have_posts() ) : 
				$loop->the_post();
				if(in_array($post->post_name,$projects_slugs)):

			?>
					<a id="<?php echo basename(get_permalink()); ?>" class="facet"><?php the_title(); ?></a>
			<?php
			endif;
			endwhile; ?>
		</div>
	</div>
</div>
<div id="warning">Warning : The following texts are made available for private academic use only. Published papers are presented here in their pre-print version. Important differences may exist between this version and the final published one. Always report to the authors and publishers for any other use.</div>
<?php get_footer(); ?>
