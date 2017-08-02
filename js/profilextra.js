jQuery(document).ready(function($){

    /*** ALTER author ***/
    // get image profile through thickbox //
    var orig_send_to_editor = window.send_to_editor;
    $('#profilextra_get_media').click(function() {
        var el = document.getElementById('profilextra_get_media');
        // innerText for IE, textContent for others
        var label = el.innerText || el.textContent;
        tb_show(label, 'media-upload.php?post_id=0&type=image&TB_iframe=true&referer=alter', false);
        //send to editor
        window.send_to_editor = function (html) {
            if (null != html){
                src =jQuery(html).attr('src');
                $("#profilextra_imgsrc").val(src);
                $('#profilextra_img')
                    .html("<img src='" + src + "'/>")
                    .show();
                $("#profilextra_clear_media").show();
            }
            tb_remove();
            window.send_to_editor = orig_send_to_editor;
        }
        return false;
    });
    $('#profilextra_clear_media').click(function() {
        $("#profilextra_img").html('');
        $("#profilextra_imgsrc").val('');
        $(this).hide();
        return false;
    });
    if (!$('#profilextra_imgsrc').val())
        $('#profilextra_clear_media').hide();

    /*** PROFILE xtra ***/
    // AJAX request //
    if (typeof profile_id !== "undefined")
    $.ajax({
        url: ajaxurl,
        type:'POST',
        data: {
            action: 'profilextra_reqs',
            todo: 'profilextra_options',
            security: ajaxnonce,
            profile_id: profile_id
        },
        success:function(results){ console.log("res--->"+results);
            if (results && results!=-1)
               profilextra_dom(JSON.parse(results));
        }
    });
    // build needed DOM & actions when ajax requested //
    function profilextra_dom(obj){
        var label_1   = obj[0];
        var label_2   = obj[1];
        var label_3   = obj[2];
        var avatar    = obj[3];
        var imgsrc    = obj[4];
        var label_4   = obj[5];
        var label_5   = obj[6];
        var avatar_opt= obj[7];
        var checked   = '';
        if (avatar == "not") checked = "checked";
        //create link and checkbox
        var dom = "<p>" + label_1 + " <a href='#'  id='profilextra_get_media'>" + label_2 + "</a> " + label_3 + "...</p><input type='hidden' name='profilextra_imgsrc' id='profilextra_imgsrc' value='" + imgsrc + "' /><input type='checkbox' value='not' id='profilextra_avatar' name='profilextra_avatar' " + checked + ">" + label_4 + "</input>";
        var ximg = "<div id='profilextra_img'></div>";
        if (avatar_opt){
            $('tr.user-profile-picture td')
                .append(dom);
            //create image dom
            $('tr.user-profile-picture td img')
                .after(ximg);
        }else{
            $('tr.user-description-wrap')
                .after("<tr><th><label for='profilextra_imgsrc'>" + label_3 +"</label></th><td>" + ximg + dom + "</td></tr>");
        }
        //fill with image
        if (imgsrc && checked)
            profilextra_toggleimg(imgsrc, "show");
        else
            profilextra_toggleimg(imgsrc, "hide");

        // show or hide img / avatar //
        function profilextra_toggleimg(imgsrc, showhide){
            switch (showhide) {
                case "hide":
                    $('#profilextra_img').hide();
                    $('tr.user-profile-picture td img').show();
                    break;
                case "show":
                    $('tr.user-profile-picture td img').hide();
                    $('#profilextra_img')
                        .html("<img src='" + imgsrc + "'/>")
                        .show();
                    break;
            }
        }

        //open thbox or show prof img when click on checkbox
        $( "input#profilextra_avatar").change( function(){
            if ($(this).prop("checked")){
                var imgsrc = $('#profilextra_imgsrc').val();
                if( imgsrc ){
                    profilextra_toggleimg(imgsrc, "show");
                }else{
                    profilextra_mediaupload();
                    $("input#profilextra_avatar")
                        .attr('checked', false);
                }
            }else{
                profilextra_toggleimg(imgsrc, "hide");
            }
        });

        // get image profile through thickbox //
        $('#profilextra_get_media').click(function() {
            profilextra_mediaupload();
        });

        function profilextra_mediaupload(){
            tb_show(label_5, 'media-upload.php?post_id=0&type=image&TB_iframe=true&referer=profile', false);
            //send to editor
            window.send_to_editor = function (html) {
                if (null != html){
                    src =jQuery(html).attr('src');
                    $("#profilextra_imgsrc").val(src);
                    $('tr.user-profile-picture td img')
                        .hide();
                    $('#profilextra_img')
                        .html("<img src='" + src + "'/>")
                        .show();
                    $("input#profilextra_avatar")
                        .attr('checked', true);
                }
                tb_remove();
            }
            return false;
        }

    } // end profilextra_dom functions

});
