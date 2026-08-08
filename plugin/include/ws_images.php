<?php
defined('PWA_BACKEND_PATH') or die('Hacking attempt!');

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
