<?php
/**
 * Scripts
 *
 * @package     EDD\User_Profiles\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Load frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function edd_user_profiles_scripts( $hook ) {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    wp_enqueue_script( 'edd-user-profiles', EDD_USER_PROFILES_URL . '/assets/js/edd-user-profiles' . $suffix . '.js', array( 'jquery' ), true );
    wp_enqueue_style( 'edd-user-profiles', EDD_USER_PROFILES_URL . '/assets/css/edd-user-profiles' . $suffix . '.css' );
}
add_action( 'wp_enqueue_scripts', 'edd_user_profiles_scripts', 100 );