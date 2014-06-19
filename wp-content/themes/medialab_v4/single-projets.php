<?php

/**
 * The template to display one project
 *
 */

get_header(); ?>

<div class="container">

	<div class="single-post">

		<?php the_post(); $postname = $post->post_name; ?>

			<h2><?php the_title(); ?></h2>
			<div style="width:100%;clear:both;align:center;">
			<?php $video = get_post_meta($post->ID, 'vimeo_id', true);
				 if (!$video) the_post_thumbnail("s_post");
					else 
					{	
						wp_enqueue_script( 'froogaloop2', 'http://a.vimeocdn.com/js/froogaloop2.min.js', array(), '1.0', true );
						echo '
							<iframe id="vimeoplayer" src="http://player.vimeo.com/video/'.$video.'?api=1&player_id=vimeoplayer" width="690px" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen ></iframe>
						';
					
						$subtitles = get_post_meta($post->ID, 'vimeo_srt', true);
						if($subtitles) {
							wp_enqueue_script( 'vimeoSrt', get_template_directory_uri().'/js/jquery.vimeo-srt.min.js', array(), '1.0', true );

							// Using javascript
							echo '
								<script type="text/javascript">
								$(document).ready(function(){
									$("#vimeoplayer").vimeoSrt({srt : "'.content_url().'/subtitles/'.$subtitles.'.srt"});
								});
								</script>
							';
						}

					}

					?>


			</div>
			<div style="width:100%;clear:both;">
			<?php the_content(); ?>			
			</div>
	</div>

	<div class="sidebar">
		
		<div class="related-people">
			<h4>Related people</h4>
				<div class="related-people-content"><?php
				$loop = new WP_Query(array( 
					'post_type' => 'people',
					'tax_query' => array(
						array('taxonomy' => 'projets','field' => 'slug','terms' => $postname)
						),
					'order' => 'ASC' ) );
				while ( $loop->have_posts() ) : $loop->the_post();
					echo '<a href="'.get_permalink().'">'.get_the_title().'</a>';
				endwhile; ?></div>
		</div>
		<div class="related-publications">
			<h4>Related publications</h4>
				<div class="related-publications-content"><?php
				$loop = new WP_Query(array( 
					'post_type' => 'publications',
					'tax_query' => array(
							array('taxonomy' => 'projets','field' => 'slug','terms' => $postname)
						),
					'order' => 'ASC' ) );
				while ( $loop->have_posts() ) : $loop->the_post();
					echo '<a href="'.get_permalink().'">'.get_the_title().'</a>';
				endwhile; ?></div>
		</div>
		<div class="related-tools">
			<h4>Related tools</h4>
			<div class="related-tools-content"><?php echo_the_tools($truc); ?></div>
		</div>
	</div>

</div>



<?php get_footer(); ?>
