<?php
namespace Lsia\Controllers;

use Lsia\Utils;
use Ginfo\Info\General as GinfoGeneral;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VersatileCollections\ArraysCollection;

/**
 * 
 * Description of AppBase goes here
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
        ContainerInterface $container, ?string $controller_name_from_uri, ?string $action_name_from_uri, 
        ServerRequestInterface $req, ResponseInterface $res
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
        
        return $this->redirect(s3MVC_MakeLink('/'));
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
            
            /** @var \Aura\Session\CsrfToken $token */
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
    
    public function postAction(ResponseInterface $response) {
        
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
            Utils::psr7RequestObjToString($this->request) . PHP_EOL;

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
                
//        $from_addr = 'donotreply@serverapi.com';
//        $subject = 'Server Info API Error:' . $email_subject;
        
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
            Utils::psr7RequestObjToString(
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
            Utils::psr7RequestObjToString(
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
        return ArraysCollection::makeNew(
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
    
    protected function getRecordDataFromPost(array $postData, array $tableCols): array {
        
        $newRecordData = [];
        
        foreach($tableCols as $colName) {
            
            if( isset($postData[$colName]) ) {
                
                $newRecordData[$colName] = $postData[$colName];
            }
        }
        
        return $newRecordData;
    }
    
    protected function generateResponse( 
        string $responseMessage, 
        int $statusCode=200, 
        bool $renderLayoutForHtmlContent=false,
        string $contentType='text/html'
    ) {
        $new_response = $this->response->withBody($this->container->get('new_response_body'));
        $layout_data = [];
        $layout_data['content'] = $responseMessage;
        
        $renderedMessage = 
            ($renderLayoutForHtmlContent)
                ? $this->renderLayout( $this->layout_template_file_name, $layout_data )
                : $responseMessage;

        $new_response->getBody()->write( $renderedMessage );

        return $new_response->withStatus($statusCode)
                            ->withHeader('Content-Type', $contentType);
    }
    
    
    /******************************************************************/
    /******************************************************************/
    //// API Data Generation Methods
    /******************************************************************/
    /******************************************************************/
    
    protected function generateSystemOverviewData() {
        
        /** @var \Linfo\Linfo $linfo */
        $linfo = $this->container->get('linfo_server_info');
        
        /** @var \Linfo\OS\OS $linfoObj */
        $linfoObj = $linfo->getParser();
        
        /** @var \Ginfo\Ginfo $ginfo */
        $ginfo = $this->container->get('ginfo_server_info');
        $ginfoObj = $ginfo->getInfo();
        $generalInfo = $ginfoObj->getGeneral();
        
        /** @var \Probe\Provider\ProviderInterface $trntInfo */
        $trntInfo = $this->container->get('trntv_server_info');
        
        $systemOverviewData = ['system_overview_schema'=>[]];
        
        ////////////////////////////////////////////////////////////////////////
        // Get host name
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['system_overview_schema']['host_name'] = \gethostname();
        
        ////////////////////////////////////////////////////////////////////////
        // Get OS Family
        ////////////////////////////////////////////////////////////////////////        
        $systemOverviewData['system_overview_schema']['os_family'] = PHP_OS_FAMILY;
        
        ////////////////////////////////////////////////////////////////////////
        // Get Kernel Version
        ////////////////////////////////////////////////////////////////////////
        
        // retrieve via linfo
        $kernelVersion = $linfoObj->getKernel();

        if (Utils::getNullIfEmpty($kernelVersion) === null) {

            // try to retrieve via ginfo or php_uname('r')
            $kernelVersion = ($generalInfo instanceof GinfoGeneral) 
                ? Utils::getDefaultIfEmpty(
                    $generalInfo->getKernel(),
                    Utils::getDefaultIfEmpty(php_uname('r'), 'Not Available')
                  ) 
                : Utils::getDefaultIfEmpty(php_uname('r'), 'Not Available');
        }
        
        $systemOverviewData['system_overview_schema']['kernel_version'] = $kernelVersion;
        
        ////////////////////////////////////////////////////////////////////////
        // Get Kernel Version
        ////////////////////////////////////////////////////////////////////////
        
        // retrieve via ginfo
        $distroName = ($generalInfo instanceof GinfoGeneral) 
                            ? Utils::getDefaultIfEmpty($generalInfo->getOsName(), '') : '';

        if (Utils::getNullIfEmpty($distroName) === null) {

            // try to retrieve via trntv/probe
            $distroName = Utils::getDefaultIfEmpty($trntInfo->getOsRelease(), 'Not Available');
        }
        
        $systemOverviewData['system_overview_schema']['distro_name'] = $distroName;
        
//s3MVC_DumpVar( str_replace('"', '', $trntInfo->getOsRelease()) );
s3MVC_DumpVar($systemOverviewData);
        
        
        $generalInfo = $ginfoObj->getGeneral();
var_dump($ginfo->getOs()->getUptime());
        $uptime = '';
        $lastBootedOn = '';
        
        if( $generalInfo->getUptime() instanceof DateInterval ) {
            
            $uptime = $generalInfo->getUptime()->format('%d days, %h hours, %i minutes, %s seconds');
            $lastBootedOn = (new DateTime())->sub($generalInfo->getUptime())->format('D, j M Y H:i:s T');
        }
        
        // TODO: Add some common software version info to the section that requires
        //       users to be logged in. E.g php, mysql, apache, python, ruby & more
        $viewData = [
            'distroNameAndVersion'  => [ 'label' => 'Distro Name and Version',  'value' => $generalInfo->getOsName() ],
            'kernelVersion'         => [ 'label' => 'Kernel Version',           'value' => $generalInfo->getKernel() ],
            'osFamily'              => [ 'label' => 'OS Family',                'value' => $linfoObj->getOS() ],
            'architecture'          => [ 'label' => 'Architecture',             'value' => $generalInfo->getArchitecture() ],
            'machineModel'          => [ 'label' => 'Machine Model',            'value' => Utils::getDefaultIfEmpty($generalInfo->getModel(), '') ],
            'lastBootedOn'          => [ 'label' => 'Last booted on',           'value' => $lastBootedOn ],
            'uptime'                => [ 'label' => 'Uptime',                   'value' => $uptime ],
            'loggedInUsers'         => [ 'label' => 'Logged in users',          'value' => Utils::getValIfTrueOrGetDefault(is_countable($generalInfo->getLoggedUsers()), count($generalInfo->getLoggedUsers()), 'Unknown') ],
            'processSummaryInfo'    => [],
        ];
        
        $processInfo = $linfoObj->getProcessStats();
        $processInfoG = $ginfoObj->getProcesses();
var_dump(count($processInfoG));
//var_dump($processInfoG);
s3MVC_DumpVar($processInfoG);
        
        if( is_array($processInfo) ) {
            
            if(array_key_exists('proc_total', $processInfo)) {
                
                $viewData['processSummaryInfo'][] = 
                    [ 'label' => 'Total Number of Processes', 'value' => $processInfo['proc_total'] ];
            }
            
            if(array_key_exists('threads', $processInfo)) {
                
                $viewData['processSummaryInfo'][] = 
                    [ 'label' => 'Total Number of Threads', 'value' => $processInfo['threads'] ];
            }
            
            if(array_key_exists('totals', $processInfo) && is_array($processInfo['totals'])) {
                
                if(array_key_exists('running', $processInfo['totals'])) {

                    $viewData['processSummaryInfo'][] = 
                        [ 'label' => 'Total Number of Running Processes', 'value' => $processInfo['totals']['running'] ];
                }
                
                if(array_key_exists('sleeping', $processInfo['totals'])) {

                    $viewData['processSummaryInfo'][] = 
                        [ 'label' => 'Total Number of Sleeping Processes', 'value' => $processInfo['totals']['sleeping'] ];
                }
                
                if(array_key_exists('stopped', $processInfo['totals'])) {

                    $viewData['processSummaryInfo'][] = 
                        [ 'label' => 'Total Number of Stopped Processes', 'value' => $processInfo['totals']['stopped'] ];
                }
                
                if(array_key_exists('zombie', $processInfo['totals'])) {

                    $viewData['processSummaryInfo'][] = 
                        [ 'label' => 'Total Number of Zombie Processes', 'value' => $processInfo['totals']['zombie'] ];
                }
            }
        } 
    }
}
