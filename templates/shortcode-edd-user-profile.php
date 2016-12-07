<?php

$user = edd_user_profiles()->page->get_queried_user();

$active_nav = ''

?>
<div id="edd-user-profile" class="edd-user-profile">
    <div id="edd-user-profiles-nav-tabs">
        <?php if( class_exists( 'EDD_Front_End_Submissions' ) ) : ?>
            <?php if( EDD_FES()->vendors->user_is_vendor( $user->ID ) ) : ?>
                <a href="#downloads"
                   class="edd-user-profiles-nav-tab <?php echo (empty($active_nav)) ? 'active' : '' ?>"
                >
                    <span class="edd-user-profiles-nav-tab-label"><?php echo EDD_FES()->helper->get_product_constant_name( $plural = true, $uppercase = true ); ?></span>
                </a>
                <?php $active_nav = 'fes'; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if( class_exists( 'EDD_Wish_Lists' ) ) : ?>
            <a href="#wish-lists"
               class="edd-user-profiles-nav-tab <?php echo (empty($active_nav)) ? 'active' : '' ?>"
            >
                <span class="edd-user-profiles-nav-tab-label"><?php echo edd_wl_get_label_plural( false ); ?></span>
            </a>
            <?php if (empty($active_nav)) { $active_nav = 'wish-lists'; } ?>
        <?php endif; ?>

        <?php if( class_exists( 'EDD_Downloads_Lists' ) ) : ?>
            <?php foreach( edd_downloads_lists()->get_available_lists() as $list_id => $list_args ) : ?>

                <?php if( isset($list_args['post_status']) && $list_args['post_status'] == 'private' ) { continue; } ?>

                <a href="#<?php echo $list_id; ?>-list"
                   class="edd-user-profiles-nav-tab <?php echo (empty($active_nav)) ? 'active' : '' ?>"
                >
                    <span class="edd-user-profiles-nav-tab-label"><?php echo $list_args['plural']; ?></span>
                </a>
                <?php if (empty($active_nav)) { $active_nav = $list_id; } ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div id="edd-user-profiles-tabs">
        <?php if( class_exists( 'EDD_Front_End_Submissions' ) ) : ?>
            <?php if( EDD_FES()->vendors->user_is_vendor( $user->ID ) ) : ?>
                <div id="downloads" class="edd-user-profiles-tab <?php echo ($active_nav == 'fes') ? 'active' : '' ?>"></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if( class_exists( 'EDD_Wish_Lists' ) ) : ?>
            <div id="wish-lists" class="edd-user-profiles-tab <?php echo ($active_nav == 'wish-lists') ? 'active' : '' ?>"></div>
        <?php endif; ?>

        <?php if( class_exists( 'EDD_Downloads_Lists' ) ) : ?>
            <?php foreach( edd_downloads_lists()->get_available_lists() as $list_id => $list_args ) : ?>

                <?php if( isset($list_args['post_status']) && $list_args['post_status'] == 'private' ) { continue; } ?>

                <div id="<?php echo $list_id; ?>-list" class="edd-user-profiles-tab <?php echo ($active_nav == $list_id) ? 'active' : '' ?>"></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>