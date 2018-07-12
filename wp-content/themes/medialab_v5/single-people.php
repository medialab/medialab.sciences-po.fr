<?php
/**
 * The template to display one person
 *
 */
get_header(); ?>
<div class="container">
	<?php $locale = get_locale() ?>
	<?php $in_english = $locale == 'en_US' ?>
	<h2 class="row-main-title">
			<span class="title-text">
				<?php the_title(); ?>
			</span>
			<a href="/people">
				<?php echo $in_english ? 'Go back to the team...' : 'Retour à l\'équipe' ?>
			</a>
	</h2>

	<div class="single-post">
	
		<?php the_post(); $pid = $post->id; ?>			
				<?php the_post_thumbnail("s_post"); ?>
				<?php the_content(); ?>
				<br><br>
				<?php  $user_name=$post->post_name; 
				?>
				
				<!--<div class="publications"> 
				<?php
				
				$pts=get_terms("publications_types");
				$publication_types_slugs=array();
				$donepublis = array();
				foreach($pts as $pt) :
					
						$publication_types_slugs[]=$pt->slug;
						$publications = new WP_Query(array( 
						'post_type' => 'publications',
						'tax_query' => array(
						 	'relation' => 'AND',
						 	array('taxonomy' => 'people','field' => 'slug','terms' => $user_name),
						 	array('taxonomy' => 'publications_types','field' => 'slug','terms' => $pt->slug)
							//array('taxonomy' => 'publication_types','field' => 'slug','terms' => array($pt->slug),"OPERATOR" => 'IN')
						)));
						
						if ($publications->have_posts()) :
							?><h3><?php echo $pt->name;?></h3><?php
							while($publications->have_posts()) :
								$publications->next_post();
								if (!isset($donepublis[$publications->post->ID])) :
									$donepublis[$publications->post->ID] = 1; ?>
									<p><a href="<?php echo get_permalink($publications->post->ID); ?>" ><?php echo $publications->post->post_excerpt; ?></a></p>
							<?php endif;
							endwhile;
						endif;
									
				endforeach;
				$others = new WP_Query(array( 
				'post_type' => 'publications',
				'tax_query' => array(
					'relation' => 'AND',
					array('taxonomy' => 'people','field' => 'slug','terms' => $user_name ),
					),
				'order' => 'DESC' ) );

				if($others->have_posts()) :
				
					if ($others->found_posts > count($donepublis))
						echo "<h3>".(count($donepublis) > 0 ? "Other p" : "P")."ublications</h3>";
					while($others->have_posts()) :
						$others->next_post();
						if (!isset($donepublis[$others->post->ID])) :
							$donepublis[$others->post->ID] = 1; ?>
						<p><a href="<?php echo get_permalink($others->post->ID); ?>" ><?php echo $others->post->post_excerpt; ?></a></p>
					<?php endif;
					endwhile;
				endif; ?>
				</div>-->
			
	</div>

	<div class="sidebar">
		<div class="related-projects">
			<h4>
				<?php echo $in_english ? 'Related projects' : 'Projets liés' ?>
			</h4>
			<div class="related-projects-content"><?php the_terms( $pid, 'projets','',''); ?></div>
		</div>
		<div class="related-tools">
			<h4>
				<?php echo $in_english ? 'Related tools' : 'Outils liés' ?>
			</h4>
			<div class="related-tools-content"><?php echo_the_tools($pid); ?></div>
		</div>
		
	</div>
	

</div>
<?php get_footer(); ?>
