<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Medialab
 */

get_header(); ?>	<!-- Début du header -->
	<div style="clear:both"></div> <!-- ???  -->
	<?php $locale = get_locale() ?>
	<?php $in_english = $locale == 'en_US' ?>
	<div id="manifesto"> <!-- Début de la zone de Une -->

		<!-- rangée 1 -->
		<h2 class="row-main-title">
			<span class="title-text">
				<?php echo $in_english ? 'News' : 'Actualités' ?>
			</span>
			<a href="/blog">
				+ <?php echo $in_english ? 'See more news...' : 'Voir plus d\'actualités' ?>
			</a>
		</h2>
		<div class="row herow columns">
			<div id="news-container" class="column">

				<div id="hero-news">
				</div>

				<ul id="list-container">
					<?php 
					query_posts( array('post_type' => array( 'blog', 'projets', 'publications'), 'category_name' => 'une', 'showposts' => 5 ));
						while ( have_posts() ) : the_post(); 
					?>
						<li class="news-card">
							<?php $link = '<a class="inherit-style" href="'.get_permalink().'" rel="bookmark" title="Permanent Link to '.get_the_title().'">'; ?>

							<div class="background-container hide-in-list-mode">
                <?php 
                $video = get_post_meta($post->ID, 'vimeo_id', true); /* la requête va chercher la vignette de la vidéo du MOOC */
                 if (!$video) {
                  echo $link;
                  echo the_post_thumbnail(array(456,456));//"thumb-zone-1");
                  echo "</a>";
                 } else echo '<iframe src="http://player.vimeo.com/video/'.$video.'" width="456px" height="343px" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'; 
                 ?>
              </div>

							<div class="content-container">
								<?php $date_debut_en = get_post_meta($post->ID, 'date_debut_en', true); $date_debut = $in_english && isset($date_debut_en) ?  
								$date_debut_en : get_post_meta($post->ID, 'date_debut_fr', true); ?>
								<?php $date_fin_en = get_post_meta($post->ID, 'date_fin_en', true); $date_fin = $in_english && isset($date_fin_en) && get_post_meta($post->ID, 'date_fin_en', true) ?  $date_fin_en : get_post_meta($post->ID, 'date_fin_fr', true); ?>
								<?php if ($date_debut || $date_fin): ?>
									<h3>
										<?php if ($date_debut): ?>
											<?php echo $date_debut ?>
										<?php endif ?>
										<?php if ($date_fin && $date_debut): ?>
											<span> - </span>
										<?php endif ?>
										<?php if ($date_fin): ?>
											<?php echo $date_fin ?>
										<?php endif ?> <?php $herogardos_type_en = get_post_meta($post->ID, 'news_type_en', true); $herogardos_type = $in_english && isset($herogardos_type_en) ?  $herogardos_type_en : get_post_meta($post->ID, 'news_type_fr', true); ?>
								<span class="hide-in-list-mode">
									- <?php echo $herogardos_type ?>
								</span>
									</h3>
								<?php endif ?>
								<h2>
									<?php echo $link; the_title(); ?></a>
								</h2>
				      	<div class="excerpt hide-in-list-mode"><?php echo_shorten_excerpt(get_the_excerpt(), 200); ?></div>

							</div>
					</li>
					<?php endwhile; ?>
				</ul>
			</div>
		</div>
		<!-- fin rangée 1 -->

		<!-- rangée 2 -->
		<div class="row">
			<div class="column" id="projects-wrapper-wrapper">
				<h2 class="row-main-title">
					<span class="title-text">
						<?php echo $in_english ? 'Ongoing projects' : 'Projets en cours' ?>
					</span>
					<a href="/projets">+ <?php echo $in_english ? 'See more projects...' : 'Voir plus de projets' ?></a>
				</h2>
				<div id="projects-wrapper">
					<div id="projects-container">
					<?php query_posts( array('post_type' => array('projets'), 'category_name' => 'vitrine', 'orderby' => 'rand', 'showposts' => 4));
					    while ( have_posts() ) : the_post(); ?>
					    <div class="item-card-container"> <!-- Zone du deuxième post -->
					      <?php $link = '<a class="inherit-style" href="'.get_permalink().'" rel="bookmark" title="Permanent Link to '.get_the_title().'">'; ?>
					      <h2 class="title"><?php echo $link; the_title(); ?></a></h2>
					      <?php $subtitle_en = get_post_meta($post->ID, 'sstitre_projet_en', true); $subtitle = $in_english && isset($subtitle_en) ? $subtitle_en : get_post_meta($post->ID, 'sstitre_projet_fr', true); ?>
					      <h3 class="subtitle">
					      	<?php echo $subtitle; ?>
					      </h3>
					      <!--<div class="content"><?php /*echo $link;the_post_thumbnail(array(456,456));*/ ?></a></div> -->
					      
					      <div class="excerpt"><?php echo_shorten_excerpt(get_the_excerpt(), 200); ?></div>
					      <div class="more">
									<?php echo '<a href="'.get_permalink().'" rel="bookmark" title="Permanent Link to '.get_the_title().'">'; ?>
										<?php echo $in_english ? 'Learn more' : 'En savoir plus' ?>
									</a>
					      </div>
					    </div>
					    <?php endwhile; ?>
					   </div>
					   
				</div>
			</div>
			<div id="twitter-container" class="column">
				<div id="twitter-wrapper">
					<a class="twitter-timeline" href="https://twitter.com/medialab_ScPo?ref_src=twsrc%5Etfw"></a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
				</div>
			</div>
		</div>
		<!-- fin rangée 2 -->

		<div style="clear:both"></div>

	</div> <!-- Fin de la zone de Une -->
<?php get_footer(); ?>
