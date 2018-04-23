<?php
/**
 * Template Name: Publications Page
 *
 */

get_header(); ?>	
<div class="container">
	<?php $locale = get_locale() ?>
	<?php $in_english = $locale == 'en_US' ?>
	<h2 class="row-main-title">
			<span class="title-text">
				<?php echo $in_english ? 'Publications' : 'Publications' ?>
			</span>
	</h2>
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
						$el = '<div class="column_display publications item-card-container '.get_object_terms('projets').' '.get_object_terms('people').' '.get_object_terms('publications_types').'">
			<h2 class="title"><a href="'.get_permalink($post->id).'">'.$post->post_title.'</a></h2>
			<h3 class="subtitle facet do-not-enlighten hide" data-target="'.get_object_terms('publications_types').'"><a href="#" title="">'.implode($terms, ',').'</a></h3><div class="img_publi img-container"><!--<a href=" '.get_permalink($post->id).'">'.get_the_post_thumbnail().'</a>--></div><div class="excerpt"><p>'.get_shorten_excerpt(get_the_excerpt(), 140).'</p>
				'.($remote_url?'<a href="'.$remote_url.'">'.$remote_url.'</a>':'').'

				</div>
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
		<!-- <div id="content_left" data-columns="1"> -->
		<?php echo $left ?>
		<!-- </div> -->

		<!-- <div id="content_right"  data-columns="1"> -->
		<?php echo $right ?>
		<!-- </div> -->

	</div>



	<div class="sidebar" id="sidebar_publi">
		<h4>
      <?php echo $in_english ? 'Publication types': 'Types de publications' ?>      
		</h4>
		<h5>
      <?php echo $in_english ? 'Filter by publication type': 'Filtrer par type de publication' ?>      
		</h5>
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
		<h4>
      <?php echo $in_english ? 'Authors': 'Auteurs' ?>     
		</h4>
		<h5>
      <?php echo $in_english ? 'Filter by author': 'Filtrer par auteur' ?>      
		</h5>
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
		<h4>
      <?php echo $in_english ? 'Related projects': 'Projets liés' ?>      
		</h4>
		<h5>
      <?php echo $in_english ? 'Filter by project': 'Filtrer par projet' ?>      
		</h5>
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
<div id="warning">
  <?php echo $in_english ? 'Warning : The following texts are made available for private academic use only. Published papers are presented here in their pre-print version. Important differences may exist between this version and the final published one. Always report to the authors and publishers for any other use.': 'Avertissement : les textes suivants sont rendus disponibles pour un usage universitaire et privé uniquement. Les textes publiés sont présentés ici dans leur version pre-print : il pourrait exister des différences importantes entre cette version et la version publiée. Il est impératif de revenir vers les auteurs et éditeurs pour un quelconque autre usage.' ?>      
</div>
<?php get_footer(); ?>
