=== EDD User Profiles ===
Contributors: rubengc
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=64N6CERD8LPZN
Tags: easy digital downloads, digital, download, downloads, edd, rubengc, user, users, profile, profiles, widget, widgets, e-commerce
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Frontend user profiles for Easy Digital Downloads

== Description ==
This plugin requires [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/ "Easy Digital Downloads").

Once activated, EDD User Profiles will add a frontend user profile.

On [edd_profile_editor] will add two new fields: avatar and description

EDD User Profiles automatically adds tabs based on other EDD plugins. Current supported plugins are:

1. EDD FES
1. EDD Wish Lists
1. EDD Downloads Lists

Includes toggleable options to override Wordpress author url (to use the user profile url instead) and EDD FES vendor shop url.

Also has support SEO by Yoast plugin.

There's a [GIT repository](https://github.com/rubengc/edd-user-profiles) too if you want to contribute a patch.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin
1. That's it!

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

== Frequently Asked Questions ==

= How can I customize a tab output?  =

First you need default action hook to desired content customization available hooks are:

1. edd_user_profiles_load_downloads_tab_content
1. edd_user_profiles_load_wish_lists_tab_content
1. edd_user_profiles_load_favorite_list_tab_content
1. edd_user_profiles_load_like_list_tab_content
1. edd_user_profiles_load_recommend_list_tab_content

Example of custom downloads tab content:

``
remove_action( 'edd_user_profiles_load_downloads_tab_content', 'edd_user_profiles_load_downloads_tab_content' );

function custom_downloads_tab_content( $user_id ) {
    // Custom code here
}
add_action( 'edd_user_profiles_load_downloads_tab_content', 'custom_downloads_tab_content' );
``

== Screenshots ==

1. Screenshot from [edd_profile_editor]

2. Screenshot from user profile page (Downloads tab)

3. Screenshot from user profile page (Wish lists tab)

4. Screenshot from user profile page (Likes tab)

5. Screenshot from EDD settings page

== Upgrade Notice ==

== Changelog ==

= 1.0 =
* Initial release