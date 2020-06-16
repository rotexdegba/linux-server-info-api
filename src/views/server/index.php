        <link type="text/css" rel="stylesheet" href="<?= s3MVC_MakeLink('/datatables/jquery.dataTables.min.css'); ?>" media="screen,projection" />
        <script type="text/javascript" src="<?= s3MVC_MakeLink('/datatables/jquery.dataTables.min.js'); ?>"></script>   

                    <div id="server-index">
                        <div class="row pad-t-2-5-on-med-and-down">
                            <div class="col s12">
                                <ul class="collection with-header">
                                    <li class="collection-header"><h4>Server Summary</h4></li>
                                    <li class="collection-item"> <strong><?= $hostName['label']; ?>:</strong> <?= $hostName['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $distroNameAndVersion['label']; ?>:</strong> <?= $distroNameAndVersion['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $kernelVersion['label']; ?>:</strong> <?= $kernelVersion['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $osFamily['label']; ?>:</strong> <?= $osFamily['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $architecture['label']; ?>:</strong> <?= $architecture['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $machineModel['label']; ?>:</strong> <?= $machineModel['value']; ?> </li>
                                    
                                    <?php if($__is_logged_in): ?>
                                    
                                        <li class="collection-item"> <strong><?= $virtualization['label']; ?>:</strong> <?= $virtualization['value']; ?> </li>
                                        <li class="collection-item"> <strong><?= $webSoftware['label']; ?>:</strong> <?= $webSoftware['value']; ?> </li>
                                        <li class="collection-item"> <strong><?= $phpVersion['label']; ?>:</strong> <?= $phpVersion['value']; ?> </li>
                                        
                                        <li class="collection-item"> 
                                            <strong><i class="material-icons tiny">memory</i> <?= $totalRamBytes['label']; ?>:</strong>
                                            <label for="ram_usage"> 
                                                <?= \Lsia\Utils::bytesToHumanReadable($usedRamBytes['value']); ?> 
                                                of <?= \Lsia\Utils::bytesToHumanReadable($totalRamBytes['value']); ?> 
                                            </label>
                                            <meter id="ram_usage" value="<?= $usedRamBytes['value']; ?>" min="0" max="<?= $totalRamBytes['value']; ?>">
                                                <?= \Lsia\Utils::bytesToHumanReadable($usedRamBytes['value']); ?> 
                                                of <?= \Lsia\Utils::bytesToHumanReadable($totalRamBytes['value']); ?> 
                                            </meter>
                                        </li>
                                        
                                        <?php if($usedSwapBytes['value'] > 0  && $totalSwapBytes['value'] > 0): ?>
                                        
                                            <li class="collection-item"> 
                                                <strong><i class="material-icons tiny">memory</i> <?= $totalSwapBytes['label']; ?>:</strong>
                                                <label for="swap_usage"> 
                                                    <?= \Lsia\Utils::bytesToHumanReadable($usedSwapBytes['value']); ?> 
                                                    of <?= \Lsia\Utils::bytesToHumanReadable($totalSwapBytes['value']); ?> 
                                                </label>
                                                <meter id="swap_usage" value="<?= $usedSwapBytes['value']; ?>" min="0" max="<?= $totalSwapBytes['value']; ?>">
                                                    <?= \Lsia\Utils::bytesToHumanReadable($usedSwapBytes['value']); ?> 
                                                    of <?= \Lsia\Utils::bytesToHumanReadable($totalSwapBytes['value']); ?> 
                                                </meter>
                                            </li>
                                            
                                        <?php endif; // if($usedSwapBytes['value'] > 0  && $totalSwapBytes['value'] > 0) ?>
                                            
                                        <li class="collection-item">

                                            <ul class="collection with-header">

                                                <li class="collection-header"><h5>CPU Information</h5></li>
                                                
                                                <li class="collection-item"> <strong><?= $overallCpuUsagePercent['label']; ?>:</strong> <?= round($overallCpuUsagePercent['value'], 2); ?> % </li>
                                                <li class="collection-item"> <strong><?= $totalNumPhyscCpuCores['label']; ?>:</strong> <?= $totalNumPhyscCpuCores['value']; ?> </li>
                                                <li class="collection-item"> <strong><?= $totalNumVirtOrLogicalProcessors['label']; ?>:</strong> <?= $totalNumVirtOrLogicalProcessors['value']; ?> </li>

                                                <li class="collection-item">
                                                    <div class="row">
                                                        <?php foreach ($cpuInfo as $cpuInfoRecord): ?>

                                                             <!-- <strong><?= $processInfo['label']; ?>:</strong> <?= $processInfo['value']; ?> -->
                                                             
                                                             <div class="col s12 m6">
                                                                 
                                                                <div class="card blue-grey darken-1">
                                                                  <div class="card-content white-text">
                                                                    <span class="card-title"><?= $cpuInfoRecord['cpu_number']['label']; ?> <?= $cpuInfoRecord['cpu_number']['value']; ?></span>
                                                                    <p>
                                                                        <ul class="collection black-text">
                                                                            <li class="collection-item">
                                                                                <?php
                                                                                    $usagePercentId = 'cpu_usage_percentage_'.$cpuInfoRecord['cpu_number']['value'];
                                                                                ?>
                                                                                <strong><?= $cpuInfoRecord['usage_percentage']['label']; ?>:</strong>
                                                                                <label for="<?= $usagePercentId; ?>"> <?= $cpuInfoRecord['usage_percentage']['value']; ?> %</label>
                                                                                <meter id="<?= $usagePercentId; ?>" value="<?= $cpuInfoRecord['usage_percentage']['value']; ?>" min="0" max="100">
                                                                                    <?= $cpuInfoRecord['usage_percentage']['value']; ?> %
                                                                                </meter>
                                                                            </li>
                                                                            <li class="collection-item"><strong><?= $cpuInfoRecord['vendor']['label']; ?>:</strong> <?= $cpuInfoRecord['vendor']['value']; ?></li>
                                                                            <li class="collection-item"><strong><?= $cpuInfoRecord['model']['label']; ?>:</strong> <?= $cpuInfoRecord['model']['value']; ?></li>
                                                                            <li class="collection-item"><strong><?= $cpuInfoRecord['speed_mhz']['label']; ?>:</strong> <?= $cpuInfoRecord['speed_mhz']['value']; ?></li>
                                                                        </ul>
                                                                    </p>

                                                                  </div>
                                                                </div>
                                                                 
                                                             </div>

                                                        <?php endforeach; ?>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                            
                                    <?php endif; ?>
                                    
                                    <li class="collection-item"> <strong><?= $lastBootedOn['label']; ?>:</strong> <?= $lastBootedOn['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $uptime['label']; ?>:</strong> <?= $uptime['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $loggedInUsers['label']; ?>:</strong> <?= $loggedInUsers['value']; ?> </li>
                                    
                                    <?php if($selinuxEnabled['value'] !== -1): ?>
                                        <li class="collection-item"> <strong><?= $selinuxEnabled['label']; ?>:</strong> <?= $selinuxEnabled['value'] ? 'Yes' : 'No'; ?> </li>
                                        <li class="collection-item"> <strong><?= $selinuxMode['label']; ?>:</strong> <?= $selinuxMode['value']; ?> </li>
                                        <li class="collection-item"> <strong><?= $selinuxPolicy['label']; ?>:</strong> <?= $selinuxPolicy['value']; ?> </li>
                                    <?php endif; ?>
                                        
                                <?php if($__is_logged_in): ?>
                                    <li class="collection-item">
                                        
                                        <ul class="collection with-header">
                                            
                                            <li class="collection-header"><h5>Process Information</h5></li>
                                            
                                            <?php foreach ($processSummaryInfo as $processInfo): ?>
                                            
                                                <li class="collection-item"> <strong><?= $processInfo['label']; ?>:</strong> <?= $processInfo['value']; ?> </li>
                                                
                                            <?php endforeach; ?>
                                            
                                            <?php if( count($processesInfo) > 0 ): ?>
                                                
                                                <li class="collection-item"> 

                                                    <table id="processes-table" class="display">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Command Line</th>
                                                                <th>Threads</th>
                                                                <th>State</th>
                                                                <th>Memory</th>
                                                                <th>Peak Memory</th>
                                                                <th>PID</th>
                                                                <th>User</th>
                                                                <th>Bytes Read</th>
                                                                <th>Bytes Written</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php foreach($processesInfo as $processInfo): ?>
                                                                <tr>
                                                                    <td><?= $processInfo['name'] ?></td>
                                                                    <td title="<?= $processInfo['command_line'] ?>">
                                                                        <?= 
                                                                            \Lsia\Utils::getValIfTrueOrGetDefault(
                                                                                strlen($processInfo['command_line']) <= 20, 
                                                                                $processInfo['command_line'], 
                                                                                substr($processInfo['command_line'], 0, 20) . '....'
                                                                            ) 
                                                                        ?>
                                                                    </td>
                                                                    <td><?= $processInfo['num_threads'] ?></td>
                                                                    <td><?= $processInfo['state'] ?></td>
                                                                    <td><?= \Lsia\Utils::bytesToHumanReadable($processInfo['memory']) ?></td>
                                                                    <td><?= \Lsia\Utils::bytesToHumanReadable($processInfo['peak_memory']) ?></td>
                                                                    <td><?= $processInfo['pid'] ?></td>
                                                                    <td><?= $processInfo['user'] ?></td>
                                                                    <td><?= $processInfo['io_bytes_read'] ?></td>
                                                                    <td><?= $processInfo['io_bytes_written'] ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                    
                                    <li class="collection-item">
                                        
                                        <ul class="collection with-header">
                                            
                                            <li class="collection-header"><h5>Service Information</h5></li>
                                                                                        
                                            <?php if( count($servicesInfo) > 0 ): ?>
                                                
                                                <li class="collection-item"> 

                                                    <table id="services-table" class="display">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Description</th>
                                                                <th>Loaded</th>
                                                                <th>Started</th>
                                                                <th>State</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php foreach($servicesInfo as $serviceInfo): ?>
                                                                <tr>
                                                                    <td><?= $serviceInfo['name'] ?></td>
                                                                    <td><?= $serviceInfo['description'] ?></td>
                                                                    <td><?= $serviceInfo['loaded'] ?></td>
                                                                    <td><?= $serviceInfo['started'] ?></td>
                                                                    <td><?= $serviceInfo['state'] ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                <?php endif; //$__is_logged_in ?>    
                                </ul>
                            </div>
                        </div>
                        
                        <?php if(!$__is_logged_in): ?>
                            <div class="row">
                                <div class="col s12 center">
                                    <h5>Please Log in to view more detailed Server Information</h5>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <script>
                        $(document).ready(function () {
                            
                            <?php if($__is_logged_in): ?>
                                $('#processes-table').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('#services-table').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('select').formSelect();
                            <?php endif; ?>
                                
                        });
                    </script>