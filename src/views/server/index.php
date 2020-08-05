<?php
function formatPositiveBytes(float $bytes, $defaultReturnVal='') {
    
    if($bytes > -1) {
        
        return \Lsia\Utils::bytesToHumanReadable($bytes);
    }
    
    return $defaultReturnVal;
}
?>
        <link type="text/css" rel="stylesheet" href="<?= s3MVC_MakeLink('/datatables/jquery.dataTables.min.css'); ?>" media="screen,projection" />
        <script type="text/javascript" src="<?= s3MVC_MakeLink('/datatables/jquery.dataTables.min.js'); ?>"></script> 

        <link type="text/css" rel="stylesheet" href="<?= s3MVC_MakeLink('/datatables/responsive.dataTables.min.css'); ?>" media="screen,projection" />
        <script type="text/javascript" src="<?= s3MVC_MakeLink('/datatables/dataTables.responsive.min.js'); ?>"></script>   

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
                                                <?= formatPositiveBytes($usedRamBytes['value']); ?> 
                                                of <?= formatPositiveBytes($totalRamBytes['value']); ?> 
                                            </label>
                                            <meter id="ram_usage" value="<?= $usedRamBytes['value']; ?>" min="0" max="<?= $totalRamBytes['value']; ?>">
                                                <?= formatPositiveBytes($usedRamBytes['value']); ?> 
                                                of <?= formatPositiveBytes($totalRamBytes['value']); ?> 
                                            </meter>
                                        </li>
                                        
                                        <?php if($usedSwapBytes['value'] > 0  && $totalSwapBytes['value'] > 0): ?>
                                        
                                            <li class="collection-item"> 
                                                <strong><i class="material-icons tiny">memory</i> <?= $totalSwapBytes['label']; ?>:</strong>
                                                <label for="swap_usage"> 
                                                    <?= formatPositiveBytes($usedSwapBytes['value']); ?> 
                                                    of <?= formatPositiveBytes($totalSwapBytes['value']); ?> 
                                                </label>
                                                <meter id="swap_usage" value="<?= $usedSwapBytes['value']; ?>" min="0" max="<?= $totalSwapBytes['value']; ?>">
                                                    <?= formatPositiveBytes($usedSwapBytes['value']); ?> 
                                                    of <?= formatPositiveBytes($totalSwapBytes['value']); ?> 
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
                                            
                                            <li class="collection-header"><h5>Disk Drive Info</h5></li>
                                                                                        
                                            <?php if( count($diskDrivesInfo) > 0 ): ?>
                                                
                                                <li class="collection-item"> 

                                                    <table id="disk-drives-table" class="display">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Vendor</th>
                                                                <th>Device</th>
                                                                <th>Bytes Read</th>
                                                                <th>Bytes Written</th>
                                                                <th>Total Size (Bytes)</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php foreach($diskDrivesInfo as $deviceInfo): ?>
                                                                <tr>
                                                                    <td>
                                                                        <?= $deviceInfo['name'] ?>
                                                                        
                                                                        <?php if(count($deviceInfo['partitions']) > 0): ?>
                                                                            <ul>
                                                                            <?php foreach($deviceInfo['partitions'] as $partition): ?>

                                                                                <li>
                                                                                    <strong> &#9492; <?= $partition['name'] ?>:</strong> 
                                                                                    <?= formatPositiveBytes($partition['size_in_bytes']) ?>
                                                                                </li>

                                                                            <?php endforeach; ?>
                                                                            </ul>
                                                                        <?php endif;//if(count($deviceInfo['partitions']) > 0) ?>
                                                                    </td>
                                                                    <td><?= $deviceInfo['vendor'] ?></td>
                                                                    <td><?= $deviceInfo['device'] ?></td>
                                                                    <td><?= formatPositiveBytes($deviceInfo['bytes_read']) ?></td>
                                                                    <td><?= formatPositiveBytes($deviceInfo['bytes_written']) ?></td>
                                                                    <td><?= formatPositiveBytes($deviceInfo['size_in_bytes']) ?></td>
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
                                            
                                            <li class="collection-header"><h5>Disk Mount Info</h5></li>
                                                                                        
                                            <?php if( count($diskMountsInfo) > 0 ): ?>
                                                
                                                <li class="collection-item"> 

                                                    <table id="disk-mounts-table" class="display">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Mount Point</th>
                                                                <th>Mount Options</th>
                                                                <th>Type</th>
                                                                <th>Size</th>
                                                                <th>Used</th>
                                                                <th>Free</th>
                                                                <th>Percent Used</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php foreach($diskMountsInfo as $deviceInfo): ?>
                                                                <tr>
                                                                    <td><?= $deviceInfo['name'] ?></td>
                                                                    <td><?= $deviceInfo['mount_point'] ?></td>
                                                                    <td><?= count($deviceInfo['options']) > 0 ? "<ul><li>" . implode("</li><li>", $deviceInfo['options']) . "</li></ul>" : ''; ?></td>
                                                                    <td><?= $deviceInfo['type'] ?></td>
                                                                    
                                                                    <td><?= formatPositiveBytes($deviceInfo['size_in_bytes']) ?></td>
                                                                    <td><?= formatPositiveBytes($deviceInfo['used_bytes']) ?></td>
                                                                    <td><?= formatPositiveBytes($deviceInfo['free_bytes']) ?></td>
                                                                    <td>
                                                                        <?php if($deviceInfo['used_percent'] > -1): ?>
                                                                        
                                                                            <?= $deviceInfo['used_percent']; ?> %
                                                                        
                                                                        <?php endif; ?>
                                                                    </td>
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
                                            
                                            <li class="collection-header"><h5>Hardware Device Info</h5></li>
                                                                                        
                                            <?php if( count($hwInfo) > 0 ): ?>
                                                
                                                <li class="collection-item"> 

                                                    <table id="hw-devices-table" class="display">
                                                        <thead>
                                                            <tr>
                                                                <th>Type</th>
                                                                <th>Name</th>
                                                                <th>Vendor</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php foreach($hwInfo as $deviceInfo): ?>
                                                                <tr>
                                                                    <td><?= $deviceInfo['type'] ?></td>
                                                                    <td><?= $deviceInfo['name'] ?></td>
                                                                    <td><?= $deviceInfo['vendor'] ?></td>
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
                                            
                                            <li class="collection-header"><h5>Sound Card Info</h5></li>
                                                                                        
                                            <?php if( count($sCardInfo) > 0 ): ?>
                                                
                                                <li class="collection-item"> 

                                                    <table id="soundcard-devices-table" class="display">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Vendor</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php foreach($sCardInfo as $deviceInfo): ?>
                                                                <tr>
                                                                    <td><?= $deviceInfo['name'] ?></td>
                                                                    <td><?= $deviceInfo['vendor'] ?></td>
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
                                            
                                            <li class="collection-header"><h5>Network Devices</h5></li>
                                                                                        
                                            <?php if( count($networkInfo) > 0 ): ?>
                                                
                                                <li class="collection-item"> 

                                                    <table id="network-devices-table" class="display">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Type</th>
                                                                <th>State</th>
                                                                <th>Port Speed</th>
                                                                <th>Bytes Received</th>
                                                                <th>Bytes Sent</th>
                                                                <th>Packets Received</th>
                                                                <th>Packets Sent</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php foreach($networkInfo as $netInfo): ?>
                                                                <tr>
                                                                    <td><?= $netInfo['name'] ?></td>
                                                                    <td><?= $netInfo['type'] ?></td>
                                                                    <td><?= $netInfo['state'] ?></td>
                                                                    <td><?= $netInfo['speed_bits_per_second'] == -1 ? '' : ($netInfo['speed_bits_per_second'] / 1000000000) . ' Gb/s'; ?></td>
                                                                    <td><?= $netInfo['num_bytes_received'] == -1 ? '' : formatPositiveBytes($netInfo['num_bytes_received']); ?></td>
                                                                    <td><?= $netInfo['num_bytes_sent'] == -1 ? '' : formatPositiveBytes($netInfo['num_bytes_sent']); ?></td>
                                                                    <td><?= $netInfo['num_received_packets'] == -1 ? '' : number_format($netInfo['num_received_packets']); ?></td>
                                                                    <td><?= $netInfo['num_sent_packets'] == -1 ? '' : number_format($netInfo['num_sent_packets']); ?></td>
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
                                                                    <td><?= formatPositiveBytes($processInfo['memory']) ?></td>
                                                                    <td><?= formatPositiveBytes($processInfo['peak_memory']) ?></td>
                                                                    <td><?= $processInfo['pid'] ?></td>
                                                                    <td><?= $processInfo['user'] ?></td>
                                                                    <td><?= formatPositiveBytes($processInfo['io_bytes_read'], 0) ?></td>
                                                                    <td><?= formatPositiveBytes($processInfo['io_bytes_written'], 0) ?></td>
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
                                $('#soundcard-devices-table').DataTable({"responsive": true, "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('#disk-drives-table').DataTable({"responsive": true, "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('#disk-mounts-table').DataTable({"responsive": true, "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('#hw-devices-table').DataTable({"responsive": true, "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('#network-devices-table').DataTable({"responsive": true, "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('#processes-table').DataTable({"responsive": true, "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('#services-table').DataTable({"responsive": true, "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('select').formSelect();
                            <?php endif; ?>
                                
                        });
                    </script>

