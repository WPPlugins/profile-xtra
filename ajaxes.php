<?php
/* ***************************** */
/*     HANDLE AJAX requests      */
/* ***************************** */

/*** AJAX for visitors and users ***/
add_action( 'wp_ajax_profilextra_reqs', 'profilextra_reqs_callback' );
add_action( 'wp_ajax_nopriv_profilextra_reqs', 'profilextra_reqs_callback' );

/*** HANDLER function ***/
function profilextra_reqs_callback() {
    //security
    check_ajax_referer( '__ajax_nonce', 'security' );
    //switch ACTIONS
    switch ($_POST['todo']) {
        case 'profilextra_options':
            $label_1 = esc_html__('You can', 'profile-xtra');
            $label_2 = esc_html__('use or upload a', 'profile-xtra');
            $label_3 = esc_html__('Profile image', 'profile-xtra');
            $label_4 = esc_html__('Use uploaded or selected image instead of avatar', 'profile-xtra');
            $profile_id = $_POST['profile_id'];
            $noavatar = get_user_meta($profile_id, 'profilextra_avatar',true);
            $imgsrc = get_user_meta($profile_id, 'profilextra_imgsrc',true);
            $label_5 = esc_html__('Upload or select your profile image', 'profile-xtra');
            $avatar_option = get_option('show_avatars');
            $echo = json_encode(array($label_1, $label_2, $label_3, $noavatar, $imgsrc, $label_4, $label_5, $avatar_option));
        break;
    }
    //return values
    echo $echo;
    wp_die();
}

/** Style the Thickbox **/
function profilextra_thickbox() {
    global $pagenow;
    if ( 'media-upload.php'==$pagenow ):
    //|| 'async-upload.php'==$pagenow
        add_filter('attachment_fields_to_edit', 'profilextra_style_umedia', 10000, 2);
        add_filter('media_upload_tabs', 'profilextra_url_tab');
    endif;
}
add_action( 'admin_init', 'profilextra_thickbox' );
function profilextra_style_umedia($form_fields,$post) {
    unset( $form_fields['align'] );
    unset( $form_fields['post_title'] );
    unset( $form_fields['image_alt'] );
    unset( $form_fields['post_excerpt'] );
    unset( $form_fields['post_content'] );
    unset( $form_fields['url'] );
    ?>
    <style>
        p.media-types.media-types-required-info, a.del-link, p.ml-submit > #save, ul.subsubsub{display: none!important;}
        td.savesend input {background: #1E8CBE!important;color: white!important;font-size: 1.2em!important;}
        td.savesend input:hover {background: #444!important;}
    </style>
    <?php
    add_filter("attribute_escape", "profilextra_umedia_btn", 10, 2);
    return $form_fields;
}
function profilextra_umedia_btn($safe_text, $text) {
    return str_replace(esc_html__('Insert into Post'), esc_html__('Use this image', 'profile-xtra'), $text);
}
function profilextra_url_tab($tabs) {
    ?>
    <style>
        p.media-types label,tr.image-only label,tr.image-only td.field{display: none;}
    </style>
    <?php
   return $tabs;
}

/*** WP Ajax Magic ***/
if(!function_exists('ajaxes_js')){
    function ajaxes_js() {
    ?>
    <script>
        var ajaxurl = <?php echo json_encode(admin_url("admin-ajax.php")); ?>;
        var ajaxnonce = <?php echo json_encode(wp_create_nonce("__ajax_nonce" )); ?>;
    </script>
    <?php
    }
}
// Add hook for admin <head></head>
add_action('admin_head', 'ajaxes_js');
// Add hook for front-end <head></head>
//add_action('wp_head', 'ajaxes_js');


/*** get user id on profile ***/
add_action('admin_head', 'profilextra_id');
function profilextra_id(){
    global $pagenow, $user_id;
    if ($pagenow == 'user-edit.php' || $pagenow == 'profile.php'){
        ?>
        <script>
            var profile_id = <?php echo esc_js($user_id); ?>;
        </script>
        <?php
    }
}
