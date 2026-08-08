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
        'Returns global configuration.',
        PWA_BACKEND_PATH . 'include/ws_config.php'
    );

    $service->addMethod(
        'pwa.categories.getInfo',
        'pwa_backend_ws_categories_getInfo',
        array(
            'cat_id' => array('type' => WS_TYPE_ID),
            'public' => array('default' => false, 'type' => WS_TYPE_BOOL),
        ),
        'Returns information about a category.',
        PWA_BACKEND_PATH . 'include/ws_categories.php'
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
 * Intercept some methods to add new data 
 * https://github.com/Piwigo/Piwigo/pull/2219
 */
function pwa_backend_ws_patch_methods($result, $methodName, $params)
{
    global $conf, $service;

    if (
        $methodName === 'pwg.categories.getImages'
        || $methodName === 'pwg.images.search'
        || $methodName === 'pwg.tags.getImages'
    ) {
        include_once(PWA_BACKEND_PATH . 'include/ws_images.php');
        pwa_backend_ws_patch_getImages($result);
    }

    if ($methodName === 'pwg.images.getInfo') {
        include_once(PWA_BACKEND_PATH . 'include/ws_images.php');
        pwa_backend_ws_patch_images_getInfo($result, $params);
    }

    if ($conf[PWA_BACKEND_ID]['add_cors_headers']) {
        @header('Access-Control-Allow-Origin: ' . $conf[PWA_BACKEND_ID]['frontend_url']);
    }

    return $result;
}
