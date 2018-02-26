<?php // ACTIVATES POSTS THUMBNAILS ON HOMEPAGE ?>
<?php add_theme_support( 'post-thumbnails' );?>
<?php // SIZE OF POSTS THUMBNAILS ON HOMEPAGE ?>
<?php // set_post_thumbnail_size( 220, 146, true );?>
<?php // ACTIVATES MENUS ?>
<?php add_theme_support('menus');?>
<?php register_nav_menus( array(   // enregistrement des menus
'primary' => 'Menu principal',      // ici mon menu principal
'footer' => 'Menu en pied de page',    // ici mon menu en pied de page
    ) );?>
<?php // ACTIVATES SIDEBARS ?>
<?php if ( function_exists('register_sidebar') )
register_sidebar(array('name'=>'footer-sidebar',
'before_widget' => '<aside class="sidebar-footer">',
'after_widget' => '</aside>',
'before_title' => '<h5>',
'after_title' => '</h5>',
));
?>
<?php //GET FIRST IMAGE OF A POST (ZONE EDITO) ?>
<?php
function catch_that_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches [1] [0];

  if(empty($first_img)){ //Defines a default image
    $first_img = "/images/default.jpg";
  }
  return $first_img;
}
?>
<?php // INCLUDE ALL POSTS TYPES IN FEED ?>
<?php 
function myfeed_request($qv) {
	if (isset($qv['feed']))
		$qv['post_type'] = get_post_types();
	return $qv;
}
add_filter('request', 'myfeed_request');
?>
<?php // EXCLUDE FIRST IMAGE OF A POST ?>
<?php
function clear_first_image($content) {
	// checks if there is a first image
	$postst = strpos($content, '<img');
	if($postst !== false):
		// #letz check if the image has a link and remove it
		$output = preg_match_all('/<a.+href=[\'"]([^\'"]+)[\'"].*><img.+src/i', $content, $matches);
		$link_img = $matches [1] [0];
		if($link_img!=""):
			// we get the first link 'begin' tag
			$lnkini = strpos($content , '<a href', 0);
			// then we get the first link 'close' tag
			$lnkend = strpos($content, '<img', $lnkini);
			// letz get the hole first link tag now
			$replnk = substr($content, $lnkini, ($lnkend-$lnkini));
			// now we replace the first link tag in the content
			$tmpctd = preg_replace("'".$replnk."'",'',$content);
			// letz clear the link tag close
			$lnkcld = strpos($content , '</a>', 0);
			$content = substr($tmpctd, 0, $lnkcld).substr($tmpctd, $lnkcld, strlen($tmpctd));
		endif;
		// #removing the first image
			// we get the first image 'open' tag
			$posini = strpos($content , '<img', 0);
			// then we get the first image 'close' tag
			$posend = strpos($content, '>', $posini);
			// letz get the hole first image tag now
			$repimg = substr($content, $posini, ($posend-$posini)+1);
			// now we replace the first image tag in the content
			$postOutput = preg_replace("'".$repimg."'",'',$content);
	else:
		$postOutput=$content;
	endif;
	return $postOutput;
}
?>
<?php //FUNCTION FOR BREADCRUMBS ?>
<?php
function myget_category_parents($id, $link = false,$separator = '/',$nicename = false,$visited = array()) {
  $chain = '';$parent = &get_category($id);
    if (is_wp_error($parent))return $parent;
    if ($nicename)$name = $parent->name;
    else $name = $parent->cat_name;
    if ($parent->parent && ($parent->parent != $parent->term_id ) && !in_array($parent->parent, $visited)) {
        $visited[] = $parent->parent;$chain .= myget_category_parents( $parent->parent, $link, $separator, $nicename, $visited );}
    if ($link) $chain .= '<span typeof="v:Breadcrumb"><a href="' . get_category_link( $parent->term_id ) . '" title="Voir tous les articles de '.$parent->cat_name.'" rel="v:url" property="v:title">'.$name.'</a></span>' . $separator;
    else $chain .= $name.$separator;
    return $chain;}
function mybread() {
  global $wp_query;$ped=get_query_var('paged');$rendu = '<div xmlns:v="http://rdf.data-vocabulary.org/#">';   
  if ( !is_home() ) {$rendu .= '<span typeof="v:Breadcrumb"><a title="'. get_bloginfo('name') .'" id="breadh" href="'.home_url().'" rel="v:url" property="v:title">'. get_bloginfo('name') .'</a></span>';}
  elseif ( is_home() ) {$rendu .= '<span id="breadex">Vous &ecirc;tes ici :</span> <span typeof="v:Breadcrumb">Accueil de '. get_bloginfo('name') .'</span>';}
  if ( is_category() ) {
    $cat_obj = $wp_query->get_queried_object();$thisCat = $cat_obj->term_id;$thisCat = get_category($thisCat);$parentCat = get_category($thisCat->parent);
    if ($thisCat->parent != 0) $rendu .= " &raquo; ".myget_category_parents($parentCat, true, " &raquo; ", true);
    if ($thisCat->parent == 0) {$rendu .= " &raquo; ";}
    if ( $ped <= 1 ) {$rendu .= single_cat_title("", false);}
    elseif ( $ped > 1 ) {
      $rendu .= '<span typeof="v:Breadcrumb"><a href="' . get_category_link( $thisCat ) . '" title="Voir tous les articles de '.single_cat_title("", false).'" rel="v:url" property="v:title">'.single_cat_title("", false).'</a></span>';}}
  elseif ( is_author()){
    global $author;$user_info = get_userdata($author);$rendu .= " &raquo; Articles de l'auteur ".$user_info->display_name."</span>";}  
  elseif ( is_tag()){
    $tag=single_tag_title("",FALSE);$rendu .= " &raquo; Articles sur le th&egrave;me <span>".$tag."</span>";}
    elseif ( is_date() ) { 
        if ( is_day() ) {
            global $wp_locale;
            $rendu .= '<span typeof="v:Breadcrumb"><a href="'.get_month_link( get_query_var('year'), get_query_var('monthnum') ).'" rel="v:url" property="v:title">'.$wp_locale->get_month( get_query_var('monthnum') ).' '.get_query_var('year').'</a></span> ';
            $rendu .= " &raquo; Archives pour ".get_the_date();} 
    else if ( is_month() ) {
            $rendu .= " &raquo; Archives pour ".single_month_title(' ',false);}
    else if ( is_year() ) {
            $rendu .= " &raquo; Archives pour ".get_query_var('year');}}
  elseif ( is_archive() && !is_category()){
        $posttype = get_post_type();
    $tata = get_post_type_object( $posttype );
    $var = '';
    $the_tax = get_taxonomy( get_query_var( 'taxonomy' ) );
    $titrearchive = $tata->labels->menu_name;
    if (!empty($the_tax)){$var = $the_tax->labels->name.' ';}
        if (empty($the_tax)){$var = $titrearchive;}
    $rendu .= ' &raquo; Archives sur "'.$var.'"';}
  elseif ( is_search()) {
    $rendu .= " &raquo; R&eacute;sultats de votre recherche <span>&raquo; ".get_search_query()."</span>";}
  elseif ( is_404()){
    $rendu .= " &raquo; 404 Page non trouv&eacute;e";}
  elseif ( is_single()){
    $category = get_the_category();
    $category_id = get_cat_ID( $category[0]->cat_name );
    if ($category_id != 0) {
      $rendu .= " &raquo; ".myget_category_parents($category_id,TRUE,' &raquo; ')."<span>".the_title('','',FALSE)."</span>";}
    elseif ($category_id == 0) {
        $post_type = get_post_type();
      $tata = get_post_type_object( $post_type );
        $titrearchive = $tata->labels->menu_name;
        $urlarchive = get_post_type_archive_link( $post_type );
      $rendu .= ' &raquo; <span typeof="v:Breadcrumb"><a class="breadl" href="'.$urlarchive.'" title="'.$titrearchive.'" rel="v:url" property="v:title">'.$titrearchive.'</a></span> &raquo; <span>'.the_title('','',FALSE).'</span>';}}
  elseif ( is_page()) {
    $post = $wp_query->get_queried_object();
    if ( $post->post_parent == 0 ){$rendu .= " &raquo; ".the_title('','',FALSE)."";}
    elseif ( $post->post_parent != 0 ) {
      $title = the_title('','',FALSE);$ancestors = array_reverse(get_post_ancestors($post->ID));array_push($ancestors, $post->ID);
      foreach ( $ancestors as $ancestor ){
        if( $ancestor != end($ancestors) ){$rendu .= '&raquo; <span typeof="v:Breadcrumb"><a href="'. get_permalink($ancestor) .'" rel="v:url" property="v:title">'. strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) .'</a></span>';}
        else {$rendu .= ' &raquo; '.strip_tags(apply_filters('single_post_title',get_the_title($ancestor))).'';}}}}
  if ( $ped >= 1 ) {$rendu .= ' (Page '.$ped.')';}
  $rendu .= '</div>';
  echo $rendu;}
?>
<?php // FUNCTIONS SORT JSON DATAS ?>
<?php
	function get_json_object($type){
		global $post;
		switch ($type){
			case "tool" :
			$url = "http://lrrr.medialab.sciences-po.fr/tools/";
			break;
			case "publication" :
			$url = "http://lrrr.medialab.sciences-po.fr/publications/";
			break;
		}
		$type_url = $type."_id";
		$object_id = get_post_meta($post->ID, $type_url, true);	
		$metas = json_decode(file_get_contents($url.$object_id."/meta.json", 0, null, null));
		return $metas;
	}
	function get_object_name($metas){
		echo $metas->name;
	}
	function get_object_image($metas){
		echo $metas->visual;
	}
?>
<?php // FUNCTION GET SLUGS OF OBJECTS FOR FACETS ?>
<?php 
	function get_object_terms($taxo){
		$terms = get_the_terms($post->ID, $taxo); // get an array of all the terms as objects.		
		$terms_slugs = array();
		if($terms == NULL){
			
		}else{
			foreach( $terms as $term ) {
				$terms_slugs[] = $term->slug; // save the slugs in an array
			}
			echo $term->slug; 
		}	
	}
?>
<?php //FUNCTION TO REMOVE P TAG AROUND IMAGES OF THE_CONTENT() ?>
<?php
function filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

add_filter('the_content', 'filter_ptags_on_images');
?>