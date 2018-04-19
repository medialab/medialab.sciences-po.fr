<?php
/**
 * Template Name: Tools Page
 *
 */

get_header(); ?>	
<div class="container">
	<?php $locale = get_locale() ?>
	<?php $in_english = $locale == 'en_US' ?>
	<h2 class="row-main-title">
			<span class="title-text">
				<?php echo $in_english ? 'Outils' : 'Tools' ?>
			</span>
			<a href="http://tools.medialab.sciences-po.fr/">
				+ <?php echo $in_english ? 'See more tools...' : 'Voir plus d\'outils' ?>
			</a>
	</h2>
	<div id="content">
		
		<?php 
		$tools_array=array();
		$tools = get_terms("tools",array('hide_empty'=>0));
		
		shuffle($tools);
		foreach ($tools as $tool) {
			$json = get_tool_metas($tool->name);
			if (!$json) continue;
			$tools_array[] = $tool->name;
			$url = "http://tools.medialab.sciences-po.fr/#".$tool->name; ?>
		<div class="column_display tools item-card-container <?php echo get_objects_for_slug('people','tools',$tool); 
		echo get_objects_for_slug('publications','tools',$tool); php?>">
			<h2 class="title"><a href="<?php echo $url; ?>"><?php echo $json->name; ?></a></h2>
			<?php if (isset($json->visual) && ($json->visual != "")) : ?>
			<div class="img-container">
				<a href="<?php echo $url; ?>">
					<!--<img src="<?php echo preg_replace("/tools\.medialab/", "lab.medialab", preg_replace("/^\//", "http://tools.medialab.sciences-po.fr/", $json->visual)); ?>" alt="<?php echo $tool->name; ?>" title="<?php echo $tool->name; ?>" />-->
						<img src="<?php echo preg_replace("/^\//", "http://tools.medialab.sciences-po.fr/", $json->visual); ?>" alt="<?php echo $tool->name; ?>" title="<?php echo $tool->name; ?>" /></a>
				</a>
			</div>


			<?php endif; ?>
			<div class="excerpt">
				<p><?php echo_shorten_excerpt($json->description, 100); ?></p>
			</div>
						
		</div>
		<?php } ?>
	</div>
	<div class="sidebar">
		<h4>Related people</h4>
		<h5>Click on a name to see who works with a tool</h5>
		<div class="sidebar-inside">
			<?php $loop = new WP_Query( array( 
				'post_type' => 'people',
				#'name' => $people_slugs,
				'tax_query' => array(
					array('taxonomy' => 'tools','field' => 'slug','terms' => $tools_array,'operator' => 'IN')
						),
				 'orderby' => 'name', 'order' => 'ASC' ) );
			while ( $loop->have_posts() ) : $loop->the_post();?>
			<a id="<?php echo basename(get_permalink()); ?>" class="facet"><?php the_title(); ?></a>
			<?php endwhile; ?>
		</div>
		<!--
		<h4>Related publications</h4>
		<h5>Click on a publication to see related tools</h5>
		<div class="sidebar-inside">
			<?php $loop = new WP_Query( array( 
				'post_type' => 'publications',
				#'name' => $people_slugs,
				'tax_query' => array(
					array('taxonomy' => 'tools','field' => 'slug','terms' => $tools_array,'operator' => 'IN')
						),
				 'order' => 'rand' ) );
			while ( $loop->have_posts() ) : $loop->the_post();?>
			<a id="<?php echo basename(get_permalink()); ?>" class="facet"><?php the_title(); ?></a>
			<?php endwhile; ?>
		</div>
	-->

	</div>
</div>

<?php get_footer(); ?>
