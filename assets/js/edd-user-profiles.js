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
    function get_url_parameter( url, parameter) {
        var sPageURL = decodeURIComponent(url),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === parameter) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    }

    function merge_objects(obj1,obj2){
        var obj3 = {};
        for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
        for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
        return obj3;
    }

    function load_tab_content(tab_id, extra_params) {
        if(tab_id !== undefined) {
            var tab_element = $('#' + tab_id);

            tab_element.html(edd_user_profiles.spinner);

            var data = {
                action: 'edd_user_profiles_load_tab_content',
                tab: tab_id,
                user: edd_user_profiles.queried_user
            };

            if(extra_params !== undefined) {
                data = merge_objects(data, extra_params);
            }

            $.ajax({
                url: edd_scripts.ajaxurl,
                data: data,
                success: function (response) {
                    tab_element.html(response);

                    // Load pagination from ajax
                    if(tab_element.find('#edd_download_pagination').length != 0) {
                        tab_element.find('#edd_download_pagination a').click(function (e) {
                            e.preventDefault();

                            load_tab_content(tab_id, { paged: get_url_parameter($(this).attr('href'), 'paged') });
                        });
                    }

                    // Load list from ajax
                    if(tab_element.find('.edd-wish-list').length != 0) {
                        tab_element.find('.edd-wish-list a').click(function (e) {
                            e.preventDefault();

                            load_tab_content(tab_id, { view: $(this).attr('href') });
                        });
                    }
                }
            });
        }
    }

    load_tab_content( $('.edd-user-profiles-tab.active').attr('id') );

    // On click a tab, if this is empty, then loads their content from ajax
    $('.edd-user-profiles-nav-tab').click(function (e) {
        e.preventDefault();

        if (!$(this).hasClass('active')) {
            $('#edd-user-profiles-nav-tabs .active').removeClass('active');
            $(this).addClass('active');

            $('#edd-user-profiles-tabs .active').removeClass('active');
            $($(this).attr('href')).addClass('active');

            if ($($(this).attr('href')).html().trim().length == 0){ // Only loads tab content if is empty
                load_tab_content($(this).attr('href').replace('#', ''));
            }
        }
    });

    // Special condition for wish lists tab, if have loaded a list loads wish lists again on click the tab
    $('.edd-user-profiles-nav-tab[href="#wish-lists"]').click(function (e) {
        e.preventDefault();
        if($($(this).attr('href')).find('.edd-wish-list').length == 0) {
            load_tab_content($(this).attr('href').replace('#', ''));
        }
    });
});