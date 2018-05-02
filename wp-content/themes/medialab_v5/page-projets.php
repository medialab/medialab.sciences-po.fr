<?php
/**
 * Template Name: Projects Page
 *
 */

get_header(); ?>	
<div class="container">
	<?php $locale = get_locale() ?>
	<?php $in_english = $locale == 'en_US' ?>
	<h2 class="row-main-title">
			<span class="title-text">
				<?php echo $in_english ? 'Projects' : 'Projets' ?>
			</span>
	</h2>
	<div id="content">
		
		<?php 
		$projects_slugs=array();
		$loop = new WP_Query( array( 'post_type' => 'projets', 'orderby' => 'rand', 'order' => 'DESC','posts_per_page'=>-1 ) );
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
			<!-- début modif tuile projet 2018 -->
			<div class="item-card-container">
	      <?php $link = '<a class="inherit-style" href="'.get_permalink().'" rel="bookmark" title="Permanent Link to '.get_the_title().'">'; ?>
	      <h2 class="title"><?php echo $link; the_title(); ?></a></h2>
	      

				<a class="img-container" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
	      
	      <?php $subtitle_en = get_post_meta($post->ID, 'sstitre_projet_en', true); $subtitle = $in_english && isset($subtitle_en) ? $subtitle_en : get_post_meta($post->ID, 'sstitre_projet_fr', true); ?>
	      <h3 class="subtitle">
	      	<?php echo $subtitle; ?>
	      </h3>
	      
	      <div class="excerpt"><?php echo_shorten_excerpt(get_the_excerpt(), 200); ?></div>
	      <div class="more">
					<?php echo '<a href="'.get_permalink().'" rel="bookmark" title="Permanent Link to '.get_the_title().'">'; ?>
						<?php echo $in_english ? 'Learn more' : 'En savoir plus' ?>
					</a>
	      </div>
	    </div>
			<!-- fin modif tuile projet 2018 -->
			<!-- <h2><a href="<?php /*the_permalink();*/ ?>"><?php /*the_title();*/ ?></a></h2> -->
			<!-- <a href="<?php /*the_permalink(); ?>"><?php the_post_thumbnail('thumbnail');*/ ?></a>
			 <p><?php /*echo_shorten_excerpt(get_the_excerpt(), 100);*/ ?></p> -->
			
		</div>
		
		<?php endwhile; ?>
	</div>
	<div class="sidebar">
		<h4>
      <?php echo $in_english ? 'Active projects': 'Projets actifs' ?>    
		</h4>
		<a class="active-p active-p-enabled">
      <?php echo $in_english ? 'Currently in médialab': 'Actuellement au médialab' ?>    
		</a>
		<a class="retired-p">
      <?php echo $in_english ? 'Previously in médialab': 'Précédemment au médialab' ?>    
		</a>
		<h4>
      <?php echo $in_english ? 'Related persons': 'Personnes liées' ?>    
		</h4>
		<h5>
      <?php echo $in_english ? 'Click on a person to see related team members': 'Cliquer sur une personne pour afficher les membres de l\'équipe qui y travaillent' ?>    
		</h5>
		<div class="sidebar-inside"><?php
			$loop = new WP_Query( array( 
				'post_type' => 'people',
				'tax_query' => array(
					array('taxonomy' => 'projets','field' => 'slug','terms' => $projects_slugs,'operator' => 'IN')
				),
				 'orderby' => 'name', 'order' => 'ASC', 'posts_per_page'=>-1 ) );
			while ( $loop->have_posts() ) : $loop->the_post();?>
			<a id="<?php echo basename(get_permalink()); ?>" class="facet"><?php the_title(); ?></a>
			<?php endwhile; ?>
		</div>
		
	</div>
</div>

<?php get_footer(); ?>
