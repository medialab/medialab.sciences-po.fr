<?php
/**
 * Template Name: People Page
 *
 */

get_header(); ?>	
<div class="container">
	<div id="content">
		<?php
            $query = new WP_Query( array ('post_type' => 'people', 'orderby' => 'rand', 'order' => 'DESC' , 'posts_per_page'=>-1) ); 
        	$projects_slugs=array();
        	$tools_slugs=array();
        	if($query->have_posts()) : 
        		while($query->have_posts()) :
        			$query->the_post();
        			array_push($projects_slugs,$post->post_name);
        			$actif = true;
        			if (get_post_meta($post->ID, "date_fin", TRUE) != "")
        				$actif = (DateTime::createFromFormat("d/m/y", $post->date_fin)->getTimestamp() > time()); 
        			$projects_slugs=array_merge($projects_slugs,explode(" ",get_object_terms('projets')));
        			$tools_slugs=array_merge($tools_slugs,explode(" ",get_object_terms('tools')));
        ?>
			<div class="column_display people <?php echo get_object_terms('projets'); ?> 
				<?php echo get_object_terms('tools'); echo " ";?>
				<?php if (!$actif) echo "in"; echo "actif"; ?>" <?php if (!$actif) echo 'style="display: none;"'?>>

				
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
			<p><?php echo_shorten_excerpt(get_the_excerpt(), 140); ?></p>
			
			</div>

        <?php endwhile; endif; ?>
	</div>
	<div class="sidebar">
		<h4>Active members</h4>
		<a class="active-p active-p-enabled">Currently in médialab</a>
		<a class="retired-p">Previously in médialab</a>
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
					echo '<a id="'.$tool.'" class="facet">'.$json->name.'</a>';
				} ?>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>
