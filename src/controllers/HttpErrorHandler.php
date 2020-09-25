<?php
namespace Lsia\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lsia\Utils;

/**
 * 
 * Description of HttpErrorHandler goes here
 * 
 */
class HttpErrorHandler extends AppBase
{   
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
    protected $login_success_redirect_controller = 'http-error-handler';
    
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
    }
    
    public function actionIndex() {
        
        return $this->redirect(s3MVC_MakeLink('/'));
    }
    
    public function actionHttpNotFound($_404_page_content = null, $_404_additional_log_message = null, $render_layout = true) {
        
        if($this->hasToken()) { // has a valid active or expired token
            
            $response = $this->response->withHeader('Content-type', 'application/json')
                                       ->withHeader('Access-Control-Allow-Origin', '*');

            $response->getBody()
                     ->write(
                        $this->generateApiJsonResponse(
                                [], 
                                static::HTTP_STATUS_NOT_FOUND
                            )
                      );
            $this->logTokenUsage(static::HTTP_STATUS_NOT_FOUND);

            return $response;
        }
        
        return parent::actionHttpNotFound($_404_page_content, $_404_additional_log_message, $render_layout);
    }
    
    public function generateServerErrorResponse(\Exception $exception, ServerRequestInterface $req = null, ResponseInterface $res = null, $render_layout = true) {
                
        if(!$this->isLoggedIn() && $this->hasValidToken()) {
            
            $response = $this->response->withHeader('Content-type', 'application/json')
                                       ->withHeader('Access-Control-Allow-Origin', '*');

            $response->getBody()
                     ->write(
                        $this->generateApiJsonResponse(
                                [], 
                                static::HTTP_STATUS_INTERNAL_SERVER_ERROR
                            )
                      );
            $this->logTokenUsage(static::HTTP_STATUS_INTERNAL_SERVER_ERROR, Utils::getThrowableAsStr($exception));

            return $response;
        }
        
        return parent::generateServerErrorResponse($exception, $req, $res, $render_layout);
    }
    
    public function preAction() {
        
        // add code that you need to be executed before each controller action method is executed
        $response = parent::preAction();
        
        return $response;
    }
    
    public function postAction(ResponseInterface $response) {
        
        // add code that you need to be executed after each controller action method is executed
        $new_response = parent::postAction($response);
        
        return $new_response;
    }
}
