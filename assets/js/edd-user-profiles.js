jQuery(document).ready(function($) {
    // Profile editor
    $('#edd_avatar_button').click(function(e) {
        $('#edd_avatar_file').click();
    });

    $('#edd_avatar_file').change(function(e) {
        var files = e.target.files;

        if(files.length == 1) {
            var data = new FormData();

            data.append('action', 'edd_user_profiles_upload_avatar');
            data.append('edd_avatar', files[0]);

            $.ajax({
                url: edd_scripts.ajaxurl,
                data: data,
                type: 'post',
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success: function( response ) {
                    if(response.url !== undefined) {
                        $('#edd_avatar').val( response.url );
                        $('#edd_avatar_preview').attr('src', response.url).parent().removeClass('edd-user-profiles-hide');
                        $('#edd_avatar_button').parent().addClass('edd-user-profiles-hide');
                    }
                }
            });
        }
    });

    $('#edd_avatar_remove_button').click(function(e) {
        e.preventDefault();

        $('#edd_avatar').val( '' );
        $('#edd_avatar_preview').attr('src', '').parent().addClass('edd-user-profiles-hide');
        $('#edd_avatar_button').parent().removeClass('edd-user-profiles-hide');
    });

    // User profile tabs
    function load_tab_content(tab_id) {
        if(tab_id !== undefined) {
            var tab_element = $('#' + tab_id);

            if (tab_element.html().trim().length == 0) { // Only loads tab content if is empty

                tab_element.html('<div class="edd-user-profiles-spinner"></div>');

                var data = {
                    action: 'edd_user_profiles_load_tab_content',
                    tab: tab_id,
                    user: edd_user_profiles.queried_user
                };

                $.ajax({
                    url: edd_scripts.ajaxurl,
                    data: data,
                    success: function (response) {
                        tab_element.html(response);
                    }
                });
            }
        }
    }

    load_tab_content( $('.edd-user-profiles-tab.active').attr('id') );

    $('.edd-user-profiles-nav-tab').click(function (e) {
        e.preventDefault();

        if (!$(this).hasClass('active')) {
            $('#edd-user-profiles-nav-tabs .active').removeClass('active');
            $(this).addClass('active');

            $('#edd-user-profiles-tabs .active').removeClass('active');
            $($(this).attr('href')).addClass('active');

            load_tab_content($(this).attr('href').replace('#', ''));
        }
    });
});