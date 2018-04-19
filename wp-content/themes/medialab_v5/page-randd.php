<?php
/**
 * Template Name: R&D Page
 *
 */

get_header(); ?>	
<div class="container">
	<?php $locale = get_locale() ?>
  <?php $in_english = $locale == 'en_US' ?>
  <h2 class="row-main-title">
      <span class="title-text">
        <?php echo $in_english ? 'News' : 'Actualités' ?>
      </span>
  </h2>
	<div id="content">
		
		<?php 
		$projects_slugs=array();
		$tools_slugs=array();
		$people_slugs=array();
		$loop = new WP_Query( array( 'post_type' => 'blog', 'order' => 'DESC' ) );
		while ( $loop->have_posts() ) : $loop->the_post();
		
		$projects_slugs=array_merge($projects_slugs,explode(" ",get_object_terms('projets')));
        $tools_slugs=array_merge($tools_slugs,explode(" ",get_object_terms('tools')));
        $people_slugs=array_merge($people_slugs,explode(" ",get_object_terms('people')));
        ?>
	
		<div class="column_display blog item-card-container <?php echo get_object_terms('projets'); ?> 
				<?php echo get_object_terms('tools');?> <?php echo get_object_terms('people');?>">
		
			
			<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="img-container">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
			</div>
			<div class="excerpt">
				<p><?php echo_shorten_excerpt(get_the_excerpt(), 140); ?></p>
			</div>	
			
		</div>
		
		<?php endwhile; ?>
	</div>
		<div class="sidebar">
		<h4>Related People</h4>
		<h5>Team members concerned by the blog posts</h5>
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
		<h5>Click on a project to see who is on it</h5>
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
		<h5>Click on a tool to see who is on it</h5>
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
	<div class="clear"></div>
</div>

<?php get_footer(); ?>
