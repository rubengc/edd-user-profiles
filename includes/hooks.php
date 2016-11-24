<?php
/**
 * Hooks
 *
 * @package     EDD\User_Profiles\Hooks
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides Wordpress comment author URL if admin optin it
 *
 * @since       1.0.0
 * @param       string     $url        The comment author's URL.
 * @param       int        $comment_ID The comment ID.
 * @param       WP_Comment $comment    The comment object.
 * @return      string EDD User Profiles URL
 */
function edd_user_profiles_comment_author_url( $url, $comment_ID, $comment ) {
    if ( ! edd_get_option( 'edd_user_profiles_page', false ) || ! edd_get_option( 'edd_user_profiles_override_author_url', false ) ) {
        return $url;
    }

    return edd_user_profiles()->get_user_profile_url( $comment->user_id );
}
add_filter( 'get_comment_author_url', 'edd_user_profiles_comment_author_url', 10, 3 );

/**
 * Add EDD User Profiles templates dir to EDD template paths
 *
 * @since       1.0.0
 * @param       array $file_paths EDD default template paths
 * @return      array $file_paths
 */
function edd_user_profiles_template_paths( $file_paths ) {

    $file_paths[1000] = EDD_USER_PROFILES_DIR . 'templates';

    return $file_paths;
}
add_filter( 'edd_template_paths', 'edd_user_profiles_template_paths' );