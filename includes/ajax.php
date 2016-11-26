<?php
/**
 * Ajax
 *
 * @package     EDD\User_Profiles\Ajax
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function edd_user_profiles_load_tab_content() {

    if( isset($_REQUEST['user']) ) {
        $user_id = $_REQUEST['user'];
    } else {
        $user_id = get_current_user_id();
    }

    do_action( 'edd_user_profiles_load_' . str_replace( '-', '_', $_REQUEST['tab'] ) . '_tab_content', $user_id );

    edd_die();
}
add_action( 'wp_ajax_edd_user_profiles_load_tab_content', 'edd_user_profiles_load_tab_content' );
add_action( 'wp_ajax_nopriv_edd_user_profiles_load_tab_content', 'edd_user_profiles_load_tab_content' );

function edd_user_profiles_load_downloads_tab_content( $user_id ) {
    echo do_shortcode( '[downloads author="' . $user_id . '"]' );
}
add_action( 'edd_user_profiles_load_downloads_tab_content', 'edd_user_profiles_load_downloads_tab_content' );

function edd_user_profiles_load_wish_lists_tab_content( $user_id ) {
    $query = array(
        'post_type' 		=> 'edd_wish_list',
        'posts_per_page' 	=> '-1',
        'author' 		    => $user_id,
    );

    $query = apply_filters( 'edd_wl_query_args', $query ); // Filter to hide EDD Downloads Lists

    $lists = get_posts( $query );

    if( $lists ) {
        ?>
        <ul class="edd-wish-list">
            <?php foreach ($lists as $id) : ?>
                <li>
                    <span class="edd-wl-item-title">
                        <a href="<?php //echo edd_wl_get_wish_list_view_uri($id); ?>" title="<?php echo the_title_attribute(array('post' => $id)); ?>"><?php echo get_the_title($id); ?></a>
                        <span class="edd-wl-item-count"><?php echo edd_wl_get_item_count($id); ?></span>
                    </span>

                    <?php // edit link
                    //echo edd_wl_edit_link($id);
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    } else {
        _e( sprintf( 'No %s found', edd_wl_get_label_plural( false ) ) );
    }
}
add_action( 'edd_user_profiles_load_wish_lists_tab_content', 'edd_user_profiles_load_wish_lists_tab_content' );

function edd_user_profiles_load_list_tab_content( $user_id ) {
    global $post;

    $original_global_post = $post;

    $list = str_replace( '-list', '', $_REQUEST['tab'] );

    $list_downloads = edd_downloads_lists_get_downloads_list( $list, $user_id );

    if( $list_downloads ) {
        $list_ids =  array_map(function( $list_download ) {
            return $list_download['id'];
        }, $list_downloads);

        echo do_shortcode( '[downloads ids="' . implode( ',', $list_ids ) . '"]' );

        $post = $original_global_post;
    } else {
        _e( sprintf( 'No %s found', strtolower( edd_downloads_lists()->get_list_args($list)['plural'] ) ) );
    }
}
add_action( 'edd_user_profiles_load_favorite_list_tab_content', 'edd_user_profiles_load_list_tab_content' );
add_action( 'edd_user_profiles_load_like_list_tab_content', 'edd_user_profiles_load_list_tab_content' );
add_action( 'edd_user_profiles_load_recommend_list_tab_content', 'edd_user_profiles_load_list_tab_content' );