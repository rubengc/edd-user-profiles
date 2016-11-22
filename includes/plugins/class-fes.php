<?php
/**
 * EDD User Profiles_FES
 *
 * @package EDD\User_Profiles\FES
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * EDD_User_Profiles_FES
 *
 * This class adds new functionalities if EDD FES is active
 *
 * @since 1.0.0
 * @access public
 */
class EDD_User_Profiles_FES {

    public function __construct() {
        // Extends EDD User Profiles settings
        add_filter( 'edd_user_profiles_settings', array( $this, 'settings' ) );

        // Redirects from FES vendor store to vendor user profile
        add_filter( 'template_redirect', array( $this, 'vendor_store_redirect' ) );

        // Overrides FES vendor store URL
        add_filter( 'fes_adjust_vendor_url', array( $this, 'adjust_vendor_url' ) );
    }

    /**
     * Adds FES based settings to EDD User Profiles settings
     *
     * @since       1.0.0
     * @return      array
     */
    public function settings( $edd_user_profiles_settings ) {
        $edd_user_profiles_settings[] = array(
            'id'    => 'edd_user_profiles_fes_settings',
            'name'  => '<strong>' . __( 'FES settings', 'edd-user-profiles' ) . '</strong>',
            'desc'  => __( 'Configure how EDD User Profiles will work with FES', 'edd-user-profiles' ),
            'type'  => 'header',
        );

        $edd_user_profiles_settings['edd_user_profiles_redirect_fes_vendor_shop'] = array(
            'id'          => 'edd_user_profiles_redirect_fes_vendor_shop',
            'name'        => __( 'Redirect FES vendor store', 'edd-user-profiles' ),
            'desc'        => __( 'Checking this option will redirect users that access to FES vendor store to the vendor user profile page.', 'edd-user-profiles' ),
            'type'        => 'checkbox',
        );

        $edd_user_profiles_settings['edd_user_profiles_override_fes_url'] = array(
            'id'          => 'edd_user_profiles_override_fes_url',
            'name'        => __( 'Override FES vendor store URL', 'edd-user-profiles' ),
            'desc'        => __( 'Checking this option will override FES vendor store URL to the user profile page.', 'edd-user-profiles' ),
            'type'        => 'checkbox',
        );

        return $edd_user_profiles_settings;
    }

    /**
     * Redirects from FES vendor store to vendor user profile page if admin optin it
     *
     * @since       1.0.0
     * @return      void
     */
    public function vendor_store_redirect() {
        if ( edd_get_option( 'edd_user_profiles_page', false ) && edd_get_option( 'edd_user_profiles_redirect_fes_vendor_shop', false ) ) {
            $fes_page_id = EDD_FES()->helper->get_option( 'fes-vendor-page', false );

            if( $fes_page_id && $fes_page_id == get_the_ID() ) {
                $user = EDD_FES()->vendor_shop->get_queried_vendor();

                if( $user ) {
                    wp_redirect( edd_user_profiles()->get_user_profile_url( $user->ID ) );
                    exit;
                }
            }
        }
    }

    /**
     * Overrides FES vendor store URL if admin optin it
     *
     * @since       1.0.0
     * @param       string $permalink Default FES base permalink
     * @return      string The modified base permalink or FES default
     */
    public function adjust_vendor_url( $permalink ) {
        if ( ! edd_get_option( 'edd_user_profiles_page', false ) || ! edd_get_option( 'edd_user_profiles_override_fes_url', false ) ) {
            return $permalink;
        }

        $page_id   = edd_get_option( 'edd_user_profiles_page', false );
        $page      = get_page( $page_id );
        $page_name = ! empty( $page->post_name ) ? $page->post_name : __( 'User', 'edd-user-profiles' );

        return untrailingslashit( $page_name );
    }
}