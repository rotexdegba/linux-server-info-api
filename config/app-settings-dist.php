<?php
$app_settings_ds = DIRECTORY_SEPARATOR;
// Copy this file to ./config/app-settings.php when setting up your app in a new environment
// You should not commit ./config/app-settings.php into version control, since it's expected
// to contain sensitive information like database passwords, etc.
return [
    
    // the settings array below will be accessible in your app's container $c via
    // $c['settings'] or $c->get('settings') and will also be accessible in the
    // container object inside ./config/dependencies.php
    
    'settings' => [
        ///////////////////////////////
        // Slim PHP Related Settings
        //////////////////////////////
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
        /////////////////////////////////////
        // End of Slim PHP Related Settings
        /////////////////////////////////////
        
        /////////////////////////////////////////////
        // Your App's Environment Specific Settings
        /////////////////////////////////////////////

        //////////////////////////////////////////////////////////////////////////////
        //
        //  Put environment specific settings below.
        //  You can access the settings via your app's container
        //  object (e.g. $c) like this: $c->get('settings')['specific_setting_1']
        //  where `specific_setting_1` can be replaced with the actual setting name
        //  e.g. like the `bind_options` setting name below.
        // 
        //////////////////////////////////////////////////////////////////////////////
        
        'vespula_auth_adapter_obj' => function(): Vespula\Auth\Auth {
    
            // Using this static singleton pattern
            // to ensure this function always returns
            // the same instance.
            static $vespulaAuthAdapterObj;
            
            if(!$vespulaAuthAdapterObj) {
                
                // Setup your vespula auth object here
                // See https://packagist.org/packages/vespula/auth
                // for documentation about how to configure 
                // an instance of Vespula\Auth\Auth with
                // the various types of adapters it provides.
                // 
                // This app provides a default instance of
                // Vespula\Auth\Auth configured that uses
                // the sql adapter to authenticate against
                // an in-memory sqlite databse.
                //      ||   ||   ||   ||   ||   ||
                //     VVVV VVVV VVVV VVVV VVVV VVVV
               
                ////////////////////////////////////////////////////////////////////////////
                // Start Vespula.Auth Authentication setup
                ////////////////////////////////////////////////////////////////////////////

                $pdo = new \PDO(
                            'sqlite::memory:', 
                            null, 
                            null, 
                            [
                                PDO::ATTR_PERSISTENT => true, 
                                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
                            ]
                        ); 

                $pass1 = password_hash('admin' , PASSWORD_DEFAULT);
                $pass2 = password_hash('root' , PASSWORD_DEFAULT);

                $sql = <<<SQL
DROP TABLE IF EXISTS "user_authentication_accounts";
CREATE TABLE user_authentication_accounts (
    username VARCHAR(255), password VARCHAR(255)
);
INSERT INTO "user_authentication_accounts" VALUES( 'admin', '$pass1' );
INSERT INTO "user_authentication_accounts" VALUES( 'root', '$pass2' );
SQL;
                $pdo->exec($sql); //add two default user accounts

                //Optionally pass a maximum idle time and a time until the session 
                //expires (in seconds)
                $expire = 3600;
                $max_idle = 1200;
                $session = new \Vespula\Auth\Session\Session($max_idle, $expire);

                $cols = ['username', 'password'];
                $from = 'user_authentication_accounts';
                $where = ''; //optional

                $adapter = new \Vespula\Auth\Adapter\Sql($pdo, $from, $cols, $where);

                $vespulaAuthAdapterObj =  new \Vespula\Auth\Auth($adapter, $session);
                
                ////////////////////////////////////////////////////////////////////////////
                // End Vespula.Auth Authentication setup
                ////////////////////////////////////////////////////////////////////////////
            }
            
            return $vespulaAuthAdapterObj;
        },
        
        'atlas' => [
            'pdo' => [
                'sqlite:'.dirname(dirname(__FILE__))."{$app_settings_ds}storage{$app_settings_ds}sqlite{$app_settings_ds}token_management_dev.sqlite"
            ],
            'namespace' => 'Lsia\\Atlas\\Models',
            'directory' => dirname(dirname(__FILE__))."{$app_settings_ds}src{$app_settings_ds}models{$app_settings_ds}atlas",
        ],
        
        ////////////////////////////////////////////////////
        // End of Your App's Environment Specific Settings
        ////////////////////////////////////////////////////
    ]
];
