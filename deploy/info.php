<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'software_repository';
$app['version'] = '2.3.0';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('software_repository_app_description');
$app['tooltip'] = lang('software_repository_app_tooltip');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('software_repository_app_name');
$app['category'] = lang('base_category_cloud');
$app['subcategory'] = lang('base_subcategory_updates');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['delete_dependency'] = array(
    'app-software-repository-core'
);

$app['core_requires'] = array(
    'app-base-core >= 1:2.2.14',
);

$app['core_file_manifest'] = array(
    'app-software-repository.cron' => array(
        'target' => '/etc/cron.d/app-software-repository',
        'config' => TRUE,
        'config_params' => 'noreplace',
    ),
);

/////////////////////////////////////////////////////////////////////////////
// App Events
/////////////////////////////////////////////////////////////////////////////

$app['event_types'] = array(
    'SOFTWARE_REPOSITORY_CONFIG_WARNING',
);
