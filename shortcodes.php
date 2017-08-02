<?php
//shortcodes as widgets on text widget
add_filter('widget_text', 'do_shortcode');

/*          SHORTCODE
    * to show user name, image, description & link to social *
*/
add_shortcode( 'profilextra', 'profilextra_user' );
function profilextra_user( $atts ){
    global $post;
    //default args
    $args = shortcode_atts( array(
        'show' => 'n,i,d', //image, description, name, social
        'wrap_class' => 'profilextras',
        'iclass' => '', //image class
        'social' => 'w', //t,f,l,g,y,v + w,e
        'social_show'=> '', //both, icon, name, bothr, iconr
        'user_id' =>''
    ), $atts );
    //alter author takes preference
    $alter_idpost = 0;
    $options = wp_parse_args(get_option('profilextra_settings'), array('alterauthor'=>'0'));
    if ($options['alterauthor'] && is_singular() && has_alter_author('altername'))
        $alter_idpost = $post->ID;
    //user id
    $user_id = $args['user_id'];
    if (!$user_id && is_singular()) 
        $user_id = get_the_author_meta('ID');
    if (!$user_id)
        $user_id = get_queried_object_id();
    if (!$user_id && !$alter_idpost) return false;

    //multi authors
    $multi = has_multi_authors();
    if ($multi) {
        $ids = explode(",", $multi);
        $ids[] = $user_id;
        $user_ids = array_unique($ids);
    }else{
        $user_ids = array($user_id);
    }
    $tmp_multi = array();
    if ($alter_idpost && $multi){ 
        $tmp_multi = explode(",", $multi);
        $user_ids = array($user_id);
    }

    //what to SHOW and its ORDER
    $show = $args['show'];
    $social = $args['social'];
    $social_show = $args['social_show'];
    $wrap_class = sanitize_html_class($args['wrap_class']);
    $iclass = sanitize_html_class($args['iclass']);
    //what to show
    $show_n = strpos(' '.$show, 'n');
    $show_s = strpos(' '.$show, 's');
    $show_i = strpos(' '.$show, 'i');
    $show_d = strpos(' '.$show, 'd');
    if(!$show_s && !empty($social))
        $show_s = 99; //last one
    //order to show
    $show_order = array();
    if ($show_i) $show_order['i'] = $show_i;
    if ($show_n) $show_order['n'] = $show_n;
    if ($show_d) $show_order['d'] = $show_d;
    if ($show_s) $show_order['s'] = $show_s;
    asort($show_order);
    //social
    if($show_s):
        //some name or icon or both to show
        $options = profilextra_get_options();
        //$social_show argument takes preference
        if ($social_show == "icon"){
            $use_icons = '1'; $use_names = '0';
        }elseif ($social_show == "name"){
            $use_icons = '0'; $use_names = '1';
        }elseif ($social_show == "both" || $social_show == "bothr"){
            $use_icons = '1'; $use_names = '1';
        }else{
            $use_icons = $options['use_icons'];
            $use_names = '0';
            if (isset($options['use_names'])) $use_names = '1';
        }
        if ($social_show == "iconr" || $social_show == "bothr")
            $use_icons = '2';
        if ($use_icons=='0' && $use_names=='0') 
            $use_names='1';//return false;
        //what to show (t,f,l,g,y,v + w,e)
        $show_st = strpos(' '.$social, 't');
        $show_sf = strpos(' '.$social, 'f');
        $show_sg = strpos(' '.$social, 'g');
        $show_sl = strpos(' '.$social, 'l');
        $show_sy = strpos(' '.$social, 'y');
        $show_sv = strpos(' '.$social, 'v');
        $show_sw = strpos(' '.$social, 'w');
        $show_se = strpos(' '.$social, 'e');
        //order to show
        $show_sorder = array();
        if ($show_st) $show_sorder['twitter'] = $show_st;
        if ($show_sf) $show_sorder['facebook'] = $show_sf;
        if ($show_sg) $show_sorder['google-plus'] = $show_sg;
        if ($show_sl) $show_sorder['linkedin'] = $show_sl;
        if ($show_sy) $show_sorder['youtube'] = $show_sy;
        if ($show_sv) $show_sorder['vimeo'] = $show_sv;
        if ($show_sw) $show_sorder['user_url'] = $show_sw;
        if ($show_se) $show_sorder['user_email'] = $show_se;
        asort($show_sorder);
    endif;
    
    /****** ITERATION 
    due to the multiple authors possibility ******/
    $html_multi = '';
    if (!empty($tmp_multi) && $alter_idpost)
        $html_multi .= "<div class='authorxtra'>";
    $html_multi .= iterate_xtras($user_ids,$show_n,$alter_idpost,$show_i,$iclass,$show_d,$show_s,$show_sorder,$show_order);
    if (!empty($tmp_multi) && $alter_idpost)
        $html_multi .= "</div>";
    if (!empty($tmp_multi))
        $html_multi .= iterate_xtras($tmp_multi,$show_n,0,$show_i,$iclass,$show_d,$show_s,$show_sorder,$show_order);
    if ($html_multi)
        return "<div class='".$wrap_class."'>".$html_multi."</div>";
}
//iterate through multiple authors
function iterate_xtras($user_ids,$show_n,$alter_idpost,$show_i,$iclass,$show_d,$show_s,$show_sorder,$show_order) {
    $iteration = count($user_ids);
    $html_all = '';
    foreach ($user_ids as $user_id):
        $htmls = $html_i = $html_d = $html_n = $html_s = '';
        //show name
        if($show_n):
            if ($alter_idpost){
                $_n = get_post_meta($alter_idpost, 'altername', true);
                $n_url = "<a href='".get_post_meta($alter_idpost, 'alterlnk', true)."' title='".$_n."'>";
            }else{
                $user_info = get_userdata($user_id);
                $_n = $user_info->display_name;
                $n_url = "<a href='".get_author_posts_url($user_id)."' title='".$_n."'>";
            }
            $html_n = "<div class='profilextra_n'>".$n_url.$_n."</a></div>";
        endif;

        //show image
        if($show_i):
            if ($alter_idpost){
                $alterimg = get_post_meta($alter_idpost, 'alterimg', true);
                $avatar = "<img src='".$alterimg."'>";
            }else{
                //get options
                $noavatar = get_user_meta($user_id, 'profilextra_avatar',true);
                $imgsrc = get_user_meta($user_id, 'profilextra_imgsrc',true);
                if ($noavatar=="not"){
                    $avatar = "<img src='". $imgsrc ."' ";
                    if ($iclass) $avatar .= "class='". $iclass ."' ";
                    $avatar .= "/>";
                }else{
                    $avatar = get_avatar( $user_id, '120', '', '', array('class'=>$iclass) );
                }
            }
            if ($avatar)
                $html_i = "<div class='profilextra_i'>".$avatar."</div>";
        endif;

        //show description
        if($show_d):
            $description = nl2br(get_the_author_meta('description', $user_id));
            if ($description)
                $html_d = "<div class='profilextra_d'>".$description."</div>";
        endif;

        //show social
        if($show_s && !$alter_idpost):            
            //do it
            $last_v = end($show_sorder);
            $xsep = $options['xsep'];
            foreach ($show_sorder as $k=>$v){
                $meta_s = profilextra_get_social($use_icons, $use_names, $k, $user_id);
                if ($meta_s):
                    if ($v == $last_v)
                        $html_s .= $meta_s;
                    else
                        $html_s .= $meta_s.$xsep;
                endif;
            }
            if ($html_s)
                $html_s = "<div class='profilextra_s'>".$html_s."</div>";
        endif;
    
        //draw it, in the right order
        foreach ($show_order as $k=>$v) {
            if ($k=="n") $htmls .= $html_n;
            if ($k=="i") $htmls .= $html_i;
            if ($k=="d") $htmls .= $html_d;
            if ($k=="s") $htmls .= $html_s;
        }
    
        if ($htmls)
            if ($iteration > 1)
                $html_all .= "<div class='authorxtra'>".$htmls."</div>";
            else
                $html_all .= $htmls;
    //ITERATION END
    endforeach;
return $html_all;
}
//draw xtra social contact
function profilextra_get_social($use_icons, $use_names, $social, $user_id){
    //get xtra contact user profile
    if ($social =="user_url" || $social=="user_email")
        $meta = get_the_author_meta( $social, $user_id );
    else
        $meta = get_user_meta( $user_id, $social, true);
    if(empty($meta)) return false;
    //do it
    $i=''; $n='';
    //icons
    if ($use_icons*1):
        if ($social=="user_url")
            $fa_icon = "fa-external-link";
        elseif ($social=="user_email")
            $fa_icon = "fa-at";
        else
            $fa_icon = "fa-". $social;
        if ($use_icons == "2" && $social!="user_email")
            $fa_icon .= "-square";
        $i = "<i class='fa ".$fa_icon."'></i>";
    endif;
    //names
    $socialname = $social;
    if ($use_names*1):
        if ($social == "user_url")
            $socialname = "website";
        elseif ($social == "user_email")
            $socialname = "email";
        $n = "<span>".$socialname."</span>";
    endif;
    if (!($i || $n)) return false;
    //let's continue building social url
    if ($social=="user_email")
        $href="mailto:".$meta;
    elseif ($social=="twitter" && strpos($meta, "twitter.com")===false)
        $href="https://twitter.com/".$meta;
    elseif ($social=="facebook" && strpos($meta, "facebook.com")===false)
        $href="https://www.facebook.com/".$meta;
    elseif ($social=="google-plus" && strpos($meta, "plus.google.com")===false)
        $href="https://plus.google.com/".$meta;
    elseif ($social=="linkedin" && strpos($meta, "linkedin.com")===false)
        $href="https://linkedin.com/in/".$meta;
    else
        $href=$meta;
    $target="_blank";
    if ($social=="user_email") $target="_top";
    //return link to social contact
    return "<a href='".$href."' target='".$target."' title='".$socialname."'>".$i.$n."</a>";
}
