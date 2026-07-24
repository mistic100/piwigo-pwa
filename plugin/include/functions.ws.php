<?php
defined('PWA_BACKEND_PATH') or die('Hacking attempt!');

/**
 * Add custom config endpoint
 * https://github.com/Piwigo/Piwigo/pull/2216
 */
function pwa_backend_ws_add_methods($arr)
{
    $service = &$arr[0];

    $service->addMethod(
        'pwa.getConfig',
        'pwa_backend_ws_get_config',
        null,
        'Returns global configuration.'
    );
}

function pwa_backend_ws_get_config()
{
    global $conf;

    $ret = array();

    $copy_fields = array(
        'gallery_locked',
        'gallery_title',
        'page_banner',
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

    $ret['derivatives'] = safe_unserialize($conf['derivatives'])['d'];
    $ret['picture_informations'] = safe_unserialize($conf['picture_informations']);
    $ret[PWA_BACKEND_ID] = safe_unserialize($conf[PWA_BACKEND_ID]);

    return trigger_change('pwa_backend_ws_get_config', $ret);
}

/**
 * Intercept some methods to add new data 
 * https://github.com/Piwigo/Piwigo/pull/2219
 */
function pwa_backend_ws_patch_methods($result, $methodName, $params)
{
    global $conf;

    if (
        $methodName === 'pwg.categories.getImages'
        || $methodName === 'pwg.images.search'
        || $methodName === 'pwg.tags.getImages'
    ) {
        pwa_backend_ws_patch_getImages($result);
    }

    if ($conf[PWA_BACKEND_ID]['add_cors_headers']) {
        @header('Access-Control-Allow-Origin: ' . $conf[PWA_BACKEND_ID]['frontend_url']);
    }

    return $result;
}

function pwa_backend_ws_patch_getImages(&$result)
{
    global $pwg_loaded_plugins;

    $images = &$result['images']->_content;

    if (empty($images)) {
        return;
    }

    $additional_fields = array(
        'latitude',
        'longitude',
    );
    if (isset($pwg_loaded_plugins['PhotoSphere'])) {
        $additional_fields[] = 'is_sphere';
    }
    if (isset($pwg_loaded_plugins['gvideo'])) {
        $additional_fields[] = 'is_gvideo';
    }
    // if (isset($pwg_loaded_plugins['piwigo-openstreetmap'])) {
    //     $additional_fields[] = 'latitude';
    //     $additional_fields[] = 'longitude';
    // }

    // if (empty($additional_fields)) {
    //     return;
    // }

    $image_ids = array_column($images, 'id');

    $query = '
    SELECT id, ' . implode(',', $additional_fields) . '
    FROM ' . IMAGES_TABLE . '
    WHERE id IN(' . implode(',', $image_ids) . ')
    ;';

    $data = query2array($query, 'id');

    foreach ($images as &$image) {
        foreach ($additional_fields as $field) {
            $image[$field] = $data[$image['id']][$field];
        }
    }
}
