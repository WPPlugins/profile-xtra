<?php

/** Add plugin options page **/
add_action( 'admin_menu', 'profilextra_menu' );
function profilextra_menu() {
    // params. title, menu title, capability, slug and function
    add_options_page( 'ProfileXtra Plugin Options', 'Profile Xtra', 'manage_options', 'profilextra', 'profilextra_options' );
}

/** Set options form **/
function profilextra_options() {
    if ( !current_user_can( 'manage_options' ) )
        wp_die( esc_html__('You do not have sufficient permissions to access this page.' ) );
    ?>
    <div id = "optionspage" class="wrap">
        <h2><?php echo esc_html__('ProfileXtra Plugin Options', 'profile-xtra');?></h2>
        <br/>
        <form action="options.php" method="post">
            <?php
            settings_fields('profilextra_ffields');
            do_settings_sections('profilextra_sections');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/** Set form fields **/
add_action( 'admin_init', 'profilextra_setfields' );
function profilextra_setfields(  ) {
    register_setting( 'profilextra_ffields', 'profilextra_settings');
    /* sections */
    $section_titles = array(
        esc_html__('Profile Image', 'profilextra' ),
        esc_html__('Social contacts', 'profilextra' ),
        esc_html__('Authors', 'profilextra' ),
        esc_html__('On Uninstall', 'profilextra' )
        );
    //settings section
    foreach ($section_titles as $k=>$section_title)
        add_settings_section(
            'profilextra_section_'.($k+1),
            $section_title,
            'profilextra_section_callback',
            'profilextra_sections'
        );
    //section callback
    function profilextra_section_callback(  ) {
        return false;
    }

    /* fields */
    $field_names = array(
        'iprofile',
        'socialxtra',
        'alterauthor',
        'on_uninstall'
    );
    $field_titles = array(
        esc_html__('Profile xtra image', 'profilextra' ),
        esc_html__('Profile xtra social contacts', 'profilextra' ),
        esc_html__('Alternative & Multiple Authors', 'profilextra' ),
        esc_html__('Choose what to do...', 'profilextra' )
    );
    //setting fields
    foreach ($field_names as $k=>$field_name)
        add_settings_field(
            $field_name,
            $field_titles[$k],
            'profilextra_field_'.($k+1).'_callback',
            'profilextra_sections',
            'profilextra_section_'.($k+1)
        );
}


/** Draw fields' content **/
function profilextra_field_1_callback() {
    $options = profilextra_get_options();
    $iprofile = $options['iprofile'];
    if (empty($iprofile)) $iprofile="0";
    ?>
        <input type='checkbox' name='profilextra_settings[iprofile]' <?php checked( $iprofile, 1 ); ?> value='1'>
        <label><em><?php echo esc_html__('Upload or select an image as your <em>profile image</em>', 'profile-xtra');?></em></label>
    <?php
}

function profilextra_field_2_callback() {
    $options = profilextra_get_options();
    $twitter = $options['twitter'];
    $facebook = $options['facebook'];
    $google_plus = $options['google-plus'];
    $linkedin = $options['linkedin'];
    $youtube = $options['youtube'];
    $vimeo = $options['vimeo'];
    if (empty($twitter)) $twitter="0";
    if (empty($facebook)) $facebook="0";
    if (empty($google_plus)) $google_plus="0";
    if (empty($linkedin)) $linkedin="0";
    if (empty($youtube)) $youtube="0";
    if (empty($vimeo)) $vimeo="0";
    $i_options = array(
        esc_html__('Do not use Font Awesome social icons', 'profilextra' ),
        esc_html__('Use Font Awesome regular social icons', 'profilextra' ),
        esc_html__('Use Font Awesome squared social icons', 'profilextra' ),
    );
    $xsep = sanitize_text_field ($options['xsep']);
    $use_icons = $options['use_icons'];
    $use_names = $options['use_names'];
    if (empty($use_names)) $use_names="0";
    ?>
    <h4><?php echo esc_html__('Include some xtra social contacts', 'profile-xtra')?></h4>
    <label><em><?php echo esc_html__('Choose whichone to include in profile page', 'profile-xtra');?></em></label><br />
    <input type='checkbox' name='profilextra_settings[twitter]' <?php checked( $twitter, 1 ); ?> value='1'>
    <label><em><?php echo esc_html__('Twitter', 'profile-xtra');?></em></label>
    <br />
    <input type='checkbox' name='profilextra_settings[facebook]' <?php checked( $facebook, 1 ); ?> value='1'>
    <label><em><?php echo esc_html__('Facebook', 'profile-xtra');?></em></label>
    <br />
    <input type='checkbox' name='profilextra_settings[google-plus]' <?php checked( $google_plus, 1 ); ?> value='1'>
    <label><em><?php echo esc_html__('Google +', 'profile-xtra');?></em></label>
    <br />
    <input type='checkbox' name='profilextra_settings[linkedin]' <?php checked( $linkedin, 1 ); ?> value='1'>
    <label><em><?php echo esc_html__('LinkedIn', 'profile-xtra');?></em></label>
    <br />
    <input type='checkbox' name='profilextra_settings[youtube]' <?php checked( $youtube, 1 ); ?> value='1'>
    <label><em><?php echo esc_html__('YouTube', 'profile-xtra');?></em></label>
    <br />
    <input type='checkbox' name='profilextra_settings[vimeo]' <?php checked( $vimeo, 1 ); ?> value='1'>
    <label><em><?php echo esc_html__('Vimeo', 'profile-xtra');?></em></label>
    <br />
    <p>&nbsp;</p>
    <h4><?php echo esc_html__('Styling the shortcode a bit', 'profile-xtra')?></h4>
    <label><em><?php echo esc_html__('Separator', 'profile-xtra');?></em></label>
    <input type='text' name='profilextra_settings[xsep]' value='<?php echo $xsep; ?>'>
    <p class="description"><?php echo esc_html__('Separator between each xtra social contact', 'profilextra' );?></p>
    <br />
    <label><em><?php echo esc_html__('Font Awesome social icons', 'profile-xtra');?></em></label>
    <select name='profilextra_settings[use_icons]'>
        <?php
        foreach ($i_options as $key=>$i_option){ ?>
            <option value='<?php echo $key;?>' <?php selected( $use_icons, $key ); ?>><?php echo $i_option;?></option>
        <?php
        }
        ?>
    </select>
    <p class="description"><?php echo esc_html__('Choose if you want to use social icons', 'profilextra' );?></p>
    <br />
    <label><em><?php echo esc_html__('Use the names of the xtra social contacts', 'profile-xtra');?></em></label>
    <p class="description"><input type='checkbox' name='profilextra_settings[use_names]' <?php checked( $use_names, 1 ); ?> value='1'> <?php echo esc_html__('If not icon is present, the name should appears', 'profilextra' );?></p>
    <?php
}
function profilextra_field_3_callback() {
    $options = profilextra_get_options();
    $alter = $options['alterauthor'];
    $multi = $options['multiauthor'];
    if (empty($alter)) $alter="0";
    if (empty($multi)) $multi="0";
    $m_posts = $options['m_posts'];
    if (empty($m_posts)) $m_posts="1";
    $m_pages = $options['m_pages'];
    if (empty($m_pages)) $m_pages="0";
    ?>
        <input type='checkbox' name='profilextra_settings[alterauthor]' <?php checked( $alter, 1 ); ?> value='1'>
        <label><em><?php echo esc_html__('Use alternative author instead of any post author', 'profile-xtra');?></em></label>
        <br /><br />
        <input type='checkbox' name='profilextra_settings[multiauthor]' <?php checked( $multi, 1 ); ?> value='1'>
        <label><em><?php echo esc_html__('Use multiple authors instead of only one post author', 'profile-xtra');?></em></label>
        <br /><br />
        <h3><?php echo esc_html__('Where to use multiple authors', 'profile-xtra');?></h3>
        <input type='checkbox' name='profilextra_settings[m_posts]' <?php checked( $m_posts, 1 ); ?> value='1'>
        <label><em><?php echo esc_html__('On regular posts', 'profile-xtra');?></em></label>
        <br />
        <input type='checkbox' name='profilextra_settings[m_pages]' <?php checked( $m_pages, 1 ); ?> value='1'>
        <label><em><?php echo esc_html__('On pages', 'profile-xtra');?></em></label>
    <?php
    $cpts = get_custom_post_types();
    if (!empty($cpts))
        foreach ($cpts as $cpt){
            if (isset($options['m_cpt_'.$cpt]))
                $value = $options['m_cpt_'.$cpt];
            else
                $value = "0";
            ?>
            <br />
            <input type='checkbox' name='profilextra_settings[m_cpt_<?php echo $cpt;?>]' <?php checked( $value, 1 ); ?> value='1'>
            <label><em><?php echo esc_html__('On custom posts', 'profile-xtra').' '.$cpt;?> </em></label>
            <?php
        }
}
function profilextra_field_4_callback() {
    //$options = get_option( 'profilextra_settings' );
    $options = profilextra_get_options();
    $value = $options['on_uninstall'];
    $keep_options = array(
        esc_html__('Keep all profile xtra metadata, and settings', 'profilextra' ),
        esc_html__('Keep only the xtra social contacts metadata', 'profilextra' ),
        esc_html__('Keep only the xtra profile image metadata', 'profilextra' ),
        esc_html__('Delete all profile xtra meta data, and settings', 'profilextra' )
    )
    ?>
    <select name='profilextra_settings[on_uninstall]'>
        <?php
        foreach ($keep_options as $key=>$keep_option){ ?>
            <option value='<?php echo $key;?>' <?php selected( $value, $key ); ?>><?php echo $keep_option;?></option>
        <?php
        }
        ?>
    </select>
    <p class="description"><?php echo esc_html__('Choose what to do once uninstalled this plugin', 'profilextra' );?></p>
    <?php
}


/*** GET REGISTERED CUSTOM POST TYPES ***/
/*
**  This function returns an array containing the
    registered custom post types
*/
if (!function_exists('get_custom_post_types')) {
    function get_custom_post_types(){
        $args = array(
            'public'   => true,
            '_builtin' => false
        );
        $post_types = get_post_types( $args, 'names', 'and' );
        return $post_types;
    }
}