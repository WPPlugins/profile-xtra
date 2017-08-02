<?php
/*** Multiple Authors xtra ***/

/* add metabox */
function profilextra_multiauthor_metabox() {
    $options = profilextra_get_options();
    $multi = $options['multiauthor'];
    if (empty($multi)) return false;
    $where = array();
    $m_posts = $options['m_posts'];
    if (!empty($m_posts)) $where[]='post';
    $m_pages = $options['m_pages'];
    if (!empty($m_pages)) $where[]='page';
    $cpts = get_custom_post_types();
    if (!empty($cpts))
        foreach ($cpts as $cpt)
            if (!empty($options['m_cpt_'.$cpt])) $where[]=$cpt;
    if(empty($where)) return false;
    add_meta_box('profilextra_multiauthor_mbox', esc_html__('Multiple Authors', 'profile-xtra'), 'profilextra_multiauthor_mbox', $where, 'side', 'high');
}
add_action('add_meta_boxes', 'profilextra_multiauthor_metabox');

/* draw metabox */
function profilextra_multiauthor_mbox() {
    $ID = $_GET['post'];
    $authors = get_post_meta($ID, 'authors', true);
    ?>
    <div class="m_authors"><?php echo __('Loading authors...','profilextra');?></div>
    <br />
    <div class="option-item" id="profilextra-multi-items">
        <label><?php echo esc_html__('Select other Authors for this post','profile-xtra');?>:</label>
        <br/>
        <select id="multi" name="multi" multiple="multiple">
        <?php 
        $m_authors = explode(",", $authors);
        $users = get_users();
        foreach ($users as $user){
            $userID = $user->ID;
            ?>
            <option <?php if (in_array($userID,$m_authors)) echo 'selected="select" ';?>value="<?php echo $userID;?>"><?php echo $user->display_name;?></option>
            <?php
        }
        ?>
        </select>
        <input type="hidden" name="authors" id="authors"value="<?php echo $authors;?>" />
        <input type="hidden" name="multiauthor_mbox_nonce" value="<?php echo wp_create_nonce('multiauthor_mbox');?>" />
    </div>


  <?php
}

/*** save metabox data ***/
function save_multiauthor_mbox($post_id) {
    // check nonce
    if (!isset($_POST['multiauthor_mbox_nonce']) || !wp_verify_nonce($_POST['multiauthor_mbox_nonce'], 'multiauthor_mbox')) return $post_id;
    // check capabilities
    if ('post' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) return $post_id;
    } elseif (!current_user_can('edit_page', $post_id)) {
        return $post_id;
    }
    // exit on autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
    //get POSTSs
    $authors = '';
    if(isset($_POST['authors']))
       $authors = sanitize_text_field($_POST['authors']);
    //name
    if (!empty($authors))
       update_post_meta($post_id, 'authors', $authors);
    else
        delete_post_meta($post_id, 'authors');
}
add_action('save_post', 'save_multiauthor_mbox');
?>
