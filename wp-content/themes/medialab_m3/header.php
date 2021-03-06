<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Wordpress
 * @subpackage Medialab m3
 * @since Medialab 2.0
 */
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width" />
	<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'medialab' ), max( $paged, $page ) );

	?></title>
    <!-- Stylesheets -->
	<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />
	<link rel="stylesheet/less" href="<?php echo get_template_directory_uri(); ?>/style.less" type="text/css" media="screen" />
	
	<!-- link rel="stylesheet" href="fluid.css" type="text/css" media="screen" / -->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,700' rel='stylesheet' type='text/css'>

	<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-1.8.js" type="text/javascript"></script>
    <!-- Scripts -->
	<script src="<?php echo get_template_directory_uri(); ?>/js/less-1.3.0.min.js" type="text/javascript"></script>
	<!--[if lt IE 9]><script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/blocksit.min.js"></script>
	<script>
	$(document).ready(function() {
		$('#pin_like').BlocksIt({
			numOfCol: 3,
			offsetX: 8,
			offsetY: 8,
			blockElement: '.column_display'
		});
		$('#pin_like_searched').BlocksIt({
			numOfCol: 4,
			offsetX: 4,
			offsetY: 4,
			blockElement: '.column_display'
		});
	});
	</script>
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>
<body>
<div class="center">
	<!-- Header -->
	<header id="top">
		<div id="logo">
			<a href="<?php bloginfo( 'url' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/logo.jpg" alt="Science Po | Medialab" /></a>
		</div>
		<div id="searchbar">
			<?php get_search_form(); ?>
		</div>
	</header>
</div>
<div class="bg-container">
	<div class="center">
	<!-- Breadcrumbs or RSS on homepage -->
		<div id="breadcrumbs">
			<a href="<?php bloginfo('rss2_url'); ?>">
				<img src="<?php echo get_template_directory_uri(); ?>/img/rss.jpg" alt="Subscribe to the RSS feed" />
				<span>Subscribe to the Medialab's RSS feed</span>
			</a>
		</div>
		<!-- Main navigation -->
		<nav id="menu">
			<?php wp_nav_menu('primary');?>
		</nav>
	</div>
</div>