<?php
namespace Lsia\Controllers;

/**
 * 
 * Description of AppBase goes here
 *
 * 
 */
class AppBase extends \Slim3MvcTools\Controllers\BaseController
{   
    const CONTROLLER_SESSION_SEGMENT_KEY = self::class;
    const CSRF_FORM_FIELD_KEY = 'DA_SUPA_DUPA_CSRF_KEY';
    
    const FLASH_MESSAGE_KEY = 'FLASH_MESSAGE_KEY';
    const FLASH_MESSAGE_CSS_CLASS_KEY = 'FLASH_MESSAGE_CSS_CLASS_KEY';
    
    /**
     *
     * @var \Aura\Session\Segment
     * 
     */
    protected $aura_session_segment = null;
    
    /**
     * 
     * Will be used in actionLogin() to construct the url to redirect to upon successful login,
     * if $_SESSION[static::SESSN_PARAM_LOGIN_REDIRECT] is not set.
     * 
     * @var string
     */
    protected $login_success_redirect_action = 'index';
    
    /**
     * 
     * Will be used in actionLogin() to construct the url to redirect to upon successful login,
     * if $_SESSION[static::SESSN_PARAM_LOGIN_REDIRECT] is not set.
     * 
     * @var string
     */
    protected $login_success_redirect_controller = 'app-base';
    
    /**
     * 
     * @param \Psr\Container\ContainerInterface $container
     * @param string $controller_name_from_uri
     * @param string $action_name_from_uri
     * @param \Psr\Http\Message\ServerRequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * 
     */
    public function __construct(
        \Psr\Container\ContainerInterface $container, $controller_name_from_uri, $action_name_from_uri, 
        \Psr\Http\Message\ServerRequestInterface $req, \Psr\Http\Message\ResponseInterface $res
    ) {
        parent::__construct($container, $controller_name_from_uri, $action_name_from_uri, $req, $res);
        
        $this->aura_session_segment = $container->get('aura_session')
                                                ->getSegment(static::CONTROLLER_SESSION_SEGMENT_KEY);
    }
    
    public function actionLogin() {
        
        $response = parent::actionLogin();
        $username = s3MVC_GetSuperGlobal('post', 'username', null);
        
        if( $this->isLoggedIn() && $username !== null ) {
            
            // set the username in session object associated with the auth object
            $this->container->get('vespula_auth')
                            ->getSession()
                            ->setUsername($username);
        }
        
        return $response;
    }
    
    public function actionIndex() {
        
        //get the contents of the view first
        $view_str = $this->renderView('index.php', ['controller_object'=>$this]);
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function actionTestMsg() {
        
        $msg = 'By utilizing elements and principles of Material Design,'
                . ' we were able to create a framework that incorporates components'
                . ' and animations that provide more feedback to users. Additionally,'
                . ' a single underlying responsive system across all platforms allow'
                . ' for a more unified user experience.!!!! <br>';
        $this->setWarningFlashMessage(str_repeat($msg, 1));
        
        return $this->redirect(s3MVC_MakeLink('/disks'));
    }
    
    public function preAction() {
        
        // add code that you need to be executed before each controller action method is executed
        $response = parent::preAction();
        
        //SESSION IDLE/EXPIRED LOGIC      
        if ( $this->vespula_auth->isIdle() ) {
                
            $msg = 'Your session has been idle for too long. Please sign in again.';
            $this->setWarningFlashMessage($msg, true);
                
        } else if ( $this->vespula_auth->isExpired() ) {

            $msg = 'Your session has expired.  Please sign in again.';
            $this->setWarningFlashMessage($msg, true);
        }

        // CSRF LOGIC
        if( $this->isPostRequestFromWebForm() && $this->isLoggedIn() )  {
            
            $csrf_value = filter_input(INPUT_POST, static::CSRF_FORM_FIELD_KEY, FILTER_SANITIZE_STRING);
            
            if ( !$csrf_value ) {
                
                $csrf_value = '';
            }
            
            $token = $this->container->get('aura_session')->getCsrfToken();
            
            if ( !$token->isValid($csrf_value) ) {
                                
                $this->logError( 'Possible Cross-Site Request Forgery.', 'Possible Cross-Site Request Forgery.');
                
                $new_response = 
                    $this->response
                         ->withBody($this->container->get('new_response_body'));

                //generate error page content using the layout and write it into the response body
                $layout_data = [];
                $layout_data['content'] = "Bad Request. Possible cross-site request forgery.";

                $error_page = $this->renderLayout( $this->layout_template_file_name, $layout_data );

                $new_response->getBody()->write( $error_page );

                $this->response = $new_response->withStatus(403)
                                               ->withHeader('Content-Type', 'text/html');
                $response = $this->response;
            }
        }
        
        return $response;
    }
    
    public function postAction(\Psr\Http\Message\ResponseInterface $response) {
        
        // add code that you need to be executed after each controller action method is executed
        $new_response = parent::postAction($response);
        
        return $new_response;
    }
    
    protected function isGetRequest() {
        
        return strtoupper($this->request->getMethod()) === 'GET';
    }
    
    protected function isPostRequest() {
        
        return strtoupper($this->request->getMethod()) === 'POST';
    }
    
    protected function isPostRequestFromWebForm() {
        
        $serverParams = $this->request->getServerParams();
        $webFormContentTypes = [
            'application/x-www-form-urlencoded',
            'multipart/form-data'
        ];
        
        return 
            (
                (
                    isset($serverParams['CONTENT_TYPE']) 
                    && 
                    in_array(strtolower($serverParams['CONTENT_TYPE']), $webFormContentTypes, true) 
                )
                || 
                (
                    isset($serverParams['HTTP_CONTENT_TYPE']) 
                    && 
                    in_array(strtolower($serverParams['HTTP_CONTENT_TYPE']), $webFormContentTypes, true) 
                )
            )
            && $this->isPostRequest();
    }
    
    public function renderLayout($file_name, array $data = []) {
        
        return parent::renderLayout($file_name, $data);
    }
    
    public function renderView($file_name, array $data = []) {
        
        return parent::renderView($file_name, $data);
    }
    
    protected function redirect($rdr_path, $status_code=302) {
        
        return $this->response->withStatus($status_code)
                              ->withHeader('Location', $rdr_path);
    }
    
    
    
    protected function setErrorFlashMessage($msg, $for_curent_request_only=false) {
        
        $msg_array = ['message'=>$msg, 'title'=>'<i class="material-icons medium">error</i>'];
        $this->setFlashMessage($msg_array, $for_curent_request_only);
        $this->setFlashMessageCssClass('white-text red darken-4', $for_curent_request_only);
    }
    
    protected function setInfoFlashMessage($msg, $for_curent_request_only=false) {
        
        $msg_array = ['message'=>$msg, 'title'=>'<i class="material-icons medium">info</i>'];
        $this->setFlashMessage($msg_array, $for_curent_request_only);
        $this->setFlashMessageCssClass('white-text blue darken-3', $for_curent_request_only);
    }

    protected function setSuccessFlashMessage($msg, $for_curent_request_only=false) {
        
        $msg_array = ['message'=>$msg, 'title'=>'<i class="material-icons medium">check_circle</i>'];
        $this->setFlashMessage($msg_array, $for_curent_request_only);
        $this->setFlashMessageCssClass('white-text teal darken-1', $for_curent_request_only);
    }

    protected function setWarningFlashMessage($msg, $for_curent_request_only=false) {
        
        $msg_array = ['message'=>$msg, 'title'=>'<i class="material-icons medium">report_problem</i>'];
        $this->setFlashMessage($msg_array, $for_curent_request_only);
        $this->setFlashMessageCssClass('white-text orange darken-3', $for_curent_request_only);
    }
    
    protected function setFlashMessage($msg, $for_curent_request_only=false) {

        if( $for_curent_request_only ) {
            
            //sets flash message for current and next request under the hood
            $this->aura_session_segment->setFlashNow(static::FLASH_MESSAGE_KEY, $msg);
            
            //remove the flash message that was set for next request
            $this->aura_session_segment->clearFlash();
            
        } else {
            
            $this->aura_session_segment->setFlash(static::FLASH_MESSAGE_KEY, $msg);
        }
    }

    protected function getLastFlashMessage() {

        return $this->aura_session_segment->getFlash(static::FLASH_MESSAGE_KEY, null);
    }

    protected function setFlashMessageCssClass($css_class, $for_curent_request_only=false) {

        if( $for_curent_request_only ) {
            
            //sets flash message for current and next request under the hood
            $this->aura_session_segment->setFlashNow(static::FLASH_MESSAGE_CSS_CLASS_KEY, $css_class);
            
            //remove the flash message that was set for next request
            $this->aura_session_segment->clearFlash();
            
        } else {
            
            $this->aura_session_segment->setFlash(static::FLASH_MESSAGE_CSS_CLASS_KEY, $css_class);
        }
    }

    protected function getLastFlashMessageCssClass() {

        return $this->aura_session_segment->getFlash(static::FLASH_MESSAGE_CSS_CLASS_KEY, null);
    }
    
    
    public function logError($msg, $email_subject, $send_mail=true) {
        
        $container = $this->container;

        $req_obj_as_str_uncleaned = 
            \Lsia\Utils::psr7RequestObjToString($this->request) . PHP_EOL;

        //scrub password if this was a post from the login form
        $req_obj_as_str = preg_replace('/&password=[^&\n]*/i', '&password=SCRUBBED_NOTHING_TOO_SEE_HERE', $req_obj_as_str_uncleaned);

//        $to_addrs = [
//            'rotexdegba@hotmail.com', 
//            'savedrotex@gmail.com',
//            'service@greenenergydirectory.com'
//        ];
        $message_body = "Error Occurred: "
                        . $this->current_uri
                        . PHP_EOL. "Message: $msg" 
                        . PHP_EOL. str_replace(PHP_EOL, PHP_EOL. "\t\t\t", "<pre>\t\t\t" . $req_obj_as_str . '</pre>');
                
        //$from_addr = 'donotreply@greenenergydirectory.com';
        //$subject = 'Ged Error:' . $email_subject;
        
        $container['logger']->error( str_replace(['<pre>', '</pre>'], ['', ''], $message_body) );
        
//        ($send_mail && $this->isProductionEnvironment())
//            && $this->sendMailViaAwsSesSmtp(
//                    $to_addrs, nl2br($message_body), $from_addr, $subject
//                );
    }
    
    protected function isProductionEnvironment() {
        
        return s3MVC_GetCurrentAppEnvironment() === S3MVC_APP_ENV_PRODUCTION;
    }
    
    public function logNotice($msg) {
        
        $req_as_str = 
            \Lsia\Utils::psr7RequestObjToString(
                $this->request,
                ['route','routeInfo'],
                true,  //$skip_req_attribs
                true,  //$skip_req_body cos posted password might be there
                true,  //$skip_req_cookie_params
                false, //$skip_req_headers
                false, //$skip_req_method
                false, //$skip_req_proto_ver
                true,  //$skip_req_query_params would be visible in the url / uri
                true,  //$skip_req_target would be visible in the url / uri
                false, //$skip_req_server_params
                true,  //$skip_req_uploaded_files
                false  //$skip_req_uri
            ) . PHP_EOL;
        
        $message_body = "Notice: "
                        . $this->current_uri
                        . PHP_EOL. "\t\tMessage:" 
                        . PHP_EOL. "\t\t\t" . str_replace(PHP_EOL, "\t\t\t", $msg)
                        . PHP_EOL . PHP_EOL. "Request Details:"
                        . PHP_EOL . str_replace(PHP_EOL, PHP_EOL. "\t\t\t", "\t\t\t".$req_as_str);
        
        $this->container['logger']->notice( $message_body );
    }
    
    public function logWarning($msg) {
        
        $req_as_str = 
            \Lsia\Utils::psr7RequestObjToString(
                $this->request,
                ['route','routeInfo'],
                true,  //$skip_req_attribs
                true,  //$skip_req_body cos posted password might be there
                true,  //$skip_req_cookie_params
                false, //$skip_req_headers
                false, //$skip_req_method
                false, //$skip_req_proto_ver
                true,  //$skip_req_query_params would be visible in the url / uri
                true,  //$skip_req_target would be visible in the url / uri
                false, //$skip_req_server_params
                true,  //$skip_req_uploaded_files
                false  //$skip_req_uri
            ) . PHP_EOL;
        
        $message_body = "Warning: "
                        . $this->current_uri
                        . PHP_EOL. "\t\tMessage:" 
                        . PHP_EOL. "\t\t\t" . str_replace(PHP_EOL, "\t\t\t", $msg)
                        . PHP_EOL . PHP_EOL. "Request Details:"
                        . PHP_EOL . str_replace(PHP_EOL, PHP_EOL. "\t\t\t", "\t\t\t".$req_as_str);
                
        $this->container['logger']->warning( $message_body );
    }
    
    protected function getTableColumnNames(string $tableName): array {
        
        /** @var \Atlas\Info\Info $atlasInfoObj */
        $atlasInfoObj = $this->container->get('atlas_info');
        
        // Table cols excuding primary key col
        return \VersatileCollections\ArraysCollection::makeNew(
                    $atlasInfoObj->fetchColumns($tableName)
                )->filterAll(
                    function($key, $item) {
                        // exclude the primary key column(s)
                        return isset($item['primary']) 
                               && ((bool)$item['primary']) !== true;
                    },
                    true
                )->getKeys()
                 ->toArray();
    }
    
    protected function newRecordDataFromPost(array $postData, array $tableCols): array {
        
        $newRecordData = [];
        
        foreach($tableCols as $colName) {
            
            if( isset($postData[$colName]) ) {
                
                $newRecordData[$colName] = $postData[$colName];
            }
        }
        
        return $newRecordData;
    }
}
