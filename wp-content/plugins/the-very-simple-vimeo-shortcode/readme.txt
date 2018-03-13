=== Plugin Name ===
Contributors: yourlocalwebmaster
Donate link: http://yourlocalwebmaster.com/donate
Tags: vimeo, video, shortcode, vimeo shortcode, simple video plugin
Requires at least: 2.0.2
Tested up to: 4.3
Stable tag: 2.9.1 trunk

A very simple shortcode to add your Vimeo videos into your WordPress website!

== Description ==

A simple shortcode to add your Vimeo videos into your website! This plugin will enable you to use shortcodes to insert your videos from Vimeo. It also allows height, width and class attributes for further customization! Websites are always affordable from <a href="http://www.yourlocalwebmaster.com">YourLocalWebmaster.com</a>

== Installation ==

1. To install very simple wordpress plugin, simply download it and upload it to the Plugins menu.
Or, Install it via the "search" feature on the plugin page. "Simple Vimeo Shortcode"

2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Why aren't my height and width attributes working? =

Perhaps you are not aware, but the attributes "height" and "width" are case sensitive. Please remember to use all lowercase letters when adding attributes.

= Are there any other available attributes? =

The Current Available Attributes are:

-height (sets height of video)
-width (sets width of video)
-class (adds a custom css class to your video)
-portrait (when false(default), removes the image from the top left of video)
-byline (when false(default), removes "From #####"  in the title
-autoplay (when true, plays the video automatically) (default: false)
-title (when false, removes the title from the video)
= Why does my video look disproportioned? =

When setting height and width attributes, they should maintain the same aspect ration of the original video..

i.e. height="250" width="400", or 1 : 1.6

= How do I enable AutoPlay? =

Autoplay was added in version 2.5. Make sure your current plugin version is > or = 2.5.  IF it is, simply add the attribute "autoplay=true" to the shortcode.

i.e. [ylwm_vimeo autoplay="true"]1234567[/ylwm_vimeo]

== Screenshots ==


== Changelog ==

= 1.0 =
Added height and width attributes

= 1.5 =
Added class attributes

= 2.0 =
Added "portrait" and "byline" attributes. (Inspired by Tim Golden <http://www.timgolden.com>)

= 2.9 =
Added "title" option.  Remove the title of the video by adding the attribute  "title=0" to the shortcode.

= 2.9.1 =
Added documentation for "title" option.  Remove the title of the video by adding the attribute  "title=0" to the shortcode.

== Upgrade Notice ==

== Usage Notes ==

**To use the plugin, simply install it, activate it and then call the shortcode within your page or post content.**

*[ylwm_vimeo]VIDEO_ID[/ylwm_vimeo]*

Where  VIDEO_ID is the id of the vimeo video.

To utilize the attributes, add height, width, byline, portrait and/or class to the shortcode.

*[ylwm_vimeo height="400" width="600" class="MYCUSTOMCLASS" portrait="false" byline="false"]VIDEO_ID[/ylwm_vimeo]*

*Auto Play*

Where  VIDEO_ID is the id of the vimeo video.

[ylwm_vimeo autoplay="true"]VIDEO_ID[/ylwm_vimeo]