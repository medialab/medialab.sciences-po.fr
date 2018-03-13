<?php
/*
Plugin Name: Simple Vimeo Shortcode
Plugin URI: http://www.yourlocalwebmaster.com/plugins/very-simple-vimeo-shortcode-plugin
Description: A simple shortcode to add your vimeo videos into site! Website are affordable from <a href="http://www.yourlocalwebmaster.com">www.YourLocalWebmaster.com</a>
Version: 2.9.1
Author: Grant Kimball
Author URI: http://www.YourLocalWebmaster.com/
License: GPL2
*/
?>
<?php
/*  Copyright 2012  Grant Kimball  (email : webmaster@yourlocalwebmaster.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php


function your_local_webmaster_vimeo_handler( $atts, $content=null, $code="" ) {
if(isset($atts['byline'])){
$byline = $atts['byline'];
}
else{
$byline = 0;
}
if(isset($atts['autoplay'])){
$auto_play = 'true';
}else{
$auto_play = 'false';
}
if(isset($atts['portrait'])){
$portrait = $atts['portrait'];
}
else{
$portrait = 0;
}

		if(isset($atts['width'])){
		$width = $atts['width'];
		}
else{
$width = "400";
}
		if(isset($atts['height'])){
		$height = $atts['height'];
		}
else{
$height = "225";
}
/*if(isset($atts['badge'])){
$badge = $atts['badge'];
}else{
$badge = 1;
}*/

if(isset($atts['title']) && ($atts['title'] == FALSE || $atts['title'] == 0 || strtolower($atts['title']) == 'no')){
	$title = 0;
}else{
	$title = 1;	
}

if(isset($atts['class'])){
$class=$atts['class'];
}
else{
$class="";
}
            $list = '<iframe src="http://player.vimeo.com/video/'.$content.'?byline='.$byline.'&portrait='.$portrait.'&title='.$title.'&autoplay='.$auto_play.'" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen class="'.$class.'"></iframe>';
           return $list;
}

add_shortcode( 'ylwm_vimeo', 'your_local_webmaster_vimeo_handler' );