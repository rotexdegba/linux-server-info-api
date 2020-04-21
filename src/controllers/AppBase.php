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
    }
    
    public function actionIndex() {
        
        //get the contents of the view first
        $view_str = $this->renderView('index.php', ['controller_object'=>$this]);
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function preAction() {
        
        // add code that you need to be executed before each controller action method is executed
        $response = parent::preAction();
        
//        $isLogInAction =  in_array(
//            strtolower($this->action_name_from_uri), 
//            ['login', 'action-login', 'actionlogin', 'action_login'] 
//        );
        
//var_dump($this->action_name_from_uri);exit;
        // always force redirect to login if not logged in
//        $potententialRedirect = 
//            ($isLogInAction) ? false : $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
//var_dump($potententialRedirect);exit;

//         $isLogOut =  in_array(
//            strtolower($this->action_name_from_uri), 
//            ['logout', 'action-logout', 'actionlogout', 'action_logout'] 
//        );
         
//        return ($potententialRedirect===false)? $response : $potententialRedirect;
        return $response;
    }
    
    public function postAction(\Psr\Http\Message\ResponseInterface $response) {
        
        // add code that you need to be executed after each controller action method is executed
        $new_response = parent::postAction($response);
        
        return $new_response;
    }
}
