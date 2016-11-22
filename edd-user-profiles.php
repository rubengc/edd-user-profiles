<?php
/**
 * Plugin Name:     EDD User Profiles
 * Plugin URI:      https://wordpress.org/plugins/edd-user-profiles/
 * Description:     Frontend user profiles for Easy Digital Downloads
 * Version:         1.0.0
 * Author:          rubengc
 * Author URI:      http://rubengc.com
 * Text Domain:     edd-user-profiles
 *
 * @package         EDD\User_Profiles
 * @author          rubengc
 * @copyright       Copyright (c) rubengc
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_User_Profiles' ) ) {

    /**
     * Main EDD_User_Profiles class
     *
     * @since       1.0.0
     */
    class EDD_User_Profiles {

        /**
         * @var         EDD_User_Profiles $instance The one true EDD_User_Profiles
         * @since       1.0.0
         */
        private static $instance;

        /**
         * @var         EDD_User_Profiles_Page $page
         * @since       1.0.0
         */
        public $page;

        /**
         * @var         EDD_User_Profiles_Editor $editor
         * @since       1.0.0
         */
        public $editor;

        /**
         * @var         array $plugins
         * @since       1.0.0
         */
        public $plugins;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_User_Profiles
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_User_Profiles();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_USER_PROFILES_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_USER_PROFILES_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_USER_PROFILES_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            require_once EDD_USER_PROFILES_DIR . 'includes/scripts.php';
            require_once EDD_USER_PROFILES_DIR . 'includes/hooks.php';

            require_once EDD_USER_PROFILES_DIR . 'includes/class-page.php';
            require_once EDD_USER_PROFILES_DIR . 'includes/class-editor.php';

            $this->page = new EDD_User_Profiles_Page();
            $this->editor = new EDD_User_Profiles_Editor();

            // Load files based on active plugins
            $this->plugins = array();

            if( class_exists('EDD_Front_End_Submissions') ) {
                require_once EDD_USER_PROFILES_DIR . 'includes/plugins/class-fes.php';
                $this->plugins['fes'] = new EDD_User_Profiles_FES();
            }

            if( class_exists( 'BadgeOS' ) ) {
                require_once EDD_USER_PROFILES_DIR . 'includes/plugins/class-badgeos.php';
                $this->plugins['fes'] = new EDD_User_Profiles_BadgeOS();
            }
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            // Register settings
            add_filter( 'edd_settings_sections_extensions', array( $this, 'settings_section' ) );
            add_filter( 'edd_settings_extensions', array( $this, 'settings' ), 1 );
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_USER_PROFILES_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_user_profiles_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-user-profiles' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-user-profiles', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-user-profiles/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-user-profiles/ folder
                load_textdomain( 'edd-user-profiles', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-user-profiles/languages/ folder
                load_textdomain( 'edd-user-profiles', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-user-profiles', false, $lang_dir );
            }
        }

        /**
         * Add settings section
         *
         * @access      public
         * @since       1.0.0
         * @param       array $sections The existing EDD settings sections array
         * @return      array The modified EDD settings sections array
         */
        public function settings_section( $sections ) {
            $sections['edd-user-profiles'] = __( 'EDD User Profiles', 'edd-user-profiles' );

            return $sections;
        }

        /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
         */
        public function settings( $settings ) {
            $pages_options = array();

            $pages = get_pages();
            if ( $pages ) {
                foreach ( $pages as $page ) {
                    $pages_options[ $page->ID ] = $page->post_title;
                }
            }

            $edd_user_profiles_settings = array(
                array(
                    'id'    => 'edd_user_profiles_settings',
                    'name'  => '<strong>' . __( 'EDD User Profiles', 'edd-user-profiles' ) . '</strong>',
                    'desc'  => __( 'Configure EDD User Profiles', 'edd-user-profiles' ),
                    'type'  => 'header',
                ),
                'edd_user_profiles_page' => array(
                    'id'          => 'edd_user_profiles_page',
                    'name'        => __( 'User Profile Page', 'edd-user-profiles' ),
                    'desc'        => __( 'This setting is used to determine which page is the user profile page.', 'edd-user-profiles' ),
                    'type'        => 'select',
                    'options'     => $pages_options,
                    'chosen'      => true,
                ),
                'edd_user_profiles_override_author_url' => array(
                    'id'          => 'edd_user_profiles_override_author_url',
                    'name'        => __( 'Override author URL', 'edd-user-profiles' ),
                    'desc'        => __( 'Checking this option will override Wordpress Author URl to the user profile page.', 'edd-user-profiles' ),
                    'type'        => 'checkbox',
                ),
            );

            $edd_user_profiles_settings = apply_filters( 'edd_user_profiles_settings', $edd_user_profiles_settings );

            if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
                $edd_user_profiles_settings = array( 'edd-user-profiles' => $edd_user_profiles_settings );
            }

            return array_merge( $settings, $edd_user_profiles_settings );
        }

        public function get_user_profile_url( $user_id = null ) {
            if ( ! edd_get_option( 'edd_user_profiles_page', false ) ) {
                return false;
            }

            if( $user_id == null ) {
                $user_id = get_current_user_id();
            }

            $url = get_permalink( edd_get_option( 'edd_user_profiles_page' ) );

            $user_data = get_userdata( $user_id );
            if( $user_data ) {
                $user_nicename = strtolower( $user_data->user_nicename );

                if ( get_option( 'permalink_structure' ) ) {
                    $url = trailingslashit( $url ) . $user_nicename;
                } else {
                    $url = add_query_arg( array( 'user' => $user_nicename ), $url );
                }
            }

            return $url;
        }

        public function get_avatar_size() {
            return apply_filters( 'edd_user_profiles_avatar_size' , array( 100, 100 ) );
        }

        /**
         * Get an attachment ID given a URL.
         *
         * @param string $url
         *
         * @return int Attachment ID on success, 0 on failure
         */
        public function get_attachment_id( $url ) {
            $attachment_id = 0;
            $dir = wp_upload_dir();

            if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
                $file = basename( $url );

                $query_args = array(
                    'post_type'   => 'attachment',
                    'post_status' => 'inherit',
                    'fields'      => 'ids',
                    'meta_query'  => array(
                        array(
                            'value'   => $file,
                            'compare' => 'LIKE',
                            'key'     => '_wp_attachment_metadata',
                        ),
                    )
                );

                $query = new WP_Query( $query_args );

                if ( $query->have_posts() ) {
                    foreach ( $query->posts as $post_id ) {
                        $meta = wp_get_attachment_metadata( $post_id );
                        $original_file       = basename( $meta['file'] );
                        $cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );

                        if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
                            $attachment_id = $post_id;
                            break;
                        }
                    }
                }
            }
            return $attachment_id;
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true EDD_User_Profiles
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_User_Profiles The one true EDD_User_Profiles
 */
function edd_user_profiles() {
    return EDD_User_Profiles::instance();
}
add_action( 'plugins_loaded', 'edd_user_profiles' );
