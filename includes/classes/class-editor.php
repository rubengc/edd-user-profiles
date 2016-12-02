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
 * EDD_User_Profiles_Edit_Profile
 *
 * This class adds new fields to [edd_profile_editor].
 *
 * @since 1.0.0
 * @access public
 */
class EDD_User_Profiles_Editor {

    public function __construct() {
        // User profile avatar field
        add_action( 'edd_profile_editor_fields_top', array( $this, 'avatar_field' ) );

        // Ajax for upload user avatar field
        add_action( 'wp_ajax_edd_user_profiles_upload_avatar', array( $this, 'upload_avatar' ) );

        // User profile description field
        add_action( 'edd_profile_editor_after_email', array( $this, 'description_field' ) );

        // Update user profile
        add_action( 'edd_pre_update_user_profile', array( $this, 'update_user_profile' ), 10, 2 );
    }

    public function avatar_field() {
        $user_id      = get_current_user_id();
        $avatar_size  = edd_user_profiles()->get_avatar_size();
        $avatar       = get_user_meta( $user_id, 'user_avatar', true );
        ?>
        <fieldset>
            <span id="edd_profile_avatar_label"><legend><?php echo apply_filters('edd_profile_avatar_label', __( 'Change your Avatar', 'edd-user-profiles' ) ); ?></legend></span>
            <div id="edd_profile_avatar_wrap" class="<?php echo apply_filters('edd_profile_avatar_wrap_class', ''); ?>">
                <label for="edd_avatar_file"><?php _e( 'Avatar', 'edd-user-profiles' ); ?></label>

                <input name="edd_avatar" id="edd_avatar" type="hidden" value="<?php echo esc_url( $avatar ); ?>" />

                <div class="edd-avatar-input-wrap <?php echo (($avatar) ? 'edd-user-profiles-hide' : '');?>">
                    <input id="edd_avatar_file" type="file" accept="image/*">
                    <button type="button" id="edd_avatar_button" class="edd-avatar-button edd-submit button"><?php _e( 'Upload Avatar', 'edd-user-profiles' ); ?></button>
                </div>
                <div class="edd-avatar-preview-wrap <?php echo ((!$avatar) ? 'edd-user-profiles-hide' : '');?>">
                    <a href="#" id="edd_avatar_remove_button" class="edd-avatar-remove-button">&times;</a>
                    <img src="<?php echo esc_url( $avatar ); ?>" alt="" id="edd_avatar_preview" style="width: <?php echo $avatar_size[0]; ?>px !important; height: <?php echo $avatar_size[1]; ?>px !important;">
                </div>
            </div>
        </fieldset>
        <?php
    }

    public function upload_avatar() {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $response = array();

        // Upload the file and
        $file = wp_handle_upload( $_FILES['edd_avatar'], array( 'test_form' => false ) );

        if ( $file && ! isset( $file['error'] ) ) {
            $user_id = get_current_user_id();
            $upload_dir = wp_upload_dir();

            $pre_user_avatar = get_user_meta( $user_id, 'pre_user_avatar', false );
            if(isset( $pre_user_avatar[0] )) {
                $pre_user_avatar = $pre_user_avatar[0];
            }

            // Removes previous user avatar before profile update
            if( isset( $pre_user_avatar['file'] ) && file_exists( $pre_user_avatar['file'] ) ) {
                unlink( $pre_user_avatar['file'] );
            }

            // Remove basedir because windows slashes (\) are not stored correctly in database arrays
            $file['file'] = str_replace($upload_dir['basedir'], '', $file['file']);

            // Store file path in a user meta to delete this if user changes their avatar before update their profile
            update_user_meta( $user_id, 'pre_user_avatar', $file, $pre_user_avatar );

            $response['url'] = $file['url'];
        } else {
            // Can not upload, so something is wrong
            $response['error'] = $file['error'];
        }

        wp_send_json( $response );
        edd_die();
    }

    public function description_field() {
        $user_id      = get_current_user_id();
        $description  = get_user_meta( $user_id, 'description', true );
        ?>

        <?php do_action( 'edd_profile_editor_before_description' ); ?>

        <fieldset>
            <p id="edd_profile_description_wrap" class="<?php echo apply_filters('edd_profile_description_wrap_class', ''); ?>">
                <label for="edd_description"><?php echo apply_filters('edd_profile_description_label', __( 'Description', 'edd-user-profiles' ) ); ?></label>
                <textarea name="edd_description" id="edd_description" class="textarea edd-input"><?php echo esc_textarea( $description ); ?></textarea>
            </p>
        </fieldset>

        <?php do_action( 'edd_profile_editor_after_description' ); ?>
        <?php
    }

    public function update_user_profile( $user_id, $userdata ) {

        if ( !current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }

        if ( ! empty( $_REQUEST['edd_avatar'] ) ) {
            $old_avatar = get_user_meta( $user_id, 'user_avatar', true );

            // If avatar changes, then start to update
            if( $old_avatar != $_REQUEST['edd_avatar'] ) {

                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );

                // Pre user avatar uploaded by ajax
                $pre_user_avatar = get_user_meta( $user_id, 'pre_user_avatar', false );

                if( isset( $pre_user_avatar[0] ) ) {
                    $pre_user_avatar = $pre_user_avatar[0];
                }

                // Try to crop the avatar
                if ( function_exists( 'wp_get_image_editor' ) ) {
                    $upload_dir = wp_upload_dir();

                    $file_path = $upload_dir['basedir'] . $pre_user_avatar['file']; // Previously, basedir have removed

                    $ext = strrchr( $file_path, '.' );
                    $file_path_w_ext = str_replace( $ext, '', $file_path );
                    $edited_file = $file_path_w_ext . '_user_avatar' . $ext; // Do not prepend '-avatar', FES do not will find this attachment

                    $editor = wp_get_image_editor( $file_path );

                    if ( !is_wp_error( $editor ) ) {
                        // Resize the avatar
                        $avatar_size = edd_user_profiles()->get_avatar_size();
                        $editor->resize( $avatar_size[0], $avatar_size[1], true );
                        $editor->save( $edited_file );

                        // If resize successfully then updates the file
                        if ( file_exists( $edited_file ) ) {
                            $edited_file_url = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $edited_file );

                            $pre_user_avatar['file'] = $edited_file;
                            $pre_user_avatar['url'] = $edited_file_url;
                        }
                    }
                }

                $name = strrchr( $pre_user_avatar['file'], '/' );
                $ext = strrchr( $name, '.' );
                $name = str_replace( $ext, '', $name ) . '-avatar';

                // Construct the attachment array
                $attachment = array(
                    'post_mime_type' => $pre_user_avatar['type'],
                    'guid' => $pre_user_avatar['url'],
                    'post_parent' => 0,
                    'post_title' => sanitize_title( $name ),
                    'post_author' => $user_id,
                    'post_content' => '',
                    'post_excerpt' => '',
                );

                // Save the data
                $id = wp_insert_attachment($attachment, $pre_user_avatar['file'], 0);

                if ( !is_wp_error($id) ) {
                    wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $pre_user_avatar['file'] ) );
                }

                // Remove old user avatar
                if (!empty($old_avatar)) {
                    $old_avatar_id = edd_user_profiles()->get_attachment_id( $old_avatar );

                    if ($old_avatar_id) {
                        wp_delete_attachment( $old_avatar_id, true );
                    }
                }

                delete_user_meta( $user_id, 'pre_user_avatar' );

                // Update user avatar meta
                update_user_meta( $user_id, 'user_avatar', $pre_user_avatar['url'], $old_avatar );
            }
        } else {
            // Clear user meta if empty avatar
            delete_user_meta( $user_id, 'user_avatar' );
        }

        if( ! empty( $_POST['edd_description'] ) ) {
            // Update user description meta
            update_user_meta( $user_id, 'description', sanitize_text_field( $_POST['edd_description'] ) );
        }

    }

}