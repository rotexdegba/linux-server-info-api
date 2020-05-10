<?php
namespace Lsia\Controllers;

use DateTime;
use Lsia\Utils;
use DateInterval;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 
 * Description of Server goes here
 * 
 */
class Server extends \Lsia\Controllers\AppBase
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
    protected $login_success_redirect_controller = 'server';
    
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
        
        /** @var \Linfo\Linfo $linfo */
        $linfo = $this->container->get('linfo_server_info');
        
        /** @var \Linfo\OS\Linux $osLinfoObj */
        $osLinfoObj = $linfo->getParser();
        
        /** @var \Ginfo\Ginfo $ginfo */
        $ginfo = $this->container->get('ginfo_server_info');
        $osGinfoObj = $ginfo->getInfo();
        $generalInfo = $osGinfoObj->getGeneral();

        $uptime = '';
        $lastBootedOn = '';
        
        if( $generalInfo->getUptime() instanceof DateInterval ) {
            
            $uptime = $generalInfo->getUptime()->format('%d days, %h hours, %i minutes, %s seconds');
            $lastBootedOn = (new DateTime())->sub($generalInfo->getUptime())->format('D, j M Y H:i:s T');
        }
        
        // TODO: Add some common software version info to the section that requires
        //       users to be logged in. E.g php, mysql, apache, python, ruby & more

        $viewData = [
            'hostName'              => [ 'label' => 'Host Name',            'value' => $generalInfo->getHostname() ],
            'distroNameAndVersion'  => [ 'label' => 'Distro Name and Version',  'value' => $generalInfo->getOsName() ],
            'kernelVersion'         => [ 'label' => 'Kernel Version',           'value' => $generalInfo->getKernel() ],
            'osFamily'              => [ 'label' => 'OS Family',                'value' => $osLinfoObj->getOS() ],
            'architecture'          => [ 'label' => 'Architecture',             'value' => $generalInfo->getArchitecture() ],
            'machineModel'          => [ 'label' => 'Machine Model',            'value' => Utils::getDefaultIfEmpty($generalInfo->getModel(), '') ],
            'lastBootedOn'          => [ 'label' => 'Last booted on',           'value' => $lastBootedOn ],
            'uptime'                => [ 'label' => 'Uptime',                   'value' => $uptime ],
            'loggedInUsers'         => [ 'label' => 'Logged in users',          'value' => Utils::getValIfTrueOrDefault(is_countable($generalInfo->getLoggedUsers()), count($generalInfo->getLoggedUsers()), 'Unknown') ],
            'processSummaryInfo'    => [],
        ];
        
        $processInfo = $osLinfoObj->getProcessStats();
        
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
        
        //get the contents of the view first
        $view_str = $this->renderView('index.php', $viewData);
        
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
