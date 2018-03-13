<?php // ACTIVATES POSTS THUMBNAILS ON HOMEPAGE ?>
<?php add_theme_support( 'post-thumbnails' );?>
<?php // SIZE OF POSTS THUMBNAILS ON HOMEPAGE ?>
<?php // set_post_thumbnail_size( 220, 146, true );?>
<?php // ACTIVATES MENUS ?>
<?php add_action( 'init', 'register_my_menus' );

error_reporting(E_ERROR | E_PARSE);

add_image_size('thumb-zone-1',9999,400);
add_image_size('thumb-zone-2',9999,190,True);
add_image_size('thumb-zone-3-4',9999,200);
add_image_size('s_post',690,200,True);

function register_my_menus() {
	register_nav_menus(
		array(
			'primary-menu' => __( 'Primary Menu' ),
			'footer-menu' => __( 'Footer Menu' )
		)
	);
}
function echo_shorten_excerpt($excerpt, $length) {
	echo get_shorten_excerpt($excerpt, $length);
}
function get_shorten_excerpt($excerpt, $length) {
	if (strlen($excerpt) > $length) {
		$excerpt = substr($excerpt, 0, $length);
		$excerpt = preg_replace('/[^\s\.]{2,}$/', '...', $excerpt);
	}
	return $excerpt;
} ?>
<?php // ACTIVATES SIDEBARS ?>
<?php if ( function_exists('register_sidebar') )
register_sidebar(array('name'=>'footer-sidebar',
'before_widget' => '<div class="f-column">',
'after_widget' => '</div>',
'before_title' => '<ul>',
'after_title' => '</ul>',
));
?>
<?php if ( function_exists('register_sidebar') )
register_sidebar(array('name'=>'main-sidebar',
'before_widget' => '<div class="sidebar">',
'after_widget' => '</div>',
'before_title' => '',
'after_title' => '',
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
  elseif ( is_home() ) {$rendu .= '<span id="breadex">You are here :</span> <span typeof="v:Breadcrumb">Home of '. get_bloginfo('name') .'</span>';}
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

	function get_tools_short_list($n) {
		$jsoncontext = stream_context_create(array('http'=>array('timeout' => 0.1)));
		$json_file = file_get_contents("http://tools.medialab.sciences-po.fr/index.json", 0, $jsoncontext, null);
		if( $json_file== "")
			$json_file=file_get_contents("index.json");

		$json = json_decode($json_file);
		$tools = array();
		//ksort($json);
		$ct = 0;
		foreach ($json as $key => $value) if (preg_match("/element/", $key)) {
			if (is_array($value)) {
				foreach ($value as $tool) {
					$tools[] = $tool;
					if ($ct > 4) break;
					$ct++;
				}
			} else $tools[] = $value;
			if ($ct > 4) break;
			$ct++;
		}
		return $tools;
	}
	function get_tool_metas($tool) {
		$jsoncontext = stream_context_create(array('http'=>array('timeout' => 0.1)));
		if ($tool) return json_decode(file_get_contents("http://tools.medialab.sciences-po.fr/".$tool."/meta.json", 0, $jsoncontext, null));
	}
	function get_json_object($type){
		global $post;
		switch ($type){
			case "tool" :
				$url = "http://tools.medialab.sciences-po.fr/";
			break;
			case "publication" :
				$url = "http://tools.medialab.sciences-po.fr/publications";
			break;
		}
		$type_url = $type."_id";
		$object_id = get_post_meta($post->ID, $type_url, true);
		$jsoncontext = stream_context_create(array('http'=>array('timeout' => 0.1)));
		$metas = json_decode(file_get_contents($url.$object_id."/meta.json", 0, $jsoncontext, null));
		return $metas;
	}
	function echo_the_tools($pid) {
		$tools = get_the_terms($pid, "tools");
		if ($tools) foreach ($tools as $tool) {
			$json = get_tool_metas($tool->name);
			if ($json) echo '<a href="http://tools.medialab.sciences-po.fr/#'.$tool->name.'">'.$json->name.'</a>';
		}
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
		$terms = get_the_terms($post->id, $taxo); // get an array of all the terms as objects.
		if($terms == NULL){
      return "";
    }else {
			$terms_slugs = array();
      foreach( $terms as $term ) {
        //if($post->post_name == get_the_terms($term->id,get_post_type($post->ID)))

				$terms_slugs[] = $term->slug; // save the slugs in an array
      }
			return implode(" ", $terms_slugs);
		}
	}
?>
<?php
  function get_objects_for_slug($object_type,$slugs_type,$slug){

    $objects = new WP_Query( array(
        'post_type' => $object_type,
        #'name' => $people_slugs,
        'tax_query' => array(
          array('taxonomy' => $slugs_type,'field' => 'slug','terms' => $slug)
            ),
         'order' => 'rand' ) );
      $objects_array=array();
      while ( $objects->have_posts() ) :
        $objects->next_post();
        $objects_array[]=$objects->post->post_name;
      endwhile;
      $objects_array=array_unique($objects_array);

      return implode(" ", $objects_array);
    }
?>
<?php //FUNCTION TO REMOVE P TAG AROUND IMAGES OF THE_CONTENT()
 // img unautop
function img_unautop($pee) {
    $pee = preg_replace('/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s', '<div class="figure">$1</div>', $pee);
    return $pee;
}
add_filter( 'the_content', 'img_unautop', 30 );


// // automatic terms in taxonomy creation
// function register_post_slug_in_taxonomy($post_id) {
//    $post = get_post($post_id);
//   if(!term_exists( $post->post_name, get_post_type($post) )) {
//     wp_insert_term(
//     $post->post_name, // the term
//     get_post_type($post), // the taxonomy
//     array(
//       'slug' => $post->post_name
//     ));
//   }
//   // else
//   // {
// //wp_update_term(1, 'category', array(
// //   'name' => 'Non Catégorisé',
// //   'slug' => 'non-categorise'
// // ));
//   // }
// }

// function delete_slug_on_delete_post($post_id) {
//   $post = get_post($post_id);

//    if(term_exists( $post->post_name, get_post_type($post))) {
//     $term= get_term_by( "slug", $post->post_name, get_post_type($post) );
//     wp_delete_term($term->id);
//   }
// }
// add_action( 'save_post', 'register_post_slug_in_taxonomy');;
// add_action('before_delete_post', 'delete_slug_on_delete_post');
?>
<?php
/**
 * Twenty Twelve functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

/**
 * Sets up theme defaults and registers the various WordPress features that
 * Twenty Twelve supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 * 	custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Twelve 1.0
 */

/**
 * Enqueues scripts and styles for front-end.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_scripts_styles() {
	global $wp_styles;

	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/*
	 * Adds JavaScript for handling the navigation menu hide-and-show behavior.
	 */
	// wp_enqueue_script( 'twentytwelve-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true );

	/*
	 * Loads our special font CSS file.
	 *
	 * The use of Open Sans by default is localized. For languages that use
	 * characters not supported by the font, the font can be disabled.
	 *
	 * To disable in a child theme, use wp_dequeue_style()
	 * function mytheme_dequeue_fonts() {
	 *     wp_dequeue_style( 'twentytwelve-fonts' );
	 * }
	 * add_action( 'wp_enqueue_scripts', 'mytheme_dequeue_fonts', 11 );
	 */

	/* translators: If there are characters in your language that are not supported
	   by Open Sans, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'twentytwelve' ) ) {
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Open Sans character subset specific to your language, translate
		   this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language. */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'twentytwelve' );

		if ( 'cyrillic' == $subset )
			$subsets .= ',cyrillic,cyrillic-ext';
		elseif ( 'greek' == $subset )
			$subsets .= ',greek,greek-ext';
		elseif ( 'vietnamese' == $subset )
			$subsets .= ',vietnamese';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => 'Open+Sans:400italic,700italic,400,700',
			'subset' => $subsets,
		);
		wp_enqueue_style( 'twentytwelve-fonts', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );
	}

	/*
	 * Loads our main stylesheet.
	 */
	wp_enqueue_style( 'twentytwelve-style', get_stylesheet_uri() );

	/*
	 * Loads the Internet Explorer specific stylesheet.
	 */
	wp_enqueue_style( 'twentytwelve-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentytwelve-style' ), '20121010' );
	$wp_styles->add_data( 'twentytwelve-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'twentytwelve_scripts_styles' );

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @since Twenty Twelve 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function twentytwelve_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'twentytwelve_wp_title', 10, 2 );

/**
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentytwelve_page_menu_args' );

/**
 * Registers our main widget area and the front page widget areas.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'twentytwelve' ),
		'id' => 'sidebar-1',
		'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'First Front Page Widget Area', 'twentytwelve' ),
		'id' => 'sidebar-2',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Second Front Page Widget Area', 'twentytwelve' ),
		'id' => 'sidebar-3',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'twentytwelve_widgets_init' );

if ( ! function_exists( 'twentytwelve_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_content_nav( $html_id ) {
	global $wp_query;

	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
			<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentytwelve' ) ); ?></div>
			<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}
endif;

if ( ! function_exists( 'twentytwelve_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentytwelve_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'twentytwelve' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite class="fn">%1$s %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author', 'twentytwelve' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'twentytwelve' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentytwelve' ); ?></p>
			<?php endif; ?>

			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'twentytwelve' ), '<p class="edit-link">', '</p>' ); ?>
			</section><!-- .comment-content -->

		<?php /*<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'twentytwelve' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply --> */ ?>
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

if ( ! function_exists( 'twentytwelve_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own twentytwelve_entry_meta() to override in a child theme.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'twentytwelve' ) );

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'twentytwelve' ) );

	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'twentytwelve' ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	if ( $tag_list ) {
		$utility_text = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', 'twentytwelve' );
	} elseif ( $categories_list ) {
		$utility_text = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', 'twentytwelve' );
	} else {
		$utility_text = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.', 'twentytwelve' );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}
endif;

/**
 * Extends the default WordPress body class to denote:
 * 1. Using a full-width layout, when no active widgets in the sidebar
 *    or full-width template.
 * 2. Front Page template: thumbnail in use and number of sidebars for
 *    widget areas.
 * 3. White or empty background color to change the layout and spacing.
 * 4. Custom fonts enabled.
 * 5. Single or multiple authors.
 *
 * @since Twenty Twelve 1.0
 *
 * @param array Existing class values.
 * @return array Filtered class values.
 */
function twentytwelve_body_class( $classes ) {
	$background_color = get_background_color();

	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'page-templates/full-width.php' ) )
		$classes[] = 'full-width';

	if ( is_page_template( 'page-templates/front-page.php' ) ) {
		$classes[] = 'template-front-page';
		if ( has_post_thumbnail() )
			$classes[] = 'has-post-thumbnail';
		if ( is_active_sidebar( 'sidebar-2' ) && is_active_sidebar( 'sidebar-3' ) )
			$classes[] = 'two-sidebars';
	}

	if ( empty( $background_color ) )
		$classes[] = 'custom-background-empty';
	elseif ( in_array( $background_color, array( 'fff', 'ffffff' ) ) )
		$classes[] = 'custom-background-white';

	// Enable custom font class only if the font CSS is queued to load.
	if ( wp_style_is( 'twentytwelve-fonts', 'queue' ) )
		$classes[] = 'custom-font-enabled';

	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	return $classes;
}
add_filter( 'body_class', 'twentytwelve_body_class' );

/**
 * Adjusts content_width value for full-width and single image attachment
 * templates, and when there are no active widgets in the sidebar.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_content_width() {
	if ( is_page_template( 'page-templates/full-width.php' ) || is_attachment() || ! is_active_sidebar( 'sidebar-1' ) ) {
		global $content_width;
		$content_width = 960;
	}
}
add_action( 'template_redirect', 'twentytwelve_content_width' );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @since Twenty Twelve 1.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function twentytwelve_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
}
add_action( 'customize_register', 'twentytwelve_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_customize_preview_js() {
	wp_enqueue_script( 'twentytwelve-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20120827', true );
}
add_action( 'customize_preview_init', 'twentytwelve_customize_preview_js' );
