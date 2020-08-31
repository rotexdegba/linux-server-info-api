<?php
namespace Lsia\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use VersatileCollections\ObjectsCollection;
use Lsia\Atlas\Models\Token\Token;

/**
 * 
 * Description of TokenUsage goes here
 * 
 */
class TokenUsage extends \Lsia\Controllers\AppBase
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
    protected $login_success_redirect_controller = 'token-usage';
    
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
        
        $resp = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if($resp !== false) {
            
            return $resp;
        }
            
        /** @var \Atlas\Orm\Atlas $atlasObj */
        $atlasObj = $this->container->get('atlas');
        
        // Inject the array of records into a versatile collection objects
        // collection object to get extended collection operation capabilities
        $tokenRecords = ObjectsCollection::makeNew(
            $atlasObj->select(Token::class)
                     ->where('generators_username = ', $this->vespula_auth->getUsername())
                     ->orderBy('date_created ASC')
                     ->with(['usages'])
                     ->fetchRecords()
        );
        
        //get the contents of the view first
        $view_str = $this->renderView('index.php', ['tokenRecords'=>$tokenRecords]);
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
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
