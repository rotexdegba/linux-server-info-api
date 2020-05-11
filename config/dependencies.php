<?php
/////////////////
// Dependencies
/////////////////

////////////////////////////////////////////////////////////////////////////////
// Start configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////
$container['logger'] = function () {

    $ds = DIRECTORY_SEPARATOR;
    $log_type = \Vespula\Log\Adapter\ErrorLog::TYPE_FILE;
    $file = S3MVC_APP_ROOT_PATH . "{$ds}logs{$ds}daily_log_" . date('Y_M_d') . '.txt';
    
    $adapter = new \Vespula\Log\Adapter\ErrorLog($log_type , $file);
    $adapter->setMessageFormat('[{timestamp}] [{level}] {message}');
    $adapter->setMinLevel(Psr\Log\LogLevel::DEBUG);
    $adapter->setDateFormat('Y-M-d g:i:s A');
    
    return new \Vespula\Log\Log($adapter);
};

// this MUST be replaced with any subclass of \\Slim3MvcTools\\Controllers\\BaseController
$container['errorHandlerClass'] = \Lsia\Controllers\HttpErrorHandler::class;

//Override the default 500 System Error Handler
$container['errorHandler'] = function ($c) {
    
    return function (
            \Psr\Http\Message\ServerRequestInterface $request, 
            \Psr\Http\Message\ResponseInterface $response, 
            \Exception $exception
          ) use ($c) {
        
        $errorHandlerClass = $c['errorHandlerClass'];
        $errorHandler = new $errorHandlerClass( $c, '', '', $request, $response);

        $response_from_pre_action = $errorHandler->preAction();
        
        // invoke the server error handler
        $action_response = $errorHandler->generateServerErrorResponse($exception, $request, $response_from_pre_action);
        
        return $errorHandler->postAction($action_response);
    };
};

// this MUST be replaced with any subclass of \\Slim3MvcTools\\Controllers\\BaseController
$container['notFoundHandlerClass'] = \Lsia\Controllers\HttpErrorHandler::class;

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    
    return function (
                \Psr\Http\Message\ServerRequestInterface $request, 
                \Psr\Http\Message\ResponseInterface $response,
                $_404_page_contents_str = null,
                $_404_page_additional_log_msg = null
            ) use ($c) {
 
        $notFoundHandlerClass = $c['notFoundHandlerClass'];
        $notFoundHandler = new $notFoundHandlerClass( $c, '', '', $request, $response);
        
        $notFoundHandler->setResponse( $notFoundHandler->preAction() );
        
        //invoke the not found handler
        $action_response = $notFoundHandler->actionHttpNotFound($_404_page_contents_str, $_404_page_additional_log_msg);
        
        return $notFoundHandler->postAction($action_response);
    };
};

// this MUST be replaced with any subclass of \\Slim3MvcTools\\Controllers\\BaseController
$container['notAllowedHandlerClass'] = \Lsia\Controllers\HttpErrorHandler::class;

//Override the default Not Allowed Handler
$container['notAllowedHandler'] = function ($c) {
    
    return function (
                \Psr\Http\Message\ServerRequestInterface $request, 
                \Psr\Http\Message\ResponseInterface $response, 
                $methods
            ) use ($c) {
        
        $notAllowedHandlerClass = $c['notAllowedHandlerClass'];
        $notAllowedHandler = new $notAllowedHandlerClass( $c, '', '', $request, $response);

        $response_from_pre_action = $notAllowedHandler->preAction();
        
        // invoke the notAllowed handler
        $action_response = $notAllowedHandler->generateNotAllowedResponse($methods, $request, $response_from_pre_action);
        
        return $notAllowedHandler->postAction($action_response);
    };
};

//Add the namespcace(s) for your web-app's controller classes or leave it
//as is, if your controllers are in the default global namespace.
//The namespaces are searched in the order which they are added 
//to the array. It would make sense to add the namespaces for your
//application in the front part of these arrays so that if a controller class 
//exists in \Slim3MvcTools\Controllers\ and / or \Slim3SkeletonMvcApp\Controllers\  
//and in your application's controller namespace(s) controllers
//in your application's namespaces are 
//Make sure you add the trailing slashes.
$container['namespaces_for_controllers'] = [
    '\\Lsia\\Controllers\\',
    '\\Slim3MvcTools\\Controllers\\',
    '\\Slim3SkeletonMvcApp\\Controllers\\'
];

//Object for rendering layout files
$container['new_layout_renderer'] = $container->factory(function ($c) {
    
    //return a new instance on each access to $container['new_layout_renderer']
    $ds = DIRECTORY_SEPARATOR;
    $path_2_layout_files = S3MVC_APP_ROOT_PATH.$ds.'src'.$ds.'layout-templates';
    $layout_renderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$path_2_layout_files]);
    
    // add common data
    foreach ($c['common_data_for_views_and_templates'] as $key => $val) {
        
        $layout_renderer->$key = $val;
    }
    
    return $layout_renderer;
});

//Object for rendering view files
$container['new_view_renderer'] = $container->factory(function ($c) {
    
    //return a new instance on each access to $container['new_view_renderer']
    $ds = DIRECTORY_SEPARATOR;
    $path_2_view_files = S3MVC_APP_ROOT_PATH.$ds.'src'.$ds.'views'."{$ds}base";
    $view_renderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$path_2_view_files]);

    // add common data
    foreach ($c['common_data_for_views_and_templates'] as $key => $val) {
        
        $view_renderer->$key = $val;
    }
    
    return $view_renderer;
});

////////////////////////////////////////////////////////////////////////////////
// End configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////
// Start Vespula.Auth Authentication setup
////////////////////////////////////////////////////////////////////////////   

if( s3MVC_GetCurrentAppEnvironment() === S3MVC_APP_ENV_PRODUCTION ) {
    
    //configuration specific to the production environment
    
    ////////////////////////////////////////////////////////////////////////////
    // Start Vespula.Auth LDAP Authentication setup
    ////////////////////////////////////////////////////////////////////////////    
    $container['vespula_auth'] = function ($c) {
        
        //Optionally pass a maximum idle time and a time until the session 
        //expires (in seconds)
        $expire = 3600;
        $max_idle = 1200;
        $session = new \Vespula\Auth\Session\Session($max_idle, $expire, 'VESPULA_AUTH_DATA_'.S3MVC_APP_ROOT_PATH);

        $bind_options = $c->get('settings')['bind_options'];

        $ldap_options = [LDAP_OPT_PROTOCOL_VERSION=>3, LDAP_OPT_REFERRALS=>0];
        
        $attributes = [
            'mail',
            'givenname'
        ];

        $uri = $c->get('settings')['ldap_server_addr'];
        $dn = null;
        
        $adapter = new \Vespula\Auth\Adapter\Ldap(
                        $uri, $dn, $bind_options, $ldap_options, $attributes
                    );
        
        return new \Vespula\Auth\Auth($adapter, $session);
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Vespula.Auth LDAP Authentication setup
    ////////////////////////////////////////////////////////////////////////////
    
} else {
    
    //configuration specific to non-production environments
    
    ////////////////////////////////////////////////////////////////////////////
    // Start Vespula.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////
    $container['vespula_auth'] = function () {
        
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
        
        return new \Vespula\Auth\Auth($adapter, $session);
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Vespula.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////////
// End Vespula.Auth Authentication setup
////////////////////////////////////////////////////////////////////////////


$container['aura_session'] = function () {

    $session_factory = new \Aura\Session\SessionFactory;
   
    return $session_factory->newInstance($_COOKIE);
};

$container['common_data_for_views_and_templates'] = function ($c) {
    
    $txt_no = 'No';
    $txt_yes = 'Yes';
    $data = [];
    $data['__yes_no_vals'] = [
        0 => $txt_no, '0' => $txt_no, false => $txt_no,
        1 => $txt_yes, '1' => $txt_yes, true => $txt_yes,
    ];
    $data['__yes_no_record_col_names'] = [];
//    $data['__acl_obj'] = $c['promis_acl'];
//    $data['__locale_obj'] = $c['vespula_locale_obj'];
//    $data['__debugbar'] = $c['debugbar'];
    $data['__current_uri_obj'] = $c['request']->getUri();
//    $data['__show_debugbar'] = $c['show_debugbar'];
//    $data['__breadcumb_items'] = $c['breadcumb_items'];
    $data['__csrf_key'] = \Lsia\Controllers\AppBase::CSRF_FORM_FIELD_KEY;
    $data['__csrf_value'] = null;

    $controller_sess_seg_key = \Lsia\Controllers\AppBase::CONTROLLER_SESSION_SEGMENT_KEY;
    $controller_sess_segment = 
        $c['aura_session']->getSegment($controller_sess_seg_key);
    
        
    $data['__logged_in_user_name'] = '';
    $data['__logged_in_user_record'] = [];
    $data['__is_logged_in'] = ($c['vespula_auth']->isValid() === true);
    
    
    if( $data['__is_logged_in'] ) {

        $data['__logged_in_user_record'] = $c['vespula_auth']->getUserdata();
        $data['__logged_in_user_name'] = $c['vespula_auth']->getUsername();
        
        $data['__csrf_value'] = htmlspecialchars(
            $c['aura_session']->getCsrfToken()
                              ->getValue(),
            ENT_QUOTES, 
            'UTF-8'
        );
    }

    $default_controller_class_name = S3MVC_APP_DEFAULT_CONTROLLER_CLASS_NAME;
    $controller_class_parts = explode('\\', $default_controller_class_name);
    $default_controller_for_uri = 
        \Slim3MvcTools\Functions\Str\toDashes( array_pop( $controller_class_parts ) );

    $data['__controller_name_from_uri'] = $c['request']->getAttribute('controller', $default_controller_for_uri);
    $data['__action_name_from_uri'] = $c['request']->getAttribute('action', '');

    $data['__last_flash_message'] = 
        $controller_sess_segment->getFlash(\Lsia\Controllers\AppBase::FLASH_MESSAGE_KEY, null);
    
    $data['__last_flash_message_css_class'] = 
        $controller_sess_segment->getFlash(\Lsia\Controllers\AppBase::FLASH_MESSAGE_CSS_CLASS_KEY, null);
    
    return $data;
};

$container['new_response_body'] = $container->factory(function () {
    
    //return a new instance on each access to $container['new_response_body']
    return new \Slim\Http\Body(fopen('php://temp', 'r+'));
});

$container['atlas'] = function ($c) {
   
    return \Atlas\Orm\Atlas::new(
        ...$c->get('settings')['atlas']['pdo']
    );
};

$container['atlas_info'] = function ($c) {
    
   $connection = \Atlas\Pdo\Connection::new(
        ...$c->get('settings')['atlas']['pdo']
    );
    
    return \Atlas\Info\Info::new($connection);
};

$container['vespula_form_obj'] = $container->factory(function () {
    
    //return a new instance on each access
    return new \Vespula\Form\Form();
});

$container['sirius_validator'] = $container->factory(function () {
    
    //return a new instance on each access
    return new \Sirius\Validation\Validator();
});

$container['ginfo_server_info'] = function ($c) {
    
    return new \Ginfo\Ginfo();
};

$container['linfo_server_info'] = function ($c) {
    
    $settings = [];
    // If you experience timezone errors, uncomment (remove //) the following line and change the timezone to your liking
    // date_default_timezone_set('America/New_York');

    /*
     * Usual configuration
     */
    $settings['byte_notation'] = 1024; // Either 1024 or 1000; defaults to 1024
    $settings['dates'] = 'm/d/y h:i A (T)'; // Format for dates shown. See php.net/date for syntax
    $settings['language'] = 'en'; // Refer to the lang/ folder for supported languages
    $settings['icons'] = true; // simple icons
    $settings['theme'] = 'default'; // Theme file (layout/theme_$n.css). Look at the contents of the layout/ folder for other themes.
    $settings['gzip'] = false; // Manually gzip output. Unneeded if your web server already does it.


    $settings['allow_changing_themes'] = false; // Allow changing the theme per user in the UI?

    /*
     * Possibly don't show stuff
     */

    // For certain reasons, some might choose to not display all we can
    // Set these to true to enable; false to disable. They default to false.
    $settings['show']['kernel'] = true;
    $settings['show']['ip'] = true;
    $settings['show']['os'] = true;
    $settings['show']['load'] = true;
    $settings['show']['ram'] = true;
    $settings['show']['hd'] = true;
    $settings['show']['mounts'] = true;
    $settings['show']['mounts_options'] = true; // Might be useless/confidential information; disabled by default.
    $settings['show']['webservice'] = true; // Might be dangerous/confidential information; disabled by default.
    $settings['show']['phpversion'] = true; // Might be dangerous/confidential information; disabled by default.
    $settings['show']['network'] = true;
    $settings['show']['uptime'] = true;
    $settings['show']['cpu'] = true;
    $settings['show']['process_stats'] = true;
    $settings['show']['hostname'] = true;
    $settings['show']['distro'] = true; # Attempt finding name and version of distribution on Linux systems
    $settings['show']['devices'] = true; # Slow on old systems
    $settings['show']['model'] = true; # Model of system. Supported on certain OS's. ex: Macbook Pro
    $settings['show']['numLoggedIn'] = true; # Number of unqiue users with shells running (on Linux)
    $settings['show']['virtualization'] = true; # whether this is a VPS/VM and what kind

    // CPU Usage on Linux (per core and overall). This requires running sleep(1) once so it slows
    // the entire page load down. Enable at your own inconvenience, especially since the load averages
    // are more useful.
    $settings['cpu_usage'] = true;

    // Sometimes a filesystem mount is mounted more than once. Only list the first one I see?
    // (note, duplicates are not shown twice in the file system totals)
    $settings['show']['duplicate_mounts'] = true;

    // Disabled by default as they require extra config below
    $settings['show']['temps'] = true;
    $settings['show']['raid'] = true;

    // Following are probably only useful on laptop/desktop/workstation systems, not servers, although they work just as well
    $settings['show']['battery'] = true;
    $settings['show']['sound'] = true;
    $settings['show']['wifi'] = false; # Not finished

    // Service monitoring
    $settings['show']['services'] = true;

    /*
     * Misc settings pertaining to the above follow below:
     */

    // Hide certain file systems / devices
    $settings['hide']['filesystems'] = array(
            //'tmpfs', 'ecryptfs', 'nfsd', 'rpc_pipefs', 'proc', 'sysfs',
            //'usbfs', 'devpts', 'fusectl', 'securityfs', 'fuse.truecrypt',
            //'cgroup', 'debugfs', 'mqueue', 'hugetlbfs', 'pstore'
    );
    $settings['hide']['storage_devices'] = array(
            //'gvfs-fuse-daemon', 'none', 'systemd-1', 'udev'
    );

    // filter mountpoints based on PCRE regex, eg '@^/proc@', '@^/sys@', '@^/dev@'
    $settings['hide']['mountpoints_regex'] = [];

    // Hide mount options for these file systems. (very, very suggested, especially the ecryptfs ones)
    $settings['hide']['fs_mount_options'] = array(
            //'ecryptfs'
    );

    // Hide hard drives that begin with /dev/sg?. These are duplicates of usual ones, like /dev/sd?
    $settings['hide']['sg'] = true; # Linux only

    // Set to true to not resolve symlinks in the mountpoint device paths. Eg don't convert /dev/mapper/root to /dev/dm-0
    $settings['hide']['dont_resolve_mountpoint_symlinks'] = false; # Linux only

    // Various softraids. Set to true to enable.
    // Only works if it's available on your system; otherwise does nothing
    $settings['raid']['gmirror'] = false;  # For FreeBSD
    $settings['raid']['mdadm'] = true;  # For Linux; known to support RAID 1, 5, and 6

    // Various ways of getting temps/voltages/etc. Set to true to enable. Currently these are just for Linux
    $settings['temps']['hwmon'] = true; // Requires no extra config, is fast, and is in /sys :)
    $settings['temps']['thermal_zone'] = true;
    $settings['temps']['hddtemp'] = true;
    $settings['temps']['mbmon'] = true;
    $settings['temps']['sensord'] = true; // Part of lm-sensors; logs periodically to syslog. slow
    $settings['temps_show0rpmfans'] = true; // Set to true to show fans with 0 RPM

    // Configuration for getting temps with hddtemp
    $settings['hddtemp']['mode'] = 'daemon'; // Either daemon or syslog
    $settings['hddtemp']['address'] = array( // Address/Port of hddtemp daemon to connect to
            'host' => 'localhost',
            'port' => 7634
    );
    // Configuration for getting temps with mbmon
    $settings['mbmon']['address'] = array( // Address/Port of mbmon daemon to connect to
            'host' => 'localhost',
            'port' => 411
    );

    /*
     * For the things that require executing external programs, such as non-linux OS's
     * and the extensions, you may specify other paths to search for them here:
     */
    $settings['additional_paths'] = array(
             //'/opt/bin' # for example
    );


    /*
     * Services. It works by specifying locations to PID files, which then get checked
     * Either that or specifying a path to the executable, which we'll try to find a running
     * process PID entry for. It'll stop on the first it finds.
     */

    // Format: Label => pid file path
    $settings['services']['pidFiles'] = array(
            // 'Apache' => '/var/run/apache2.pid', // uncomment to enable
            // 'SSHd' => '/var/run/sshd.pid'
    );

    // Format: Label => path to executable or array containing arguments to be checked
    $settings['services']['executables'] = array(
             'MySQLd' => '/usr/sbin/mysqld', // uncomment to enable
             'Apache' => '/usr/sbin/httpd' // uncomment to enable
            // 'BuildSlave' => array('/usr/bin/python', // executable
            //						1 => '/usr/local/bin/buildslave') // argv[1]
    );

    /*
     * Debugging settings
     */

    // Show errors? Disabled by default to hide vulnerabilities / attributes on the server
    $settings['show_errors'] = true;

    // Show results from timing ourselves? Similar to above.
    // Lets you see how much time getting each bit of info takes.
    $settings['timer'] = false;

    // Compress content, can be turned off to view error messages in browser
    $settings['compress_content'] = false;

    /*
     * Occasional sudo
     * Sometimes you may want to have one of the external commands here be ran as root with
     * sudo. This requires the web server user be set to "NOPASS" in your sudoers so the sudo
     * command just works without a prompt.
     *
     * Add names of commands to the array if this is what you want. Just the name of the command;
     * not the complete path. This also applies to commands called by extensions.
     *
     * Note: this is extremely dangerous if done wrong
     */
    $settings['sudo_apps'] = [
        //'ps' // For example
    ];
    
    return new \Linfo\Linfo($settings);
};

$container['trntv_server_info'] = function ($c) {
    
    /** returns an instance of \Probe\Provider\ProviderInterface */
    return \Probe\ProviderFactory::create();
};

$container['danielme85_server_info'] = function ($c) {
    
    return new \danielme85\Server\Info();
};
