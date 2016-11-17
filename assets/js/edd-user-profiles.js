jQuery(document).ready(function($) {
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
                        $('#edd_avatar_preview').attr('src', response.url);
                    }
                }
            });
        }
    });

    $('#edd_avatar_remove_button').click(function(e) {
        e.preventDefault();
    });
});