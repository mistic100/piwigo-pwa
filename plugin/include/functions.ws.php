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
        'pwa_backend_ws_getConfig',
        null,
        'Returns global configuration.'
    );

    $service->addMethod(
        'pwa.categories.getInfo',
        'pwa_backend_ws_categories_getInfo',
        array(
            'cat_id' => array('type' => WS_TYPE_ID),
            'public' => array('default' => false, 'type' => WS_TYPE_BOOL),
        ),
        'Returns information about a category.'
    );

    // add custom parameters (not required, only used by the API explorer)
    $service->_methods['pwg.images.getInfo']['signature']['content_cat_id'] = array(
        'type' => WS_TYPE_INT | WS_TYPE_POSITIVE,
        'default' => null,
        'flags' => WS_PARAM_OPTIONAL,
    );
    $service->_methods['pwg.images.getInfo']['signature']['content_tag_id'] = array(
        'type' => WS_TYPE_INT | WS_TYPE_POSITIVE,
        'default' => null,
        'flags' => WS_PARAM_OPTIONAL,
    );
    $service->_methods['pwg.images.getInfo']['signature']['content_order'] = array(
        'type' => 0,
        'default' => null,
        'info' => 'id, file, name, hit, rating_score, date_creation, date_available, random',
        'flags' => WS_PARAM_OPTIONAL,
    );
}

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
        'page_banner',
        'week_starts_on',
        'order_by',
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
 * Returns information about a category
 */
function pwa_backend_ws_categories_getInfo($params)
{
    global $user, $conf;

    $where[] = 'id=' . (int)($params['cat_id']);
    $join_type = 'INNER';
    $join_user = $user['id'];

    if ($params['public']) {
        $where[] = 'status = "public"';
        $where[] = 'visible = "true"';

        $join_user = $conf['guest_id'];
    }

    $query = '
SELECT
    id, name, comment, permalink, status,
    uppercats, global_rank, id_uppercat,
    nb_images, count_images AS total_nb_images,
    representative_picture_id,
    date_last, max_date_last, count_categories AS nb_categories,
    image_order
FROM ' . CATEGORIES_TABLE . '
    ' . $join_type . ' JOIN ' . USER_CACHE_CATEGORIES_TABLE . '
    ON id=cat_id AND user_id=' . $join_user . '
WHERE ' . implode("\n    AND ", $where);

    $result = pwg_query($query);

    if (pwg_db_num_rows($result) == 0) {
        return new PwgError(404, 'cat_id not found');
    }

    $row = pwg_db_fetch_assoc($result);

    $row['url'] = make_index_url(
        array('category' => $row)
    );

    foreach (array('id', 'nb_images', 'total_nb_images', 'nb_categories') as $key) {
        $row[$key] = (int)$row[$key];
    }

    $row['name_raw'] = $row['name'];

    $row['name'] = strip_tags(
        trigger_change(
            'render_category_name',
            $row['name'],
            'ws_categories_getList'
        )
    );

    $row['comment_raw'] = $row['comment'];

    $row['comment'] = (string) trigger_change(
        'render_category_description',
        $row['comment'],
        'ws_categories_getList'
    );

    if (empty($row['image_order'])) {
        $row['image_order'] = str_replace('ORDER BY ', '', $conf['order_by']);
    }

    if (!empty($row['uppercats'])) {
        $query = '
SELECT id, name
FROM ' . CATEGORIES_TABLE . '
WHERE id IN (' . $row['uppercats'] . ')
AND id != ' . $row['id'] . '
;';
        $row['parents'] = query2array($query);

        foreach ($row['parents'] as $parent) {
            $parent['name'] = strip_tags(
                trigger_change(
                    'render_category_name',
                    $parent['name'],
                    'ws_categories_getList'
                )
            );
        }
    } else {
        $row['parents'] = array();
    }

    return $row;
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

    if ($methodName === 'pwg.images.getInfo') {
        pwa_backend_ws_patch_images_getInfo($result, $params);
    }

    if ($conf[PWA_BACKEND_ID]['add_cors_headers']) {
        @header('Access-Control-Allow-Origin: ' . $conf[PWA_BACKEND_ID]['frontend_url']);
    }

    return $result;
}

/**
 * Add additional data to image info
 */
function pwa_backend_ws_patch_images_getInfo(&$result, $params)
{
    global $conf;

    if (isset($result['image'])) {
        $image = &$result['image']->_content;
    } else {
        $image = &$result;
    }

    // add navigation info in the current category/tag (number of images, previous item, next item)
    $order = isset($params['content_order']) ? 'ORDER BY ' . $params['content_order'] : $conf['order_by'];

    if (isset($params['content_cat_id'])) {
        $cat_id = $params['content_cat_id'];
        $categories = &$image['categories']->_content;

        $found = false;
        foreach ($categories as $cat) {
            if ($cat['id'] === $cat_id) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            return;
        }

        $items = get_image_ids_for_categories(array($cat_id), 'AND', '', $order, true);
    } else if (isset($params['content_tag_id'])) {
        $tag_id = $params['content_tag_id'];
        $tags = &$image['tags']->_content;

        $found = false;
        foreach ($tags as $tag) {
            if ($tag['id'] === $tag_id) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            return;
        }

        $items = get_image_ids_for_tags(array($tag_id), 'AND', '', $order, true);
    }

    if (isset($items)) {
        $rank_of = array_flip($items);

        $navigation = array(
            'nb_images' => count($items),
            'current_rank' => $rank_of[$image['id']],
        );

        $ids = array();

        if ($navigation['current_rank'] !== 0) {
            $navigation['previous_item'] = $items[$navigation['current_rank'] - 1];
            $ids[] = $navigation['previous_item'];
            // $navigation['first_item'] = $items[ 0 ];
        }

        if ($navigation['current_rank'] !== count($items) - 1) {
            $navigation['next_item'] = $items[$navigation['current_rank'] + 1];
            $ids[] = $navigation['next_item'];
            // $navigation['last_item'] = $items[ $navigation['last_rank'] ];
        }

        if (!empty($ids)) {
            $query = '
SELECT id, name
FROM ' . IMAGES_TABLE . '
WHERE id IN (' . implode(',', $ids) . ')
;';
            $query_result = pwg_query($query);

            while ($row = pwg_db_fetch_assoc($query_result)) {
                if (isset($navigation['previous_item']) and $row['id'] == $navigation['previous_item']) {
                    $navigation['previous_item'] = $row;
                } elseif (isset($navigation['next_item']) and $row['id'] == $navigation['next_item']) {
                    $navigation['next_item'] = $row;
                }
            }
        }

        $image['navigation'] = $navigation;
    }
}

/**
 * Add additional data to images lists
 */
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
