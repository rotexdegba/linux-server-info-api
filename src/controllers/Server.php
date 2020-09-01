<?php
namespace Lsia\Controllers;

use DateTime;
use Lsia\Utils;
use DateInterval;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VersatileCollections\ArraysCollection;
use VersatileCollections\ObjectsCollection;
use VersatileCollections\GenericCollection;
use VersatileCollections\StringsCollection;
use VersatileCollections\MultiSortParameters;

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
        
        $systemOverviewData = $this->generateSystemOverviewData();
//s3MVC_DumpVar($systemOverviewData);

        $viewData = [
            'hostName'              => [ 'label' => 'Host Name',                'value' => $systemOverviewData['host_name'] ],
            'distroNameAndVersion'  => [ 'label' => 'Distro Name and Version',  'value' => $systemOverviewData['distro_name'] ],
            'kernelVersion'         => [ 'label' => 'Kernel Version',           'value' => $systemOverviewData['kernel_version'] ],
            'osFamily'              => [ 'label' => 'OS Family',                'value' => $systemOverviewData['os_family'] ],
            'architecture'          => [ 'label' => 'Architecture',             'value' => $systemOverviewData['architecture'] ],
            'machineModel'          => [ 'label' => 'Machine Model',            'value' => $systemOverviewData['system_model'] ],
            'webSoftware'           => [ 'label' => 'Web Server Software',      'value' => $systemOverviewData['web_software'] ],
            'phpVersion'            => [ 'label' => 'PHP Version',              'value' => $systemOverviewData['php_version'] ],
            'virtualization'        => [ 'label' => 'Virtualization Technology','value' => $systemOverviewData['virtualization'] ],
            
            'selinuxEnabled'        => [ 'label' => 'Selinux Enabled',          'value' => $systemOverviewData['selinux_enabled'] ],
            'selinuxMode'           => [ 'label' => 'Selinux Mode',             'value' => $systemOverviewData['selinux_mode'] ],
            'selinuxPolicy'         => [ 'label' => 'Selinux Policy',           'value' => $systemOverviewData['selinux_policy'] ],
            
            'totalRamBytes'         => [ 'label' => 'Ram Memory Usage',         'value' => $systemOverviewData['total_ram_bytes'] ],
            'totalSwapBytes'        => [ 'label' => 'Swap Memory Usage',        'value' => $systemOverviewData['total_swap_bytes'] ],
            'usedRamBytes'          => [ 'label' => 'Used Ram Memeory',         'value' => $systemOverviewData['used_ram_bytes'] ],
            'usedSwapBytes'         => [ 'label' => 'Used Swap Memory',         'value' => $systemOverviewData['used_swap_bytes'] ],
            
            'overallCpuUsagePercent'=> [ 'label' => 'Overall CPU Percentage',   'value' => $systemOverviewData['overall_cpu_usage_percent'] ],
            'totalNumPhyscCpuCores' => [ 'label' => 'Total Number of Physical CPU Cores',   'value' => $systemOverviewData['total_num_physical_cpu_cores'] ],
            'totalNumVirtOrLogicalProcessors'=> [ 'label' => 'Total Number of Virtual / Logical Processors',   'value' => $systemOverviewData['total_num_virtual_or_logical_processors'] ],
            
            'lastBootedOn'          => [ 'label' => 'Last booted on',           'value' => (new DateTime())->setTimestamp($systemOverviewData['last_booted_timestamp'])->format('D, j M Y H:i:s T') ],
            'uptime'                => [ 'label' => 'Uptime',                   'value' => $systemOverviewData['uptime_text'] ],
            'loggedInUsers'         => [ 'label' => 'Logged in users',          'value' => Utils::getValIfTrueOrGetDefault($systemOverviewData['number_of_logged_in_users'] > -1, $systemOverviewData['number_of_logged_in_users'], 'Unknown') ],
            'processSummaryInfo'    => [
                                          [ 'label' => 'Total Number of Processes',             'value' => $systemOverviewData['total_number_of_processes'] ],
                                          [ 'label' => 'Total Number of Threads',               'value' => $systemOverviewData['total_number_of_threads'] ],
                                          [ 'label' => 'Total Number of Running Processes',     'value' => $systemOverviewData['total_number_of_running_processes_linux'] ],
                                          [ 'label' => 'Total Number of Sleeping Processes',    'value' => $systemOverviewData['total_number_of_sleeping_processes_linux'] ],
                                          [ 'label' => 'Total Number of Stopped Processes',     'value' => $systemOverviewData['total_number_of_stopped_processes_linux'] ],
                                          [ 'label' => 'Total Number of Zombie Processes',      'value' => $systemOverviewData['total_number_of_zombie_processes_linux'] ]
                                       ],
            'cpuInfo'               => [],
            'hwInfo'                => ArraysCollection::makeNew($this->generatePciAndUsbHardwareInfoData())
                                            ->sortByMultipleFields( 
                                                new MultiSortParameters('type', \SORT_ASC, (\SORT_FLAG_CASE | \SORT_NATURAL)),
                                                new MultiSortParameters('name', \SORT_ASC, (\SORT_FLAG_CASE | \SORT_NATURAL))
                                            )
                                            ,
            'sCardInfo'             => ArraysCollection::makeNew($this->generateSoundCardInfoData())
                                            ->sortByMultipleFields(
                                                new MultiSortParameters('name', \SORT_ASC, (\SORT_FLAG_CASE | \SORT_NATURAL))
                                            )
                                            ,
            'networkInfo'           => ArraysCollection::makeNew($this->generateNetworkInfoData())
                                            ->sortByMultipleFields( new MultiSortParameters('name', \SORT_ASC, (\SORT_FLAG_CASE | \SORT_NATURAL)) )
                                            ,
            'diskDrivesInfo'        => ArraysCollection::makeNew($this->generateDiskDrivesData())
                                            ->sortByMultipleFields( new MultiSortParameters('name', \SORT_ASC, (\SORT_FLAG_CASE | \SORT_NATURAL)) )
                                            ,
            'diskMountsInfo'        => ArraysCollection::makeNew($this->generateDiskMountsData())
                                            ->sortByMultipleFields( new MultiSortParameters('name', \SORT_ASC, (\SORT_FLAG_CASE | \SORT_NATURAL)) )
                                            ,
            'processesInfo'         => ArraysCollection::makeNew($this->generateProcessData())
                                            ->sortByMultipleFields( new MultiSortParameters('name', \SORT_ASC, (\SORT_FLAG_CASE | \SORT_NATURAL)) )
                                            ->transform(function($key, $val) {
                                                
                                                $val['memory'] = $val['memory'] < 0 ? 0 : $val['memory'];
                                                $val['peak_memory'] = $val['peak_memory'] < 0 ? 0 : $val['peak_memory'];
                                                $val['io_bytes_read'] = $val['io_bytes_read'] < 0 ? 0 : $val['io_bytes_read'];
                                                $val['io_bytes_written'] = $val['io_bytes_written'] < 0 ? 0 : $val['io_bytes_written'];
                                                
                                                return $val;
                                            }),
            'servicesInfo'              => ArraysCollection::makeNew($this->generateServicesData())
                                            ->sortByMultipleFields( new MultiSortParameters('name', \SORT_ASC, (\SORT_FLAG_CASE | \SORT_NATURAL)) )
                                            ->transform(function($key, $val) {
                                                
                                                $val['loaded'] = $val['loaded'] ? 'Yes' : 'No';
                                                $val['started'] = $val['started'] ? 'Yes' : 'No';
                                                
                                                return $val;
                                            }),
        ];
        
        // Add cpu info data
        foreach ($systemOverviewData['cpus_info'] as $cpuInfo) {
            
            $viewData['cpuInfo'][] = [
                'cpu_number'        => [ 'label' => 'CPU Core Number', 'value' => $cpuInfo['cpu_number'] ],
                'usage_percentage'  => [ 'label' => 'Percent Usage',   'value' => $cpuInfo['usage_percentage'] ],
                'vendor'            => [ 'label' => 'Vendor',          'value' => $cpuInfo['vendor'] ],
                'model'             => [ 'label' => 'Model',           'value' => $cpuInfo['model'] ],
                'speed_mhz'         => [ 'label' => 'Speed',           'value' => round($cpuInfo['speed_mhz']/1000, 2) . ' GHz' ]
            ];
        }
        
        //get the contents of the view first
        $view_str = $this->renderView('index.php', $viewData);
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function actionServerOverview() {

        $response = $this->response->withHeader('Content-type', 'application/json');
        
        $response->getBody()
                 ->write(
                    $this->generateApiJsonResponse(
                            $this->generateSystemOverviewData(), 
                            $this->getApiHttpStatusCodeForResponse(['GET'])
                        )
                  );
        $this->logTokenUsage();
        
        return $response;
    }
    
    public function actionCpusInfo() {

        $response = $this->response->withHeader('Content-type', 'application/json');

        $response->getBody()
                 ->write(
                    $this->generateApiJsonResponse(
                            $this->generateCpuInfoData(), 
                            $this->getApiHttpStatusCodeForResponse(['GET'])
                        )
                  );
        $this->logTokenUsage();
        
        return $response;
    }
    
    public function actionHardwareInfo() {

        $response = $this->response->withHeader('Content-type', 'application/json');

        $response->getBody()
                 ->write(
                    $this->generateApiJsonResponse(
                            $this->generatePciAndUsbHardwareInfoData(), 
                            $this->getApiHttpStatusCodeForResponse(['GET'])
                        )
                  );
        $this->logTokenUsage();
        
        return $response;
    }
    
    public function actionSoundCardInfo() {

        $response = $this->response->withHeader('Content-type', 'application/json');

        $response->getBody()
                 ->write(
                    $this->generateApiJsonResponse(
                            $this->generateSoundCardInfoData(), 
                            $this->getApiHttpStatusCodeForResponse(['GET'])
                        )
                  );
        $this->logTokenUsage();
        
        return $response;
    }
    
    public function actionDiskDrivesInfo() {

        $response = $this->response->withHeader('Content-type', 'application/json');

        $response->getBody()
                 ->write(
                    $this->generateApiJsonResponse(
                            $this->generateDiskDrivesData(), 
                            $this->getApiHttpStatusCodeForResponse(['GET'])
                        )
                  );
        $this->logTokenUsage();
        
        return $response;
    }
    
    public function actionDiskMountsInfo() {

        $response = $this->response->withHeader('Content-type', 'application/json');
        
        $response->getBody()
                 ->write(
                    $this->generateApiJsonResponse(
                            $this->generateDiskMountsData(), 
                            $this->getApiHttpStatusCodeForResponse(['GET'])
                        )
                  );
        $this->logTokenUsage();
        
        return $response;
    }
    
    public function actionNetworkInfo() {

        $response = $this->response->withHeader('Content-type', 'application/json');

        $response->getBody()
                 ->write(
                    $this->generateApiJsonResponse(
                            $this->generateNetworkInfoData(), 
                            $this->getApiHttpStatusCodeForResponse(['GET'])
                        )
                  );
        $this->logTokenUsage();
        
        return $response;
    }
    
    public function actionProcesses() {

        $response = $this->response->withHeader('Content-type', 'application/json');

        $response->getBody()
                 ->write(
                    $this->generateApiJsonResponse(
                            $this->generateProcessData(), 
                            $this->getApiHttpStatusCodeForResponse(['GET'])
                        )
                  );
        $this->logTokenUsage();
        
        return $response;
    }
    
    public function actionServices() {

        $response = $this->response->withHeader('Content-type', 'application/json');

        $response->getBody()
                 ->write(
                    $this->generateApiJsonResponse(
                            $this->generateServicesData(), 
                            $this->getApiHttpStatusCodeForResponse(['GET'])
                        )
                  );
        $this->logTokenUsage();
        
        return $response;
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
