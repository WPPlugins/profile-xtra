<?php
/*** Alternative Author xtra ***/

/* add metabox */
function profilextra_alterauthor_metabox() {
    add_meta_box('profilextra_alterauthor_mbox', esc_html__('Alternative Author', 'profile-xtra'), 'profilextra_alterauthor_mbox', 'post', 'normal', 'high');
}
add_action('add_meta_boxes', 'profilextra_alterauthor_metabox');

/* draw metabox */
function profilextra_alterauthor_mbox() {
    $ID = $_GET['post'];
    ?>
    <div class="option-item" id="profilextra-alter-items">
        <label><?php echo esc_html__('Alternative Author\'s Full Name','profile-xtra');?>:</label>
        <input type="text" name="altername" id="altername" value="<?php echo get_post_meta($ID, 'altername', true);?>" />
        <br/>
        <label><?php echo esc_html__('Alternative Author\'s Link','profile-xtra');?>:</label>
        <input type="text" name="alterlnk" id="alterlnk" value="<?php echo get_post_meta($ID, 'alterlnk', true);?>" />
        <br/>
        <?php $alterimg = get_post_meta($ID, 'alterimg', true);?>
        <label><?php echo esc_html__('Alternative Author\'s Image','profile-xtra');?>:</label>
        <input type='hidden' name='profilextra_imgsrc' id='profilextra_imgsrc' value='<?php echo $alterimg;?>' />
        <div id='profilextra_img'><?php if ($alterimg) echo "<img src='".$alterimg."'>";?></div>
        <a id="profilextra_get_media" href="#"><?php echo esc_html__('Get Image','profile-xtra');?></a><a id="profilextra_clear_media" href="#"><?php echo esc_html__('Clear Image','profile-xtra');?></a>
        <br />
        <label><?php echo esc_html__('Alternative Author\'s Data','profile-xtra');?>:</label>
        <textarea name="alterdata" id="alterdata"><?php echo get_post_meta($ID, 'alterdata', true)?></textarea>
        <br/>
        <input type="hidden" name="alterauthor_mbox_nonce" value="<?php echo wp_create_nonce('alterauthor_mbox');?>" />
    </div>
  <?php
}
/*** save metabox data ***/
function save_alterauthor_mbox($post_id) {
    // check nonce
    if (!isset($_POST['alterauthor_mbox_nonce']) || !wp_verify_nonce($_POST['alterauthor_mbox_nonce'], 'alterauthor_mbox')) return $post_id;
    // check capabilities
    if ('post' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) return $post_id;
    } elseif (!current_user_can('edit_page', $post_id)) {
        return $post_id;
    }
    // exit on autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
    //get POSTSs
    $altername = '';
    $alterimg = ''; $alterlnk = ''; $alterdata = '';
    if(isset($_POST['altername']))
       $altername = sanitize_text_field($_POST['altername']);
    if(isset($_POST['profilextra_imgsrc']))
       $alterimg = esc_url($_POST['profilextra_imgsrc']);
    if(isset($_POST['alterlnk']))
       $alterlnk = esc_url($_POST['alterlnk']);
    if(isset($_POST['alterdata']))
       $alterdata = esc_textarea($_POST['alterdata']);
    //name
    if (!empty($altername))
       update_post_meta($post_id, 'altername', $altername);
    else
        delete_post_meta($post_id, 'altername');
    //image
    if (!empty($alterimg))
        update_post_meta($post_id, 'alterimg', $alterimg);
    else
        delete_post_meta($post_id, 'alterimg');
    //data
    if(!empty($alterdata))
        update_post_meta($post_id, 'alterdata', $alterdata);
    else
        delete_post_meta($post_id, 'alterdata');
    //link
    if (!empty($alterlnk))
        update_post_meta($post_id, 'alterlnk', $alterlnk);
    else
        delete_post_meta($post_id, 'alterlnk');
}
add_action('save_post', 'save_alterauthor_mbox');
?>
