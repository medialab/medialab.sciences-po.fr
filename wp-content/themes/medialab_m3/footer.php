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
<div class="bg-container" id="footer-transfert">
</div>
	<div class="center">
		<footer>
			<div class="f-column">
				<?php wp_nav_menu('footer');?>
			</div>
			<div class="f-column">
				<ul>
					<li><a href="#">Research at Sciences Po</a></li>
					<li><a href="#">Sciences Po's website</a></li>
					<li><a href="#">Controversies website</a></li>
					<li><a href="#">The MACOSPOL project</a></li>
				</ul>
			</div>
			<div class="f-column">
				<p>Project supported by</p>
				<img src="<?php echo get_template_directory_uri(); ?>/img/idf-logo.png" alt="La région Ile de France" />
			</div>
			<div class="f-column">
				<img src="<?php echo get_template_directory_uri(); ?>/img/logo-small.jpg" alt="Sciences Po - Médialab" />
				<p>Sciences Po | médialab<br />
				13 rue de l'Université<br />
				75007 Paris, France</p>
			</div>
		</footer>
		<?php wp_footer(); ?>
	</div>
</body>
</html>