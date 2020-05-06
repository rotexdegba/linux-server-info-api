<?php
namespace Lsia\Controllers;
/**
 * 
 * Description of Disks goes here
 *
 * 
 */
class Disks extends AppBase
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
    protected $login_success_redirect_controller = 'disks';
    
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
    
    public function actionUsage() {
        
        $data = [
            'raw'=>\Lsia\Utils::generateDiskUsageData(),
            'human_readable'=>\Lsia\Utils::generateDiskUsageDataHumanReadable()
        ];
        $this->response->getBody()->write(json_encode($data));

        return $this->response->withHeader('Content-Type', 'application/json');
    }
    
    public function preAction() {
        
        // add code that you need to be executed before each controller action method is executed
        $response = parent::preAction();
        
        return $response;
    }
    
    public function postAction(\Psr\Http\Message\ResponseInterface $response) {
        
        // add code that you need to be executed after each controller action method is executed
        $new_response = parent::postAction($response);
        
        return $new_response;
    }
}
