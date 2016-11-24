<?php

    $user = edd_user_profiles()->page->get_queried_user();

    if( $user ) {
        ?>
            <div id="edd-user-profiles-nav-tabs">
                <?php if( class_exists( 'EDD_Front_End_Submissions' ) ) : ?>
                    <a href="#edd-user-profiles-tab-downloads"
                       id="edd-user-profiles-nav-tab-downloads"
                       class="edd-user-profiles-nav-tab"
                    >
                        <span class="edd-user-profiles-nav-tab-label"><?php echo EDD_FES()->helper->get_product_constant_name( $plural = true, $uppercase = true ); ?></span>
                    </a>
                <?php endif; ?>
                <?php if( class_exists( 'EDD_Wish_Lists' ) ) : ?>
                    <a href="#edd-user-profiles-tab-wish-lists"
                       id="edd-user-profiles-nav-tab-wish-lists"
                       class="edd-user-profiles-nav-tab"
                    >
                        <span class="edd-user-profiles-nav-tab-label"><?php echo edd_wl_get_label_plural( false ); ?></span>
                    </a>
                <?php endif; ?>
                <?php if( class_exists( 'EDD_Downloads_Lists' ) ) : ?>
                    <?php foreach( edd_downloads_lists()->get_lists() as $list_id => $list_args ) : ?>
                        <a href="#edd-user-profiles-tab-<?php echo $list_id; ?>-list"
                           id="edd-user-profiles-nav-tab-<?php echo $list_id; ?>-list"
                           class="edd-user-profiles-nav-tab"
                        >
                            <span class="edd-user-profiles-nav-tab-label"><?php echo $list_args['plural']; ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div id="edd-user-profiles-tabs">
                <?php if( class_exists( 'EDD_Front_End_Submissions' ) ) : ?>
                    <div id="edd-user-profiles-tab-downloads">
                        <?php echo do_shortcode( '[downloads author="' . $user->ID . '"]' ); ?>
                    </div>
                <?php endif; ?>
                <?php if( class_exists( 'EDD_Wish_Lists' ) ) : ?>
                    <div id="edd-user-profiles-tab-wish-lists">
                        <?php

                        $query = array(
                            'post_type' 		=> 'edd_wish_list',
                            'posts_per_page' 	=> '-1',
                            'post_author' 		=> $user->ID,
                        );

                        $query = apply_filters( 'edd_wl_query_args', $query ); // Filter to hide EDD Downloads Lists

                        $lists = get_posts( $query );

                        if( $lists ) {
                            foreach( $lists as $list ) {
                                echo $list->post_title . '<br>';
                            }
                        } else {
                            _e( sprintf( 'No %s found', edd_wl_get_label_plural( false ) ) );
                        }

                        ?>
                    </div>
                <?php endif; ?>
                <?php if( class_exists( 'EDD_Downloads_Lists' ) ) : ?>
                    <?php foreach( edd_downloads_lists()->get_lists() as $list_id => $list_args ) : ?>
                        <div id="edd-user-profiles-tab-<?php echo $list_id; ?>-list">
                            <?php
                            $list_downloads = edd_downloads_lists_get_downloads_list( $list_id, $user->ID );

                            if( $list_downloads ) {
                                foreach( $list_downloads as $list_download ) {
                                    $download = get_post( $list_download['id'] );

                                    echo $download->post_title . '<br>';
                                }
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php


    }