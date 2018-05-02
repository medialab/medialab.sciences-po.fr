<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Medialab m3
 * @since Medialab 2.0
 */
?>
	
	<footer>
    <?php $locale = get_locale() ?>
    <?php $in_english = $locale == 'en_US' ?>
		<!-- div class="f-column" id="f-menu">
			<?php wp_nav_menu (array ( 'theme_location' => 'footer-menu') ); ?>
		</div -->
		<!-- div style="float:right; padding-right:10px; padding-left:10px; padding-top:6px" ><img  src="<?php echo get_template_directory_uri(); ?>/img/idf-logo.png" alt="La région Ile de France" /></div>

		<div style="float:right; font-size:12px; padding-left:15px; line-height:40px; height:40px;">
			Project supported by 
		</div -->

    <div class="footer-row">
      <div id="logo">
        <a href="<?php bloginfo( 'url' ); ?>"><img height="43" src="<?php bloginfo( 'url' ); ?>/wp-content/uploads/2016/04/logo.png" alt="Science Po | Medialab" /></a>
      </div>
      <?php /*dynamic_sidebar('footer-sidebar');*/ ?> 
      <nav id="menu">
        <?php /*wp_nav_menu('footer-menu');*/ ?>
        <ul class="menu">
          <li class="menu-item menu-item-type-post_type menu-item-object-page">
            <a href="/credits">
              <?php print $in_english ? 'Credits' : 'Crédits' ?>
            </a>
          </li>
          <li class="menu-item menu-item-type-post_type menu-item-object-page">
            <a href="/contact">
              <?php print $in_english ? 'Contact' : 'Contact' ?>
            </a>
          </li>

          
        </ul>
      </nav>

    </div>
    <div class="footer-row">
      <div class="medialab-adresse">
        27 rue St Guillaume 75337 Paris Cedex 07
      </div>

      <div class="medialab-info">
        <a href="https://github.com/medialab">
          <img  src="<?php echo get_template_directory_uri(); ?>/img/octochegevarra.png" alt="Compte github du médialab"/>
        </a>
        <a href="http://www.medialab.sciences-po.fr/feed/">
          <img  src="<?php echo get_template_directory_uri(); ?>/img/rss.png" alt="Flux RSS du médialab"/>
        </a>
        <a href="https://twitter.com/medialab_ScPo">
          <img  src="<?php echo get_template_directory_uri(); ?>/img/twitter.png" alt="Compte twitter du médialab"/>
        </a>
      </div>
    </div>

	</footer>
	<?php wp_footer(); ?>
</div>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-37695848-2']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</body>
</html>