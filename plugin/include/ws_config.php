<?php
defined('PWA_BACKEND_PATH') or die('Hacking attempt!');

/**
 * Returns backend configuration
 */
function pwa_backend_ws_getConfig()
{
    global $conf;

    $ret = array();

    $copy_fields = array(
        'gallery_locked',
        'gallery_title',
        'week_starts_on',
        'category_url_style',
        'picture_url_style',
        'tag_url_style',

        'activate_comments',
        'comments_forall',
        'comments_order',
        'comments_author_mandatory',
        'comments_email_mandatory',
        'comments_enable_website',
        'comments_validation',
        'user_can_delete_comment',
        'user_can_edit_comment',

        'rate',
        'rate_anonymous',
        'rate_items',

        'nb_categories_page',
        'nb_comment_page',
    );

    foreach ($copy_fields as $field) {
        $ret[$field] = $conf[$field];
    }

    $ret['page_banner'] = trigger_change(
        'render_page_banner',
        str_replace('%gallery_title%', $conf['gallery_title'], $conf['page_banner'])
    );

    $ret['image_order'] = str_replace('ORDER BY ', '', $conf['order_by']);
    $ret['derivatives'] = safe_unserialize($conf['derivatives'])['d'];
    $ret['picture_informations'] = safe_unserialize($conf['picture_informations']);

    return $ret;
}
