                    <div id="server-index">
                        <div class="row pad-t-2-5-on-med-and-down">
                            <div class="col s12">
                                <ul class="collection with-header">
                                    <li class="collection-header"><h5>Server Summary</h5></li>
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

                                                <li class="collection-header"><h6>CPU Information</h6></li>
                                                
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
                                    
                                    <li class="collection-item">
                                        
                                        <ul class="collection with-header">
                                            
                                            <li class="collection-header"><h6>Process Information</h6></li>
                                            
                                            <?php foreach ($processSummaryInfo as $processInfo): ?>
                                            
                                                <li class="collection-item"> <strong><?= $processInfo['label']; ?>:</strong> <?= $processInfo['value']; ?> </li>
                                                
                                            <?php endforeach; ?>
                                                
                                        </ul>
                                    </li>
                                    
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
