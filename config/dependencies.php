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
