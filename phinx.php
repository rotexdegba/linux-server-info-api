<?php
define('S3MVC_APP_ENV_DEV', 'development');
define('S3MVC_APP_ENV_PRODUCTION', 'production');
define('S3MVC_APP_ENV_STAGING', 'staging');
define('S3MVC_APP_ENV_TESTING', 'testing');
$env = include './config/env.php';

if (!in_array($env, [S3MVC_APP_ENV_DEV, S3MVC_APP_ENV_PRODUCTION, S3MVC_APP_ENV_TESTING])) {

    // default to dev
    $env = S3MVC_APP_ENV_DEV;
}

$app_settings = include './config/app-settings.php';

$phinx_config_array = [
    'migration_base_class' => "\\Lsia\\BasePhinxDbMigration",
    'paths' => [
        'migrations' => "%%PHINX_CONFIG_DIR%%/phinx/migrations",
        'seeds' => "%%PHINX_CONFIG_DIR%%/phinx/seeds"
    ],
    'environments' => [
        'default_migration_table' => "phinxlog",
        'default_database' => 'development',
        $env => [
            'adapter' => $app_settings['settings']['db_adapter'],
               'name' => $app_settings['settings']['db_name'],
            'charset' => $app_settings['settings']['db_charset']
        ],
    ],
    'version_order' => "creation",
];

if ($phinx_config_array['environments'][$env]['adapter'] !== 'sqlite') {

    $phinx_config_array['environments'][$env]['host'] = $app_settings['settings']['db_host'];
    $phinx_config_array['environments'][$env]['user'] = $app_settings['settings']['db_uname'];
    $phinx_config_array['environments'][$env]['pass'] = $app_settings['settings']['db_passwd'];
    $phinx_config_array['environments'][$env]['port'] = $app_settings['settings']['db_port'];
    
} else {

    $phinx_config_array['environments'][$env]['suffix'] = ".sqlite";
}

if (!array_key_exists(S3MVC_APP_ENV_DEV, $phinx_config_array['environments'])) {

    // make sure there is always a 'development' environment definition
    $phinx_config_array['environments'][S3MVC_APP_ENV_DEV] = [
        'adapter' => $app_settings['settings']['db_adapter'],
           'name' => $app_settings['settings']['db_name'],
        'charset' => $app_settings['settings']['db_charset']
    ];

    if ($phinx_config_array['environments'][S3MVC_APP_ENV_DEV]['adapter'] !== 'sqlite') {

        $phinx_config_array['environments'][S3MVC_APP_ENV_DEV]['host'] = $app_settings['settings']['db_host'];
        $phinx_config_array['environments'][S3MVC_APP_ENV_DEV]['user'] = $app_settings['settings']['db_uname'];
        $phinx_config_array['environments'][S3MVC_APP_ENV_DEV]['pass'] = $app_settings['settings']['db_passwd'];
        $phinx_config_array['environments'][S3MVC_APP_ENV_DEV]['port'] = $app_settings['settings']['db_port'];
        
    } else {

        $phinx_config_array['environments'][S3MVC_APP_ENV_DEV]['suffix'] = ".sqlite";
    }
}

return $phinx_config_array;
