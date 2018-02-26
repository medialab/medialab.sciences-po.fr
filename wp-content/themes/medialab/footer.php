<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Medialab
 * @since Medialab 2.0
 */
?>
<footer class="container_12" id="mainfooter">
	
	<div class="grid_3">
		<?php wp_nav_menu('footer');?>
	</div>
	
	<div class="grid_3">
		<a href="http://www.sciences-po.fr">Sciences Po's website</a><br />
           <a href="http://www.sciences-po.fr/recherche">Research in Sciences Po </a> <br />
           <a href="http://medialab.sciences-po.fr/controversies">Controversies websites</a> <br />
           <a href="http://www.mappingcontroversies.net/">The MACOSPOL project</a> <br />
	</div>
	
	<div class="grid_3">
		<p>Project financed by</p>
		<img src="<?php echo get_template_directory_uri(); ?>/img/idf_blanc.png" alt="The logo of the medialab" />
	</div>
	
	<div class="grid_3">
		<img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="The logo of the medialab" />
		<p>Science Po - Medialab <br />
		13 rue de l'Université<br />
		75007 Paris, FRANCE</p>
	</div>
	
</footer>

<?php wp_footer(); ?>

</body>
</html>