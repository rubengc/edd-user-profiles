<?php
/**
 * Shortcodes
 *
 * @package     EDD\User_Profile\Shortcodes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * EDD User Profile shortcode
 *
 * @since  1.0
 * @param $atts
 * @param null $content
 * @return mixed|null|void
 */
function edd_user_profile_shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
            'id' => '',
            'title' => '',
        ), $atts, 'edd_user_profile' )
    );

    $user = edd_user_profiles()->page->get_queried_user();

    // Prevents list from displaying if it's private
    if ( ! $user )
        return;

    ob_start();

    do_action( 'edd_user_profile_before' );

    edd_get_template_part( 'shortcode', 'edd-user-profile' );

    do_action( 'edd_user_profile_after' );

    $content = ob_get_clean();

    return $content;
}
add_shortcode( 'edd_user_profile', 'edd_user_profile_shortcode' );