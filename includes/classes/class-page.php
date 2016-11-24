<?php
/**
 * EDD User Profiles_Page
 *
 * @package EDD\User_Profiles\Page
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * EDD_User_Profiles_Page
 *
 * This class creates the user profile page.
 *
 * @since 1.0.0
 * @access public
 */
class EDD_User_Profiles_Page {
    /**
     * Frontend User Profiles Page Actions and Filters.
     *
     * Registers actions and filters used to make the user profile pages.
     *
     * @since 2.0.0
     * @access public
     *
     * @return void
     */
    public function __construct() {
        add_action( 'the_content', array( $this, 'content' ), 10 );
        add_filter( 'init', array( $this, 'add_rewrite_rules' ),0 );
        add_filter( 'query_vars', array( $this, 'query_vars' ), 0 );
        add_filter( 'the_title',  array( $this, 'change_the_title' ), 11, 2 );
        add_action( 'save_post', array( $this, 'user_page_updated' ), 10, 1 );
        add_action( 'admin_init', array( $this, 'after_user_page_update' ), 10 );
        add_filter( 'wp_title',  array( $this, 'wp_title' ), 11, 1 );
        add_filter( 'pre_get_document_title',  array( $this, 'wp_title' ), 11, 1 );
    }

    /**
     * Frontend User Profiles Page content
     *
     * Creates the content shown on the user profile page.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $content Content of the page/post being rendered.
     * @return string Content to display on page.
     */
    public function content( $content ) {

        $has_shortcode = false;

        if ( function_exists( 'has_shortcode' ) ) {
            $has_shortcode = has_shortcode( $content, 'edd_user_profile' );
        }

        if ( $this->get_queried_user() && ! $has_shortcode ) {
            return do_shortcode( '[edd_user_profile]' );
        } else {
            return $content;
        }

    }

    /**
     * Frontend User Profiles Page Query Vars.
     *
     * Registers the user query arg for use in making the user profile page.
     *
     * @since 2.0.0
     * @access public
     *
     * @param array $query_vars Query vars already registered.
     * @return array Query vars registered in WordPress.
     */
    public function query_vars( $query_vars ) {
        $query_vars[] = 'user';

        return $query_vars;
    }

    /**
     * Frontend User Profiles Page Rewrite Rules.
     *
     * Makes the rewrite rules to make pretty permalinks for the user profile pages.
     *
     * @since 2.0.0
     * @access public
     *
     * @return void
     */
    public function add_rewrite_rules() {

        if ( ! edd_get_option( 'edd_user_profiles_page', false ) ) {
            return;
        }

        $page_id   = edd_get_option( 'edd_user_profiles_page', false );
        $page      = get_page( $page_id );
        $page_name = ! empty( $page->post_name ) ? $page->post_name : __( 'User', 'edd-user-profiles' );
        $url 	   = untrailingslashit( $page_name );
        /**
         * Frontend User Profiles Page URL.
         *
         * Adjusts the default permalink to user profile page.
         *
         * @since 2.0.0
         *
         * @param  string $url Default vendor url.
         */
        $permalink = apply_filters( 'user_profiles_adjust_user_url', $url );

        // Remove beginning slash
        if ( substr( $permalink, 0, 1 ) == '/' ) {
            $permalink = substr( $permalink, 1, strlen( $permalink ) );
        }

        add_rewrite_rule("{$page_name}/([^/]+)/page/?([2-9][0-9]*)", "index.php?page_id={$page_id}&user=\$matches[1]&paged=\$matches[2]", 'top');
        add_rewrite_rule("{$page_name}/([^/]+)", "index.php?page_id={$page_id}&user=\$matches[1]", 'top');
    }

    /**
     * Retrieves the currently displayed vendor.
     *
     * This is used when display a user's profile page.
     *
     * @since 2.2.10
     * @access public
     *
     * @global $wp_query WP_Query Check to make sure the query object is an object, else return.
     * @return object|false WP User Object or false.
     */
    public function get_queried_user() {

        global $wp_query;

        if( ! is_object( $wp_query ) ) {
            return false;
        }

        $user   = false;
        $requested_user = get_query_var( 'user' );

        if ( ! empty( $requested_user ) ) {
            if ( is_numeric( $requested_user ) ) {
                $user = get_userdata( absint( $requested_user ) );
            } else {
                $user = get_user_by( 'slug', $requested_user );
            }
        }
        return $user;
    }

    /**
     * Frontend User Profiles Page Title.
     *
     * Changes the title of the user profile page to a custom title.
     *
     * @since 2.0.0
     * @access public
     *
     * @param  string $title Existing title of page.
     * @param  int $id Post id of page.
     * @return string New title of page.
     */
    public function change_the_title( $title, $id = null ) {
        $user_page = edd_get_option( 'edd_user_profiles_page', false );
        if ( ! is_page( $user_page ) || $id != $user_page || is_admin() ) {
            // if this is not an user profile page
            return $title;
        } else {
            $user = $this->get_queried_user();
            if ( ! $user ) {
                $title = __( 'Users', 'edd-user-profiles' );
            } else {
                $title = $user->display_name;
            }

            $id    = !empty( $user->ID ) ? $user->ID : 0;
            /**
             * User Profile Page Title.
             *
             * Adjusts the default title to the vendor shop page.
             *
             * @since 2.0.0
             *
             * @param  string $title User display name.
             * @param  int $id User ID.
             */
            $title = apply_filters( 'user_profiles_change_the_title', $title , $id );
            remove_filter( 'the_title', array( $this, 'change_the_title' ) );
            return $title;
        }
    }

    public function wp_title( $title ) {
        $user_page = edd_get_option( 'edd_user_profiles_page', false );
        if ( ! is_page( $user_page ) || is_admin() ) {
            // if this is not the vendor page
            return $title;
        } else {
            $user = $this->get_queried_user();
            if ( ! $user ) {
                $title = __( 'Users', 'edd-user-profiles' );;
            } else {
                $title = $user->display_name;
            }
            remove_filter( 'wp_title', array( $this, 'wp_title' ) );
            return $title;
        }
    }

    /**
     * User Profile Page Updated.
     *
     * When the user profile page is updated, refresh the rewrite rules.
     *
     * @since 2.0.0
     * @access public
     *
     * @param  int $post_id Post id of page.
     * @return int Post id of page.
     */
    public function user_page_updated( $post_id ) {

        if ( ! edd_get_option( 'edd_user_profiles_page', false ) ) {
            return;
        }

        $page_id = edd_get_option( 'edd_user_profiles_page', false );

        if ( (int) $page_id !== (int) $post_id ) {
            return;
        }

        $this->add_rewrite_rules();

        // Set an option so we know to flush the rewrites at the next admin_init
        add_option( 'edd_user_profiles_permalinks_updated', 1, '', 'no' );

        return $post_id;
    }

    /**
     * After User Profile Page Updated.
     *
     * When the vendor page is updated, refresh the
     * rewrite rules.
     *
     * @since 2.0.0
     * @access public
     *
     * @return void
     */
    public function after_user_page_update() {
        $updated = get_option( 'edd_user_profiles_permalinks_updated' );

        if ( empty( $updated ) ) {
            return;
        }

        flush_rewrite_rules();

        delete_option( 'edd_user_profiles_permalinks_updated' );
    }
}
