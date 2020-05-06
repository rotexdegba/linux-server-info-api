<?php
namespace Lsia\Controllers;

/**
 * 
 * Description of Tokens goes here
 * 
 */
class Tokens extends \Lsia\Controllers\AppBase
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
    protected $login_success_redirect_controller = 'tokens';
    
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
    
    public function actionMyTokens() {
        
        $resp = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if($resp !== false) {
            
            return $resp;
        }
            
        /** @var \Atlas\Orm\Atlas $atlasObj */
        $atlasObj = $this->container->get('atlas');
        
        $tokenRecords = $atlasObj->select(\Lsia\Atlas\Models\Token\Token::class)
                                 ->where('generators_username = ', $this->vespula_auth->getUsername())
                                 ->fetchRecords();
        
        //get the contents of the view first
        $view_str = $this->renderView('my-tokens.php', ['tokenRecords'=>$tokenRecords]);
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    protected function showAddForm(array $errorMessages=[]) {
        
        $commonData = $this->container->get('common_data_for_views_and_templates');
        $tokenFormPresetData = [
            '__csrf_key'            => $commonData['__csrf_key'], // name for hidden input
            '__csrf_value'          => s3MVC_GetSuperGlobal('post', $commonData['__csrf_key'], $commonData['__csrf_value']), // value for hidden input
            
            'generators_username'   => s3MVC_GetSuperGlobal('post', 'generators_username', $commonData['__logged_in_user_name']), // hidden input
            'token'                 => s3MVC_GetSuperGlobal('post', 'token', bin2hex(random_bytes(64))), // readonly input
            'date_created'          => s3MVC_GetSuperGlobal('post', 'date_created', date('Y-m-d H:i:s')), // hidden input
            'date_last_edited'      => s3MVC_GetSuperGlobal('post', 'date_last_edited', date('Y-m-d H:i:s')), // hidden input
            'creators_ip'           => $this->request->getServerParams()['REMOTE_ADDR'], // hidden input
            'max_requests_per_day'  => s3MVC_GetSuperGlobal('post', 'max_requests_per_day', '0'),
            'expiry_date'           => s3MVC_GetSuperGlobal('post', 'expiry_date', date('Y-m-d', strtotime('+1 months'))),
        ];

        //get the contents of the view first
        $view_str = $this->renderView(
            'add.php', 
            [
                'formData' => $tokenFormPresetData,
                'vespForm' => $this->container->get('vespula_form_obj'),
                'errorMessages' => $errorMessages,
            ]
        );
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function actionAdd() {
        
        $resp = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if($resp !== false) {
            
            return $resp;
        }
        
        return $this->showAddForm();
    }
    
    public function actionDoAdd() {
        
        // CSRF check from preAction would have led to 403
        if( $this->response->getStatusCode() === 403 ) {
            
            return $this->response;
        }
        
        $resp = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if($resp !== false) {
            
            return $resp;
        }
        
        if( $this->isPostRequest() ) {
            
            $tblCols = $this->getTableColumnNames('tokens');
            $newRecordData = $this->newRecordDataFromPost(s3MVC_GetSuperGlobal('post'), $tblCols);
            
            /** @var \Atlas\Orm\Atlas $atlasObj */
            $atlasObj = $this->container->get('atlas');
        
            /** @var \Lsia\Atlas\Models\Token\TokenRecord $newRecord */
            $newRecord = $atlasObj->newRecord(\Lsia\Atlas\Models\Token\Token::class);
            
            $validationRules = $newRecord->getSiriusValidationRules();
            
            /** @var \Sirius\Validation\Validator $validator */
            $validator = $this->container->get('sirius_validator');
            
            foreach ($validationRules as $validationRule) {
            
                $validator->add(...$validationRule);
            }
            
            // tweak the posted expiry_date value from YYYY-MM-DD 
            // to YYYY-MM-DD HH:MM:SS
            if(isset($newRecordData['expiry_date'])) {
                
                $newRecordData['expiry_date'] .= ' 00:00:00';
            }
            
            if( $validator->validate($newRecordData) ) {

                $newRecord->set($newRecordData); // inject data into the record
                
                try {
                    $atlasObj->insert($newRecord);
                    $this->setSuccessFlashMessage('Token Successfully Created!');
                    return $this->redirect(s3MVC_MakeLink('/tokens/my-tokens'));
                    
                } catch (\Exception $exc) {
                    
                    $this->logError($exc->getTraceAsString(), 'Error Saving New Token');
                    $this->setErrorFlashMessage('Token Not Successfully Created!');
                    return $this->redirect(s3MVC_MakeLink('/'));
                }
                            
            } else {
                
                return $this->showAddForm($validator->getMessages());
            }
        } else {
            
            return $this->showAddForm();
        }
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
