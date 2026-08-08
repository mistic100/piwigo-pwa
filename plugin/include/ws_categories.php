<?php
defined('PWA_BACKEND_PATH') or die('Hacking attempt!');

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
