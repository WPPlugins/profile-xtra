<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

$options = get_option( 'profilextra_settings' );
$keep_option = $options['on_uninstall'];
/** remembering keep options **/
/*
    0:  Keep all profile xtra metadata, and settings
    1:  Keep only the xtra social contacts metadata
    2:  Keep only the xtra profile image metadata
    3:  Keep only the alterauthor metadata
    4:  Delete all profile xtra meta data, and settings
*/

if ($keep_option == '0') return;

global $wpdb;

/* Delete xtra user & post metas */

if ($keep_option != '3')
$wpdb->query("
        DELETE FROM {$wpdb->postmeta} WHERE meta_key in ('altername', 'galterlnk', 'alterimg', 'alterdata')
    ");

if ($keep_option != '2')
$wpdb->query("
    DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%profilextra_%'
");

if ($keep_option != '1')
$wpdb->query("
        DELETE FROM {$wpdb->usermeta} WHERE meta_key in ('twitter', 'google-plus', 'linkedin', 'facebook')
    ");

/* Delete option settings */
delete_option( 'profilextra_settings' );
?>
