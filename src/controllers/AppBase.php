<?php
namespace Lsia\Controllers;

use DateTime;
use Lsia\Utils;
use DateInterval;
use Ginfo\Info\Cpu as GinfoCpu;
use Ginfo\Info\Disk as GinfoDisk;
use Ginfo\Info\Memory as GinfoMemory;
use Ginfo\Info\General as GinfoGeneral;
use Ginfo\Info\Selinux as GinfoSelinux;

use Lsia\Atlas\Models\Token\Token;
use Lsia\Atlas\Models\Token\TokenRecord;
use Lsia\Atlas\Models\TokenUsage\TokenUsage;

use Psr\Http\Message\UriInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use VersatileCollections\ArraysCollection;
use VersatileCollections\NumericsCollection;
use VersatileCollections\MultiSortParameters;

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
    
    const API_TOKEN_KEY_NAME = 'token';
    
    const HTTP_STATUS_INFO = [
        200 => 'Ok',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        429 => 'Too Many Requests. Token has exceeded the maximum allowable requests assigned to it for one day. Try again after 24 hours.',
        500 => 'Internal Server Error',
    ];
    
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_UNAUTHORIZED = 401;
    const HTTP_STATUS_FORBIDDEN = 403;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_METHOD_NOT_ALLOWED = 405;
    const HTTP_STATUS_DAILY_RATE_LIMIT_EXCEEDED = 429;
    const HTTP_STATUS_INTERNAL_SERVER_ERROR = 500;
    
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
        
        return $this->redirect(s3MVC_MakeLink('/'));
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
            $this->setWarningFlashMessage($msg, false);
                
        } else if ( $this->vespula_auth->isExpired() ) {

            $msg = 'Your session has expired.  Please sign in again.';
            $this->setWarningFlashMessage($msg, false);
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
                                
                $this->logError('Possible Cross-Site Request Forgery.');
                $response = $this->generateResponse('Possible Cross-Site Request Forgery.', 403, true);
            }
            
        } else if(
            $this->isPostRequestFromWebForm() 
            && !$this->isLoggedIn()
            && s3MVC_GetSuperGlobal('post', static::CSRF_FORM_FIELD_KEY, null) !== null
            && $this->action_name_from_uri !== 'login'
            && $this->action_name_from_uri !== 'action-login'
            && $this->action_name_from_uri !== 'logout'
            && $this->action_name_from_uri !== 'action-logout'
        ) {
            // Token submitted but user is logged out
            $this->logError('Possible Cross-Site Request Forgery. Form submitted while logged out.');
            $response = $this->generateResponse('Possible Cross-Site Request Forgery. Form submitted while logged out.', 403, true);
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
    
    protected function createLogMessage($msg, $severity='Notice') {
        
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
        
        return "$severity: "
                . $this->current_uri
                . PHP_EOL. "\t\tMessage:" 
                . PHP_EOL. "\t\t\t" . str_replace(PHP_EOL, "\t\t\t", $msg)
                . PHP_EOL . PHP_EOL. "Request Details:"
                . PHP_EOL . str_replace(PHP_EOL, PHP_EOL. "\t\t\t", "\t\t\t".$req_as_str);
    }
    
    protected function isProductionEnvironment() {
        
        return s3MVC_GetCurrentAppEnvironment() === S3MVC_APP_ENV_PRODUCTION;
    }
    
    public function logError($msg) {

        $this->container['logger']->error($this->createLogMessage($msg, 'Error'));
    }

    public function logNotice($msg) {
        
        $this->container['logger']->notice($this->createLogMessage($msg, 'Notice'));
    }
    
    public function logWarning($msg) {
                        
        $this->container['logger']->warning($this->createLogMessage($msg, 'Warning'));
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
                )->getKeys() // each key is a column name
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
    
    protected function generateApiJsonResponse(array $data, int $httpStatusCode):string {
        
        $result = [
               'status_code' => array_key_exists($httpStatusCode, static::HTTP_STATUS_INFO) ? $httpStatusCode : static::HTTP_STATUS_OK, 
               'status_desc' => array_key_exists($httpStatusCode, static::HTTP_STATUS_INFO) ? static::HTTP_STATUS_INFO[$httpStatusCode] : static::HTTP_STATUS_INFO[static::HTTP_STATUS_OK], 
                      'data' => [], 
            'time_generated' => (new DateTime())->format('c')
        ];
        
        if($httpStatusCode === static::HTTP_STATUS_OK) {
        
            $result['data'] = $data;
        }
        
        return json_encode($result);
    }
    
    protected function getApiHttpStatusCodeForResponse(array $allowedMethods=['GET']): int {
        
        $statusCode = static::HTTP_STATUS_OK;
        
        if(!$this->canAccessApiData()) {
            
            $statusCode = static::HTTP_STATUS_UNAUTHORIZED;
            
        } else {
        
            $upperCasedAllowedMethods = array_map(
                function($item) {
                    return is_string($item) ? strtoupper($item) : $item;
                }, 
                $allowedMethods
            );

            $currentMethod = strtoupper($this->request->getMethod());

            if(!in_array($currentMethod, $upperCasedAllowedMethods)) {

                $statusCode = static::HTTP_STATUS_METHOD_NOT_ALLOWED;
                
            } else {
                
                // Only check token daily limit for non-logged in users
                if(!$this->isLoggedIn() && $this->hasTokenExceededDailyRequestLimit()) {
                    
                    $statusCode = static::HTTP_STATUS_DAILY_RATE_LIMIT_EXCEEDED;
                }
            }
        }
        
        return $statusCode;
    }
    
    protected function hasTokenExceededDailyRequestLimit(): bool {
        
        // get token 
        // query token usage for records for the token in the past 24hrs
        // if number of records is more than or equal to tokens.max_requests_per_day
        // return true
        $tokenExceededDailyLimit = false;
        
        if($this->hasValidToken()) {
            
            $token = s3MVC_GetSuperGlobal('get', static::API_TOKEN_KEY_NAME, null);
            
            /** @var \Atlas\Orm\Atlas $atlasObj */
            $atlasObj = $this->container->get('atlas');
            
            $tokenRecord = $atlasObj->select(Token::class)
                                    ->where('token = ', $token)
                                    ->fetchRecord();

            if($tokenRecord instanceof TokenRecord) {
                
                /** @var \Lsia\Atlas\Models\TokenUsage\TokenUsageRecord $newRecord */
                $numRecordsInPast24Hrs = 
                    $atlasObj->select(TokenUsage::class)
                            ->columns('count(*)')
                            ->where('token_id = ', $tokenRecord->id)
                            ->andWhere('date_time_of_request >= ', date('Y-m-d H:i:s', strtotime("-1 days")) )
                            ->fetchValue();

                $tokenExceededDailyLimit = 
                    is_numeric($numRecordsInPast24Hrs)
                    && is_numeric($tokenRecord->max_requests_per_day)
                    && $tokenRecord->max_requests_per_day > 0
                    && $numRecordsInPast24Hrs > $tokenRecord->max_requests_per_day;
            } // if($tokenRecord instanceof TokenRecord)
        } // if($this->hasValidToken())
        
        return $tokenExceededDailyLimit;
    }
    
    protected function canAccessApiData() {
        
        // Logged in users accessing this web app via their browser
        // are allowed to access data from this app.
        
        // Non-logged in users accessing this web app via a browser or
        // other clients like curl, postman, etc, must supply a valid token
        // in order to be able to access data from this app.
        
        return $this->isLoggedIn() || $this->hasValidToken();
    }
    
    /**
     * 
     * @param int $httpStatusCode must be one of 200, 401, 403, 404, 405, 429 or 500
     * @param string $optionalErrorMessage
     */
    protected function logTokenUsage(int $httpStatusCode, string $optionalErrorMessage='') {
        
        if($this->hasToken()) {
            
            $token = s3MVC_GetSuperGlobal('get', static::API_TOKEN_KEY_NAME, null);
            
            /** @var \Atlas\Orm\Atlas $atlasObj */
            $atlasObj = $this->container->get('atlas');
            
            $tokenRecord = $atlasObj->select(Token::class)
                                    ->where('token = ', $token)
                                    ->fetchRecord();

            if(
                $tokenRecord instanceof TokenRecord 
                && $this->request->getUri() instanceof UriInterface
            ) {
                /** @var \Lsia\Atlas\Models\TokenUsage\TokenUsageRecord $newRecord */
                $newRecord = $atlasObj->newRecord(TokenUsage::class);
                
                /** @var \Lsia\ClientIpDetector $ipDetector */
                $ipDetector = $this->container->get('ip_detector');
                
                if($optionalErrorMessage === '' && $httpStatusCode !== 200) {
                    
                    $optionalErrorMessage = 
                        (array_key_exists($httpStatusCode, static::HTTP_STATUS_INFO))
                            ? static::HTTP_STATUS_INFO[$httpStatusCode]
                            : 'Unknown Error';
                }
                
                $newRecord->token_id = $tokenRecord->id;
                $newRecord->request_uri = $this->request->getUri()->getPath() ?: '/';
                $newRecord->date_time_of_request = date('Y-m-d H:i:s');
                $newRecord->request_full_details = Utils::psr7RequestObjToString($this->request);
                $newRecord->requesters_ip = $ipDetector->getDetectedIp($this->request);
                $newRecord->http_status_code = $httpStatusCode;
                $newRecord->request_error_details = $optionalErrorMessage;
                
                try {
                    $atlasObj->insert($newRecord);
                    
                } catch (\Exception $exc) {
                    
                    $this->logError('Error Saving New TokenUsage Record: '.PHP_EOL.$exc->getTraceAsString());
                }
            }
        } // if($this->hasValidToken())
    }
    
    /**
     * 
     * Do we have a valid active or expired token
     * 
     * @return bool
     */
    protected function hasToken(): bool {
        
        $hasToken = false;
        
        $token = s3MVC_GetSuperGlobal('get', static::API_TOKEN_KEY_NAME, null);
        
        if(!is_null($token) && is_string($token) && !Utils::isEmptyString($token)) {
            
            /** @var \Atlas\Orm\Atlas $atlasObj */
            $atlasObj = $this->container->get('atlas');
            
            $tokenRecord = $atlasObj->select(Token::class)
                                    ->where('token = ', $token)
                                    ->fetchRecord();

            $hasToken = ($tokenRecord instanceof TokenRecord);
        }
        
        return $hasToken;
    }
    
    /**
     * 
     * Do we have a valid active token (not an expired or non existent one)
     * 
     * @return bool
     */
    protected function hasValidToken(): bool {
        
        // Get the user supplied token named by the value of 
        // static::API_TOKEN_KEY_NAME
        // If token was retreived from the $_GET
        //   Query the DB to get the record associated with the token
        //   If record found
        //     Then check that the token has not expired
        //     If Token has not expired
        //        return true
        //     Else
        //        return false
        //   Else record not found
        //     return false
        // Else token was not retreivable from $_GET
        //   return false
        
        $hasValidToken = false;
        
        $token = s3MVC_GetSuperGlobal('get', static::API_TOKEN_KEY_NAME, null);
        
        if(!is_null($token) && is_string($token) && !Utils::isEmptyString($token)) {
            
            /** @var \Atlas\Orm\Atlas $atlasObj */
            $atlasObj = $this->container->get('atlas');
            
            $tokenRecord = $atlasObj->select(Token::class)
                                    ->where('token = ', $token)
                                    ->fetchRecord();

            if($tokenRecord instanceof TokenRecord) {
                
                // A token is expired if its expiry date is the same value or 
                // in the past relative to the current date
                $isExpired = strtotime(date('Y-m-d H:i:s')) >= strtotime(date($tokenRecord->expiry_date));
                
                if(!$isExpired) {
                    
                    $hasValidToken = true;
                }
            }
        }
        
        return $hasValidToken;
    }
    
    /******************************************************************/
    /******************************************************************/
    //// API Data Generation Methods
    /******************************************************************/
    /******************************************************************/
    
    protected function generateProcessData(): array {

        /** @var \Ginfo\Ginfo $ginfo */
        $ginfo = $this->container->get('ginfo_server_info');
        $ginfoObj = $ginfo->getInfo();
        $processDataToReturn = [];
        
        $processData = $ginfoObj->getProcesses();
        
        if( Utils::isCountableWithData($processData) ) {
            
            /** @var \Ginfo\Info\Process $processDatum */
            foreach($processData as $processDatum) {
                
                $processDataToReturn[] = [
                    'name'              => Utils::getDefaultIfEmpty($processDatum->getName(), ''),
                    'command_line'      => Utils::getDefaultIfEmpty($processDatum->getCommandLine(), ''),
                    'num_threads'       => Utils::getDefaultIfEmpty($processDatum->getThreads(), -1),
                    'state'             => Utils::getDefaultIfEmpty($processDatum->getState(), ''),
                    'memory'            => Utils::getDefaultIfEmpty($processDatum->getMemory(), -1.0),
                    'peak_memory'       => Utils::getDefaultIfEmpty($processDatum->getPeakMemory(), -1.0),
                    'pid'               => Utils::getDefaultIfEmpty($processDatum->getPid(), -1),
                    'user'              => Utils::getDefaultIfEmpty($processDatum->getUser(), ''),
                    'io_bytes_read'     => Utils::getDefaultIfEmpty($processDatum->getIoRead(), -1.0),
                    'io_bytes_written'  => Utils::getDefaultIfEmpty($processDatum->getIoWrite(), -1.0),
                ];
            }
        }
        
        return $processDataToReturn;
    }
    
    protected function generateDiskDrivesData(): array {

        /** @var \Ginfo\Ginfo $ginfo */
        $ginfo = $this->container->get('ginfo_server_info');
        $ginfoObj = $ginfo->getInfo();
        $diskDriveDataToReturn = [];
        
        $diskObj = $ginfoObj->getDisk();
        
        if( 
            $diskObj instanceof GinfoDisk 
            && Utils::isCountableWithData($diskObj->getDrives()) 
        ) {
            /** @var \Ginfo\Info\Disk\Drive $diskDriveDatum */
            foreach($diskObj->getDrives() as $diskDriveDatum) {
                
                $partitions = [];
                
                /** @var \Ginfo\Info\Disk\Drive\Partition $partition */
                foreach(Utils::getDefaultIfEmpty($diskDriveDatum->getPartitions(), []) as $partition) {
                    
                    $partitions[] = [
                        'name'          => Utils::getDefaultIfEmpty($partition->getName(), ''),
                        'size_in_bytes' => Utils::getDefaultIfEmpty($partition->getSize(), -1.0),
                    ];
                }
                
                $diskDriveDataToReturn[] = [
                    'name'          => Utils::getDefaultIfEmpty($diskDriveDatum->getName(), ''),
                    'vendor'        => Utils::getDefaultIfEmpty($diskDriveDatum->getVendor(), ''),
                    'device'        => Utils::getDefaultIfEmpty($diskDriveDatum->getDevice(), ''),
                    'bytes_read'    => Utils::getDefaultIfEmpty($diskDriveDatum->getReads(), -1.0),
                    'bytes_written' => Utils::getDefaultIfEmpty($diskDriveDatum->getWrites(), -1.0),
                    'size_in_bytes' => Utils::getDefaultIfEmpty($diskDriveDatum->getSize(), -1.0),
                    'partitions'    => ArraysCollection::makeNew($partitions)
                                            ->sortByMultipleFields( new MultiSortParameters('name', \SORT_ASC, (\SORT_FLAG_CASE | \SORT_NATURAL)) )
                                            ->toArray(),
                ];
            }
        }
        
        return $diskDriveDataToReturn;
    }
    
    protected function generateDiskMountsData(): array {

        /** @var \Ginfo\Ginfo $ginfo */
        $ginfo = $this->container->get('ginfo_server_info');
        $ginfoObj = $ginfo->getInfo();
        $diskMountDataToReturn = [];
        $diskObj = $ginfoObj->getDisk();
        
        if( 
            $diskObj instanceof GinfoDisk 
            && Utils::isCountableWithData($diskObj->getMounts()) 
        ) {
            /** @var \Ginfo\Info\Disk\Mount $diskMountDatum */
            foreach($diskObj->getMounts() as $diskMountDatum) {
                                
                $diskMountDataToReturn[] = [
                    'name'          => Utils::getDefaultIfEmpty($diskMountDatum->getDevice(), ''),
                    'mount_point'   => Utils::getDefaultIfEmpty($diskMountDatum->getMount(), ''),
                    'type'          => Utils::getDefaultIfEmpty($diskMountDatum->getType(), ''),
                    'size_in_bytes' => Utils::getDefaultIfEmpty($diskMountDatum->getSize(), -1.0),
                    'used_bytes'    => Utils::getDefaultIfEmpty($diskMountDatum->getUsed(), -1.0),
                    'free_bytes'    => Utils::getDefaultIfEmpty($diskMountDatum->getFree(), -1.0),
                    'free_percent'  => Utils::getDefaultIfEmpty($diskMountDatum->getFreePercent(), -1.0),
                    'used_percent'  => Utils::getDefaultIfEmpty($diskMountDatum->getUsedPercent(), -1.0),
                    'options'        => Utils::getDefaultIfEmpty($diskMountDatum->getOptions(), []),
                ];
            }
        }
        
        return $diskMountDataToReturn;
    }
    
    protected function generateServicesData(): array {

        /** @var \Ginfo\Ginfo $ginfo */
        $ginfo = $this->container->get('ginfo_server_info');
        $ginfoObj = $ginfo->getInfo();
        $servicesDataToReturn = [];
        
        $servicesData = $ginfoObj->getServices();
        
        if( Utils::isCountableWithData($servicesData) ) {
            
            /** @var \Ginfo\Info\Service $serviceDatum */
            foreach($servicesData as $serviceDatum) {
                
                $servicesDataToReturn[] = [
                    'name'         => Utils::getDefaultIfEmpty($serviceDatum->getName(), ''),
                    'description'  => Utils::getDefaultIfEmpty($serviceDatum->getDescription(), ''),
                    'loaded'       => Utils::getValIfTrueOrGetDefault($serviceDatum->isLoaded(), 1, 0),
                    'started'      => Utils::getValIfTrueOrGetDefault($serviceDatum->isStarted(), 1, 0),
                    'state'        => Utils::getDefaultIfEmpty($serviceDatum->getState(), ''),
                ];
            }
        }
        
        return $servicesDataToReturn;
    }
    
    protected function generateSystemOverviewData(): array {
        
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
        
        $systemOverviewData = [];
        
        ////////////////////////////////////////////////////////////////////////
        // Get host name
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['host_name'] = \gethostname() ?: '';
        
        ////////////////////////////////////////////////////////////////////////
        // Get OS Family
        ////////////////////////////////////////////////////////////////////////        
        $systemOverviewData['os_family'] = PHP_OS_FAMILY;
        
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
                    Utils::getDefaultIfEmpty(php_uname('r'), '')
                  ) 
                : Utils::getDefaultIfEmpty(php_uname('r'), '');
        }
        
        $systemOverviewData['kernel_version'] = $kernelVersion;
        
        ////////////////////////////////////////////////////////////////////////
        // Get Distro Name
        ////////////////////////////////////////////////////////////////////////
        
        // retrieve via ginfo
        $distroName = ($generalInfo instanceof GinfoGeneral) 
                            ? Utils::getDefaultIfEmpty($generalInfo->getOsName(), '') : '';

        if (Utils::getNullIfEmpty($distroName) === null) {

            // try to retrieve via trntv/probe
            $distroName = Utils::getDefaultIfEmpty($trntInfo->getOsRelease(), '');
        }
        
        $systemOverviewData['distro_name'] = $distroName;
        
        ////////////////////////////////////////////////////////////////////////
        // Get Architecture
        ////////////////////////////////////////////////////////////////////////        
        $systemOverviewData['architecture'] = 
                    Utils::getDefaultIfEmpty(php_uname('m'), '');
        
        ////////////////////////////////////////////////////////////////////////
        // Get System Model
        ////////////////////////////////////////////////////////////////////////        
        $systemOverviewData['system_model'] = 
            ($generalInfo instanceof GinfoGeneral) 
                ? Utils::getDefaultIfEmpty($generalInfo->getModel(), '') : '';
        
        ////////////////////////////////////////////////////////////////////////
        // Get Uptime
        ////////////////////////////////////////////////////////////////////////
        
        // retrieve via ginfo
        $uptime = ($ginfo->getOs() instanceof \Ginfo\OS\OS) 
                    ? Utils::getDefaultIfEmpty($ginfo->getOs()->getUptime(), '') : '';

        if ( Utils::getNullIfEmpty($uptime) === null ) {

            // try to retrieve via trntv/probe
            $uptime = Utils::getDefaultIfEmpty($trntInfo->getUptime(), -1);
        }
        
        $systemOverviewData['uptime'] = (int)$uptime;
        
        ////////////////////////////////////////////////////////////////////////
        // Get Uptime Text
        ////////////////////////////////////////////////////////////////////////

        // retrieve via ginfo
        // TODO: BUG: Monday July 13th, discovered that getUptime() in ginfo is not returning the 
        // accurate value. The server has been up for 32 days, 19 hours, 24 minutes
        // Ginfo was returning 2 days, 19 hours, 23 minutes, 46 seconds. Will revert to linfo for
        // this data as it returns a reasonable value like 32 days, 19 hours, 24 minutes, 16 seconds
//        $uptimeText = ($generalInfo instanceof GinfoGeneral && $generalInfo->getUptime() instanceof DateInterval) 
//                        ? 
//                        Utils::getDefaultIfEmpty(
//                            $generalInfo->getUptime()->format('%d days, %h hours, %i minutes, %s seconds')
//                            , ''
//                        ) 
//                        : 
//                        '';

//        if ( Utils::getNullIfEmpty($uptimeText) === null ) {

            // try to retrieve via linfo
            $uptimeText = 
                (
                    is_array($linfoObj->getUpTime()) 
                    && array_key_exists('text', $linfoObj->getUpTime())
                )
                ? $linfoObj->getUpTime()['text']
                : 
                (
                    is_string($linfoObj->getUpTime())
                        ? $linfoObj->getUpTime() : ''
                );
//        }
        
        $systemOverviewData['uptime_text'] = $uptimeText;
        
        ////////////////////////////////////////////////////////////////////////
        // Last Booted Timestamp
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['last_booted_timestamp'] = 
            Utils::getValIfTrueOrGetDefault(
                $systemOverviewData['uptime'] !== -1, 
                (new DateTime())->getTimestamp() - $systemOverviewData['uptime'], 
                -1
            );
        
        ////////////////////////////////////////////////////////////////////////
        // Web Server Software
        ////////////////////////////////////////////////////////////////////////
        
        //retrieve from $_SERVER['SERVER_SOFTWARE']
        $systemOverviewData['web_software'] = 
            s3MVC_GetSuperGlobal('server', 'SERVER_SOFTWARE', 'Unknown');
        
        ////////////////////////////////////////////////////////////////////////
        // PHP Version
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['php_version'] = \PHP_VERSION;
        
        ////////////////////////////////////////////////////////////////////////
        // Virtualization
        ////////////////////////////////////////////////////////////////////////
        
        // retrieve via linfo
        $virtualization = method_exists($linfoObj, 'getVirtualization')
                        && is_array($linfoObj->getVirtualization()) 
                        && isset($linfoObj->getVirtualization()['method'])
                        ? $linfoObj->getVirtualization()['method'] : NULL;

        if (Utils::getNullIfEmpty($virtualization) === null) {

            // try to retrieve via ginfo
            $virtualization = ($generalInfo instanceof GinfoGeneral) 
                                ? Utils::getDefaultIfEmpty(
                                    $generalInfo->getVirtualization(),
                                    ''
                                  ) 
                                : '';
        }
        
        $systemOverviewData['virtualization'] = $virtualization;
        
        ////////////////////////////////////////////////////////////////////////
        // Free Ram (bytes)
        ////////////////////////////////////////////////////////////////////////
        
        // retrieve via linfo
        $freeRam = is_array($linfoObj->getRam()) 
                        && isset($linfoObj->getRam()['free'])
                        ? $linfoObj->getRam()['free'] : NULL;

        if (Utils::getNullIfEmpty($freeRam) === null) {

            // try to retrieve via ginfo
            $freeRam = ($ginfoObj->getMemory() instanceof GinfoMemory) 
                                ? Utils::getDefaultIfEmpty(
                                    $ginfoObj->getMemory()->getFree(),
                                    -1
                                  ) 
                                : -1;
        }
        
        $systemOverviewData['free_ram_bytes'] = (int)$freeRam;
        
        ////////////////////////////////////////////////////////////////////////
        // Free Swap Memory (bytes)
        ////////////////////////////////////////////////////////////////////////
        
        // retrieve via linfo
        $freeSwapRam = is_array($linfoObj->getRam()) 
                        && isset($linfoObj->getRam()['swapFree'])
                        ? $linfoObj->getRam()['swapFree'] : NULL;

        if (Utils::getNullIfEmpty($freeSwapRam) === null) {

            // try to retrieve via ginfo
            $freeSwapRam = ($ginfoObj->getMemory() instanceof GinfoMemory) 
                                ? Utils::getDefaultIfEmpty(
                                    $ginfoObj->getMemory()->getSwapFree(),
                                    -1
                                  ) 
                                : -1;
        }
        
        $systemOverviewData['free_swap_bytes'] = (int)$freeSwapRam;
        
        ////////////////////////////////////////////////////////////////////////
        // Total Ram (bytes)
        ////////////////////////////////////////////////////////////////////////

        // retrieve via linfo
        $totalRam = is_array($linfoObj->getRam()) 
                        && isset($linfoObj->getRam()['total'])
                        ? $linfoObj->getRam()['total'] : NULL;

        if (Utils::getNullIfEmpty($totalRam) === null) {

            // try to retrieve via ginfo or trntv/probe
            $totalRam = ($ginfoObj->getMemory() instanceof GinfoMemory) 
                                ? Utils::getDefaultIfEmpty(
                                    $ginfoObj->getMemory()->getTotal(),
                                        Utils::getDefaultIfEmpty(
                                            $trntInfo->getTotalMem(),
                                            -1
                                        ) 
                                  ) 
                                : -1;
        }
        
        $systemOverviewData['total_ram_bytes'] = (int)$totalRam;
        
        ////////////////////////////////////////////////////////////////////////
        // Total Swap Memory (bytes)
        ////////////////////////////////////////////////////////////////////////

        // retrieve via linfo
        $totalSwap = is_array($linfoObj->getRam()) 
                        && isset($linfoObj->getRam()['swapTotal'])
                        ? $linfoObj->getRam()['swapTotal'] : NULL;

        if (Utils::getNullIfEmpty($totalSwap) === null) {

            // try to retrieve via ginfo or trntv/probe
            $totalSwap = ($ginfoObj->getMemory() instanceof GinfoMemory) 
                                ? Utils::getDefaultIfEmpty(
                                    $ginfoObj->getMemory()->getSwapTotal(),
                                        Utils::getDefaultIfEmpty(
                                            $trntInfo->getTotalSwap(),
                                            -1
                                        ) 
                                  ) 
                                : -1;
        }
        
        $systemOverviewData['total_swap_bytes'] = (int)$totalSwap;
        
        ////////////////////////////////////////////////////////////////////////
        // Used Ram (bytes)
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['used_ram_bytes'] =
            (int) Utils::getValIfTrueOrGetDefault(
                ($systemOverviewData['total_ram_bytes'] > -1) ,
                $systemOverviewData['total_ram_bytes'] 
                - $systemOverviewData['free_ram_bytes'],
                -1
            );
        
        ////////////////////////////////////////////////////////////////////////
        // Used Swap Memory (bytes)
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['used_swap_bytes'] =
            (int) Utils::getValIfTrueOrGetDefault(
                ($systemOverviewData['total_swap_bytes'] > -1) ,
                $systemOverviewData['total_swap_bytes'] 
                - $systemOverviewData['free_swap_bytes'],
                -1
            );
        
        ////////////////////////////////////////////////////////////////////////
        // Overall CPU Usage %
        ////////////////////////////////////////////////////////////////////////
        
        /** @var \VersatileCollections\CollectionInterface $cpuInfo **/
        $cpuInfo = is_array($linfoObj->getCPU())
                    ? ArraysCollection::makeNew($linfoObj->getCPU())
                    : ArraysCollection::makeNew();
        
        // $cpuInfo should look like below:
        //[
        //    0 => [
        //        'usage_percentage' => 2.06,
        //        'Vendor' => 'GenuineIntel',
        //        'Model' => 'Intel(R) Core(TM) i5-3570 CPU @ 3.40GHz',
        //        'MHz' => '3392.314',
        //    ],
        //    .......
        //    .......
        //]
        
        $systemOverviewData['overall_cpu_usage_percent'] =
            $cpuInfo->count() > 0 
                ?
                    $cpuInfo->column('usage_percentage') // extract usage_percentage values from each item in the collection
                            ->getAsNewType(NumericsCollection::class) // create a numeric collection containing the usage_percentage values
                            ->sum() / $cpuInfo->count() // calculate the sum of all the usage_percentage values and divide by the number of items
                :   -1.0;
        
        ////////////////////////////////////////////////////////////////////////
        // Total number of Physical CPU Cores
        ////////////////////////////////////////////////////////////////////////

        // retrieve via ginfo
        $totalNumPhysicalCpuCores = 
            ($ginfoObj->getCpu() instanceof GinfoCpu) 
                ? Utils::getDefaultIfEmpty(
                    $ginfoObj->getCpu()->getCores(),
                    NULL
                  ) 
                : NULL;

        if (Utils::getNullIfEmpty($totalNumPhysicalCpuCores) === NULL) {

            // try to retrieve via trntv/probe
            $totalNumPhysicalCpuCores = Utils::getDefaultIfEmpty(
                                            $trntInfo->getCpuPhysicalCores(),
                                            -1
                                        );
        }
        
        $systemOverviewData['total_num_physical_cpu_cores'] = (int)$totalNumPhysicalCpuCores;
        
        ////////////////////////////////////////////////////////////////////////
        // Total number of Logical / Virtual Processors
        ////////////////////////////////////////////////////////////////////////

        // retrieve via linfo
        $totalNumVirtualProcessors = is_array($linfoObj->getCPU()) 
                                        ? count($linfoObj->getCPU()) : NULL;

        if (Utils::getNullIfEmpty($totalNumVirtualProcessors) === NULL) {

            // try to retrieve via ginfo or trntv/probe
            $totalNumVirtualProcessors = 
                ($ginfoObj->getCpu() instanceof GinfoCpu) 
                    ? Utils::getDefaultIfEmpty(
                        $ginfoObj->getCpu()->getVirtual(),
                        Utils::getDefaultIfEmpty(
                            $trntInfo->getCpuCores(),
                            -1
                        ) 
                      ) 
                    : -1;
        }
        
        $systemOverviewData['total_num_virtual_or_logical_processors'] = (int)$totalNumVirtualProcessors;
        
        ////////////////////////////////////////////////////////////////////////
        // Total number of processes
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['total_number_of_processes'] = 
            (
                is_array($linfoObj->getProcessStats()) 
                && array_key_exists('proc_total', $linfoObj->getProcessStats())
            )
            ? (int)$linfoObj->getProcessStats()['proc_total'] 
            : -1;
        
        ////////////////////////////////////////////////////////////////////////
        // Total number of threads
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['total_number_of_threads'] = 
            (
                is_array($linfoObj->getProcessStats()) 
                && array_key_exists('threads', $linfoObj->getProcessStats())
            )
            ? (int)$linfoObj->getProcessStats()['threads'] 
            :-1;
        
        ////////////////////////////////////////////////////////////////////////
        // Total number of running processes (linux only)
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['total_number_of_running_processes_linux'] = 
            
                is_array($linfoObj->getProcessStats()) && array_key_exists('totals', $linfoObj->getProcessStats())
                && is_array($linfoObj->getProcessStats()['totals']) && array_key_exists('running', $linfoObj->getProcessStats()['totals'])
                ?  
                (int)$linfoObj->getProcessStats()['totals']['running']
                : 
                -1
            ;
        
        ////////////////////////////////////////////////////////////////////////
        // Total number of sleeping processes (linux only)
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['total_number_of_sleeping_processes_linux'] = 
                    
                is_array($linfoObj->getProcessStats()) && array_key_exists('totals', $linfoObj->getProcessStats())
                && is_array($linfoObj->getProcessStats()['totals']) && array_key_exists('sleeping', $linfoObj->getProcessStats()['totals'])
                ?
                (int)$linfoObj->getProcessStats()['totals']['sleeping']
                :
                -1;
        
        ////////////////////////////////////////////////////////////////////////
        // Total number of stopped processes (linux only)
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['total_number_of_stopped_processes_linux'] = 
            
                is_array($linfoObj->getProcessStats()) && array_key_exists('totals', $linfoObj->getProcessStats())
                && is_array($linfoObj->getProcessStats()['totals']) && array_key_exists('stopped', $linfoObj->getProcessStats()['totals'])
                ?
                (int)$linfoObj->getProcessStats()['totals']['stopped']
                :
                -1
            ;
        
        ////////////////////////////////////////////////////////////////////////
        // Total number of zombie processes (linux only)
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['total_number_of_zombie_processes_linux'] = 
            
                is_array($linfoObj->getProcessStats()) && array_key_exists('totals', $linfoObj->getProcessStats())
                && is_array($linfoObj->getProcessStats()['totals']) && array_key_exists('zombie', $linfoObj->getProcessStats()['totals'])
                ?
                (int)$linfoObj->getProcessStats()['totals']['zombie']
                :
                -1
            ;
        
        ////////////////////////////////////////////////////////////////////////
        // Number of active users
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['number_of_logged_in_users'] = 
            
                ($generalInfo instanceof GinfoGeneral) && is_countable($generalInfo->getLoggedUsers())
                ?
                count($generalInfo->getLoggedUsers())
                :
                -1
            ;
        
        ////////////////////////////////////////////////////////////////////////
        // CPU Info
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['cpus_info'] = $this->generateCpuInfoData();
        
        ////////////////////////////////////////////////////////////////////////
        // Selinux Info
        ////////////////////////////////////////////////////////////////////////
        $systemOverviewData['selinux_enabled'] = -1;
        $systemOverviewData['selinux_mode'] = '';
        $systemOverviewData['selinux_policy'] = '';
        
        $gInfoSelinuxObj = $ginfoObj->getSelinux();

        if( $gInfoSelinuxObj instanceof GinfoSelinux ) {
            
            $systemOverviewData['selinux_enabled'] = (int)$gInfoSelinuxObj->isEnabled();
            $systemOverviewData['selinux_mode'] = $gInfoSelinuxObj->getMode();
            $systemOverviewData['selinux_policy'] = $gInfoSelinuxObj->getPolicy();
        }

        return $systemOverviewData;
    }
    
    protected function generateCpuInfoData(): array {
        
        $cpuInfoData = [];
        
        /** @var \Linfo\Linfo $linfo */
        $linfo = $this->container->get('linfo_server_info');
        
        /** @var \Linfo\OS\OS $linfoObj */
        $linfoObj = $linfo->getParser();
        
        $data = $linfoObj->getCPU();

        if(Utils::isCountableWithData($data)) {

            foreach($data as $cpuNumber=>$datum) {
                
                $cpuInfoData[] = [
                    'cpu_number'        =>  $cpuNumber,
                    'usage_percentage'  =>  (
                                                array_key_exists('usage_percentage', $datum)
                                                ? (float)$datum['usage_percentage']
                                                : -1.0
                                            ),
                    'vendor'            =>  (
                                                array_key_exists('Vendor', $datum) 
                                                ? $datum['Vendor']
                                                :''
                                            ),
                    'model'             =>  (
                                                array_key_exists('Model', $datum)
                                                ? $datum['Model']
                                                : ''
                                            ),
                    'speed_mhz'         =>  (
                                                array_key_exists('MHz', $datum)
                                                ? (float)$datum['MHz']
                                                : -1.0
                                            ),
                ];
            } // foreach($data as $cpuNumber=>$datum)
        } // if(Utils::isCountableWithData($data))
        
        return $cpuInfoData;
    }
    
    protected function generatePciAndUsbHardwareInfoData(): array {

        $pciAndUsbHardwareInfoData = [];
        
        /** @var \Linfo\Linfo $linfo */
        $linfo = $this->container->get('linfo_server_info');
        
        /** @var \Linfo\OS\OS $linfoObj */
        $linfoObj = $linfo->getParser();

        /** @var \Ginfo\Ginfo $ginfo */
        $ginfo = $this->container->get('ginfo_server_info');
        $ginfoObj = $ginfo->getInfo();
        
        $ginfoPciHwData = $ginfoObj->getPci();
        $ginfoUsbHwData = $ginfoObj->getUsb();
        
        if(Utils::isCountableWithData($ginfoPciHwData)) {

            /** @var \Ginfo\Info\Pci $pciHwRecord */
            foreach($ginfoPciHwData as $pciHwRecord) {

                $pciAndUsbHardwareInfoData[] = [
                    'name'      => $pciHwRecord->getName(),
                    'vendor'    => $pciHwRecord->getVendor(),
                    'type'      => 'PCI',
                ];
            } // foreach($ginfoPciHwData as $pciHwRecord)
        } // if(Utils::isCountableWithData($ginfoPciHwData))
        
        if(Utils::isCountableWithData($ginfoUsbHwData)) {

            /** @var \Ginfo\Info\Usb $usbHwRecord */
            foreach($ginfoUsbHwData as $usbHwRecord) {

                $pciAndUsbHardwareInfoData[] = [
                    'name'      => $usbHwRecord->getName(),
                    'vendor'    => $usbHwRecord->getVendor(),
                    'type'      => 'USB',
                ];
            } // foreach($ginfoUsbHwData as $usbHwRecord)
        } // if(Utils::isCountableWithData($ginfoUsbHwData))
        
        if( 
            count($pciAndUsbHardwareInfoData) === 0 
            && method_exists($linfoObj, 'getDevs')
        ) {
            // Could not get the data via ginfo. Try linfo
            $pciAndUsbHwData = $linfoObj->getDevs();
            
            foreach ($pciAndUsbHwData as $pciOrUsbHwRecord) {
                $pciAndUsbHardwareInfoData[] = [
                    'name'      => Utils::arrayGet($pciOrUsbHwRecord, 'device'),
                    'vendor'    => Utils::arrayGet($pciOrUsbHwRecord, 'vendor'),
                    'type'      => strtoupper(Utils::arrayGet($pciOrUsbHwRecord, 'type', 'UNKNOWN')),
                ];
            }
        }
        
        return $pciAndUsbHardwareInfoData;
    }
    
    protected function generateSoundCardInfoData(): array {

        $soundCardInfoData = [];
        
        /** @var \Linfo\Linfo $linfo */
        $linfo = $this->container->get('linfo_server_info');
        
        /** @var \Linfo\OS\OS $linfoObj */
        $linfoObj = $linfo->getParser();

        /** @var \Ginfo\Ginfo $ginfo */
        $ginfo = $this->container->get('ginfo_server_info');
        $ginfoObj = $ginfo->getInfo();
        
        $ginfoSoundCardData = $ginfoObj->getSoundCard();
        
        if(Utils::isCountableWithData($ginfoSoundCardData)) {

            /** @var \Ginfo\Info\SoundCard $soundCardRecord */
            foreach($ginfoSoundCardData as $soundCardRecord) {

                $soundCardInfoData[] = [
                    'name'      => $soundCardRecord->getName(),
                    'vendor'    => $soundCardRecord->getVendor(),
                ];
            } // foreach($ginfoSoundCardData as $soundCardRecord)
        } // if(Utils::isCountableWithData($ginfoSoundCardData))
        
        if(count($soundCardInfoData) === 0 && method_exists($linfoObj, 'getSoundCards')) {
            
            // Could not get the data via ginfo. Try linfo
            $linfoSoundCardData = $linfoObj->getSoundCards();
            
            foreach ($linfoSoundCardData as $linfoSoundCardRecord) {
                
                $soundCardInfoData[] = [
                    'name'      => Utils::arrayGet($linfoSoundCardRecord, 'card'),
                    'vendor'    => Utils::arrayGet($linfoSoundCardRecord, 'vendor'),
                ];
            } // foreach ($linfoSoundCardData as $linfoSoundCardRecord)
        } // if(count($soundCardInfoData) === 0 && method_exists($linfoObj, 'getSoundCards'))
        
        return $soundCardInfoData;
    }
    
    protected function generateNetworkInfoData() : array {
        
        $networkInfoData = [];
        
        /** @var \Linfo\Linfo $linfo */
        $linfo = $this->container->get('linfo_server_info');
        
        /** @var \Linfo\OS\OS $linfoObj */
        $linfoObj = $linfo->getParser();
        
        /** @var \Ginfo\Ginfo $ginfo */
        $ginfo = $this->container->get('ginfo_server_info');
        $ginfoObj = $ginfo->getInfo();
        
        $ginfoNetworkData = $ginfoObj->getNetwork();
        $linfoNetworkData = method_exists($linfoObj, 'getNet') ? $linfoObj->getNet() : [];

        $getDataFromLinfoNetworkData = function(string $networkIfaceName, string $attributeName, $defaultVal='') use ($linfoNetworkData) {
            
            return (
                    is_array($linfoNetworkData) 
                    && array_key_exists($networkIfaceName, $linfoNetworkData)
                    && is_array($linfoNetworkData[$networkIfaceName])
                    && array_key_exists($attributeName, $linfoNetworkData[$networkIfaceName])
                   )
                    ?
                    $linfoNetworkData[$networkIfaceName][$attributeName]
                    :
                    $defaultVal
                    ;
        };

        if( Utils::isCountableWithData($ginfoNetworkData) ) {
            
            /** @var \Ginfo\Info\Network $gNeworkDatum */
            foreach ($ginfoNetworkData as $gNeworkDatum) {
                
                $networkInfoData[] = [
                    'name'                  => $gNeworkDatum->getName(),
                    // linfo returns Mbits/s while ginfo returns bits/s, 
                    // we want bits/s so no need to convert here
                    'speed_bits_per_second' => is_null($gNeworkDatum->getSpeed()) ? -1 : $gNeworkDatum->getSpeed(),
                    'type'                  => Utils::getDefaultIfEmpty($gNeworkDatum->getType(), 'unknown'),
                    'state'                 => Utils::getDefaultIfEmpty($gNeworkDatum->getState(), 'unknown'),
                    
                    'num_bytes_received'    => is_null($gNeworkDatum->getStatsReceived()) ? -1 : $gNeworkDatum->getStatsReceived()->getBytes(),
                    'num_received_errors'   => is_null($gNeworkDatum->getStatsReceived()) ? -1 : $gNeworkDatum->getStatsReceived()->getErrors(),
                    'num_received_packets'  => is_null($gNeworkDatum->getStatsReceived()) ? -1 : $gNeworkDatum->getStatsReceived()->getPackets(),
                    
                    'num_bytes_sent'        => is_null($gNeworkDatum->getStatsSent()) ? -1 : $gNeworkDatum->getStatsSent()->getBytes(),
                    'num_sent_errors'       => is_null($gNeworkDatum->getStatsSent()) ? -1 : $gNeworkDatum->getStatsSent()->getErrors(),
                    'num_sent_packets'      => is_null($gNeworkDatum->getStatsSent()) ? -1 : $gNeworkDatum->getStatsSent()->getPackets(),
                    
                    'gateway'               => $getDataFromLinfoNetworkData($gNeworkDatum->getName(), 'gateway', ''),
                    'ipv4'                  => $getDataFromLinfoNetworkData($gNeworkDatum->getName(), 'ipv4', ''),
                    'mac'                   => $getDataFromLinfoNetworkData($gNeworkDatum->getName(), 'mac', ''),
                ];
            }
            
        } elseif ( Utils::isCountableWithData($linfoNetworkData) ) {
            
            foreach ($linfoNetworkData as $ifaceName => $lNeworkDatum) {

                $portSpeed = $getDataFromLinfoNetworkData($ifaceName, 'port_speed', -1);

                if( !is_numeric($portSpeed) ) {

                    $portSpeed = -1;
                }

                $networkInfoData[] = [
                    'name'                  => $ifaceName,
                    // linfo returns Mbits/s while ginfo returns bits/s
                    // so convert from Mbits/s to bits/s if necessary
                    'speed_bits_per_second' => ($portSpeed === -1) ? $portSpeed : ((float)$portSpeed) * 1000000,
                    'type'                  => $getDataFromLinfoNetworkData($ifaceName, 'type', 'unknown'),
                    'state'                 => $getDataFromLinfoNetworkData($ifaceName, 'state', 'unknown'),

                    'num_bytes_received'    => (int)Utils::arrayGet($getDataFromLinfoNetworkData($ifaceName, 'recieved', []), 'bytes', -1),
                    'num_received_errors'   => (int)Utils::arrayGet($getDataFromLinfoNetworkData($ifaceName, 'recieved', []), 'errors', -1),
                    'num_received_packets'  => (int)Utils::arrayGet($getDataFromLinfoNetworkData($ifaceName, 'recieved', []), 'packets', -1),

                    'num_bytes_sent'        => (int)Utils::arrayGet($getDataFromLinfoNetworkData($ifaceName, 'sent', []), 'bytes', -1),
                    'num_sent_errors'       => (int)Utils::arrayGet($getDataFromLinfoNetworkData($ifaceName, 'sent', []), 'errors', -1),
                    'num_sent_packets'      => (int)Utils::arrayGet($getDataFromLinfoNetworkData($ifaceName, 'sent', []), 'packets', -1),

                    'gateway'               => $getDataFromLinfoNetworkData($ifaceName, 'gateway', ''),
                    'ipv4'                  => $getDataFromLinfoNetworkData($ifaceName, 'ipv4', ''),
                    'mac'                   => $getDataFromLinfoNetworkData($ifaceName, 'mac', ''),
                ];
            }
        }
        
        return $networkInfoData;
    }
}
