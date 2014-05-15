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
		<!-- div class="f-column" id="f-menu">
			<?php wp_nav_menu (array ( 'theme_location' => 'footer-menu') ); ?>
		</div -->
		<!-- div style="float:right; padding-right:10px; padding-left:10px; padding-top:6px" ><img  src="<?php echo get_template_directory_uri(); ?>/img/idf-logo.png" alt="La région Ile de France" /></div>

		<div style="float:right; font-size:12px; padding-left:15px; line-height:40px; height:40px;">
			Project supported by 
		</div -->

		<div style="float:left; width:50%; font-size:12px; padding-left:15px; height:40px; line-height:40px">
			Sciences Po | médialab &bull; 27 rue St Guillaume &bull;
			75337 Paris Cedex 07
		</div>
		<div style="float:right; font-size:12px; padding-right:15px; height:40px; line-height:40px">
			<a href="http://www.medialab.sciences-po.fr/feed/">
				<img  src="<?php echo get_template_directory_uri(); ?>/img/rss.jpg" alt="Flux RSS du médialab"/>
			</a>
		</div>
		<div style="clear:both"></div>
		<div style="float:left; font-size:9px; padding-left:15px; height:40px; line-height:40px">
			Les contenus de ce site web, sauf mention contraire, sont placés sous licence Creative Commons <a href="https://creativecommons.org/licenses/by/3.0/fr/legalcode">CC-BY</a>.
		</div>
		<div class="allungato">
			<?php dynamic_sidebar('footer-sidebar'); ?> 
			<div style="clear:both"></div>
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