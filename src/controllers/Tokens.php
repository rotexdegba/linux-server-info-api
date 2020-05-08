<?php
namespace Lsia\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VersatileCollections\ObjectsCollection;
use Lsia\Atlas\Models\Token\TokenRecord;
use Lsia\Atlas\Models\Token\Token;
use VersatileCollections\MultiSortParameters;

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
        ContainerInterface $container, ?string $controller_name_from_uri, ?string $action_name_from_uri, 
        ServerRequestInterface $req, ResponseInterface $res
    ) {
        parent::__construct($container, $controller_name_from_uri, $action_name_from_uri, $req, $res);
    }
    
    public function actionIndex() {
        
        //get the contents of the view first
        $view_str = $this->renderView('index.php', ['controller_object'=>$this]);
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function actionMyTokens($idOfLastEditedToken='') {
        
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
                     ->fetchRecords()
        );
        
        // Filter out active records by comparing each record's expiry_date
        // to the current date. Doing it here instead of using sqlite's
        // date function in the atlas query above in order to make switching
        // db engines easy and portable, don't want to be embedding sqlite
        // specific function calls in queries.
        $activeTokenRecords = $tokenRecords->filterAll(
            function($key, TokenRecord $item) {

                return strtotime(date('Y-m-d H:i:s')) < strtotime(date($item->expiry_date));
            }, 
            true,  // bool $copy_keys 
            false, // bool $bind_callback_to_this
            true   // bool $remove_filtered_items
        );
        
        // Records remaining in $tokenRecords are the expired ones
        $expiredTokenRecords = $tokenRecords;
        
        if( $expiredTokenRecords->count() > 0 ) {
            
            // Sort expired records by expiry_date
            // at the collection level. Atlas query
            // above ordered records by date_created ASC
            $sortParam1 = new MultiSortParameters('expiry_date', SORT_ASC, SORT_STRING);
            $expiredTokenRecords->sortMeByMultipleFields($sortParam1);
        }
        
        //get the contents of the view first
        $view_str = $this->renderView(
            'my-tokens.php', 
            [
                'idOfLastEditedToken'=> $idOfLastEditedToken,
                'activeTokenRecords'=>$activeTokenRecords,
                'expiredTokenRecords'=>$expiredTokenRecords,
            ]
        );
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    protected function showAddEditForm(array $errorMessages=[], array $dataToEdit=[], $populateWithRecordDataForEdits=false) {
        
        $isEditing = count($dataToEdit) > 0;
        $commonData = $this->container->get('common_data_for_views_and_templates');
        $tokenFormPresetData = [
            'id'                    => ($isEditing ? $dataToEdit['id'] : null),
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

        if( $isEditing && $populateWithRecordDataForEdits ) {
            
            foreach ($dataToEdit as $field => $val) {
                
                $tokenFormPresetData[$field] = $val;
            }
            
            // expiry date is stored in the db in the
            // 'Y-m-d H:i:s' format. Need to convert it 
            // to 'Y-m-d' for the native html5 date picker
            // the 'H:i:s' part is always '00:00:00' in the
            // db anyways and will be glued back when saving
            // the record
            if( isset($dataToEdit['expiry_date']) ) {
                
                $tokenFormPresetData['expiry_date'] = explode(' ', $dataToEdit['expiry_date'])[0];
            }
        }

        //get the contents of the view first
        $view_str = $this->renderView(
            'add-edit.php', 
            [
                'formData' => $tokenFormPresetData,
                'vespForm' => $this->container->get('vespula_form_obj'),
                'errorMessages' => $errorMessages,
                'formTitle' => ($isEditing ? 'Edit Token' : 'Add Token'),
                'formAction' => ($isEditing ? s3MVC_MakeLink('/tokens/do-edit/'.$dataToEdit['id']) : s3MVC_MakeLink('/tokens/do-add')),
            ]
        );
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function actionAdd() {
        
        $resp = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if($resp !== false) {
            
            return $resp;
        }
        
        return $this->showAddEditForm();
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
            $newRecordData = $this->getRecordDataFromPost(s3MVC_GetSuperGlobal('post'), $tblCols);
            
            /** @var \Atlas\Orm\Atlas $atlasObj */
            $atlasObj = $this->container->get('atlas');
        
            /** @var \Lsia\Atlas\Models\Token\TokenRecord $newRecord */
            $newRecord = $atlasObj->newRecord(Token::class);
            
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
                    return $this->redirect(s3MVC_MakeLink('/tokens/my-tokens'));
                }
                            
            } else {
                
                return $this->showAddEditForm($validator->getMessages());
            }
        } else {
            
            return $this->showAddEditForm();
        }
    }
    
    public function actionEdit($id) {
        
        /** @var \Lsia\Atlas\Models\Token\TokenRecord $tokenRecord */
        $tokenRecord = $this->getAndValidateRecordForEditOrDelete($id.'');
                
        if( $tokenRecord instanceof ResponseInterface ) {
            
            // validation failed, a response with details was 
            // returned instead of a record
            return $tokenRecord; 
        }
        
        return $this->showAddEditForm([], $tokenRecord->getRow()->getArrayCopy(), true);
    }
    
    public function actionDoEdit($id) {
        
        // CSRF check from preAction would have led to 403
        if( $this->response->getStatusCode() === 403 ) {
            
            return $this->response;
        }
        
        /** @var \Lsia\Atlas\Models\Token\TokenRecord $tokenRecord */
        $tokenRecord = $this->getAndValidateRecordForEditOrDelete($id.'');
                
        if( $tokenRecord instanceof ResponseInterface ) {
            
            // validation failed, a response with details was 
            // returned instead of a record
            return $tokenRecord; 
        }
        
        if( $this->isPostRequest() ) {
            
            $tblCols = $this->getTableColumnNames('tokens');
            $recordDataFromPost = $this->getRecordDataFromPost(s3MVC_GetSuperGlobal('post'), $tblCols);
            
            /** @var \Atlas\Orm\Atlas $atlasObj */
            $atlasObj = $this->container->get('atlas');
            
            $validationRules = $tokenRecord->getSiriusValidationRules();
            
            /** @var \Sirius\Validation\Validator $validator */
            $validator = $this->container->get('sirius_validator');
            
            foreach ($validationRules as $validationRule) {
            
                $validator->add(...$validationRule);
            }
            
            // tweak the posted expiry_date value from YYYY-MM-DD 
            // to YYYY-MM-DD HH:MM:SS
            if(isset($recordDataFromPost['expiry_date'])) {
                
                $recordDataFromPost['expiry_date'] .= ' 00:00:00';
            }
            
            if( $validator->validate($recordDataFromPost) ) {

                $tokenRecord->set($recordDataFromPost); // inject data into the record
                $date = new \DateTime();
                $tokenRecord->date_last_edited = $date->format('Y-m-d H:i:s');
                
                try {
                    $atlasObj->update($tokenRecord);
                    $this->setSuccessFlashMessage('Token Successfully Updated!');
                    return $this->redirect(s3MVC_MakeLink('/tokens/my-tokens/'.$tokenRecord->id));
                    
                } catch (\Exception $exc) {
                    
                    $this->logError($exc->getTraceAsString(), 'Error Updating Token');
                    $this->setErrorFlashMessage('Token Not Successfully Updated!');
                    return $this->redirect(s3MVC_MakeLink('/tokens/my-tokens/'.$tokenRecord->id));
                }
                            
            } else {
                
                return $this->showAddEditForm($validator->getMessages(), $tokenRecord->getRow()->getArrayCopy(), false);
            }
        } else {
            
            // not a post request, populate form with record data
            return $this->showAddEditForm([], $tokenRecord->getRow()->getArrayCopy(), true);
        }
    }
    
    public function actionDelete($id) {
        
        /** @var \Lsia\Atlas\Models\Token\TokenRecord $tokenRecord */
        $tokenRecord = $this->getAndValidateRecordForEditOrDelete($id.'');
                
        if( $tokenRecord instanceof ResponseInterface ) {
            
            // validation failed, a response with details was 
            // returned instead of a record
            return $tokenRecord; 
        }
        
        /** @var \Atlas\Orm\Atlas $atlasObj */
        $atlasObj = $this->container->get('atlas');
        
        try {
            $atlasObj->delete($tokenRecord);
            $this->setSuccessFlashMessage('Token Successfully Deleted!');
            
            return $this->redirect(s3MVC_MakeLink('/tokens/my-tokens'));

        } catch (\Exception $exc) {

            $this->logError($exc->getTraceAsString(), 'Error Deleting Token');
            $this->setErrorFlashMessage('Token Not Successfully Deleted!');
            
            return $this->redirect(s3MVC_MakeLink("/tokens/my-tokens/{$id}"));
        }
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
    
    protected function getAndValidateRecordForEditOrDelete(string $id) {
        
        /** @var \Atlas\Orm\Atlas $atlasObj */
        $atlasObj = $this->container->get('atlas');
        
        $tokenRecord = $atlasObj->fetchRecord(Token::class, $id);
        
        if( !($tokenRecord instanceof TokenRecord) ) {
            
            $tokenRecord = $this->generateResponse('Not found', 404, true);
            
        } else {
        
            if( $this->isLoggedIn() && $this->vespula_auth->getUsername() !== $tokenRecord->generators_username ) {

                $tokenRecord = $this->generateResponse('Not permitted', 403, true);
                
            } else if( !$this->isLoggedIn() ) {
                
                $tokenRecord = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
            }
        }
            
        return $tokenRecord;
    }
}
