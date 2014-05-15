<?php
/**
 * Template Name: Publications Page
 *
 */

get_header(); ?>	
<div class="container">
	<div id="content">
		
		<?php $people_slugs = $tools_slugs = $projects_slugs = $publication_type_slugs = array();
		$loop = new WP_Query( array( 'post_type' => 'publications', 'orderby' => 'date', 'nopaging'=>true));
		while ( $loop->have_posts() ) : $loop->the_post();
			//if ($post->date_debut) $time = DateTime::createFromFormat("d/m/y", $post->date_debut)->getTimestamp();
			//else $time = $post->id;

			$projects_slugs=array_merge($projects_slugs,explode(" ",get_object_terms('projets')));
			$tools_slugs=array_merge($tools_slugs,explode(" ",get_object_terms('tools')));
			$people_slugs=array_merge($people_slugs,explode(" ",get_object_terms('people')));
			$publication_type_slugs=array_merge($publication_type_slugs,explode(" ",get_object_terms('publication_types')));

			echo '<div class="column_display publications '.get_object_terms('projets').' '.get_object_terms('tools').' '.get_object_terms('people').' '.get_object_terms('publication_types').'">'.the_post_thumbnail('thumbnail').'<h2><a href="'.get_permalink($post->id).'">'.$post->post_title.'</a></h2><p>'.get_shorten_excerpt(get_the_excerpt(), 140).'</p></div>';
		endwhile; ?>
	</div>
	<div class="sidebar">
		<h4>Publications types</h4>
		<h5>filter by publication types</h5>
		<div class="sidebar-inside">
			<?php 
				$publication_type_slugs=array_unique($publication_type_slugs);
				
				foreach ($publication_type_slugs as $publication_type) :
					//echo $publication_type;
					$pt=get_term_by("slug",$publication_type,"publication_types");
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
		<h4>Related tools</h4>
		<h5>filter by tool</h5>
		<div class="sidebar-inside">
			<?php 
				$tools_slugs=array_unique($tools_slugs);
			foreach ($tools_slugs as $tool) {
			$json = get_tool_metas($tool);
			?>
			<a id="<?php echo $tool; ?>" class="facet"><?php echo $json->name; ?></a>
			<?php } ?>
		</div>
	</div>
</div>
<i>Warning : The following texts are made available for private academic use only. Published papers are presented here in their pre-print version. Important differences may exist between this version and the final published one. Always report to the authors and publishers for any other use.</i>
<?php get_footer(); ?>
