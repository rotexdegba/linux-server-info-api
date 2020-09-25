<?php
namespace Lsia\Controllers;

use \Psr\Container\ContainerInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ServerRequestInterface;

/**
 * 
 * Description of ApiDocs goes here
 * 
 */
class ApiDocs extends \Lsia\Controllers\AppBase
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
    protected $login_success_redirect_controller = 'api-docs';
    
    protected $mdToHtmlLinkMap = [];

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
        
        $this->mdToHtmlLinkMap = [
            '(index.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/index').')',
            '(../index.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/index').')',
            '(http-status-codes.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/http-status-codes').')',
            
            '(server-cpus-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-cpus-info').')',
            '(../server-cpus-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-cpus-info').')',
            
            '(server-disk-drives-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-disk-drives-info').')',
            '(../server-disk-drives-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-disk-drives-info').')',
            
            '(server-disk-mounts-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-disk-mounts-info').')',
            '(../server-disk-mounts-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-disk-mounts-info').')',
            
            '(server-hardware-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-hardware-info').')',
            '(../server-hardware-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-hardware-info').')',
            
            '(server-network-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-network-info').')',
            '(../server-network-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-network-info').')',
            
            '(server-processes.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-processes').')',
            '(../server-processes.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-processes').')',
            
            '(server-server-overview.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-server-overview').')',
            '(../server-server-overview.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-server-overview').')',
            
            '(server-services.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-services').')',
            '(../server-services.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-services').')',
            
            '(server-sound-card-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-sound-card-info').')',
            '(../server-sound-card-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-sound-card-info').')',
            
            '(cpu-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/cpu-info/objects').')',
            '(objects/cpu-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/cpu-info/objects').')',
            
            '(disk-drive-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/disk-drive-info/objects').')',
            '(objects/disk-drive-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/disk-drive-info/objects').')',
            '(disk-drive-partition-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/disk-drive-partition-info/objects').')',
            
            '(objects/disk-mount-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/disk-mount-info/objects').')',
            '(objects/hardware-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/hardware-info/objects').')',
            '(objects/network-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/network-info/objects').')',
            '(objects/process-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/process-info/objects').')',
            '(objects/server-overview-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/server-overview-info/objects').')',
            '(objects/service-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/service-info/objects').')',
            '(objects/sound-card-info.md)' => '('.s3MVC_MakeLink('/api-docs/get-doc/sound-card-info/objects').')',
        ];
    }
    
    public function actionGetDoc(string $docName, string $subFolder='') {
        
        /** @var \League\CommonMark\GithubFlavoredMarkdownConverter $mdGenerator */
        $mdGenerator = $this->container->get('md_2_html_converter');

        $mdAsStr = $this->loadMarkDownFromDocsFolder($docName.'.md', $subFolder);
        
        $docStr = $mdGenerator->convertToHtml(
            $this->convertMdLinksToHtmlLinks($mdAsStr)
        );
        
        $view_str = $this->renderView('get-doc.php', ['doc'=>$docStr]);
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function actionIndex() {
        
        return $this->actionGetDoc("index");
    }
    
    protected function loadMarkDownFromDocsFolder(string $fileName='index.md', string $subFolder=''):string {
        
        $path = S3MVC_APP_ROOT_PATH.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR;
        
        if(strlen($subFolder) > 0) {
            
            $path .= $subFolder . DIRECTORY_SEPARATOR;
        }
        
        return file_get_contents($path.$fileName);
    }
    
    protected function convertMdLinksToHtmlLinks(string $markDownContent): string {
        
        return str_replace( array_keys($this->mdToHtmlLinkMap), array_values($this->mdToHtmlLinkMap), $markDownContent);
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
